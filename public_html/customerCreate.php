<?php
require_once("../includes/head.php");
require_once("../includes/braintree_init.php");
//declare variables from le post
$nonce = $_POST["nonce"];
$result = $gateway->customer()->create(array(
    'firstName' => '3d',
    'lastName' => 'Secure',
    'company' => 'Braintree',
    'email' => 'email@cool.man',
    'phone' => '312.555.1234',
    'fax' => '614.555.5678',
    'paymentMethodNonce' => $nonce
));
if ($result->success) {
  echo "Success!
";
  echo $result->customer->id . "\n";

} else {
  echo "Error
";
  print_r($result->message);
}
?>
