<?php
session_start();

/*
 * receipt.php
 * Displays the successfully stored order receipt.
 */

/* Do not allow direct URL access without a completed order. */
if (!isset($_SESSION["order_id"])) {
    header("Location: payment.php");
    exit();
}

include("settings.php");

$conn = new mysqli($host, $user, $pwd, $sql_db);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");

$order_id = (int)$_SESSION["order_id"];

$sql = "SELECT
            order_id,
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
            order_time,
            order_status,
            comments
        FROM orders
        WHERE order_id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Unable to prepare receipt query.");
}

$stmt->bind_param("i", $order_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();

    unset($_SESSION["order_id"]);

    header("Location: payment.php");
    exit();
}

$order = $result->fetch_assoc();

$stmt->close();
$conn->close();

function output($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function money($value) {
    return "Rs. " . number_format((float)$value, 2);
}
?>

<?php include("header.inc"); ?>

<section class="banner">
    <h1>Order Receipt</h1>
    <p>Your Titan Gaming Desktops order has been successfully received.</p>
</section>

<section class="receipt-container">

    <div class="success-message">
        <h2>Order Successfully Submitted</h2>
        <p>
            Thank you, <?php echo output($order["fname"]); ?>.
            Your order has been recorded successfully.
        </p>
    </div>


    <section class="receipt-section">

        <h2>Order Information</h2>

        <div class="receipt-grid">

            <div>
                <strong>Order Number</strong>
                <span>#<?php echo output($order["order_id"]); ?></span>
            </div>

            <div>
                <strong>Order Date</strong>
                <span><?php echo output($order["order_time"]); ?></span>
            </div>

            <div>
                <strong>Order Status</strong>
                <span class="status"><?php echo output($order["order_status"]); ?></span>
            </div>

            <div>
                <strong>Total Cost</strong>
                <span class="total"><?php echo money($order["order_cost"]); ?></span>
            </div>

        </div>

    </section>


    <section class="receipt-section">

        <h2>Customer Details</h2>

        <table class="receipt-table">

            <tr>
                <th>First Name</th>
                <td><?php echo output($order["fname"]); ?></td>
            </tr>

            <tr>
                <th>Last Name</th>
                <td><?php echo output($order["lname"]); ?></td>
            </tr>

            <tr>
                <th>Email</th>
                <td><?php echo output($order["email"]); ?></td>
            </tr>

            <tr>
                <th>Phone</th>
                <td><?php echo output($order["phone"]); ?></td>
            </tr>

            <tr>
                <th>Preferred Contact</th>
                <td><?php echo output($order["contact"]); ?></td>
            </tr>

        </table>

    </section>


    <section class="receipt-section">

        <h2>Delivery Address</h2>

        <table class="receipt-table">

            <tr>
                <th>Street Address</th>
                <td><?php echo output($order["str_addr"]); ?></td>
            </tr>

            <tr>
                <th>Suburb / Town</th>
                <td><?php echo output($order["suburb"]); ?></td>
            </tr>

            <tr>
                <th>State</th>
                <td><?php echo output($order["state"]); ?></td>
            </tr>

            <tr>
                <th>Postcode</th>
                <td><?php echo output($order["pcode"]); ?></td>
            </tr>

        </table>

    </section>


    <section class="receipt-section">

        <h2>Product Details</h2>

        <table class="receipt-table">

            <tr>
                <th>Product</th>
                <td><?php echo output($order["product"]); ?></td>
            </tr>

            <tr>
                <th>Quantity</th>
                <td><?php echo output($order["quantity"]); ?></td>
            </tr>

            <tr>
                <th>Additional Options</th>
                <td>
                    <?php
                    echo $order["options"] !== ""
                        ? output($order["options"])
                        : "None";
                    ?>
                </td>
            </tr>

            <tr>
                <th>Order Cost</th>
                <td class="total"><?php echo money($order["order_cost"]); ?></td>
            </tr>

        </table>

    </section>


    <section class="receipt-section">

        <h2>Payment Details</h2>

        <table class="receipt-table">

            <tr>
                <th>Card Type</th>
                <td><?php echo output($order["card_type"]); ?></td>
            </tr>

            <tr>
                <th>Name on Card</th>
                <td><?php echo output($order["card_name"]); ?></td>
            </tr>

            <tr>
                <th>Card Number</th>
                <td><?php echo output($order["card_number"]); ?></td>
            </tr>

            <tr>
                <th>Expiry Date</th>
                <td><?php echo output($order["expiry"]); ?></td>
            </tr>

        </table>

        <p class="security-note">
            For security, the complete credit card number and CVV are not displayed.
        </p>

    </section>


    <?php if ($order["comments"] !== ""): ?>

        <section class="receipt-section">

            <h2>Comments</h2>

            <p class="comments">
                <?php echo nl2br(output($order["comments"])); ?>
            </p>

        </section>

    <?php endif; ?>


    <div class="receipt-actions">

        <button type="button" onclick="window.print()">
            Print Receipt
        </button>

        <a href="index.php">
            Back to Home
        </a>

    </div>

</section>

<?php include("footer.inc"); ?>
