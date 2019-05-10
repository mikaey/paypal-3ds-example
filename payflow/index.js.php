<?php

require_once( '../config.php' );

// Function to generate a random GUID
function get_guid() {
  $data = PHP_MAJOR_VERSION < 7 ? openssl_random_pseudo_bytes(16) : random_bytes(16);
  $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // Set version to 0100
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // Set bits 6-7 to 10
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// Generate a random GUID to use as an order number
$uuid = get_guid();

// Header for the JWT
$header = [
  'alg' => 'HS256',
  'typ' => 'JWT',
  'jti' => $uuid
];

// Body of the JWT
$body = [
  'jti' => $uuid,
  'iat' => time(),
  'iss' => $cardinal_config[ 'api_identifier' ],
  'OrgUnitId' => $cardinal_config[ 'org_unit_id' ],
  'Payload' => [
    'OrderDetails' => [
      'OrderNumber' => get_guid(),
      'Amount' => '100',
      'CurrencyCode' => 'GBP'
    ]
  ],
  'ReferenceId' => get_guid(),
  'ObjectifyPayload' => true,
  'exp' => time() + 3600
];

// Generate the signature for the JWT
// For both the header and the body:
//   1. JSON-encode the data
//   2. Base64-encode the resulting JSON
//   3. Trim off any trailing '=' from the Base64-encoded data
$jwt_b64 = rtrim( base64_encode( json_encode( $body ) ), '=' );
$header_b64 = rtrim( base64_encode( json_encode( $header ) ), '=' );

// The signature will be based off the header and the body, which will be concatenated together with a '.' separating them
$jwt_token = $header_b64 . '.' . $jwt_b64;

// To generate the signature:
//   1. Hash the combined header+body using SHA256 (you can use another algorithm, but you need to be sure to update the header
//      accordingly; additionally, make sure your function outputs raw binary data instead of hex digits).  The key for the
//      signature must be the API key assigned to you by Cardinal.
//   2. Base64-encode the resulting hash
//   3. Trim any trailing '=' from the Base64-encoded data
$jwt_signature = rtrim( base64_encode( hash_hmac( 'sha256', $jwt_token, $cardinal_config[ 'api_key' ], true ) ), '=' );

// And assemble the completed JWT by appending it to the combined header+body
$jwt_final = $jwt_token . '.' . $jwt_signature;

?>
// The order ID is used both inside and outside of the JWT, so we'll put it into a variable that will be visible to
// the browser's JS VM
var orderId = "<?= $uuid ?>";

// These variables are used to ensure that the "submit" button isn't disabled until both (a) Cardinal Cruise has had
// a chance to initialize, and (b) the DOM is ready.
var cardinalSetupDone = false;
var documentReady = false;

function updateStatus(msg) {
  $('#status').html(msg);
}

function updateTdsStatus(msg) {
  $('#tdsstatus').html(msg);
}

// Set some Cardinal options
Cardinal.configure({
  logging: {
    level: 'on'
  }
});

// Set a callback to be called when Cardinal is done initializing
Cardinal.on('payments.setupComplete', function(setupCompleteData) {
  console.log('Setup complete');
  console.log(setupCompleteData);
  cardinalSetupDone = true;

  // If the DOM is ready to go, then enable the Submit button.
  if(documentReady) {
    updateStatus('Initialized; please enter your details below and click "Submit" when done');
    $('#submit-button').removeAttr('disabled');
  } else {
    updateStatus('Waiting for DOM initialization to complete');
  }
});

// Set a callback to be called when 3DS validation is finished
Cardinal.on('payments.validated', function(data, jwt) {
  console.log('Payment validated');
  console.log(data);
  console.log(jwt);

  switch(data.ActionCode) {
    case 'SUCCESS': // Buyer enrolled in 3DS and successfully authenticated
      updateStatus('Validation successful; processing payment');
      updateTdsStatus('Buyer enrolled and successfully validated');
      submitPayment(data.Payment.ExtendedData);
      break;
    case 'NOACTION': // Buyer not enrolled in 3DS
      updateStatus('Validation successful; processing payment');
      updateTdsStatus('Buyer not enrolled');
      submitPayment(data.Payment.ExtendedData);
      break;
    case 'FAILURE': // Buyer enrolled in 3DS but failed to authenticate themselves
      updateStatus('Validation failed');
      updateTdsStatus('Buyer enrolled but not validated');
      $('#submit-button').removeAttr('disabled');
      break;
    case 'ERROR': // Something else went wrong
      updateStatus('Validation failed (error occurred during validation)');
      $('#submit-button').removeAttr('disabled');
      break;
  }
});

// Tell Cardinal Cruise to initialize itself
Cardinal.setup('init', {
  jwt: '<?= $jwt_final; ?>'
});

// Submit the payment to our server for processing
function submitPayment(data) {
  var req = {
    first_name:      $('#first_name').val(),
    last_name:       $('#last_name').val(),
    address:         $('#address').val(),
    address2:        $('#address2').val(),
    city:            $('#city').val(),
    state:           $('#state').val(),
    zip:             $('#zip').val(),
    country:         $('#country').val(),
    ship_first_name: $('#ship_first_name').val(),
    ship_last_name:  $('#ship_last_name').val(),
    ship_address:    $('#ship_address').val(),
    ship_address2:   $('#ship_address2').val(),
    ship_city:       $('#ship_city').val(),
    ship_state:      $('#ship_state').val(),
    ship_zip:        $('#ship_zip').val(),
    ship_country:    $('#ship_country').val(),
    account_number:  $('#account_number').val(),
    exp_date:        $('#exp_month').val() + $('#exp_year').val().substr(2),
    cvv2:            $('#cvv2').val(),
    eciflag:         data.ECIFlag,
    cavv:            data.CAVV,
    xid:             data.XID,
    enrolled:        data.Enrolled,
    paresstatus:     data.PAResStatus
  };

  $.ajax('ajax.php', {
    data: req,
    method: 'POST'
  }).done(function(data) {
    if(data.ok) {
      updateStatus('Transaction succeeded; PNREF = ' + data.pnref);
    } else {
      updateStatus('Transaction failed; error = ' + data.error);
    }
    $('#submit-button').removeAttr('disabled');
  }).fail(function() {
    updateStatus('Communication error while processing transaction');
    $('#submit-button').removeAttr('disabled');
  });
}

$(document).ready(function() {
  // Set what happens when the buyer clicks on the "Submit" button
  $('#submit-button').click(function() {
    var data = {
      OrderDetails: {
        OrderNumber: orderId,
        Amount: "100",
        CurrencyCode: "GBP"
      },
      Consumer: {
        BillingAddress: {
          FirstName:   $('#first_name').val(),
          LastName:    $('#last_name').val(),
          Address1:    $('#address').val(),
          Address2:    $('#address2').val(),
          Address3:    $('#address3').val(),
          City:        $('#city').val(),
          State:       $('#state').val(),
          PostalCode:  $('#zip').val(),
          CountryCode: $('#country').val(),
          Phone1:      $('#phone').val(),
          Phone2:      $('#phone2').val()
        },
        ShippingAddress: {
          FirstName:   $('#ship_first_name').val(),
          LastName:    $('#ship_last_name').val(),
          Address1:    $('#ship_address').val(),
          Address2:    $('#ship_address2').val(),
          Address3:    $('#ship_address3').val(),
          City:        $('#ship_city').val(),
          State:       $('#ship_state').val(),
          PostalCode:  $('#ship_zip').val(),
          CountryCode: $('#ship_country').val(),
          Phone1:      $('#ship_phone').val(),
          Phone2:      $('#ship_phone2').val()
        },
        Account: {
          AccountNumber:   $('#account_number').val(),
          ExpirationMonth: $('#exp_month').val(),
          ExpirationYear:  $('#exp_year').val(),
          NameOnAccount:   $('#card_name').val(),
          CardCode:        $('#cvv2').val()
        }
      }
    };

    $('#submit-button').attr('disabled', 'disabled');
    updateStatus('Validation started');
    updateTdsStatus('Validation in progress');

    // Tell Cardinal Cruise to begin the authentication process
    Cardinal.start('cca', data);
  });

  documentReady = true;

  // If Cardinal Cruise is ready to go, then enable the Submit button.
  if(cardinalSetupDone) {
    updateStatus('Initialized; please enter your details below and click "Submit" when done');
    $('#submit-button').removeAttr('disabled');
  } else {
    updateStatus('Waiting for Cardinal Songbird initialization to complete');
  }
});
