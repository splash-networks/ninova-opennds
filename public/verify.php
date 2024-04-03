<?php

require 'header.php';
require 'config.php';

$table_name = $_SERVER['TABLE_OTP'];
$_SESSION['result'] = false;
$otp_error = false;

if (!isset($_POST['verify'])) {
  $_SESSION["name"] = $_POST['name'];
  $_SESSION["email"] = $_POST['email'];
  $phone = $_POST['full_phone'];

  $phone = (int)str_replace("+", "", $phone);
  $_SESSION["phone"] = $phone;

  $digits = $_SERVER['OTP_DIGITS'];
  $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
  echo $otp . PHP_EOL;

  mysqli_query($con, "
    CREATE TABLE IF NOT EXISTS `$table_name` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `phone` varchar(16) NOT NULL,
    `otp` varchar(10) NOT NULL,
    `valid` BIT NOT NULL,
    `created_at` varchar(45) NOT NULL,
    PRIMARY KEY (`id`)
    )");

  mysqli_query($con, "INSERT INTO `$table_name` (`phone`, `otp`, `valid`, `created_at`) VALUES ('$phone', '$otp', 1, NOW())");

  $curl = curl_init();

  $data = array(
    "type" => 1,
    "sendingType" => 0,
    "title" => "X tarihli tekil test",
    "content" => "Merhaba doğrulama kodunuz : " . $otp,
    "number" => $phone,
    "encoding" => 0,
    "sender" => "MERPABILGI",
    "periodicSettings" => null,
    "sendingDate" => null,
    "validity" => 60,
    "pushSettings" => null
  );

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://smslogin.nac.com.tr:9587/sms/create',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'Authorization: Basic ' . $_SERVER['AUTH']
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
} else {
  $_SESSION['code'] = trim($_POST['code']);

  $result = mysqli_query($con, "SELECT * FROM `$table_name` WHERE phone = '" . $_SESSION['phone'] . "' AND valid = 1 AND NOW() <= DATE_ADD(created_at, INTERVAL 15 MINUTE) ORDER BY `id` DESC LIMIT 1");
  $count = mysqli_num_rows($result);

  if (!empty($count)) {
    $row = mysqli_fetch_array($result);
    if ($row['otp'] === $_SESSION['code']) {
      echo "otp correct" . PHP_EOL;
      $_SESSION['result'] = true;
      $result = mysqli_query($con, "UPDATE `$table_name` SET valid = 0 WHERE id = '" . $row['id'] . "'");
      header("Location: connect.php");
    } else {
      $otp_error = true;
      echo "otp wrong" . PHP_EOL;
    }
  }
}

?>
<!doctype html>
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
  <?php
  if ($otp_error) { ?>
    <meta http-equiv="refresh" content="2;url=index.php" />
  <?php } ?>
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
      <section class="section">
        <div class="container">
          <?php
          if ($otp_error) { ?>
            <p class="content has-text-centered" style="color: red;">
              Sorry, the OTP you entered is not correct. Please try again.
            </p>
          <?php
          } else { ?>
            <div id="contact_form" class="content has-text-centered">
              Please enter the <?php echo $digits; ?> digit code received on your provided phone number
            </div>
            <br>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return codeCheck()">
              <div class="field">
                <div class="control has-icons-left">
                  <input class="input" type="number" name="code" id="code" placeholder="Code" required>
                  <span class="icon is-small is-left">
                    <i class="fas fa-comment"></i>
                  </span>
                </div>
              </div>
              <p class="help is-danger" id="codeError" style="display: none; margin-bottom: 10px;">
                Code Invalid: not a <?php echo $digits; ?> digit number
              </p>
              <div class="buttons is-centered">
                <input class="button is-link" type="submit" name="verify" value="Verify">
              </div>
            </form>
          <?php
          } ?>
        </div>
      </section>
    </div>
  </div>
  <script>
    function codeCheck() {
      var codeInput = document.getElementById('code').value;

      //Checking the number of digits:
      if (codeInput.length != <?php echo htmlspecialchars($digits); ?> || isNaN(codeInput)) {
        document.getElementById("codeError").style.display = "block";
        return false;
      } else {
        return true;
      }
    }
  </script>
</body>

</html>