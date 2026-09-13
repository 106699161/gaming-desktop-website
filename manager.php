<?php
require_once "manager_auth.inc";
require_manager_login();
require_once "settings.php";

$conn = new mysqli($host, $user, $pwd, $sql_db);

if ($conn->connect_error) {
    die("Database connection failed.");
}

/* Update order status */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    $order_id = (int)((isset($_POST["order_id"]) ? $_POST["order_id"] : 0));
    $status = (isset($_POST["status"]) ? $_POST["status"] : "");

    $allowed = ["PENDING", "FULFILLED", "PAID", "ARCHIVED"];

    if ($order_id > 0 && in_array($status, $allowed, true)) {
        $stmt = $conn->prepare(
            "UPDATE orders SET order_status = ? WHERE order_id = ?"
        );
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: manager.php");
    exit;
}

/* Cancel/delete only pending orders */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cancel_order"])) {
    $order_id = (int)((isset($_POST["order_id"]) ? $_POST["order_id"] : 0));

    if ($order_id > 0) {
        $stmt = $conn->prepare(
            "DELETE FROM orders
             WHERE order_id = ? AND order_status = 'PENDING'"
        );
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: manager.php");
    exit;
}

$query_type = (isset($_GET["query_type"]) ? $_GET["query_type"] : "all");
$search = trim((isset($_GET["search"]) ? $_GET["search"] : ""));

$sql = "
    SELECT order_id, order_time, fname, lname, product, quantity,
           options, order_cost, order_status
    FROM orders
";

$params = [];
$types = "";

if ($query_type === "customer") {
    $sql .= " WHERE CONCAT(fname, ' ', lname) LIKE ?";
    $params[] = "%" . $search . "%";
    $types = "s";
} elseif ($query_type === "product") {
    $sql .= " WHERE product LIKE ?";
    $params[] = "%" . $search . "%";
    $types = "s";
} elseif ($query_type === "pending") {
    $sql .= " WHERE order_status = 'PENDING'";
} elseif ($query_type === "cost") {
    $sql .= " ORDER BY order_cost DESC";
} else {
    $sql .= " ORDER BY order_time DESC";
}

$rows = [];

$stmt = $conn->prepare($sql);

if ($types !== "") {
call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $params));
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$stmt->close();
$conn->close();
?>
<?php include("header.inc"); ?>

<section class="banner">
    <h1>Manager Order Management</h1>
    <p>View, search, update and cancel customer orders.</p>
</section>

<section class="manager-panel">

    <div class="manager-links">
        <a href="manager.php?query_type=all">All Orders</a>
        <a href="manager.php?query_type=pending">Pending Orders</a>
        <a href="manager.php?query_type=cost">Sort by Cost</a>
        <a href="reports.php">Advanced Reports</a>

        <?php if (((isset($_SESSION["manager_role"]) ? $_SESSION["manager_role"] : "")) === "SUPER"): ?>
            <a href="manager_register.php">Register Manager</a>
        <?php endif; ?>

        <a href="manager_logout.php">Logout</a>
    </div>

    <form method="get" action="manager.php" class="manager-filter-form">
        <label for="query_type">Search / Query</label>

        <select name="query_type" id="query_type">
            <option value="all" <?php echo $query_type === "all" ? "selected" : ""; ?>>
                All Orders
            </option>
            <option value="customer" <?php echo $query_type === "customer" ? "selected" : ""; ?>>
                Customer Name
            </option>
            <option value="product" <?php echo $query_type === "product" ? "selected" : ""; ?>>
                Product
            </option>
            <option value="pending" <?php echo $query_type === "pending" ? "selected" : ""; ?>>
                Pending Orders
            </option>
            <option value="cost" <?php echo $query_type === "cost" ? "selected" : ""; ?>>
                Total Cost
            </option>
        </select>

        <input type="text" name="search"
               value="<?php echo htmlspecialchars($search); ?>"
               placeholder="Customer name or product">

        <button type="submit">Search</button>
    </form>

    <p>
        Logged in as:
        <strong><?php echo htmlspecialchars($_SESSION["manager_username"]); ?></strong>
    </p>

    <div class="table-scroll">
        <table border="1" cellpadding="10" cellspacing="0" width="100%">
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Product Details</th>
                <th>Customer</th>
                <th>Cost</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo (int)$row["order_id"]; ?></td>

                    <td><?php echo htmlspecialchars($row["order_time"]); ?></td>

                    <td>
                        <?php echo htmlspecialchars($row["product"]); ?><br>
                        Quantity: <?php echo (int)$row["quantity"]; ?><br>
                        Options: <?php echo htmlspecialchars($row["options"] ?: "None"); ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $row["fname"] . " " . $row["lname"]
                        );
                        ?>
                    </td>

                    <td>
                        Rs. <?php echo number_format((float)$row["order_cost"], 2); ?>
                    </td>

                    <td><?php echo htmlspecialchars($row["order_status"]); ?></td>

                    <td>
                        <form method="post" action="manager.php">
                            <input type="hidden" name="order_id"
                                   value="<?php echo (int)$row["order_id"]; ?>">

                            <select name="status">
                                <?php
                                $statuses = ["PENDING", "FULFILLED", "PAID", "ARCHIVED"];
                                foreach ($statuses as $status):
                                ?>
                                    <option value="<?php echo $status; ?>"
                                        <?php echo $row["order_status"] === $status ? "selected" : ""; ?>>
                                        <?php echo $status; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button type="submit" name="update_status">
                                Update
                            </button>
                        </form>

                        <?php if ($row["order_status"] === "PENDING"): ?>
                            <form method="post" action="manager.php"
                                  onsubmit="return confirm('Cancel this pending order?');">
                                <input type="hidden" name="order_id"
                                       value="<?php echo (int)$row["order_id"]; ?>">
                                <button type="submit" name="cancel_order">
                                    Cancel
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>

<?php include("footer.inc"); ?>
