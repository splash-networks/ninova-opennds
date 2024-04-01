<?php

require 'header.php';
include 'config.php';

$mac = $_SESSION["mac"];
$authaction = $_SESSION['authaction'];
$tok = $_SESSION['tok'];
$redir = $_SESSION['REDIRECT_URL'];
$table_name = $_SERVER['TABLE_DATA'];

if ($_SESSION["user_type"] == "new") {

    $name = $_SESSION['name'];
    $phone = $_SESSION['phone'];
    $email = $_SESSION['email'];

    mysqli_query($con, "
    CREATE TABLE IF NOT EXISTS `$table_name` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(45) NOT NULL,
    `phone` varchar(45) NOT NULL,
    `email` varchar(45) NOT NULL,
    `mac` varchar(45) NOT NULL,
    `last_updated` varchar(45) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (mac)
    )");

    mysqli_query($con, "INSERT INTO `$table_name` (name, phone, email, mac, last_updated) VALUES ('$name', '$phone', '$email','$mac', NOW())");
}

mysqli_close($con);

?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <title>
        <?php echo htmlspecialchars($business_name); ?> WiFi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" href="assets/styles/bulma.min.css" />
    <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/all.css" />
    <link rel="icon" type="image/png" href="assets/images/favicomatic/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="assets/images/favicomatic/favicon-16x16.png" sizes="16x16" />
    <link rel="stylesheet" href="assets/styles/style.css" />
</head>

<body>
    <div class="page">

        <div class="head">
            <br>
            <figure id="logo">
                <img src="assets/images/logo.png">
            </figure>
        </div>

        <div class="main">
            <seection class="section">
                <div class="container">
                    <div id="margin_zero" class="content has-text-centered is-size-6">Great! Your code has been approved!</div>
                    <br>
                    <div id="margin_zero" class="content has-text-centered is-size-6">Please wait while you are being</div>
                    <div id="margin_zero" class="content has-text-centered is-size-6">authorized on the network</div>
                </div>
            </seection>
        </div>

    </div>

    <form id="form1" name="form1" method=GET action="<?php echo htmlspecialchars($authaction); ?>">
        <input name=tok value="<?php echo htmlspecialchars($tok); ?>" type="hidden">
        <input name=redir value="<?php echo htmlspecialchars($redir); ?>" type="hidden">
    </form>

    <script type="text/javascript">
        window.onload = function () {
            window.setTimeout(function () {
                document.form1.submit();
            }, 2000);
        };
    </script>

</body>

</html>