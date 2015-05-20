<?php

/*
 * E-Way authentication details and SOAP endpoint
 */

return array(
/*
|------------------------------
| eWAYCustomerID
|------------------------------
|
| E-Way Customer ID.
|
*/
'eWAYCustomerID' => env('EWAY_CUSTOMERID', '87654321'),
		
/*
|------------------------------
| Username
|------------------------------
|
| E-Way username.
|
*/		
'Username' => env('EWAY_USERNAME', 'test@eway.com.au'),
		
/*
|------------------------------
| Password
|------------------------------
|
| E-Way Password
|
*/		
'Password' =>  env('EWAY_PASSWORD', 'test123'),
		
/*
|------------------------------
| soapEndPoint
|------------------------------
|
| E-Way SOAP endpoint.
|
*/		
'soapEndPoint' => env('EWAY_ENDPOINT', 'https://www.eway.com.au/gateway/ManagedPaymentService/test/managedCreditCardPayment.asmx?WSDL'),
		
);
