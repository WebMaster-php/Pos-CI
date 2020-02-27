<?php
$message = "
<html>
<head>
<title>Notification Email</title>
</head>
<body>
<p>Hi '".$firstname."' '".$lastname."',</p>
<p>An inventory item has been marked as '".$status."' to the Account '".$firstname."' on '".$date."'.</p>
<p>The inventory item is: '".$account_id."': '".$account_name."', Serial number: '".$serial_number."'.</p>
<p>You can view the inventory item with the following link: '".$account_id."': '".$account_name."' </p>
<p>Kind Regards,</p>
<p>VersiPOS Team</p>
</body>
</html>
";
?>