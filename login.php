<?php

require_once './common.php';

$validation = [
    'username' => '',
    'password' => '',
    'usernameErr' => '',
    'passwordErr' => '',
    'errorMessage' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username'])) {
        $validation['usernameErr'] = translate('enter_username');
    } else {
        $validation['username'] = strip_tags($_POST['username']);
    }

    if (empty($_POST['password'])) {
        $validation['passwordErr'] = translate('enter_password');
    } else {
        $validation['password'] = strip_tags($_POST['password']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$validation['usernameErr'] && !$validation['passwordErr']) {
    if ($validation['username'] === ADMIN_USERNAME && password_verify($validation['password'], ADMIN_PASSWORD)) {
        $_SESSION['login_user'] = $validation['username'];
        header('Location: ./products.php');
    } else {
        $validation['errorMessage'] = translate('invalid');
    }
}

?>

<?php require_once './view/header.view.php'; ?>

    <div class="content-wrapper">
        <form action="./login.php" method="post">
            <input
                    type="text"
                    name="username"
                    placeholder="<?= translate('username'); ?>"
                    value="<?= $_POST['username'] ?? ''; ?>"
            >
            <br>
            <span class="error"><?= $validation['usernameErr']; ?></span>
            <br><br>
            <input
                    type="text"
                    name="password"
                    placeholder="<?= translate('password'); ?>"
                    value="<?= $_POST['password'] ?? ''; ?>"
            >
            <br>
            <span class="error"><?= $validation['passwordErr']; ?></span>
            <?php if ($validation['errorMessage']): ?>
                <p class="error"><?= $validation['errorMessage']; ?></p>
            <?php endif; ?>
            <br><br>
            <input type="submit" value="<?= translate('login'); ?>">
        </form>
    </div>

<?php require_once './view/footer.view.php'; ?>