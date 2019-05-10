<?php

// The following parameters are provided to you by Cardinal Commerce when you sign up with them.
$cardinal_config = [
  // Set this to your API key.
  'api_key'        => '',
  // Set this to your API identifier.
  'api_identifier' => '',
  // Set this to your OrgUnit ID.
  'org_unit_id'    => '',
  // Set this to the appropriate SongbirdUrl (this one is for Cardinal's staging environment).
  'songbird_url'   => 'https://songbirdstag.cardinalcommerce.com/edge/v1/songbird.js'
];

// If you're using PayPal's DoDirectPayment API, fill in your API credentials below.
// If you're using Payflow, don't worry about this.
$paypal_config = [
  'user'      => '',
  'pwd'       => '',
  'signature' => '',
  // API host you want to use.  This one is for PayPal's Sandbox environment.
  // For production, use https://api-3t.paypal.com/nvp
  'host'      => 'https://api-3t.sandbox.paypal.com/nvp'
];

// If you're using Payflow, fill in your API credentials below.
// If you're using PayPal's DoDirectPayment API, don't worry about this.
$payflow_config = [
  'username' => '',
  'password' => '',
  'vendor'   => '',
  'partner'  => '',
  // API host you want to use.  This one is for Payflow's test environment.
  // For production, use https://payflowpro.paypal.com
  'host'     => 'https://pilot-payflowpro.paypal.com'
];
