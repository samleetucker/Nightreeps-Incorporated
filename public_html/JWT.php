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
    <h4><p>Here is a code example. It is generated on the server-side.</p></h4>
    <div><pre class="code"><code class="php">$gateway->clientToken()->generate([
  'customerId' => 242967293,
  'merchantAccountId' => 'MindSapling-CAD'
])
</code></pre></div>
    <p>
      However, currently in sandbox, and soon to be in production, these<a href="https://internal.braintreepayments.com/display/DD/Client+Authorization+-+JWT+Cheat+Sheet" target="_blank"> client tokens will expire in 24 hours </a>. That is why a client token should be generated every time a checkout page is landed on.
    </p>
    <br>
    <h3 align="center"><p>Prove It</p></h3>
    <!-- Time to get saucy. Below is going to be the script that loads all of the Hosted Fields components and communicates with the server-side junk! -->

    <form method="post" id="details" action="/sale.php">
      <label for="amount" class="hosted-fields--label">Amount <a href="https://developers.braintreepayments.com/reference/general/testing/php#transaction-amounts" target="_blank">(use testing values)</a></label>
      <input type="number" class="hosted-field" id="amount" name="amount" placeholder="100.00"required>

      <label for="card-number" class="hosted-fields--label">Card Number</label>
      <div id="card-number" class="hosted-field"></div>

      <label for="cvv" class="hosted-fields--label">CVV</label>
      <div id="cvv" class="hosted-field"></div>

      <label for="expiration-date" class="hosted-fields--label">Expiration Date</label>
      <div id="expiration-date" class="hosted-field"></div>

      <input class="button" type="submit" value="Request Payment Method" disabled />
      <p><div>Fill out the form and click the button to get a nonce! Click it again to make a transaction.</div></p>
      <div id="nonce-display" name="nonce-display" hidden></div>
      <input type="hidden" id="nonce" name="nonce" />
    </form>
    <script>
      var form = document.querySelector('#details');
      var submit = document.querySelector('input[type="submit"]');
      var nonce = document.querySelector('#nonce-display');
      var gate = 0;

      var client_token = "eyJ2ZXJzaW9uIjoyLCJhdXRob3JpemF0aW9uRmluZ2VycHJpbnQiOiJleUowZVhBaU9pSktWMVFpTENKaGJHY2lPaUpGVXpJMU5pSXNJbXRwWkNJNklqSXdNVGd3TkRJMk1UWXRjMkZ1WkdKdmVDSXNJbWx6Y3lJNklrRjFkR2g1SW4wLmV5SmxlSEFpT2pFMU56WTJNelkxTlRZc0ltcDBhU0k2SWpkbE16TTROVGszTFRka1pqUXROR0k0WWkxaFl6RmpMV0l6TVdabFl6QTVPVEF3TkNJc0luTjFZaUk2SW5SME9ITnlkSEJ3T0hsbVoyWm5hSEFpTENKcGMzTWlPaUpCZFhSb2VTSXNJbTFsY21Ob1lXNTBJanA3SW5CMVlteHBZMTlwWkNJNkluUjBPSE55ZEhCd09IbG1aMlpuYUhBaUxDSjJaWEpwWm5sZlkyRnlaRjlpZVY5a1pXWmhkV3gwSWpwMGNuVmxmU3dpY21sbmFIUnpJanBiSW0xaGJtRm5aVjkyWVhWc2RDSmRMQ0p2Y0hScGIyNXpJanA3SW1OMWMzUnZiV1Z5WDJsa0lqb3lOREk1TmpjeU9UTXNJbTFsY21Ob1lXNTBYMkZqWTI5MWJuUmZhV1FpT2lKTmFXNWtVMkZ3YkdsdVp5MURRVVFpZlgwLjAwVGdPWEo4U1c1ZEhsaE1xTWRHSWpzMTgtYnVyVlNsYkxUaVYtVks3aGNESHlsVlVwNEk2ZzlrVW1VSi00X1ZuVXkzRHNsdXJuWHhQNjRHcmZaelZ3P2N1c3RvbWVyX2lkPSIsImNvbmZpZ1VybCI6Imh0dHBzOi8vYXBpLnNhbmRib3guYnJhaW50cmVlZ2F0ZXdheS5jb206NDQzL21lcmNoYW50cy90dDhzcnRwcDh5ZmdmZ2hwL2NsaWVudF9hcGkvdjEvY29uZmlndXJhdGlvbiIsImdyYXBoUUwiOnsidXJsIjoiaHR0cHM6Ly9wYXltZW50cy5zYW5kYm94LmJyYWludHJlZS1hcGkuY29tL2dyYXBocWwiLCJkYXRlIjoiMjAxOC0wNS0wOCJ9LCJjaGFsbGVuZ2VzIjpbXSwiZW52aXJvbm1lbnQiOiJzYW5kYm94IiwiY2xpZW50QXBpVXJsIjoiaHR0cHM6Ly9hcGkuc2FuZGJveC5icmFpbnRyZWVnYXRld2F5LmNvbTo0NDMvbWVyY2hhbnRzL3R0OHNydHBwOHlmZ2ZnaHAvY2xpZW50X2FwaSIsImFzc2V0c1VybCI6Imh0dHBzOi8vYXNzZXRzLmJyYWludHJlZWdhdGV3YXkuY29tIiwiYXV0aFVybCI6Imh0dHBzOi8vYXV0aC52ZW5tby5zYW5kYm94LmJyYWludHJlZWdhdGV3YXkuY29tIiwiYW5hbHl0aWNzIjp7InVybCI6Imh0dHBzOi8vb3JpZ2luLWFuYWx5dGljcy1zYW5kLnNhbmRib3guYnJhaW50cmVlLWFwaS5jb20vdHQ4c3J0cHA4eWZnZmdocCJ9LCJ0aHJlZURTZWN1cmVFbmFibGVkIjp0cnVlLCJwYXlwYWxFbmFibGVkIjp0cnVlLCJwYXlwYWwiOnsiZGlzcGxheU5hbWUiOiJNaW5kU2FwbGluZyIsImNsaWVudElkIjpudWxsLCJwcml2YWN5VXJsIjoiaHR0cDovL2V4YW1wbGUuY29tL3BwIiwidXNlckFncmVlbWVudFVybCI6Imh0dHA6Ly9leGFtcGxlLmNvbS90b3MiLCJiYXNlVXJsIjoiaHR0cHM6Ly9hc3NldHMuYnJhaW50cmVlZ2F0ZXdheS5jb20iLCJhc3NldHNVcmwiOiJodHRwczovL2NoZWNrb3V0LnBheXBhbC5jb20iLCJkaXJlY3RCYXNlVXJsIjpudWxsLCJhbGxvd0h0dHAiOnRydWUsImVudmlyb25tZW50Tm9OZXR3b3JrIjp0cnVlLCJlbnZpcm9ubWVudCI6Im9mZmxpbmUiLCJ1bnZldHRlZE1lcmNoYW50IjpmYWxzZSwiYnJhaW50cmVlQ2xpZW50SWQiOiJtYXN0ZXJjbGllbnQzIiwiYmlsbGluZ0FncmVlbWVudHNFbmFibGVkIjp0cnVlLCJtZXJjaGFudEFjY291bnRJZCI6Ik1pbmRTYXBsaW5nLUNBRCIsImN1cnJlbmN5SXNvQ29kZSI6IkNBRCJ9LCJtZXJjaGFudElkIjoidHQ4c3J0cHA4eWZnZmdocCIsInZlbm1vIjoib2ZmIiwibWVyY2hhbnRBY2NvdW50SWQiOiJNaW5kU2FwbGluZy1DQUQifQ=="


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
              client: client_token,
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
    </script>

  </div>

  <div class="right">
    <!-- empty div for possible content. -->
    <p></p>
  </div>
</div>

<div style="text-align:center;padding:10px;margin-top:7px;"> {•̃̾_•̃̾} </div>
</html>
