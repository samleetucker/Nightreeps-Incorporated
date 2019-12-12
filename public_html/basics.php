<html>
<!-- Loads the header, for consistency across pages. -->
<?php require_once("../includes/head.php"); ?>
<!-- Braintree init, the file which creates the gateway using api keys -->
<?php require_once("../includes/braintree_init.php"); ?>
<div class="wrapper">
  <header>
    <h1 align="center">Hosted Fields ₍ᵔ·͈༝·͈ᵔ₎</h1>
  </header>
  <p align="center">Let's make a simple Hosted Fields page.</p>
  <br></br>
  <p>First we are going to run a script that <a href="https://developers.braintreepayments.com/reference/request/client-token/generate/php">generates le client token!</a></p>
  <h4><p>Here is the code and its result: </p></h4>
    <div class="well well-sm"><?php highlight_string("<?php
  \$gateway->clientToken()->generate(
    ['customerId' => 242967293,
    'merchantAccountId' => 'MindSapling-CAD'
  ])
  ?>");?></div>
    <p><button data-toggle="collapse" data-target="#c1">show/hide result</button>
      <div id="c1" class="collapse">
        <pre><code id="client_token"></code></pre>
      </div>
    </p>
  <!-- This script is creating the client token and injecting it into the HTML. The client token variable is going to be used later for client authorization! -->
  <script>var client_token = "<?php echo($gateway->clientToken()->generate(
    ['customerId' => 242967293,
    'merchantAccountId' => 'MindSapling-CAD']
  ));?>"
  document.getElementById("client_token").innerHTML = client_token;
  </script>
<p>The cool thing about these dudes is that you can actually decode them to see the parameters used in the above code. Lots of useful information can be found in a client token. </p>
<p><button data-toggle="collapse" data-target="#c2">Here is an example</button>
  <div id="c2" class="collapse">
    <pre><code id="decode"></code>
      "version":2,
      "authorizationFingerprint":"eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiIsImtpZCI6IjIwMTgwNDI2MTYtc2FuZGJveCIsImlzcyI6IkF1dGh5In0.eyJleHAiOjE1NzYyMTI1MDEsImp0aSI6IjY4OTU0MGVjLTM1MDQtNGIxNi05YzU3LTZiNmRhMTQ4YjA0NSIsInN1YiI6InR0OHNydHBwOHlmZ2ZnaHAiLCJpc3MiOiJBdXRoeSIsIm1lcmNoYW50Ijp7InB1YmxpY19pZCI6InR0OHNydHBwOHlmZ2ZnaHAiLCJ2ZXJpZnlfY2FyZF9ieV9kZWZhdWx0Ijp0cnVlfSwicmlnaHRzIjpbIm1hbmFnZV92YXVsdCJdLCJvcHRpb25zIjp7ImN1c3RvbWVyX2lkIjoyNDI5NjcyOTMsIm1lcmNoYW50X2FjY291bnRfaWQiOiJNaW5kU2FwbGluZy1DQUQifX0.LwDd-s35WTMeFScTO9Tlfum0W5pleySG69JYDLJobMJOchWyCHFGe-50G2tMx2x-2eJ3R4qRaWdc2BZNm24Jjg?customer_id=",
      "configUrl":"https://api.sandbox.braintreegateway.com:443/merchants/tt8srtpp8yfgfghp/client_api/v1/configuration",
      "graphQL": {
          "url": "https://payments.sandbox.braintree-api.com/graphql", "date": "2018-05-08"
      }
      ,
      "challenges":[],
      "environment":"sandbox",
      "clientApiUrl":"https://api.sandbox.braintreegateway.com:443/merchants/tt8srtpp8yfgfghp/client_api",
      "assetsUrl":"https://assets.braintreegateway.com",
      "authUrl":"https://auth.venmo.sandbox.braintreegateway.com",
      "analytics": {
          "url": "https://origin-analytics-sand.sandbox.braintree-api.com/tt8srtpp8yfgfghp"
      }
      ,
      "threeDSecureEnabled":true,
      "paypalEnabled":true,
      "paypal": {
          "displayName": "MindSapling", "clientId": null, "privacyUrl": "http://example.com/pp", "userAgreementUrl": "http://example.com/tos", "baseUrl": "https://assets.braintreegateway.com", "assetsUrl": "https://checkout.paypal.com", "directBaseUrl": null, "allowHttp": true, "environmentNoNetwork": true, "environment": "offline", "unvettedMerchant": false, "braintreeClientId": "masterclient3", "billingAgreementsEnabled": true, "merchantAccountId": "MindSapling-CAD", "currencyIsoCode": "CAD"
      }
      ,
      "merchantId":"tt8srtpp8yfgfghp",
      "venmo":"off",
      "merchantAccountId":"MindSapling-CAD"
    </pre>
  </div>
</p>
  <p>Next we are going to need to make the Hosted Fields forms. This page is going to use the basic example which is on our
    <a href="https://developers.braintreepayments.com/guides/hosted-fields/setup-and-integration/javascript/v3#basic-integration">Hosted Fields Dev Docs.</a>
  </p>
  <p>
    The above client token is being used to initialize the components below! Don't forget, <a href="https://internal.braintreepayments.com/display/DD/Client+Authorization+-+JWT+Cheat+Sheet"> client tokens expire in 24 hours </a>. That is why a client token is generated every time this page is landed on.
  </p>

  <!-- Time to get saucy. Below is going to be the script that loads all of the Hosted Fields components and communicates with the server-side junk! -->

  <form method="post" id="details" action="/sale.php">
    <label for="card-number" class="hosted-fields--label">Card Number</label>
    <div id="card-number" class="hosted-field"></div>

    <label for="cvv" class="hosted-fields--label">CVV</label>
    <div id="cvv" class="hosted-field"></div>

    <label for="expiration-date" class="hosted-fields--label">Expiration Date</label>
    <div id="expiration-date" class="hosted-field"></div>
    <input type="submit" value="Request Payment Method" disabled />
    <p><div>Fill out the form and click the button to get a nonce! Click it again to make a transaction.</div></p>
    <div id="nonce-display" name="nonce-display" hidden></div>
    <input type="hidden" id="nonce" name="nonce" />
  </form>
  <script>
    var form = document.querySelector('#details');
    var submit = document.querySelector('input[type="submit"]');
    var nonce = document.querySelector('#nonce-display');
    var gate = 0;
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
  </script>

</div>
</html>
