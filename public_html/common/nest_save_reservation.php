<?php

header('Content-Type: application/json');

if (! isset($_GET['token']) ){
    echo json_encode ([ "status" => 400, "response" => "Token is required"]);
    http_response_code(400);
    return;
}
if (! isset($_GET['store_name']) ){
    echo json_encode ([ "status" => 400, "response" => "Store name is required"]);
    http_response_code(400);
    return;
}
if (! isset($_GET['product_code']) ){
    echo json_encode ([ "status" => 400, "response" => "Product code name is required"]);
    http_response_code(400);
    return;
}

$storedToken = $_GET['token'];
$storeName = $_GET['store_name'];
$productCode = $_GET['product_code'];

/*
export interface ReservationToSaveDto {
  deviceType: string;
  bookDate: string;
  scheduledTime: string;
  deviceSerial: string;
  customer: Client;
  description: string;
  storeId: string;
  languageCode: string
}

export interface Client {
  name: string;
  lastname: string;
  phone: string;
  email: string;
}
*/
$inputJSON = file_get_contents('php://input');
$requestBody = json_decode($inputJSON, TRUE); //convert JSON into array


$messageLog = [];
$messageLog[] = "Token ".$storedToken;
$messageLog[] = "storeName ".$storeName;
$messageLog[] = "productCode ".$productCode;

require_once __DIR__ . "/StoreAppleInfo.php";
$storeAppleInfoService = new StoreAppleInfo();
$storeInfo = $storeAppleInfoService->getStoreAppleInfoByStore($storeName);
$url = "https://api-partner-connect.apple.com/gsx/api/reservation/create";

$requestHeaders = array(
    'X-Apple-SoldTo: ' . $storeInfo["REST_SoldTo"],
    'X-Apple-ShipTo: ' . $storeInfo["REST_ShipTo"],
    'X-Apple-Auth-Token: ' . $storedToken,
    'X-Apple-Service-Version: v5',
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Apple-Client-Locale: en-US'
);

$laguageCode = $requestBody['languageCode'];
$dateTimeAppointment = $requestBody['scheduledTime'];//esta hora debe estar en utc0
$deviceSerial = $requestBody['deviceSerial'];

$postData = '
{
    "product": {
        "issueReported": "New Web Reservation",
        "productCode": "'. $requestBody["deviceType"] .'"
    },
    "notes": {
        "note": "Booking Reservation"
    },
    "emailLanguageCode": "'.$laguageCode.'",
    "shipToCode": "'. $storeInfo["REST_ShipTo"].'",
    "reservationType": "CIN",
    "correlationId": "12345",
    "reservationDate": "'.$dateTimeAppointment.'",
    "device": {
        "id": "'.$deviceSerial.'"
    },
    "customer": {
        "firstName": "'.$requestBody['customer']['name'].'",
        "lastName": "'.$requestBody['customer']['lastname'].'",
        "address": {
            "line4": "",
            "city": "",
            "countryCode": "",
            "postalCode": "",
            "county": "",
            "stateCode": "",
            "line3": "",
            "line2": "",
            "line1": ""
        },
        "phone": {
            "phoneCountryCd": "CA",
            "primaryPhone": "'.$requestBody['customer']['phone'].'"
        },
        "emailId": "'. $requestBody['customer']['email'].'",
        "governmentId": ""
    }
}';


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSLCERT, $storeInfo["REST_CERT_PATH"]);
curl_setopt($ch, CURLOPT_SSLKEY, $storeInfo["REST_SSL_KEY"]);
curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $storeInfo["REST_CERT_PASS"]);
curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


$result = curl_exec($ch);
if($result === false){
    $messageLog[] = "This is error trying to consume apple api = ". json_encode(curl_error($ch));
    http_response_code(500);
    echo json_encode ([ "status" => 500, "response" => "Error trying to consume apple api", "log"=> $messageLog]);
    return;
}

$messageLog[] = "This is response from apple = ". ($result);

$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if(! (isResponse2xx($statusCode)) ){//if apple response is not ok
    http_response_code($statusCode);
    $messageLog[] = "This is error from apple api = ". json_encode(curl_error($ch));
    echo json_encode ([ "status" => $statusCode, "response" => "Error from apple api ", "log"=> $messageLog]);
    return;
}

http_response_code(200);
echo json_encode ([ "status" => $statusCode, "response" => $result, "log"=> $messageLog ]);
return;

function isResponse2xx($statusCode){
    $statusCodeString = (string)$statusCode;
    $firstDigit = $statusCodeString[0];

    if ($firstDigit === '2') 
        return true; 
    else 
        return false;
}
?>