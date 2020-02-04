<html>
<!-- Loads the header, for consistency across pages. -->
<?php require_once("../includes/head.php"); ?>
<!-- Braintree init, the file which creates the gateway using api keys -->
<?php require_once("../includes/braintree_init.php"); ?>
<body style="font-family:Verdana;">
<!-- generating a client token -->
  <script>var client_token = "<?php echo($gateway->clientToken()->generate());?>"
  </script>
<div style="overflow:auto">
  <div class="menu">
    <!-- navigation will go here -->
    <?php require_once("../includes/nav.html"); ?>
  </div>

<div class="main">
  <h2>3D Secure Verifications</h2>
<!-- all the stuff you need for 3D Secure. -->
  <p> Omg you need a lot of info for 3D Secure. </p>
  <div>
  <form id="3DSInfo" onsubmit="retrn false;">
    <input type="text" id="amount" placeholder="amount" class="other-input" onchange="getParam();"></input>
    <input type="text" id="email" placeholder="email address" class="other-input" onchange="getParam();"></input>
    <input type="text" id="givenName" placeholder="first name" class="other-input" onchange="getParam();"></input>
    <input type="text" id="surname" placeholder="last name" class="other-input" onchange="getParam();"></input>
    <input type="text" id="phoneNumber" placeholder="phone number" class="other-input" onchange="getParam();"></input>
    <input type="text" id="streetAddress" placeholder="address" class="other-input" onchange="getParam();"></input>
    <input type="text" id="extendedAddress" placeholder="extended address" class="other-input" onchange="getParam();"></input>
    <input type="text" id="locality" placeholder="city" class="other-input" onchange="getParam();"></input>
    <input type="text" id="region" placeholder="region/state" class="other-input" onchange="getParam();"></input>
    <input type="text" id="postalCode" placeholder="zip" class="other-input" onchange="getParam();"></input>
    <input type="text" id="countryCodeAlpha2" placeholder="Country Code" class="other-input" onchange="getParam();"></input>
  </form>
  </div>
<!-- Drop-in UI form. -->
      <form method="post" id="details" action="/customerCreate.php">
        <input type="hidden" id="nonce" name="nonce" />
      </form>
<br>
    <div id="dropin-container" style="float: left width:70%;"></div>
      <button id="submit-button" class="button">Request payment method</button>
      <script>
        var button = document.querySelector('#submit-button');
        var form = document.querySelector('#details');
        var submit = document.querySelector('input[type="submit"]');
        function getParam(){
          var threeDSecureParameters = {
            amount: document.getElementById('amount').value,
            email: document.getElementById('email').value,
            billingAddress: {
              givenName: document.getElementById('givenName').value, // ASCII-printable characters required, else will throw a validation error
              surname: document.getElementById('surname').value, // ASCII-printable characters required, else will throw a validation error
              phoneNumber: document.getElementById('phoneNumber').value,
              streetAddress: document.getElementById('streetAddress').value,
              extendedAddress: document.getElementById('extendedAddress').value,
              locality: document.getElementById('locality').value,
              region: document.getElementById('region').value,
              postalCode: document.getElementById('postalCode').value,
              countryCodeAlpha2: document.getElementById('countryCodeAlpha2').value
            }
          };
          console.log(threeDSecureParameters.email);
          braintree.dropin.create({
            authorization: client_token,
            container: '#dropin-container',
            threeDSecure: true
          }, function (createErr, instance) {
            button.addEventListener('click', function () {
              instance.requestPaymentMethod({
                threeDSecure: threeDSecureParameters
              },function (requestPaymentMethodErr, payload) {
                document.querySelector('#nonce').value = payload.nonce;
                var lenonce = payload.nonce;
                console.log(lenonce);
                form.submit()
              });
            });
          });
        };
      </script>

</div>

  <div class="right">
    <!-- empty div for possible content. -->
    <p></p>
  </div>
</div>

<div style="text-align:center;padding:10px;margin-top:7px;"> {•̃̾_•̃̾} </div>
</html>
