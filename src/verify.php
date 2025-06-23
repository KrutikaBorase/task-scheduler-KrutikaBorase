<?php
require_once 'functions.php';

$verified = false;
if (isset($_GET['email']) && isset($_GET['code'])) {
    $email = $_GET['email'];
    $code = $_GET['code'];
    $verified = verifySubscription($email, $code);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Subscription Verification</title>
</head>
<body>
    <h2 id="verification-heading">Subscription Verification</h2>
    <?php if ($verified): ?>
        <p>Your subscription has been verified successfully!</p>
    <?php else: ?>
        <p>Invalid verification link or code.</p>
    <?php endif; ?>
</body>
</html>
