<?php require_once( '../config.php' ); ?><!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>3DSecure Test Page</title>
        <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
        <script src="<?= $cardinal_config[ 'songbird_url' ] ?>"></script>
        <script src="./index.js.php"></script>
        <style type="text/css">
         th {
             text-align: right;
         }
        </style>
    </head>
    <body>
        <h1>3D Secure Test Page</h1>
        <div><strong>Status:</strong> <span id="status">Waiting for initialization</span></div>
        <div><strong>3DS Status:</strong> <span id="tdsstatus">Not started</span></div>
        <fieldset>
            <legend>Billing address</legend>
            <table>
                <tr>
                    <th>First name:</th>
                    <td><input type="text" id="first_name" size="40"></td>
                </tr>
                <tr>
                    <th>Last name:</th>
                    <td><input type="text" id="last_name" size="40"></td>
                </tr>
                <tr>
                    <th>Address:</th>
                    <td><input type="text" id="address" size="80"></td>
                </tr>
                <tr>
                    <th>Address 2:</th>
                    <td><input type="text" id="address2" size="80"></td>
                </tr>
                <tr>
                    <th>Address 3:</th>
                    <td><input type="text" id="address3" size="80"></td>
                <tr>
                    <th>City:</th>
                    <td><input type="text" id="city" size="40"></td>
                </tr>
                <tr>
                    <th>State</th>
                    <td><input type="text" id="state" size="2"></td>
                </tr>
                <tr>
                    <th>Postal code:</th>
                    <td><input type="text" id="zip" size="9"></td>
                </tr>
                <tr>
                    <th>Country code:</th>
                    <td><input type="text" id="country" size="2"></td>
                </tr>
                <tr>
                    <th>Phone:</th>
                    <td><input type="text" id="phone" size="20"></td>
                </tr>
                <tr>
                    <th>Phone 2:</th>
                    <td><input type="text" id="phone2" size="20"></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><input type="email" id="email"></td>
                </tr>
                <tr>
                    <th>Email 2:</th>
                    <td><input type="email" id="email2"></td>
                </tr>
            </table>
        </fieldset>
        <fieldset>
            <legend>Shipping address</legend>
            <table>
                <tr>
                    <th>First name:</th>
                    <td><input type="text" id="ship_first_name" size="40"></td>
                </tr>
                <tr>
                    <th>Last name:</th>
                    <td><input type="text" id="ship_last_name" size="40"></td>
                </tr>
                <tr>
                    <th>Address:</th>
                    <td><input type="text" id="ship_address" size="80"></td>
                </tr>
                <tr>
                    <th>Address 2:</th>
                    <td><input type="text" id="ship_address2" size="80"></td>
                </tr>
                <tr>
                    <th>Address 3:</th>
                    <td><input type="text" id="ship_address3" size="80"></td>
                </tr>
                <tr>
                    <th>City:</th>
                    <td><input type="text" id="ship_city" size="40"></td>
                </tr>
                <tr>
                    <th>State</th>
                    <td><input type="text" id="ship_state" size="2"></td>
                </tr>
                <tr>
                    <th>Postal code:</th>
                    <td><input type="text" id="ship_zip" size="9"></td>
                </tr>
                <tr>
                    <th>Country code:</th>
                    <td><input type="text" id="ship_country" size="2"></td>
                </tr>
                <tr>
                    <th>Phone:</th>
                    <td><input type="text" id="ship_phone" size="20"></td>
                </tr>
                <tr>
                    <th>Phone 2:</th>
                    <td><input type="text" id="ship_phone2" size="20"></td>
                </tr>
            </table>
        </fieldset>
        <fieldset>
            <legend>Credit Card Details</legend>
            <table>
                <tr>
                    <th>Account number:</th>
                    <td><input type="text" id="account_number" size="16"></td>
                </tr>
                <tr>
                    <th>Expiration date:</th>
                    <td><input type="text" id="exp_month" size="2">/<input type="text" id="exp_year" size="4"></td>
                </tr>
                <tr>
                    <th>Name on card:</th>
                    <td><input type="text" id="card_name" size="80"></td>
                </tr>
                <tr>
                    <th>CVV2:</th>
                    <td><input type="text" id="cvv2" size="3"></td>
                </tr>
            </table>
        </fieldset>
        <button id="submit-button" disabled="disabled">Submit</button>
    </body>
</html>
