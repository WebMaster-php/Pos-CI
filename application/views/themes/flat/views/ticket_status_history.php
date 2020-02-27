<style>
.modal_ticket_status
{
	padding: 0px !important;
	   padding-bottom: 16px !important;
	    margin-bottom:5px !important
}
.modal_ticket_status h3
{
	padding: 0px !important;
	margin: 0px !important;
	font-size: 21px !important; 
}
.pdg_ticket_body
{
	padding-top:10px;
}
.subheading_body
{
  font-size: 16px;
}
</style>
<div class="modal-header modal_ticket_status">
<?php 
 //echo "<pre>"; print_r($tickets); exit('tickets'); ?>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h3><?php echo _l('Ticket #').$tickets['ticketid'].' '.'|'.' '.$tickets['subject'].' '.'|'.' '. date('m/d/Y h:i A' ,strtotime($tickets['date']));?></h3>
<h4 class="modal-title" id="myModalLabel"></h4>
 

</div>

	<?php if(isset($ticket_body['message']) && $ticket_body['notify_ticket_body'] ==1){?>
		<div class="pdg_ticket_body"> <p><?php echo $ticket_body['message']?></p> </div>
		<hr/>
	<?php }?>

<?php 
//$t=time();
//echo($t . "<br>");
//echo(date("Y-m-d H:i A",$t));
//exit('inn');
// echo "<pre>"; print_r($ticket_body); exit('tickets');
if($tickets_history){ 
	foreach($tickets_history as $tickets_status_history){?>
	<h3 class="subheading_body"><?php 
		foreach ($staff as $value) {
			if($value['staffid'] == $tickets_status_history['user_id']){
			echo $value['firstname'].' ';
			}
		}
 ?> Status changed to <?php foreach($statuses as $stat){ 
		if($stat['ticketstatusid']==$tickets_status_history['ticket_status']){
			echo $stat['name'];
		} 
	}?>
	<?php echo ' '.'on'.' '. date('m/d/Y h:i A' ,strtotime($tickets_status_history['created_at']))?></h3>
	<p><?php
	if($tickets_status_history['email_sended'] == 1 ){ 
		if($tickets_status_history['ticket_description'] == '')
		
		echo $ticket_body['message'];
		
		else{
			echo $tickets_status_history['ticket_description'];
		}
	}?>
	</p>
	<hr/>
<?php }
}
else{?>
	
		<h3 class="subheading_body">
			<?php 
				// if($tickets['firstname'] != '' && $tickets['lastname'] != '' ){
				// 	echo $tickets['firstname'] .' '.$tickets['lastname'] .' has added an internal note ' . date('m/d/Y h:i A' ,strtotime($tickets['date']));
				// }
				// else{
					echo ' Ticket opened on ' . date('m/d/Y h:i A' ,strtotime($tickets['date']));	
				// }
				?>
				
		</h3>
		<hr/>

<?php }?>