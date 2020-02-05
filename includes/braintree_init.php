<?php
require_once("../vendor/autoload.php");

$gateway = new Braintree_Gateway([
  'environment' => 'sandbox',
  'merchantId' => '5zmrvsycrfhd2tc8',
  'publicKey' => '9j84yprmzqw497d8',
  'privateKey' => 'ec2f1b7a2333e010a7d8e7193a8d68bc'
]);

// Nicks AIB Sandbox
// $gateway = new Braintree_Gateway([
//   'environment' => 'sandbox',
//   'merchantId' => 'cdrsbfyxsb24hz4v',
//   'publicKey' => 'xtyrsdhg765vs253',
//   'privateKey' => '7ec92acf803134f45848e6562377d58c'
// ]);
