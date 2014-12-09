<?php
require '../src/reCaptcha.php';
$captcha = new reCAPTCHA();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Google reCAPTCHA example for PHP</title>
</head>
<body>
<?php
if (isset($_POST['g-recaptcha-response'])) {
    $result = $captcha->verify($_POST['g-recaptcha-response']);
    var_dump($result);
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <?php $captcha->show(); ?>
    <input type="submit" value="Send" />
</form>
</body>
</html>