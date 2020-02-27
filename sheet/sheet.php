<?php
/*
 * BEFORE RUNNING:
 * ---------------
 * 1. If not already done, enable the Google Sheets API
 *    and check the quota for your project at
 *    https://console.developers.google.com/apis/api/sheets
 * 2. Install the PHP client library with Composer. Check installation
 *    instructions at https://github.com/google/google-api-php-client.
 */

// Autoload Composer.
require_once __DIR__ . '/vendor/autoload.php';

$client = getClient();

$service = new Google_Service_Sheets($client);

// The ID of the spreadsheet to update.
$spreadsheetId = '19A6q7nY_il2fdTFgGrtMrtbTnsPaDCZKjbZawwnHOqY';  // TODO: Update placeholder value.

// The A1 notation of a range to search for a logical table of data.
// Values will be appended after the last row of the table.
$range = 'A1: D';  // TODO: Update placeholder value.

// TODO: Assign values to desired properties of `requestBody`:
$requestBody = new Google_Service_Sheets_ValueRange();
echo '<pre>';
print_r($requestBody);
exit;
$response = $service->spreadsheets_values->append($spreadsheetId, $range, $requestBody);

// TODO: Change code below to process the `response` object:
echo '<pre>', var_export($response, true), '</pre>', "\n";

function getClient() {
    // TODO: Change placeholder below to generate authentication credentials. See
  // https://developers.google.com/sheets/quickstart/php#step_3_set_up_the_sample
  //
  // Authorize using one of the following scopes:
  //   'https://www.googleapis.com/auth/drive'
  //   'https://www.googleapis.com/auth/drive.file'
  //   'https://www.googleapis.com/auth/spreadsheets'
  	$client = new Google_Client();
	
	
	$con = new mysqli("localhost","root","","googlesheet");
	// Check connection
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	
	$jsonfile ='';
    // Load previously authorized credentials from a file.
	
	$sql = "SELECT * FROM code WHERE company_id = 1";
	
	$result = $con->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$jsonfile = $row["code"];
		}
	} else {
		echo "0 results";
	}
	
	// Load previously authorized credentials from a file.
    $credentialsPath = $jsonfile;
		
    if (file_exists($credentialsPath)) {
        $accessToken = json_decode(file_get_contents($credentialsPath), true);
    } 

    $client->setAccessToken($accessToken);
	// print_r($client->isAccessTokenExpired());
	// exit;
    // Refresh the token if it's expired.
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
    }
	// print_r($client);
	// exit;
    return $client;
}
?>