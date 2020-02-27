<?php
session_start();
// Autoload Composer.
require_once __DIR__ . '/vendor/autoload.php';



$con = new mysqli("localhost","versiposcrm","zG0USHl1xQ;!","versiposcrm");
// Check connection
if (mysqli_connect_errno())
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
  
include('settings.php');

// Google passes a parameter 'code' in the Redirect Url
if(isset($_GET['code'])) {
	try {
		$client = getClient();

		$service = new Google_Service_Sheets($client);

		// Redirect to the page where user can create spreadsheet
		header('Location: create.php');
		exit();
	}
	catch(Exception $e) {
		echo $e->getMessage();
		exit();
	}
}




function getClient()
{
	$con = new mysqli("localhost","root","","googlesheet");
	// Check connection
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
    // $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAuthConfig('credentials.json');

    $client->setAccessType('offline');
	$client->setApprovalPrompt('force');

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
	
    $credentialsPath = $jsonfile;
	$credentialsPath;
	
    if (file_exists($credentialsPath)) {
        $accessToken = json_decode(file_get_contents($credentialsPath), true);
		echo "<pre>";
		print_r($accessToken);
    } else {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        // printf("Open the following link in your browser:\n%s\n", $authUrl);
        // print 'Enter verification code: ';
       
	   //$authCode = $_GET['code'].'#';
	   $authCode = $_GET['code'];

        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        // Check to see if there was an error.
        if (array_key_exists('error', $accessToken)) {
            throw new Exception(join(', ', $accessToken));
        }

        // Store the credentials to disk.
        if (!file_exists(dirname($credentialsPath))) {
			$credentialsPath = time().'.json';
            mkdir(dirname($credentialsPath), 0700, true);
			$sql = "INSERT INTO code (company_id, code) VALUES (1, '".$credentialsPath."')";

			if ($con->query($sql) === TRUE) {
				//echo "New record created successfully";
			}
        }
		// $tokenRefresh = $client->getRefreshToken();
		// echo $tokenRefresh;
		// exit;
		// $sql = "UPDATE code SET refresh_token = '".$tokenRefresh."' WHERE id = '1'";
		// $sql=  mysql_query("UPDATE code SET refresh_token ='".$tokenRefresh."', WHERE company_id = '1' ");
		// if ($con->query($sql) === TRUE) {
			//echo "Record updated successfully";
		// }
        file_put_contents($credentialsPath, json_encode($accessToken));
        printf("Credentials saved to %s\n", $credentialsPath);
    }
	
	$client->setAccessToken($accessToken);
  
	// Refresh the token if it's expired.
    if ($client->isAccessTokenExpired()) 
	{
		$tokenRefresh = $client->getRefreshToken();
        $client->fetchAccessTokenWithRefreshToken($tokenRefresh);
		$sql=  mysql_query("UPDATE code SET refressh_token ='".$tokenRefresh."', WHERE company_id = '1' ");
		if ($con->query($sql) === TRUE) {
			echo "Record updated successfully";
		}
        file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
    }
    return $client;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">

#logo {
	text-align: center;
	width: 200px;
    display: block;
    margin: 100px auto;
    border: 2px solid #2980b9;
    padding: 10px;
    background: none;
    color: #2980b9;
    cursor: pointer;
    text-decoration: none;
}

</style>
</head>

<body>

<?php
$login_url = 'https://accounts.google.com/o/oauth2/auth?response_type=code&accesstype=offline&client_id='. CLIENT_ID .'&redirect_uri=' . urlencode(CLIENT_REDIRECT_URL) . '&state&scope=' . urlencode('https://www.googleapis.com/auth/spreadsheets') . '&approval_prompt=force'

//$login_url = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/drive.file') . '&redirect_uri=' . urlencode(CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=offline';

?>

<a id="logo" href="<?= $login_url  ?>">Login with Google</a>

</body>
</html>