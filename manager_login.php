<?php
session_start();
require_once "settings.php";

$conn = new mysqli($host, $user, $pwd, $sql_db);

if ($conn->connect_error) {
    die("Database connection failed.");
}

/* Create manager table if it does not exist. */
$conn->query("
    CREATE TABLE IF NOT EXISTS managers (
        manager_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('SUPER','MANAGER') NOT NULL DEFAULT 'MANAGER',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

/* Create the required tutor/super-manager account on first use. */
$check = $conn->query("SELECT manager_id FROM managers LIMIT 1");

if ($check && $check->num_rows === 0) {
    $username = "admin";
    $password_hash = hash("sha256", "password");
    $role = "SUPER";

    $stmt = $conn->prepare(
        "INSERT INTO managers (username, password_hash, role) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $username, $password_hash, $role);
    $stmt->execute();
    $stmt->close();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim((isset($_POST["username"]) ? $_POST["username"] : ""));
    $password = (isset($_POST["password"]) ? $_POST["password"] : "");

    if ($username === "" || $password === "") {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT manager_id, username, password_hash, role
             FROM managers
             WHERE username = ?
             LIMIT 1"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $manager = $result->fetch_assoc();
        $stmt->close();

        if ($manager && hash("sha256", $password) === $manager["password_hash"]) {
            session_regenerate_id(true);

            $_SESSION["manager_logged_in"] = true;
            $_SESSION["manager_id"] = $manager["manager_id"];
            $_SESSION["manager_username"] = $manager["username"];
            $_SESSION["manager_role"] = $manager["role"];

            header("Location: manager.php");
            exit;
        }

        $error = "Invalid username or password.";
    }
}

$conn->close();
?>
<?php include("header.inc"); ?>

<section class="banner">
    <h1>Manager Login</h1>
    <p>Authorised staff access to the Titan Gaming Desktops order management system.</p>
</section>

<section class="manager-auth-box">
    <?php if ($error): ?>
        <p class="manager-error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="manager_login.php" novalidate>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="login-btn">Login</button>
    </form>

    <p class="manager-note">
        Super Manager can register additional managers after logging in.
    </p>
</section>

<?php include("footer.inc"); ?>
