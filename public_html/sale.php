<?php
require_once("../includes/braintree_init.php");
//declare variables from le post
$nonce = $_POST["nonce"];
echo($nonce);
$result = $gateway->transaction()->sale([
  'amount' => '10.00',
  'paymentMethodNonce' => $nonce,
  'options' => [
    'submitForSettlement' => True
  ]
]);

if ($result->success) {
print($result);
} else {
  foreach($result->errors->deepAll() AS $error) {
      print_r($error->attribute . ": " . $error->code . " " . $error->message . "\n");
  }
}

?>
