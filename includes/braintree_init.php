<?php
// session_start();
require_once("../vendor/autoload.php");

if(file_exists(__DIR__ . "/../.env")) {
    $dotenv = new Dotenv\Dotenv(__DIR__ . "/../");
    $dotenv->load();
}

$gateway = new Braintree_Gateway([
  'environment' => 'sandbox',
  'merchantId' => 'tt8srtpp8yfgfghp',
  'publicKey' => 't93rv9zd344vn2td',
  'privateKey' => '43b01fc0041eac2b913a2f251d3e1e48'
]);

// Nicks AIB Sandbox
// $gateway = new Braintree_Gateway([
//   'environment' => 'sandbox',
//   'merchantId' => 'cdrsbfyxsb24hz4v',
//   'publicKey' => 'xtyrsdhg765vs253',
//   'privateKey' => '7ec92acf803134f45848e6562377d58c'
// ]);
