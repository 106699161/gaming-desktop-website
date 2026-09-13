<?php
require_once "manager_auth.inc";
require_super_manager();
require_once "settings.php";

$conn = new mysqli($host, $user, $pwd, $sql_db);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->query("
    CREATE TABLE IF NOT EXISTS managers (
        manager_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('SUPER','MANAGER') NOT NULL DEFAULT 'MANAGER',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim((isset($_POST["username"]) ? $_POST["username"] : ""));
    $password = (isset($_POST["password"]) ? $_POST["password"] : "");
    $confirm = (isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : "");

    if (!preg_match('/^[A-Za-z0-9_]{4,50}$/', $username)) {
        $error = "Username must contain 4-50 letters, numbers or underscores.";
    } elseif (strlen($password) < 8) {
        $error = "Password must contain at least 8 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT manager_id FROM managers WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        if ($exists) {
            $error = "That username is already registered.";
        } else {
            $hash = hash("sha256", $password);
            $role = "MANAGER";

            $stmt = $conn->prepare(
                "INSERT INTO managers (username, password_hash, role)
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $username, $hash, $role);

            if ($stmt->execute()) {
                $message = "Manager account created successfully.";
            } else {
                $error = "Unable to create the manager account.";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>
<?php include("header.inc"); ?>

<section class="banner">
    <h1>Register Manager</h1>
    <p>Create an authorised manager account for the order management system.</p>
</section>

<section class="manager-auth-box manager-register-box">
    <?php if ($message): ?>
        <p class="manager-success"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="manager-error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="manager_register.php" novalidate>
        <label for="username">New Manager Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Register Manager</button>
    </form>
</section>

<?php include("footer.inc"); ?>
