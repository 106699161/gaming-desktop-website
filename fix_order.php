<?php
session_start();

/*
 * fix_order.php
 * Displays validation errors returned by process_order.php.
 */

if ($_SERVER["REQUEST_METHOD"] !== "GET" || !isset($_SESSION["order_form"])) {
    header("Location: payment.php");
    exit();
}

$form = $_SESSION["order_form"];
$errors = isset($_SESSION["order_errors"]) ? $_SESSION["order_errors"] : array();

function value($name) {
    global $form;
    return isset($form[$name]) ? htmlspecialchars($form[$name], ENT_QUOTES, "UTF-8") : "";
}

function error_message($name) {
    global $errors;
    if (isset($errors[$name])) {
        return '<span class="error-message">' .
               htmlspecialchars($errors[$name], ENT_QUOTES, "UTF-8") .
               '</span>';
    }
    return "";
}

function selected($name, $option) {
    global $form;
    return (isset($form[$name]) && $form[$name] === $option) ? "selected" : "";
}

function checked_option($option) {
    global $form;
    return (isset($form["options"]) &&
            is_array($form["options"]) &&
            in_array($option, $form["options"], true)) ? "checked" : "";
}

function checked_contact($option) {
    global $form;
    return (isset($form["contact"]) && $form["contact"] === $option) ? "checked" : "";
}
?>

<?php include("header.inc"); ?>

<section class="banner">
    <h1>Fix Your Order</h1>
    <p>
        Some information needs to be corrected before your order can be processed.
        Please review the highlighted fields and submit the form again.
    </p>
</section>

<section class="container fix-order">

    <h2>Correct Your Order Details</h2>

    <div class="error-summary">
        <strong>Please correct the following information:</strong>
        <ul>
            <?php foreach ($errors as $message): ?>
                <li><?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <form action="process_order.php" method="post" novalidate>

        <fieldset>
            <legend>Customer Details</legend>

            <label for="fname">First Name</label>
            <input
                type="text"
                id="fname"
                name="fname"
                maxlength="25"
                value="<?php echo value("fname"); ?>"
                class="<?php echo isset($errors["fname"]) ? "invalid" : ""; ?>">
            <?php echo error_message("fname"); ?>

            <label for="lname">Last Name</label>
            <input
                type="text"
                id="lname"
                name="lname"
                maxlength="25"
                value="<?php echo value("lname"); ?>"
                class="<?php echo isset($errors["lname"]) ? "invalid" : ""; ?>">
            <?php echo error_message("lname"); ?>

            <label for="email">Email</label>
            <input
                type="text"
                id="email"
                name="email"
                value="<?php echo value("email"); ?>"
                class="<?php echo isset($errors["email"]) ? "invalid" : ""; ?>">
            <?php echo error_message("email"); ?>

            <label for="phone">Phone Number</label>
            <input
                type="text"
                id="phone"
                name="phone"
                maxlength="10"
                value="<?php echo value("phone"); ?>"
                class="<?php echo isset($errors["phone"]) ? "invalid" : ""; ?>">
            <?php echo error_message("phone"); ?>

            <label>Preferred Contact</label>

            <div class="radio-group">
                <label>
                    <input type="radio" name="contact" value="Email"
                        <?php echo checked_contact("Email"); ?>>
                    Email
                </label>

                <label>
                    <input type="radio" name="contact" value="Phone"
                        <?php echo checked_contact("Phone"); ?>>
                    Phone
                </label>

                <label>
                    <input type="radio" name="contact" value="Post"
                        <?php echo checked_contact("Post"); ?>>
                    Post
                </label>
            </div>

            <?php echo error_message("contact"); ?>

        </fieldset>


        <fieldset>
            <legend>Delivery Address</legend>

            <label for="str_addr">Street Address</label>
            <input
                type="text"
                id="str_addr"
                name="str_addr"
                maxlength="100"
                value="<?php echo value("str_addr"); ?>"
                class="<?php echo isset($errors["str_addr"]) ? "invalid" : ""; ?>">
            <?php echo error_message("str_addr"); ?>

            <label for="suburb">Suburb / Town</label>
            <input
                type="text"
                id="suburb"
                name="suburb"
                maxlength="20"
                value="<?php echo value("suburb"); ?>"
                class="<?php echo isset($errors["suburb"]) ? "invalid" : ""; ?>">
            <?php echo error_message("suburb"); ?>

            <label for="state">State</label>
            <select
                id="state"
                name="state"
                class="<?php echo isset($errors["state"]) ? "invalid" : ""; ?>">

                <option value="">Select State</option>
                <option value="VIC" <?php echo selected("state", "VIC"); ?>>VIC</option>
                <option value="NSW" <?php echo selected("state", "NSW"); ?>>NSW</option>
                <option value="QLD" <?php echo selected("state", "QLD"); ?>>QLD</option>
                <option value="NT" <?php echo selected("state", "NT"); ?>>NT</option>
                <option value="WA" <?php echo selected("state", "WA"); ?>>WA</option>
                <option value="SA" <?php echo selected("state", "SA"); ?>>SA</option>
                <option value="TAS" <?php echo selected("state", "TAS"); ?>>TAS</option>
                <option value="ACT" <?php echo selected("state", "ACT"); ?>>ACT</option>
            </select>
            <?php echo error_message("state"); ?>

            <label for="pcode">Postcode</label>
            <input
                type="text"
                id="pcode"
                name="pcode"
                maxlength="4"
                value="<?php echo value("pcode"); ?>"
                class="<?php echo isset($errors["pcode"]) ? "invalid" : ""; ?>">
            <?php echo error_message("pcode"); ?>

        </fieldset>


        <fieldset>
            <legend>Product Details</legend>

            <label for="product">Select Product</label>

            <select
                id="product"
                name="product"
                class="<?php echo isset($errors["product"]) ? "invalid" : ""; ?>">

                <option value="">Choose Gaming PC</option>
                <option value="ASUS ROG STRIX" <?php echo selected("product", "ASUS ROG STRIX"); ?>>
                    ASUS ROG STRIX
                </option>
                <option value="MSI Infinite RS" <?php echo selected("product", "MSI Infinite RS"); ?>>
                    MSI Infinite RS
                </option>
                <option value="Alienware Aurora" <?php echo selected("product", "Alienware Aurora"); ?>>
                    Alienware Aurora
                </option>
                <option value="Corsair Vengeance" <?php echo selected("product", "Corsair Vengeance"); ?>>
                    Corsair Vengeance
                </option>
                <option value="HP Omen" <?php echo selected("product", "HP Omen"); ?>>
                    HP Omen
                </option>
                <option value="Lenovo Legion" <?php echo selected("product", "Lenovo Legion"); ?>>
                    Lenovo Legion
                </option>
                <option value="NZXT Player One" <?php echo selected("product", "NZXT Player One"); ?>>
                    NZXT Player One
                </option>
                <option value="Custom RGB Gaming PC" <?php echo selected("product", "Custom RGB Gaming PC"); ?>>
                    Custom RGB Gaming PC
                </option>
            </select>

            <?php echo error_message("product"); ?>

            <label for="qty">Quantity</label>

            <input
                type="text"
                id="qty"
                name="qty"
                value="<?php echo value("qty"); ?>"
                class="<?php echo isset($errors["qty"]) ? "invalid" : ""; ?>">

            <?php echo error_message("qty"); ?>


            <label>Additional Product Options</label>

            <div class="check-group">

                <label>
                    <input type="checkbox"
                           name="options[]"
                           value="RTX Graphics"
                           <?php echo checked_option("RTX Graphics"); ?>>
                    RTX Graphics (+Rs.50,000)
                </label>

                <label>
                    <input type="checkbox"
                           name="options[]"
                           value="RGB Lighting"
                           <?php echo checked_option("RGB Lighting"); ?>>
                    RGB Lighting (+Rs.15,000)
                </label>

                <label>
                    <input type="checkbox"
                           name="options[]"
                           value="Liquid Cooling"
                           <?php echo checked_option("Liquid Cooling"); ?>>
                    Liquid Cooling (+Rs.30,000)
                </label>

                <label>
                    <input type="checkbox"
                           name="options[]"
                           value="DDR5 RAM"
                           <?php echo checked_option("DDR5 RAM"); ?>>
                    DDR5 RAM (+Rs.25,000)
                </label>

                <label>
                    <input type="checkbox"
                           name="options[]"
                           value="SSD Storage"
                           <?php echo checked_option("SSD Storage"); ?>>
                    SSD Storage (+Rs.20,000)
                </label>

                <label>
                    <input type="checkbox"
                           name="options[]"
                           value="WiFi 6"
                           <?php echo checked_option("WiFi 6"); ?>>
                    WiFi 6 (+Rs.10,000)
                </label>

            </div>

            <?php echo error_message("options"); ?>

            <label for="comments">Comments</label>

            <textarea
                id="comments"
                name="comments"
                rows="5"><?php echo value("comments"); ?></textarea>

        </fieldset>


        <fieldset>
            <legend>Credit Card Details</legend>

            <p class="card-warning">
                For security, credit card details were not saved from the previous
                submission. Please enter them again.
            </p>

            <label for="card_type">Credit Card Type</label>

            <select
                id="card_type"
                name="card_type"
                class="<?php echo isset($errors["card_type"]) ? "invalid" : ""; ?>">

                <option value="">Select Card Type</option>
                <option value="Visa">Visa</option>
                <option value="Mastercard">Mastercard</option>
                <option value="American Express">American Express</option>

            </select>

            <?php echo error_message("card_type"); ?>


            <label for="card_name">Name on Credit Card</label>

            <input
                type="text"
                id="card_name"
                name="card_name"
                autocomplete="off"
                class="<?php echo isset($errors["card_name"]) ? "invalid" : ""; ?>">

            <?php echo error_message("card_name"); ?>


            <label for="card_number">Credit Card Number</label>

            <input
                type="text"
                id="card_number"
                name="card_number"
                autocomplete="off"
                class="<?php echo isset($errors["card_number"]) ? "invalid" : ""; ?>">

            <?php echo error_message("card_number"); ?>


            <label for="expiry">Card Expiry Date (MM-YY)</label>

            <input
                type="text"
                id="expiry"
                name="expiry"
                placeholder="MM-YY"
                autocomplete="off"
                class="<?php echo isset($errors["expiry"]) ? "invalid" : ""; ?>">

            <?php echo error_message("expiry"); ?>


            <label for="cvv">CVV</label>

            <input
                type="password"
                id="cvv"
                name="cvv"
                maxlength="4"
                autocomplete="off"
                class="<?php echo isset($errors["cvv"]) ? "invalid" : ""; ?>">

            <?php echo error_message("cvv"); ?>

        </fieldset>


        <button type="submit" class="checkout-btn">
            Submit Corrected Order
        </button>

    </form>

</section>

<?php include("footer.inc"); ?>
