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
  $title = "Verification Created!";
  // echo "Success!";
  // echo $result->customer->id . "\n";
  $dudeStuff = $result->customer->paymentMethods[0]->verification->threeDSecureInfo;
  // print_r('<div class="main"><pre class="code"><code class="prettyprint lang-php">' . $dudeStuff . '</code></pre></div>');
  $message = "
  <td> {$dudeStuff->liabilityShifted} </td>
  <td> {$dudeStuff->liabilityShiftPossible} </td>
  <td> {$dudeStuff->status} </td>
  <td> {$dudeStuff->enrolled} </td>
  <td> {$dudeStuff->cavv} </td>
  <td> {$dudeStuff->acsTransactionId} </td>
  <td> {$dudeStuff->eciFlag} </td>
  <td> {$dudeStuff->threeDSecureVersion} </td>";
} else {
  echo "Error";
  print_r($result->message);
}
?>
<body style="font-family:Verdana;">
<div style="overflow:auto">
<div class="menu">
    <?php require_once("../includes/nav.html"); ?>
</div>
  <div class="main">
    <body>
      <h2><?php echo $title;?></h2>
      <br>
      <p>Here is the 3D Secure info</p>
      <table>
        <th>Liability Shifted</th>
        <th>Liability Shifted Possible</th>
        <th>Status</th>
        <th>Enrolled</th>
        <th>CAVV</th>
        <th>ACS Transaction ID</th>
        <th>ECI Flag</th>
        <th>Version</th>
          <tr>
          <?php echo $message;?>
          </tr>
      </table>
  </div>
</div>
    </body>
<div style="text-align:center;padding:10px;margin-top:7px;"> {•̃̾_•̃̾} </div>
<!-- $paymentMethod = $gateway->paymentMethod()->find('8bdtmvg');
$dudeStuff = $paymentMethod->verification->threeDSecureInfo;
print_r($dudeStuff); -->
