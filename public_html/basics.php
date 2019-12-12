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
  <p>First we are going to run a script that generates le client token!</p>
  <h4><p>Here is the code and its result: </h4>
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
  <p>Next we are going to need to make the Hosted Fields forms. This page is going to use the basic example which is on our
    <a href="https://developers.braintreepayments.com/guides/hosted-fields/setup-and-integration/javascript/v3#basic-integration">Hosted Fields Dev Docs.</a>
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
    <p><div>Fill out the form and click the button to get a nonce!</div></p>
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
