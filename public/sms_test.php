<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$otp = 123456;

$curl = curl_init();

$data = array(
  "type" => 1,
  "sendingType" => 0,
  "title" => "X tarihli tekil test",
  "content" => "Merhaba doğrulama kodunuz : " . $otp,
  "number" => 905552025050,
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
echo $response;
