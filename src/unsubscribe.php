<?php
require_once 'functions.php';

$unsubscribed = false;
if (isset($_GET['email'])) {
    $email = $_GET['email'];
    $unsubscribed = unsubscribeEmail($email);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe</title>
</head>
<body>
    <h2 id="unsubscription-heading">Unsubscribe from Task Updates</h2>
    <?php if ($unsubscribed): ?>
        <p>You have been unsubscribed successfully.</p>
    <?php else: ?>
        <p>Unable to unsubscribe. The email may not be in our subscriber list.</p>
    <?php endif; ?>
</body>
</html>
