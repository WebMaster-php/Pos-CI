<?php 
echo APPPATH;
//require_once(APPPATH . 'third_party/php-imap/Imap.php'); 
//$imap       = new Imap('{imap.gmail.com:993/imap/ssl}INBOX', 'hafizadil321@gmail.com', 'adil 12345', 'ssl');
//if ($imap->isConnected() === true) {
//exit('inn');
//}
//exit('inn');
//echo "<pre>"; print_r($connection); 
exit('out');

?>
<h1>Gmail Email Inbox using PHP with IMAP</h1>
<?php
if (! function_exists('imap_open')) {
echo "IMAP is not configured.";
exit();
} else {
?>
<div id="listData" class="list-form-container">
<?php

// / Connecting Gmail server with IMAP /
	
$connection = imap_open('{imap.gmail.com:993/imap/ssl}INBOX', 'hafizadil321@gmail.com', 'adil 12345') or die('Cannot connect to Gmail: ' . imap_last_error());

// / Search Emails having the specified keyword in the email subject /

	echo "<pre>"; print_r($connection); exit('thi');
$search_string = 'SUBJECT testing';

$emailData = imap_search($connection, $search_string);

// if(imap_connect()){

// }
echo "<pre>"; print_r($emailData); exit('okkkk');

if (! empty($emailData)) {
?>
<table>
<?php
foreach ($emailData as $emailIdent) {

$overview = imap_fetch_overview($connection, $emailIdent, 0);
$message = imap_fetchbody($connection, $emailIdent, '1.1');
$messageExcerpt = substr($message, 0, 150);
$partialMessage = trim(quoted_printable_decode($messageExcerpt));
$date = date("d F, Y", strtotime($overview[0]->date));
?>
<tr>
<td><span class="column">
<?php echo $overview[0]->from; ?>
</span></td>
<td class="content-div"><span class="column">
<?php echo $overview[0]->subject; ?> - <?php echo $partialMessage; ?>
</span><span class="date">
<?php echo $date; ?>
</span></td>
</tr>
<?php
} // End foreach
?>
</table>
<?php
} // end if

imap_close($connection);
}
?>
</div>