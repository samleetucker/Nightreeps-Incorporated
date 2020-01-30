<?php require_once("../includes/braintree_init.php"); ?>
<html lang='en'>
<head>
  <?php require_once("../includes/head.php"); ?>
</head>
</style>
<body style="font-family:Verdana;">
  <div style="overflow:auto">
    <div class="menu">
      <?php require_once("../includes/nav.html"); ?>
    </div>
      <div class="main">
        <h2>Vault Manager</h2>
        <h3>Overview</h3>
          <p>The Vault Manager is a client-side component which allows the client to fetch payment information about any given customer. This enables client-side integrations to swiftly fetch and display payment methods to customers for them to interact with.</p>
        <h3>Use cases & limitations</h3>
          <p>The main benefit of using the Vault Manager is that it eliminates the use of any communication with your server to fetch the payment methods for a customer. As well, if you would like to escape the limitations of the Drop-in UI, you can create a custom UI that has payment method data populated by the Vault Manager. Using the nonces returned from the Vault Manager, any of our server-side API calls can be made based on the customer’s selection, minimizing the amount of back-and-fourth between your client and server.</p>
          <p>Vault Manager will only display the 8 most recently used payment methods, listed in order of most recently used. You can use the <span style="background-color: #E6E6E6;font-weight: bold;">defaultFirst</span> option to display the customer’s default payment method first, but if the default payment method is not one of those 8 results, it will still not be collected/displayed.</p>
        <h3>Drop-in UI</h3>
          <p>Braintree’s Vault Manager component is available
         to be implemented automatically as part of the Drop-in UI.
         The Drop-in UI has the Vault Manager built-in and can be easily switched on and off without many changes
         to your code. <a href="https://developers.braintreepayments.com/guides/drop-in/customization/javascript/v3#display-a-saved-payment-method">Learn more at our Drop-in UI Customization page.</a></p>
         <img border="0" alt="Drop-in UI Demonstration" src="https://developers.braintreepayments.com/img/developers/client-sdk-drop-in-web.png" style="width: 75%">
        <h3>Standalone</h3>
          <p>Alternatively, the Vault Manager can be implemented standalone as a way to fetch payment method details, generate nonces, and display payment methods of any given customer ID. </p>
          <p>The Vault Manager is available directly from our servers which you can include on your site as a script tag or download the file and save locally:<p>
          <pre class="code"><code class="prettyprint">&#60;script src="https://js.braintreegateway.com/web/3.57.0/js/vault-manager.min.js">&#60;/script></code></pre>
        <h3>Client-side implementation</h3>
          <p>To begin implementing this component, you will need to setup a client. Set up the SDK and create a client. If you use other payment method types, such as PayPal, then you can re-use the same client.</p>
          <p>The client must <a href="https://developers.braintreepayments.com/reference/request/client-token/generate/php#customer_id">include a customer ID</a> for Vault Manager to work.</p>
          <p>Pass the <span style="background-color: #E6E6E6;font-weight: bold;">clientInstance</span> to <span style="background-color: #E6E6E6;font-weight: bold;">braintree.vaultManager.create</span> within the options object:</p>
        <pre class="code"><code class="prettyprint lang-javascript">braintree.vaultManager.create({
    client: clientInstance
}, function(err, vaultManagerInstance){
  if(err){
    //Handle errors in vault manager creation
  }
    //fetch payment method info
  });
});</code></pre>
        <h3>Fetching payment methods </h3>
          <p>Fetching payment methods can be done by iterating through the paymentMethods returned from the <span style="background-color: #E6E6E6;font-weight: bold;">fetchPaymentMethods()</span> callback. These results can be iterated through to find all of their relevant data.</p>
        <div><pre class="code"><code class="prettyprint">braintree.vaultManager.create({
  client: clientInstance
}, function(err, vaultManagerInstance){
  vaultManagerInstance.fetchPaymentMethods(function (err, paymentMethods) {
    paymentMethods.forEach(function (paymentMethod) {
      // paymentMethod.nonce < transactable nonce associated with payment method
      // paymentMethod.details < object with additional information about payment method
      // paymentMethod.type < a constant signifying the type
    });
  });
});</code></pre></div>
<p>The following paramters can be used to fetch specific data.</p>
<ul>
  <li><span style="background-color: #E6E6E6;font-weight: bold;">paymentMethod.nonce</span> - returns a nonce to be used in transactions, or updates to the payment method </li>
<li><span style="background-color: #E6E6E6;font-weight: bold;">paymentMethod.details</span> - returns a JSON object of all of the details associated with the nonce such as BIN, Card Brand, and Last 4</li>
<li><span style="background-color: #E6E6E6;font-weight: bold;">paymentMethod.type</span> - signifies the type of payment method</li>
</ul>
        <h3>Example</h3>
      <p>Here is an example of the above script iterating through the Payment method details for customer: 692024293. Note that this is all fetched on the client-side, and there is no server-side commuinication taking place to fetch this data.</p>
      <br>
        <a class="button" data-toggle="collapse" href="#collapseJSON" role="button" aria-expanded="false" aria-controls="collapseJSON">
          View JSON formatted card details
        </a>
        <div class="collapse" id="collapseJSON">
          <div>
            <br>
            <pre class="code"><code id="innerCollapse"></code></pre>
          </div>
        </div>
      </p>
        <br>
      <div style="overflow-x:auto;">
        <table id="display">
          <th>Card Type</th>
          <th>Last 4</th>
          <th>BIN</th>
          <th>Nonce</th>
        </table>
      </div>
        <script>
        var client_token = "<?php echo($gateway->clientToken()->generate(
          [
            "customerId" => 692024293
          ]
        ));?>";
        // console.log(client_token);
        braintree.client.create({
          authorization: client_token
        }, function (err, clientInstance) {
          braintree.vaultManager.create({
            client: clientInstance
          }, function(err, vaultManagerInstance){
            vaultManagerInstance.fetchPaymentMethods(function (err, paymentMethods) {
              paymentMethods.forEach(function (paymentMethod) {
                console.log(paymentMethod.nonce);
                console.log(paymentMethod.details);
                pmt = paymentMethod.details;
                var fetchedNonce = paymentMethod.nonce;
                var clps = document.getElementById("innerCollapse");
                var table = document.getElementById("display");
                var ui = document.getElementById("ui");
                clps.innerHTML += '<p>' + JSON.stringify(pmt, null, 2) + '</p>' + '<p> Nonce:' + fetchedNonce + '</p>';
                table.innerHTML += '<tr><td>' + pmt.cardType + '</td>' + '<td>' + pmt.lastFour + '</td>' + '<td>' + pmt.bin + '</td>' + '<td>' + fetchedNonce + '</td></tr>';
                // paymentMethod.nonce <- transactable nonce associated with payment method
                // paymentMethod.details <- object with additional information about payment method
                // paymentMethod.type <- a constant signifying the type
              });
            });
          });
        });
        </script>

      </div>
    </body>
  </div>
  <div style="text-align:center;padding:10px;margin-top:7px;"> {•̃̾_•̃̾} </div>
  </html>
