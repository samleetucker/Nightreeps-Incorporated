<html>
<!-- server-side transaction sale call -->
<?php
require_once("../includes/head.php");
require_once("../includes/braintree_init.php");
//declare variables from le post
$nonce = $_POST["nonce"];
$amount = $_POST["amount"];
$name = $_POST["name"];
$firstLast = explode(" ", $name);
// echo($nonce);
$result = $gateway->transaction()->sale([
  'amount' => $amount,
  'paymentMethodNonce' => $nonce,
  'options' => [
    'submitForSettlement' => True
  ],
  'customer' => [
    'firstName' => $firstLast[0],
    'lastName' => $firstLast[1]
  ]
]);

// Result handling
if ($result->success) {
// print($result);
} else {
  foreach($result->errors->deepAll() AS $error) {
      // print_r($error->attribute . ": " . $error->code . " " . $error->message . "\n");
  }
}
?>
<div align="center" class="main">
<h1> R e s u l t </h1>
  <div>
<p>Amount: <?php echo($result->transaction->amount)?></p>
<p>Status: <?php echo($result->transaction->status)?> </p>
<p>ID: <?php echo($result->transaction->id)?> </p>
  </div>
</div>
</html>
