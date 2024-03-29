<?php

require_once './common.php';

$username = $password = '';

$validation = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username'])) {
        $validation['usernameErr'] = translate('enter_username');
    } else {
        $username = strip_tags($_POST['username']);
    }

    if (empty($_POST['password'])) {
        $validation['passwordErr'] = translate('enter_password');
    } else {
        $password = strip_tags($_POST['password']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !array_filter($validation)
    && $username === ADMIN_USERNAME
    && password_verify($password, HASHED_ADMIN_PASSWORD)
) {
    $_SESSION['login_user'] = $username;
    header('Location: ./products.php');
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !array_filter($validation)
    && ($username !== ADMIN_USERNAME
        || !password_verify($password, HASHED_ADMIN_PASSWORD))
) {
    $validation['errorMessage'] = translate('invalid');
}

?>

<?php require_once './view/header.view.php'; ?>

    <div class="content-wrapper">
        <form action="./login.php" method="post">
            <input
                    type="text"
                    name="username"
                    placeholder="<?= translate('username'); ?>"
                    value="<?= $username; ?>"
            >
            <br>
            <?php if (isset($validation['usernameErr'])): ?>
                <span class="error"><?= $validation['usernameErr']; ?></span>
            <?php endif; ?>
            <br><br>
            <input
                    type="password"
                    name="password"
                    placeholder="<?= translate('password'); ?>"
                    value="<?= $password; ?>"
            >
            <br>
            <?php if (isset($validation['passwordErr'])): ?>
                <span class="error"><?= $validation['passwordErr']; ?></span>
            <?php endif; ?>
            <?php if (isset($validation['errorMessage'])): ?>
                <p class="error"><?= $validation['errorMessage']; ?></p>
            <?php endif; ?>
            <br><br>
            <input type="submit" value="<?= translate('login'); ?>">
        </form>
    </div>

<?php require_once './view/footer.view.php'; ?>