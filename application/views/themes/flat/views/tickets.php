<style>
.bg-closed {background-color: #008080;}
.border-closed {background-color: #008080;}
.alert-color {
	background-color: #f4422d; !important; 
	border:1px solid #f4492d; !important ;
}
.widget-flat {border: #008080;}
.badge-success {
	background-color: #f2556b;
}
</style>
<?php //print_r($ticket_statuses);exit(); ?>
<div class="wrapper">
	<div class="container-fluid">

		<!-- Page-Title -->
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="btn-group pull-right">
						<ol class="breadcrumb hide-phone p-0 m-0">
							<li class="breadcrumb-item " ><a href="<?php base_url();?>profile_new"><?php echo $contact->firstname; echo " "; echo $contact->lastname?></a></li>
							<li class="breadcrumb-item active" >Support</li>
						</ol>
					</div>
					<?php if(empty($ticket_statuses))
					{?>
						<h4>Tickets Statuses are not created by the Adminstration</h4>
					<?php	} ?>
					<h4 class="page-title">Tickets</h4>

				</div>
			</div>
		</div>
		<!-- end page title end breadcrumb -->
		<div class="row">
			<div class="col-12">
				<div class="card-box">
					<h4 class="header-title">Manage Tickets</h4> 
					<a href="<?php echo site_url('clients/open_ticket'); ?>" class="btn btn-primary waves-light waves-effect alert-color">Request Support</a>
					<div class="text-center mt-4 mb-4">
						<div class="row">
								<div class="col-xs-6 col-sm-3">
								<!--<a href="javascript:void(0)" class="getticketclick" data-id="2" >
									<div class="card-box bg-primary widget-flat border-primary text-white" style= "background-color: <?php// echo $ticket_statuses[1]['statuscolor'];?> !important;" >
										<?php //$where_tickets1 = array('contactid'=>get_contact_user_id(),'status'=>$ticket_statuses[1]['ticketstatusid']);?>
										<?php //$where_tickets3 = array('contactid'=>get_contact_user_id(),'status'=>$ticket_statuses[2]['ticketstatusid']);?>

										<i class="fi-archive"></i>
										<?php 
										// $onhold		= total_rows_avis('tbltickets',$where_tickets3);
										// $inprogress 	= total_rows_avis('tbltickets',$where_tickets1);
										// $com			= $onhold + $inprogress;
										?>
										<h3 class="m-b-10"><?php //echo $com ;?></h3>
										<p class="text-uppercase m-b-5 font-13 font-600"><?php //echo "Tickets ".$ticket_statuses[1]['name']?></p>
									</div>
								 </a>-->	
							</div> 
							<div class="col-xs-6 col-sm-3">
								<a href="javascript:void(0)" class="getticketclick" data-id="2" >
									<div class="card-box widget-flat border-danger bg-danger text-white" style= "background-color: <?php echo $ticket_statuses[0]['statuscolor'];?> !important;">
										<?php 
											 $where_tickets0 = array('contactid'=>get_contact_user_id(),'status'=>$ticket_statuses[0]['ticketstatusid']);
											 $where_tickets1 = array('contactid'=>get_contact_user_id(),'status'=>$ticket_statuses[1]['ticketstatusid']);
											 $where_tickets3 = array('contactid'=>get_contact_user_id(),'status'=>$ticket_statuses[2]['ticketstatusid']);
										?>

										<i class="fi-tag"></i>
										<!-- sajid code start -->
										<?php
										$compsite = 0;
										$open_t		= total_rows_avis('tbltickets',$where_tickets0);
										$onhold		= total_rows_avis('tbltickets',$where_tickets3);
										$inprogress 	= total_rows_avis('tbltickets',$where_tickets1);
										//if($open_t){
											$compsite			= $open_t + $onhold +$inprogress;	
										//}
										?>
										<h3 class="m-b-10"><?php echo $compsite ;?></h3>
										<!-- sajid code end -->
										<p class="text-uppercase m-b-5 font-13 font-600"><?php echo $ticket_statuses[0]['name']. " ". "Tickets";?></p>
									</div>
								</a>
							</div>
 						
							<div class="col-xs-6 col-sm-3">
								<a href="javascript:void(0)" class="getticketclick" data-id="4" >
									<div class="card-box widget-flat border-success bg-success text-white"style= "background-color:<?php echo $ticket_statuses[4]['statuscolor'];?> !important;" >
										<?php 
											 $where_tickets2 = array('contactid'=>get_contact_user_id(),'status'=>$ticket_statuses[3]['ticketstatusid']);
											 $where_tickets4 = array('contactid'=>get_contact_user_id(),'status'=>$ticket_statuses[4]['ticketstatusid']);
										?>
										<!-- <i class="fi-help"></i> -->
										<i class="fi-delete"></i>
										<?php
										$compsite = 0;
										$closed		= total_rows_avis('tbltickets',$where_tickets4);
										$resolved_t		= total_rows_avis('tbltickets',$where_tickets2);
										//if($resolved_t){
											$compsite			= $resolved_t + $closed;	
										//}
										?>
										<h3 class="m-b-10"><?php echo $compsite ;?></h3>
										<p class="text-uppercase m-b-5 font-13 font-600"><?php echo $ticket_statuses[4]['name']." " ."Tickets"?></p>
										<!-- <p class="text-uppercase m-b-5 font-13 font-600"><?php //echo "Resolved Tickets " ; ?></p> -->
									</div>
								</a>
							</div>
							<div class="col-xs-6 col-sm-3">
								<!-- <a href="javascript:void(0)" class="getticketclick" data-id="5" >
									<div class="card-box bg-closed widget-flat border-closed text-white"style= "background-color:<?php //echo $ticket_statuses[4]['statuscolor'];?> !important;" >
										<?php //$where_tickets4 = array('contactid'=>get_contact_user_id(),'status'=>$ticket_statuses[4]['ticketstatusid']);?>
										<?php
										// $compsite = 0;
										// $closed_t		= total_rows_avis('tbltickets',$where_tickets4);
										// if($closed_t){
										// 	$compsite			= $closed_t ;	
										// }
										?>
										<h3 class="m-b-10"><?php //echo $compsite ;?></h3>
										<p class="text-uppercase m-b-5 font-13 font-600"><?php //echo $ticket_statuses[4]['name']." " ."Tickets"?></p>
									</div>
								</a> -->
							</div>
						</div>
					</div>
					<?php get_template_part('tickets_table'); ?>	
				</div>
			</div>
		</div>		
		<!-- end row -->

	</div> <!-- end container -->
</div>
<script type="text/javascript">
function show_ticket_status(ticket_id){

		$.ajax({
			type:'POST',
			url:'<?php echo base_url(); ?>clients/tickets_history/'+ticket_id,
			success:function(data){
          $('#ticket_history_status_').modal('show');
			//alert('inn');
			//	alert(data);
				$('#show_history_data').html(data);
			}
			});
	}				
</script>
<!-- end wrapper -->
<script>
	$('body').on('click', '.getticketclick', function(){
		
		var id = $(this).attr('data-id'); 
		$.ajax({
			type:'POST',
			url:'<?php echo base_url(); ?>clients/gettickets',
			data:{'id':id},
			success:function(data){
				if(data){
					$('#tickesbystatus').empty();
					$('#tickesbystatus').append(data);
				}

			}
		});
	});
</script>