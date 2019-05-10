<?php

require_once( '../config.php' );

header( 'Content-Type: application/json' );

$input_param_mapping = [
  'first_name'      => 'BILLTOFIRSTNAME',
  'last_name'       => 'BILLTOLASTNAME',
  'address'         => 'BILLTOSTREET',
  'address_2'       => 'BILLTOSTREET2',
  'city'            => 'BILLTOCITY',
  'state'           => 'BILLTOSTATE',
  'zip'             => 'BILLTOZIP',
  'country'         => 'BILLTOCOUNTRY',
  'ship_first_name' => 'SHIPTOFIRSTNAME',
  'ship_last_name'  => 'SHIPTOLASTNAME',
  'ship_address'    => 'SHIPTOSTREET',
  'ship_address2'   => 'SHIPTOSTREET2',
  'ship_city'       => 'SHIPTOCITY',
  'ship_state'      => 'SHIPTOSTATE',
  'ship_zip'        => 'SHIPTOZIP',
  'ship_country'    => 'SHIPTOCOUNTRY',
  'account_number'  => 'ACCT',
  'exp_date'        => 'EXPDATE',
  'cvv2'            => 'CVV2',
  'eciflag'         => 'ECI',
  'cavv'            => 'CAVV',
  'xid'             => 'XID',
  'enrolled'        => 'MPIVENDOR3DS',
  'paresstatus'     => 'AUTHSTATUS3DS'
];

$params = [
  'USER'     => $payflow_config[ 'username' ],
  'PWD'      => $payflow_config[ 'password' ],
  'VENDOR'   => $payflow_config[ 'vendor' ],
  'PARTNER'  => $payflow_config[ 'partner' ],
  'TRXTYPE'  => 'S',
  'TENDER'   => 'C',
  'AMT'      => '1.00',
  'CURRENCY' => 'GBP'
];

foreach( $input_param_mapping as $post_field => $param_name ) {
  parse_input_param( $post_field, $param_name, $params );
}

error_log( print_r( $params, true ) );

$curl = curl_init( $payflow_config[ 'host' ] );

curl_setopt( $curl, CURLOPT_POST, true );
curl_setopt( $curl, CURLOPT_POSTFIELDS, payflow_encode( $params ) );
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

$result = curl_exec( $curl );

if( false === $result ) {
  fail( 'cURL error: ' . curl_error( $curl ) );
}

$resp = payflow_decode( $result );

if( !array_key_exists( 'RESULT', $resp ) ) {
  fail( 'No RESULT in response' );
}

if( 0 != $resp[ 'RESULT' ] ) {
  if( array_key_exists( 'RESPMSG', $resp ) && strlen( trim( $resp[ 'RESPMSG' ] ) ) ) {
    fail( 'Transaction failed: ' . $resp[ 'RESPMSG' ] );
  } else {
    fail( 'Transaction failed; no RESPMSG in response' );
  }
}

if( array_key_exists( 'PNREF', $resp ) && strlen( trim( $resp[ 'PNREF' ] ) ) ) {
  die( json_encode( [ 'ok' => true, 'pnref' => $resp[ 'PNREF' ] ] ) );
} else {
  fail( 'Transaction succeeded, but no PNREF in response' );
}

function fail( $msg ) {
  die( json_encode( [ 'ok' => false, 'error' => $msg ] ) );
}

function parse_input_param( $post_field_name, $param_name, &$target_arr ) {
  if( array_key_exists( $post_field_name, $_POST ) && strlen( trim( $_POST[ $post_field_name ] ) ) ) {
    $target_arr[ $param_name ] = trim( $_POST[ $post_field_name ] );
  }
}

function payflow_encode( $params ) {
  $out = [];
  foreach( $params as $index => $value ) {
    if( preg_match( '/[&=]/', $value ) ) {
      $out[] = $index . '[' . strlen( $value ) . ']=' . $value;
    } else {
      $out[] = "$index=$value";
    }
  }

  return implode( '&', $out );
}

function payflow_decode( $str ) {
  $out = [];
  while( strlen( $str ) ) {
    if( preg_match( '/^([A-Z_\d]+)\[(\d+)]=/', $str, $matches ) ) {
      $param_name_length = strlen( $matches[0] );
      $param_length = $matches[2];
      $param_name = $matches[1];
      $param_value = substr( $str, $param_name_length, $param_length );
      $str = substr( $str, $param_name_length + $param_length + 1 ); // Add an extra character for the "&"

      $out[ $param_name ] = $param_value;
    } else if( preg_match( '/^([A-Z_\d]+)=([^&]*)(&|$)?/', $str, $matches ) ) {
      $param_length = strlen( $matches[0] );
      $param_name = $matches[1];
      $param_value = $matches[2];;
      $str = substr( $str, $param_length );

      $out[ $param_name ] = $param_value;
    } else if( preg_match( '/^([^&]*)(&|$)/', $str, $matches ) ) {
      $str = substr( $str, strlen( $matches[0] ) );
    } else {
      $str = '';
    }
  }

  return $out;
}
