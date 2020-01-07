<html>
<!-- server-side transaction sale call -->
<?php
require_once("../includes/head.php");
require_once("../includes/braintree_init.php");
//declare variables from le post
$nonce = $_POST["nonce"];
$amount = $_POST["amount"];
$name = $_POST["name"];
$postalCode = $_POST["postalCode"];
$firstLast = explode(" ", $name);
$title = "title";
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
  ],
  'billing' => [
    'postalCode' => $postalCode
  ]
]);

// Result handling
if ($result->success) {
$title = "Success!";
} else {
  $title = "Failure :(";
  foreach($result->errors->deepAll() AS $error) {
      // print_r($error->attribute . ": " . $error->code . " " . $error->message . "\n");
  }
}
?>

<body style="font-family:Verdana;">
<div style=";padding:15px;text-align:center;">
  <header>
    <h1 align="center"> Result </h1>
  </header>
</div>
<div style="overflow:auto">
<div class="menu">
    <?php require_once("../includes/nav.html"); ?>
</div>
  <div class="main">
    <body>
      <h3><?php echo $title;?></h3>
      <p>Amount: <?php echo($result->transaction->amount)?></p>
      <p>Status: <?php echo($result->transaction->status)?> </p>
      <p>ID: <?php echo($result->transaction->id)?> </p>
  </div>
</div>
    </body>
<div style="text-align:center;padding:10px;margin-top:7px;"> {•̃̾_•̃̾} </div>
</html>
