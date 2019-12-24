<html>
<!-- Loads the header, for consistency across pages. -->
<?php require_once("../includes/head.php"); ?>
<!-- Braintree init, the file which creates the gateway using api keys -->
<?php require_once("../includes/braintree_init.php"); ?>
<body style="font-family:Verdana;">
  <div style=";padding:15px;text-align:center;">
    <header>
      <h1 align="center">Expired Client Token Demo</h1>
    </header>
    <h3></h3>
  </div>

  <div style="overflow:auto">
    <div class="menu">
      <?php require_once("../includes/nav.html"); ?>
    </div>

    <div class="main">
      <br></br>
      <p>The first thing any client-side component needs is client authorization. Most merchant will opt to use a <a href="https://developers.braintreepayments.com/reference/request/client-token/generate/php" target="_blank">client token.</a></p>
        <p>However, currently in sandbox, and soon to be in production, these<a href="https://internal.braintreepayments.com/display/DD/Client+Authorization+-+JWT+Cheat+Sheet" target="_blank"> client tokens will expire in 24 hours </a>. That is why a client token should be generated every time a checkout page is landed on.
      </p>
      <br>
      <h4>So what happens if a client token is expired, but still used?</h4>
      <p>Tokens issued before the change will continue to be valid for the foreseeable future. A merchant does not have to worry about those tokens automatically expiring.
        Any token issued after the change will expire after 24 hours. These exired tokens will lead to a few pieces of behavior which Support can look out for when fielding requests.</p>
        <p>The biggest red flag for this will be merchants who reach out with blank checkout fields, or a Drop-in UI that does not load. Notice how the fields load, and the non Hosted Fields work just fine:</p>
        <br>
        <form method="post" id="details" action="/sale.php">
          <label for="name" class="hosted-fields--label">Name</label>
          <input type="name" class="hosted-field" id="name" name="name" placeholder="Johnny Utah"required>

          <label for="address" class="hosted-fields--label">Address</label>
          <input type="address" class="hosted-field" id="address" name="address" placeholder="24123 Green Herron Drive"required>

          <input type="number" class="hosted-field" id="amount" name="amount" value="100.00"required hidden>

          <label for="card-number" class="hosted-fields--label">Card Number</label>
          <div id="card-number" class="hosted-field"></div>

          <label for="cvv" class="hosted-fields--label">CVV</label>
          <div id="cvv" class="hosted-field"></div>

          <label for="expiration-date" class="hosted-fields--label">Expiration Date</label>
          <div id="expiration-date" class="hosted-field"></div>

          <input class="button" type="submit" value="Place Order" disabled />
          <div id="nonce-display" name="nonce-display" hidden></div>
          <input type="hidden" id="nonce" name="nonce" />
        </form>
        <br>
        <script>
        var form = document.querySelector('#details');
        var submit = document.querySelector('input[type="submit"]');
        var nonce = document.querySelector('#nonce-display');
        var gate = 0;

        var client_token = "eyJ2ZXJzaW9uIjoyLCJhdXRob3JpemF0aW9uRmluZ2VycHJpbnQiOiJleUowZVhBaU9pSktWMVFpTENKaGJHY2lPaUpGVXpJMU5pSXNJbXRwWkNJNklqSXdNVGd3TkRJMk1UWXRjMkZ1WkdKdmVDSXNJbWx6Y3lJNklrRjFkR2g1SW4wLmV5SmxlSEFpT2pFMU56WTJNelkxTlRZc0ltcDBhU0k2SWpkbE16TTROVGszTFRka1pqUXROR0k0WWkxaFl6RmpMV0l6TVdabFl6QTVPVEF3TkNJc0luTjFZaUk2SW5SME9ITnlkSEJ3T0hsbVoyWm5hSEFpTENKcGMzTWlPaUpCZFhSb2VTSXNJbTFsY21Ob1lXNTBJanA3SW5CMVlteHBZMTlwWkNJNkluUjBPSE55ZEhCd09IbG1aMlpuYUhBaUxDSjJaWEpwWm5sZlkyRnlaRjlpZVY5a1pXWmhkV3gwSWpwMGNuVmxmU3dpY21sbmFIUnpJanBiSW0xaGJtRm5aVjkyWVhWc2RDSmRMQ0p2Y0hScGIyNXpJanA3SW1OMWMzUnZiV1Z5WDJsa0lqb3lOREk1TmpjeU9UTXNJbTFsY21Ob1lXNTBYMkZqWTI5MWJuUmZhV1FpT2lKTmFXNWtVMkZ3YkdsdVp5MURRVVFpZlgwLjAwVGdPWEo4U1c1ZEhsaE1xTWRHSWpzMTgtYnVyVlNsYkxUaVYtVks3aGNESHlsVlVwNEk2ZzlrVW1VSi00X1ZuVXkzRHNsdXJuWHhQNjRHcmZaelZ3P2N1c3RvbWVyX2lkPSIsImNvbmZpZ1VybCI6Imh0dHBzOi8vYXBpLnNhbmRib3guYnJhaW50cmVlZ2F0ZXdheS5jb206NDQzL21lcmNoYW50cy90dDhzcnRwcDh5ZmdmZ2hwL2NsaWVudF9hcGkvdjEvY29uZmlndXJhdGlvbiIsImdyYXBoUUwiOnsidXJsIjoiaHR0cHM6Ly9wYXltZW50cy5zYW5kYm94LmJyYWludHJlZS1hcGkuY29tL2dyYXBocWwiLCJkYXRlIjoiMjAxOC0wNS0wOCJ9LCJjaGFsbGVuZ2VzIjpbXSwiZW52aXJvbm1lbnQiOiJzYW5kYm94IiwiY2xpZW50QXBpVXJsIjoiaHR0cHM6Ly9hcGkuc2FuZGJveC5icmFpbnRyZWVnYXRld2F5LmNvbTo0NDMvbWVyY2hhbnRzL3R0OHNydHBwOHlmZ2ZnaHAvY2xpZW50X2FwaSIsImFzc2V0c1VybCI6Imh0dHBzOi8vYXNzZXRzLmJyYWludHJlZWdhdGV3YXkuY29tIiwiYXV0aFVybCI6Imh0dHBzOi8vYXV0aC52ZW5tby5zYW5kYm94LmJyYWludHJlZWdhdGV3YXkuY29tIiwiYW5hbHl0aWNzIjp7InVybCI6Imh0dHBzOi8vb3JpZ2luLWFuYWx5dGljcy1zYW5kLnNhbmRib3guYnJhaW50cmVlLWFwaS5jb20vdHQ4c3J0cHA4eWZnZmdocCJ9LCJ0aHJlZURTZWN1cmVFbmFibGVkIjp0cnVlLCJwYXlwYWxFbmFibGVkIjp0cnVlLCJwYXlwYWwiOnsiZGlzcGxheU5hbWUiOiJNaW5kU2FwbGluZyIsImNsaWVudElkIjpudWxsLCJwcml2YWN5VXJsIjoiaHR0cDovL2V4YW1wbGUuY29tL3BwIiwidXNlckFncmVlbWVudFVybCI6Imh0dHA6Ly9leGFtcGxlLmNvbS90b3MiLCJiYXNlVXJsIjoiaHR0cHM6Ly9hc3NldHMuYnJhaW50cmVlZ2F0ZXdheS5jb20iLCJhc3NldHNVcmwiOiJodHRwczovL2NoZWNrb3V0LnBheXBhbC5jb20iLCJkaXJlY3RCYXNlVXJsIjpudWxsLCJhbGxvd0h0dHAiOnRydWUsImVudmlyb25tZW50Tm9OZXR3b3JrIjp0cnVlLCJlbnZpcm9ubWVudCI6Im9mZmxpbmUiLCJ1bnZldHRlZE1lcmNoYW50IjpmYWxzZSwiYnJhaW50cmVlQ2xpZW50SWQiOiJtYXN0ZXJjbGllbnQzIiwiYmlsbGluZ0FncmVlbWVudHNFbmFibGVkIjp0cnVlLCJtZXJjaGFudEFjY291bnRJZCI6Ik1pbmRTYXBsaW5nLUNBRCIsImN1cnJlbmN5SXNvQ29kZSI6IkNBRCJ9LCJtZXJjaGFudElkIjoidHQ4c3J0cHA4eWZnZmdocCIsInZlbm1vIjoib2ZmIiwibWVyY2hhbnRBY2NvdW50SWQiOiJNaW5kU2FwbGluZy1DQUQifQ=="

        function host(){
          braintree.client.create({
            authorization: client_token
          }, function (clientErr, clientInstance) {
            if (clientErr) {
              console.error(clientErr);
              return;
            }

                // This example shows Hosted Fields, but you can also use this
                // client instance to create additional components here, such as
                // PayPal or Data Collector.

                braintree.hostedFields.create({
                  client: clientInstance,
                  styles: {
                    'input': {
                      'font-size': '14px'
                    },
                    'input.invalid': {
                      'color': 'red'
                    },
                    'input.valid': {
                      'color': 'green'
                    }
                  },
                  fields: {
                    number: {
                      selector: '#card-number',
                      placeholder: '4111 1111 1111 1111'
                    },
                    cvv: {
                      selector: '#cvv',
                      placeholder: '123'
                    },
                    expirationDate: {
                      selector: '#expiration-date',
                      placeholder: '10/2019'
                    }
                  }
                }, function (hostedFieldsErr, hostedFieldsInstance) {
                  if (hostedFieldsErr) {
                    console.error(hostedFieldsErr);
                    return;
                  }

                  submit.removeAttribute('disabled');
                  nonce.removeAttribute('hidden');
                  form.addEventListener('submit', function (event) {
                    event.preventDefault();
                  if (gate == 0){
                    hostedFieldsInstance.tokenize(function (tokenizeErr, payload) {
                      if (tokenizeErr) {
                        console.error(tokenizeErr);
                        return;
                      }
                      var lenonce = payload.nonce;

                      document.getElementById("nonce-display").innerHTML = "<p><pre><code>" + lenonce + "</code></pre></p>";
                      console.log('Got a nonce: ' + lenonce);
                      submit.value ='Click again to full send!';
                      gate = 1
                      console.log(gate)
                      document.querySelector('#nonce').value = payload.nonce;
                    });
                  };
                    if (gate == 1) {
                      console.log("seinding!!")
                      form.submit();
                    };
                  }, false);
                });
              });
        }
        host();
        function fix(){
          client_token = "<?php echo($gateway->clientToken()->generate(
            ['customerId' => 242967293,
            'merchantAccountId' => 'MindSapling-CAD']
          ));?>"
          host()
        }
      </script>
    <h4> So what needs to be done? </h4>
    <p>It is our recommendation to generate a client token when the checkout page is landed on. This will avoid any problems with client tokens going any longer than 24 hours. Since the client token used above is expired, a new one will need to be generated to remedy the issue.
      <h5><p>Here is a code example. It is generated on the server-side:</p></h5>
      <div><pre class="code"><code class="php">$gateway->clientToken()->generate([
  'merchantAccountId' => 'MindSapling-CAD'
])</code></pre></div>
      <p>You can click the below button to run this code, and watch the Hosted Fields above get fixed!</p>
<button class="button"onclick="fix()">Fix the fields</button>
<br></br>
    </div>

    <div class="right">
      <!-- empty div for possible content. -->
      <p></p>
    </div>
  </div>

  <div style="text-align:center;padding:10px;margin-top:7px;"> {•̃̾_•̃̾} </div>
  </html>
