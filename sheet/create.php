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
exit;
$client->setApplicationName("Test");
$service = new Google_Service_Sheets($client);
// TODO: Assign values to desired properties of `requestBody`:
$requestBody = new Google_Service_Sheets_Spreadsheet();

$response = $service->spreadsheets->create($requestBody);

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
	
	
	$con = new mysqli("localhost","versiposcrm","zG0USHl1xQ;!","versiposcrm");
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
	
	//echo "<pre>";
	//print_r($accessToken);
    
	$client->setAccessToken($accessToken);
	
	/print_r($client->isAccessTokenExpired());
	 
    // Refresh the token if it's expired.
    if ($client->isAccessTokenExpired()) {
        echo $tokenRefresh = $client->getRefreshToken();
		echo $client->fetchAccessTokenWithRefreshToken($tokenRefresh);
		
		exit('expire token');
		$sql=  mysql_query("UPDATE code SET refressh_token ='".$tokenRefresh."', WHERE company_id = '1' ");
		if($con->query($sql) === TRUE) {
			//echo "Record updated successfully";
		}
		
        file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
		exit("expired token conditions");
    }
	print_r($client);
	exit;
    return $client;
}
?>