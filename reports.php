<?php
require_once "manager_auth.inc";
require_manager_login();
require_once "settings.php";

$conn = new mysqli($host, $user, $pwd, $sql_db);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$report = (isset($_GET["report"]) ? $_GET["report"] : "popular");
$report_title = "";
$rows = [];
$summary = "";

if ($report === "popular") {
    $report_title = "Most Popular Products";

    $sql = "
        SELECT product,
               SUM(quantity) AS total_quantity,
               COUNT(*) AS number_of_orders
        FROM orders
        GROUP BY product
        ORDER BY total_quantity DESC, number_of_orders DESC
    ";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

} elseif ($report === "fulfilled") {
    $report_title = "Fulfilled Orders Between Two Dates";

    $from = (isset($_GET["from"]) ? $_GET["from"] : "");
    $to = (isset($_GET["to"]) ? $_GET["to"] : "");

    if ($from !== "" && $to !== "") {
        $stmt = $conn->prepare("
            SELECT order_id, order_time, fname, lname, product,
                   quantity, order_cost, order_status
            FROM orders
            WHERE order_status = 'FULFILLED'
              AND DATE(order_time) BETWEEN ? AND ?
            ORDER BY order_time DESC
        ");

        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $stmt->close();
    }

} elseif ($report === "average") {
    $report_title = "Average Orders Per Day";

    $sql = "
        SELECT
            COUNT(*) AS total_orders,
            COUNT(DISTINCT DATE(order_time)) AS active_days,
            ROUND(
                COUNT(*) / NULLIF(COUNT(DISTINCT DATE(order_time)), 0),
                2
            ) AS average_orders_per_day
        FROM orders
    ";

    $result = $conn->query($sql);
    $summary = $result->fetch_assoc();

} elseif ($report === "revenue") {
    $report_title = "Revenue by Order Status";

    $sql = "
        SELECT order_status,
               COUNT(*) AS number_of_orders,
               COALESCE(SUM(order_cost), 0) AS total_revenue,
               ROUND(COALESCE(AVG(order_cost), 0), 2) AS average_order_value
        FROM orders
        GROUP BY order_status
        ORDER BY total_revenue DESC
    ";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

$conn->close();
?>
<?php include("header.inc"); ?>

<section class="banner">
    <h1>Advanced Manager Reports</h1>
    <p>Compound MySQL reports for analysing orders and business performance.</p>
</section>

<section class="manager-panel reports-panel">
    <div class="manager-links">
        <a href="reports.php?report=popular">Most Popular Product</a>
        <a href="reports.php?report=average">Average Orders / Day</a>
        <a href="reports.php?report=revenue">Revenue by Status</a>
        <a href="reports.php?report=fulfilled">Fulfilled Between Dates</a>
        <a href="manager.php">Back to Manager</a>
    </div>

    <h2><?php echo htmlspecialchars($report_title); ?></h2>

    <?php if ($report === "fulfilled"): ?>
        <form method="get" action="reports.php" class="report-form">
            <input type="hidden" name="report" value="fulfilled">

            <label for="from">From date</label>
            <input type="date" id="from" name="from"
                   value="<?php echo htmlspecialchars((isset($_GET["from"]) ? $_GET["from"] : "")); ?>" required>

            <label for="to">To date</label>
            <input type="date" id="to" name="to"
                   value="<?php echo htmlspecialchars((isset($_GET["to"]) ? $_GET["to"] : "")); ?>" required>

            <button type="submit">Run Report</button>
        </form>
    <?php endif; ?>

    <?php if ($report === "average" && $summary): ?>
        <div class="report-summary">
            <h3>Total Orders: <?php echo (int)$summary["total_orders"]; ?></h3>
            <h3>Days With Orders: <?php echo (int)$summary["active_days"]; ?></h3>
            <h3>Average Orders Per Day:
                <?php echo htmlspecialchars($summary["average_orders_per_day"]); ?>
            </h3>
        </div>
    <?php endif; ?>

    <?php if ($report === "popular"): ?>
        <table border="1" cellpadding="10" cellspacing="0" width="100%">
            <tr>
                <th>Rank</th>
                <th>Product</th>
                <th>Total Quantity</th>
                <th>Number of Orders</th>
            </tr>
            <?php $rank = 1; foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo $rank++; ?></td>
                    <td><?php echo htmlspecialchars($row["product"]); ?></td>
                    <td><?php echo (int)$row["total_quantity"]; ?></td>
                    <td><?php echo (int)$row["number_of_orders"]; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($report === "fulfilled"): ?>
        <table border="1" cellpadding="10" cellspacing="0" width="100%">
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Cost</th>
                <th>Status</th>
            </tr>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo (int)$row["order_id"]; ?></td>
                    <td><?php echo htmlspecialchars($row["order_time"]); ?></td>
                    <td><?php echo htmlspecialchars($row["fname"] . " " . $row["lname"]); ?></td>
                    <td><?php echo htmlspecialchars($row["product"]); ?></td>
                    <td><?php echo (int)$row["quantity"]; ?></td>
                    <td>Rs. <?php echo number_format((float)$row["order_cost"], 2); ?></td>
                    <td><?php echo htmlspecialchars($row["order_status"]); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($report === "revenue"): ?>
        <table border="1" cellpadding="10" cellspacing="0" width="100%">
            <tr>
                <th>Status</th>
                <th>Orders</th>
                <th>Total Revenue</th>
                <th>Average Order Value</th>
            </tr>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["order_status"]); ?></td>
                    <td><?php echo (int)$row["number_of_orders"]; ?></td>
                    <td>Rs. <?php echo number_format((float)$row["total_revenue"], 2); ?></td>
                    <td>Rs. <?php echo number_format((float)$row["average_order_value"], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</section>

<?php include("footer.inc"); ?>
