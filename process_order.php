<?php
session_start();

/*
 * process_order.php
 * Server-side processing for the Titan Gaming Desktops payment form.
 */

/* Do not allow direct access through the browser. */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: payment.php");
    exit();
}

include("settings.php");

$conn = new mysqli($host, $user, $pwd, $sql_db);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");

/* Create the orders table automatically if it does not exist. */
$create_table = "CREATE TABLE IF NOT EXISTS orders (
    order_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(25) NOT NULL,
    lname VARCHAR(25) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(10) NOT NULL,
    contact VARCHAR(10) NOT NULL,
    str_addr VARCHAR(100) NOT NULL,
    suburb VARCHAR(20) NOT NULL,
    state CHAR(3) NOT NULL,
    pcode CHAR(4) NOT NULL,
    product VARCHAR(60) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    options TEXT,
    card_type VARCHAR(25) NOT NULL,
    card_name VARCHAR(80) NOT NULL,
    card_number VARCHAR(20) NOT NULL,
    expiry VARCHAR(5) NOT NULL,
    order_cost DECIMAL(12,2) NOT NULL,
    order_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    order_status ENUM('PENDING','FULFILLED','PAID','ARCHIVED') NOT NULL DEFAULT 'PENDING',
    comments TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!$conn->query($create_table)) {
    die("Unable to create orders table.");
}

/* Sanitize submitted values. */
function sanitize($value) {
    return trim(stripslashes(strip_tags((string)$value)));
}

function old_value($name) {
    return isset($_POST[$name]) ? sanitize($_POST[$name]) : "";
}

$errors = array();

$fname     = old_value("fname");
$lname     = old_value("lname");
$email     = old_value("email");
$phone     = old_value("phone");
$contact   = old_value("contact");
$str_addr  = old_value("str_addr");
$suburb    = old_value("suburb");
$state     = old_value("state");
$pcode     = old_value("pcode");
$product   = old_value("product");
$qty_raw   = old_value("qty");

$card_type   = old_value("card_type");
$card_name   = old_value("card_name");
$card_number = preg_replace("/\D/", "", old_value("card_number"));
$expiry      = old_value("expiry");
$cvv         = preg_replace("/\D/", "", old_value("cvv"));

$comments = old_value("comments");

$options = array();

if (isset($_POST["options"]) && is_array($_POST["options"])) {
    foreach ($_POST["options"] as $option) {
        $options[] = sanitize($option);
    }
}


/* ================= CUSTOMER VALIDATION ================= */

if ($fname === "" || !preg_match("/^[A-Za-z ]{1,25}$/", $fname)) {
    $errors["fname"] = "First name must contain letters and spaces only.";
}

if ($lname === "" || !preg_match("/^[A-Za-z ]{1,25}$/", $lname)) {
    $errors["lname"] = "Last name must contain letters and spaces only.";
}

if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Please enter a valid email address.";
}

if ($phone === "" || !preg_match("/^[0-9]{10}$/", $phone)) {
    $errors["phone"] = "Phone number must contain exactly 10 digits.";
}

$allowed_contacts = array("Email", "Phone", "Post");

if (!in_array($contact, $allowed_contacts, true)) {
    $errors["contact"] = "Please select a preferred contact method.";
}


/* ================= ADDRESS VALIDATION ================= */

if ($str_addr === "") {
    $errors["str_addr"] = "Street address is required.";
}

if ($suburb === "" || !preg_match("/^[A-Za-z0-9 .'-]{1,20}$/", $suburb)) {
    $errors["suburb"] = "Please enter a valid suburb or town.";
}

$allowed_states = array("VIC", "NSW", "QLD", "NT", "WA", "SA", "TAS", "ACT");

if (!in_array($state, $allowed_states, true)) {
    $errors["state"] = "Please select a valid state.";
}

if (!preg_match("/^[0-9]{4}$/", $pcode)) {

    $errors["pcode"] = "Postcode must contain exactly 4 digits.";

} else {

    $postcode = (int)$pcode;
    $postcode_valid = false;

    switch ($state) {

        case "NSW":
            $postcode_valid = ($postcode >= 2000 && $postcode <= 2999);
            break;

        case "ACT":
            $postcode_valid = ($postcode >= 2600 && $postcode <= 2618);
            break;

        case "VIC":
            $postcode_valid = (($postcode >= 3000 && $postcode <= 3999) ||
                               ($postcode >= 8000 && $postcode <= 8999));
            break;

        case "QLD":
            $postcode_valid = (($postcode >= 4000 && $postcode <= 4999) ||
                               ($postcode >= 9000 && $postcode <= 9999));
            break;

        case "SA":
            $postcode_valid = ($postcode >= 5000 && $postcode <= 5999);
            break;

        case "WA":
            $postcode_valid = ($postcode >= 6000 && $postcode <= 6999);
            break;

        case "TAS":
            $postcode_valid = ($postcode >= 7000 && $postcode <= 7999);
            break;

        case "NT":
            $postcode_valid = ($postcode >= 800 && $postcode <= 999);
            break;
    }

    if (!$postcode_valid && !isset($errors["state"])) {
        $errors["pcode"] = "Postcode does not match the selected state.";
    }
}


/* ================= PRODUCT VALIDATION ================= */

$product_prices = array(

    "ASUS ROG STRIX"       => 1000000,
    "MSI Infinite RS"      => 850000,
    "Alienware Aurora"     => 450000,
    "Corsair Vengeance"    => 700000,
    "HP Omen"              => 650000,
    "Lenovo Legion"        => 680000,
    "NZXT Player One"      => 590000,
    "Custom RGB Gaming PC" => 1250000
);

$option_prices = array(

    "RTX Graphics"  => 50000,
    "RGB Lighting"  => 15000,
    "Liquid Cooling" => 30000,
    "DDR5 RAM"      => 25000,
    "SSD Storage"   => 20000,
    "WiFi 6"        => 10000
);

if (!array_key_exists($product, $product_prices)) {
    $errors["product"] = "Please select a valid product.";
}

if (!ctype_digit($qty_raw) || (int)$qty_raw < 1 || (int)$qty_raw > 10) {
    $errors["qty"] = "Quantity must be between 1 and 10.";
}

foreach ($options as $option) {

    if (!array_key_exists($option, $option_prices)) {
        $errors["options"] = "Invalid product option selected.";
        break;
    }
}


/* ================= CREDIT CARD VALIDATION ================= */

$allowed_cards = array("Visa", "Mastercard", "American Express");

if (!in_array($card_type, $allowed_cards, true)) {
    $errors["card_type"] = "Please select a valid credit card type.";
}

if ($card_name === "" || !preg_match("/^[A-Za-z ]{1,40}$/", $card_name)) {
    $errors["card_name"] = "Card name must contain letters and spaces only (maximum 40 characters).";
}


/*
 * Card rules required by the assignment:
 * Visa       = 16 digits, starts with 4
 * Mastercard = 16 digits, starts from 51 to 55
 * Amex       = 15 digits, starts with 34 or 37
 */

if ($card_type === "Visa") {

    if (!preg_match("/^4[0-9]{15}$/", $card_number)) {
        $errors["card_number"] =
            "Visa must contain 16 digits and start with 4.";
    }

} elseif ($card_type === "Mastercard") {

    if (!preg_match("/^5[1-5][0-9]{14}$/", $card_number)) {
        $errors["card_number"] =
            "Mastercard must contain 16 digits and start from 51 to 55.";
    }

} elseif ($card_type === "American Express") {

    if (!preg_match("/^(34|37)[0-9]{13}$/", $card_number)) {
        $errors["card_number"] =
            "American Express must contain 15 digits and start with 34 or 37.";
    }
}

if (!preg_match("/^(0[1-9]|1[0-2])-[0-9]{2}$/", $expiry)) {

    $errors["expiry"] = "Expiry date must use MM-YY format.";

} else {

    list($month, $year) = explode("-", $expiry);

    $expiry_month = (int)$month;
    $expiry_year  = 2000 + (int)$year;

    $current_month = (int)date("n");
    $current_year  = (int)date("Y");

    if ($expiry_year < $current_year ||
        ($expiry_year === $current_year &&
         $expiry_month < $current_month)) {

        $errors["expiry"] = "Credit card has expired.";
    }
}

if (!preg_match("/^[0-9]{3,4}$/", $cvv)) {
    $errors["cvv"] = "CVV must contain 3 or 4 digits.";
}


/* ================= SEND ERRORS TO FIX_ORDER ================= */

if (!empty($errors)) {

    /*
     * Keep normal form values so fix_order.php can refill them.
     * Credit-card fields are deliberately NOT saved.
     */
    $_SESSION["order_form"] = array(

        "fname"    => $fname,
        "lname"    => $lname,
        "email"    => $email,
        "phone"    => $phone,
        "contact"  => $contact,

        "str_addr" => $str_addr,
        "suburb"   => $suburb,
        "state"    => $state,
        "pcode"    => $pcode,

        "product"  => $product,
        "qty"      => $qty_raw,
        "options"  => $options,

        "comments" => $comments
    );

    $_SESSION["order_errors"] = $errors;

    header("Location: fix_order.php");
    exit();
}


/* ================= CALCULATE TOTAL ================= */

$quantity = (int)$qty_raw;

$unit_price = $product_prices[$product];

$options_total = 0;

foreach ($options as $option) {
    $options_total += $option_prices[$option];
}

$order_cost = ($unit_price + $options_total) * $quantity;

$options_text = implode(", ", $options);


/*
 * Do not store the full card number.
 * Keep only the last four digits for the receipt/order record.
 */
$masked_card =
    str_repeat("*", strlen($card_number) - 4) .
    substr($card_number, -4);


/* ================= INSERT ORDER ================= */

$sql = "INSERT INTO orders
    (
        fname,
        lname,
        email,
        phone,
        contact,
        str_addr,
        suburb,
        state,
        pcode,
        product,
        quantity,
        options,
        card_type,
        card_name,
        card_number,
        expiry,
        order_cost,
        order_status,
        comments
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Unable to prepare order query.");
}

$stmt->bind_param(
    "ssssssssssisssssds",
    $fname,
    $lname,
    $email,
    $phone,
    $contact,
    $str_addr,
    $suburb,
    $state,
    $pcode,
    $product,
    $quantity,
    $options_text,
    $card_type,
    $card_name,
    $masked_card,
    $expiry,
    $order_cost,
    $comments
);

if (!$stmt->execute()) {
    die("Unable to save the order.");
}

$order_id = $stmt->insert_id;


/* ================= PASS TO RECEIPT ================= */

$_SESSION["order_id"] = $order_id;

unset($_SESSION["order_form"]);
unset($_SESSION["order_errors"]);

$stmt->close();
$conn->close();

/* process_order.php does not display an HTML page. */
header("Location: receipt.php");
exit();
?>
