<?php

require_once( '../config.php' );

header( 'Content-Type: application/json' );

$input_param_mapping = [
  'first_name'      => 'FIRSTNAME',
  'last_name'       => 'LASTNAME',
  'address'         => 'STREET',
  'address_2'       => 'STREET2',
  'city'            => 'CITY',
  'state'           => 'STATE',
  'zip'             => 'ZIP',
  'country'         => 'COUNTRYCODE',
  'ship_name'       => 'SHIPTONAME',
  'ship_address'    => 'SHIPTOSTREET',
  'ship_address2'   => 'SHIPTOSTREET2',
  'ship_city'       => 'SHIPTOCITY',
  'ship_state'      => 'SHIPTOSTATE',
  'ship_zip'        => 'SHIPTOZIP',
  'ship_country'    => 'SHIPTOCOUNTRY',
  'account_number'  => 'ACCT',
  'exp_date'        => 'EXPDATE',
  'cvv2'            => 'CVV2',
  'eciflag'         => 'ECI3DS',
  'cavv'            => 'CAVV',
  'xid'             => 'XID',
  'enrolled'        => 'MPIVENDOR3DS',
  'paresstatus'     => 'AUTHSTATUS3DS'
];

$params = [
  'USER'          => $paypal_config[ 'user' ],
  'PWD'           => $paypal_config[ 'pwd' ],
  'SIGNATURE'     => $paypal_config[ 'signature' ],
  'METHOD'        => 'DoDirectPayment',
  'VERSION'       => '204.0',
  'PAYMENTACTION' => 'Sale',
  'AMT'           => '1.00',
  'CURRENCY'      => 'GBP'
];

foreach( $input_param_mapping as $post_field => $param_name ) {
  parse_input_param( $post_field, $param_name, $params );
}

error_log( print_r( $params, true ) );

$curl = curl_init( $paypal_config[ 'host' ] );

curl_setopt( $curl, CURLOPT_POST, true );
curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $params ) );
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

$result = curl_exec( $curl );

if( false === $result ) {
  fail( 'cURL error: ' . curl_error( $curl ) );
}

parse_str( $result, $resp );

if( !array_key_exists( 'ACK', $resp ) ) {
  fail( 'No ACK in response' );
}

if( 'Success' != $resp[ 'ACK' ] && 'SuccessWithWarning' != $resp[ 'ACK' ] ) {
  if( array_key_exists( 'L_SHORTMESSAGE0', $resp ) && strlen( trim( $resp[ 'L_SHORTMESSAGE0' ] ) ) ) {
    fail( 'Transaction failed: ' . $resp[ 'L_SHORTMESSAGE0' ] );
  } else {
    fail( 'Transaction failed; no error message in response' );
  }
}

if( array_key_exists( 'TRANSACTIONID', $resp ) && strlen( trim( $resp[ 'TRANSACTIONID' ] ) ) ) {
  die( json_encode( [ 'ok' => true, 'txnid' => $resp[ 'TRANSACTIONID' ] ] ) );
} else {
  fail( 'Transaction succeeded, but no TRANSACTIONID in response' );
}

function fail( $msg ) {
  die( json_encode( [ 'ok' => false, 'error' => $msg ] ) );
}

function parse_input_param( $post_field_name, $param_name, &$target_arr ) {
  if( array_key_exists( $post_field_name, $_POST ) && strlen( trim( $_POST[ $post_field_name ] ) ) ) {
    $target_arr[ $param_name ] = trim( $_POST[ $post_field_name ] );
  }
}
