<?php

require_once './common.php';

$username = $usernameErr = '';
$password = $passwordErr = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Enter your username";
    } else {
        $username = strip_tags($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = 'Enter your password';
    } else {
        $password = strip_tags($_POST["password"]);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$usernameErr && !$passwordErr) {
    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD)) {
        $_SESSION['login_user'] = $username;
        header('Location: ./products.php');
    } else {
        $errorMessage = 'Invalid username and/or password';
    }
}

?>

<?php require_once './view/header.view.php'; ?>

    <form action="./login.php" method="post">
        <input
                type="text"
                name="username"
                placeholder="Username"
                value="<?= $_POST["username"] ?? ''; ?>"
        >
        <br>
        <span class="error"><?= $usernameErr; ?></span>
        <br><br>
        <input
                type="text"
                name="password"
                placeholder="Password"
                value="<?= $_POST["password"] ?? ''; ?>"
        >
        <br>
        <span class="error"><?= $passwordErr; ?></span>
        <?php if ($errorMessage): ?>
            <p class="error"><?= $errorMessage; ?></p>
        <?php endif; ?>
        <br><br>
        <input type="submit" value="<?= translate('login'); ?>">
    </form>

<?php require_once './view/footer.view.php'; ?>