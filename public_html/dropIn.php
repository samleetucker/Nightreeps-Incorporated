<html>
<!-- Loads the header, for consistency across pages. -->
<?php require_once("../includes/head.php"); ?>
<!-- Braintree init, the file which creates the gateway using api keys -->
<?php require_once("../includes/braintree_init.php"); ?>
<body style="font-family:Verdana;">
<!-- generating a client token -->
  <script>var client_token = "<?php echo($gateway->clientToken()->generate());?>"
  </script>

<div style=";padding:15px;text-align:center;">
  <header>
    <h1 align="center">Drop-in UI Demo</h1>
  </header>
    <h3></h3>
</div>

<div style="overflow:auto">
  <div class="menu">
    <!-- navigation will go here -->
    <?php require_once("../includes/nav.html"); ?>
  </div>

<div class="main">
    <!-- <h3 align="center"><p>Drop-in UI w/ 3D Secure</p></h3> -->
      <form method="post" id="details" action="/customerCreate.php">
        <input type="hidden" id="nonce" name="nonce" />
      </form>
    <div id="dropin-container" style="float: none; width:70%;"></div>
      <button id="submit-button" class="button">Request payment method</button>
      <script>
        var button = document.querySelector('#submit-button');
        var form = document.querySelector('#details');
        var submit = document.querySelector('input[type="submit"]');
        var threeDSecureParameters = {
          amount: '500.00',
          email: 'test@example.com',
          billingAddress: {
          givenName: 'Jill', // ASCII-printable characters required, else will throw a validation error
          surname: 'Doe', // ASCII-printable characters required, else will throw a validation error
          phoneNumber: '8101234567',
          streetAddress: '555 Smith St.',
          extendedAddress: '#5',
          locality: 'Oakland',
          region: 'CA',
          postalCode: '12345',
          countryCodeAlpha2: 'US'
        },
        additionalInformation: {
          workPhoneNumber: '8101234567',
          shippingGivenName: 'Jill',
          shippingSurname: 'Doe',
          shippingPhone: '8101234567',
          shippingAddress: {
            streetAddress: '555 Smith St.',
            extendedAddress: '#5',
            locality: 'Oakland',
            region: 'CA',
            postalCode: '12345',
            countryCodeAlpha2: 'US'
          }
        },
      };

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
      </script>

</div>

  <div class="right">
    <!-- empty div for possible content. -->
    <p></p>
  </div>
</div>

<div style="text-align:center;padding:10px;margin-top:7px;"> {•̃̾_•̃̾} </div>
</html>
