<?php init_head(); ?>
<style>
.vertically {
	width: 20px;
	word-break: break-all !important;
	background-color: #507ac3;
	color: #ffffff;
	margin-top: 125px;
	margin-right: -20px;
	padding: 7px;
	float: right;
	clear: both;
	font-weight: 500;
	z-index: 9999999;
}
#myDiv {
	position: absolute;
	z-index: 9;
	background-color: #f1f1f1;	
	border: 1px solid #d3d3d3;
}
#myDivheader {
	cursor: move;
	z-index: 10;
}
.datastyle {
	font-size: 80%;
	color: #89867D;
}
#his thead th {
	border: 1px solid #dcdcdc !important;
	background-color: #F2F3F3 !important;
}
#his tbody td {
	border: 1px solid #dcdcdc !important;
}
#heading {
	padding-top: 10px !important;
	background: #F8F8F8 !important;
}
</style>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <?php if(isset($client) && $client->active == 0){ ?>
            <div class="alert alert-warning">
               <?php echo _l('customer_inactive_message'); ?>
               <br />
               <a href="<?php echo admin_url('clients/mark_as_active/'.$client->userid); ?>"><?php echo _l('mark_as_active'); ?></a>
            </div>
            <?php } ?>
            <?php 
			 //echo "<pre>"; print_r($client);
			 if(isset($client) && $client->leadid != NULL){ ?>
            <div class="alert alert-info">
               <a href="#" onclick="init_lead(<?php echo $client->leadid; ?>); return false;"><?php echo _l('customer_from_lead',_l('lead')); ?></a>
            </div>
            <?php } ?>
            <?php if(isset($client) && (!has_permission('customers','','view') && is_customer_admin($client->userid))){?>
            <div class="alert alert-info">
               <?php echo _l('customer_admin_login_as_client_message',get_staff_full_name(get_staff_user_id())); ?>
            </div>
            <?php } ?>
			<?php //echo $tabname = $this->uri->segment('group');?>
         </div>
		 <?php // button notes and activity start?>
		 <div class="">
			<a style="margin-top:200px; cursor:pointer; position:absolute; right:25px;" class="vertically" value="none" onclick="myFunction();">NOTES</a>
			<a style="margin-top:307px; cursor:pointer; position:absolute; right:25px; "class="vertically" value="none" onclick="myActivity();">ACTIVITY</a>
		 </div>
		<?php // button notes and activity start?>
		<?php// here code of notes + activity ?>               
			<div id="myDiv" class='draggable' style="display:none; position:absolute; left:70%;  top:100px;">
			  <div class="row">
				<div id ="myDivheader" class= "col-md-12">
				  <div class="modal-content" style="border: 3px solid #D2E0F4;">
					<div class="modal-header" style="padding: 5px 10px; background: linear-gradient(to bottom, #dbe8f9 0%, #c7d7ee 100%);">
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick= "myFunction()"><span aria-hidden="true">×</span></button>
					  <h4 class="modal-title" id="myModalLabel"> <span class="add-title"><i style="color:#5A9F43;" class="fa fa-file-text" aria-hidden="true"></i> <?php echo _l('add_notes'); ?></span> </h4>
					</div>
					<?php $company_id = $this->uri->segment(4);?>
					<?php $tab = $_SERVER['QUERY_STRING'];
						  $tab = explode("=",$tab);
						  $tabname = $tab[1];
						  if($tabname == ''){ $tabname = "profile" ;}?>
					<form enctype="multipart/form-data" id="formPostnote" method="post">
					  <input type="hidden" class ="company_id" name="company_id" value="<?php echo $company_id?>">
					  <input type="hidden" class = "tabname" name="tabname" value="<?php echo $tabname?>">
					  <div class="modal-body" style="padding:5px 0px; height:320px; overflow-y: scroll;">
						<div class="col-md-12">
						  <textarea style="background: linear-gradient(to right, #feffe0 0%, #fafbaf 100%);" id="notes" name="notes" class="form-control" rows="4"></textarea>
						</div>
						<div class="clear10" style="height:10px; clear:both;"></div>
						<div style="text-align: center;">
						  <button style="width:70px;" type="button" onclick="form_submit(this.form.id)" id="btnSubmit" class="btn btn-info"><?php echo _l('submit'); ?></button>
						  <button style="width:70px;" type="button" class="btn btn-default" data-dismiss="modal" onclick="myFunction()"><?php echo _l('cancel'); ?></button>
						</div>
						<div class="clearfix"></div>
						<div class="col-sm-12" style="padding: 0px; margin-top: 5px;">
						  <div class="alert alert-success" id="success_alert" style="display:none;">Note added successfuly </div>
						  <div class="alert alert-danger" id="danger_alert" style="display:none;">Please add ssome note first </div>
						  <table  class="table no-margin project-overview-table">
							<tbody id="not" style="background: linear-gradient(to right, #feffe0 0%, #fafbaf 100%);">
							</tbody>
						  </table>
						</div>
					  </div>
					</form>
				  </div>
				</div>
			 </div>
			 </div>
			<?php// end of content of "notes"?>
			<?php// start of content of "Activity"?>
			 <div id="activity" style="cursor: move; display:none; position: absolute; z-index: 9999999; top: 100px; left:30%; " >
				  <div class="row">
					<div class= "col-md-10 col-centered ">
					  <div   class="modal-content">
						<div id = "heading" class="modal-header" style="width: 420px; height: 320px;">
						  <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick = "myActivity()"><span aria-hidden="true">×</span></button>
						  <h4 class="modal-title" id="myModalLabel"> <span class="add-title-1"><?php //echo _l('history'); ?>History</span> </h4>
						  <div class="modal-body" style="padding:5px 0px; height:251px; overflow-y: scroll;">
							<table id="his" class="table no-margin project-overview-table">
							  <thead>
							  <th>Date</th>
								<th>User</th>
								<th>Action</th>
								  </thead>
							  <tbody >
							  </tbody>
							</table>
						  </div>
						</div>
					  </div>
					</div>
				  </div>
				</div>
			<?php// start of content of "Activity"?>				  
		 <?php if($group == 'profile'){ ?>
         <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <button class="btn btn-info only-save customer-form-submiter">
               <?php echo _l( 'submit'); ?>
            </button>
            <?php if(!isset($client)){ ?>
            <button class="btn btn-info save-and-add-contact customer-form-submiter">
               <?php echo _l( 'save_customer_and_add_contact'); ?>
            </button>
            <?php } ?>
         </div>
         <?php } ?>
         <?php if(isset($client)){ ?>
         <div class="col-md-3">
            <div class="panel_s">
               <div class="panel-body customer-profile-tabs">
                  <h4 class="customer-heading-profile bold">
                       <?php if(has_permission('customers','','delete') || is_admin()){ ?>
                  <div class="btn-group pull-left mright10">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                     </a>
                     <ul class="dropdown-menu dropdown-menu-left">
                        <?php if(is_admin()){ ?>
                        <li>
                           <a href="<?php echo admin_url('clients/login_as_client/'.$client->userid); ?>" target="_blank">
                              <i class="fa fa-share-square-o"></i> <?php echo _l('login_as_client'); ?>
                           </a>
                        </li>
                        <?php } ?>
                        <?php if(has_permission('customers','','delete')){ ?>
                        <li>
                           <a href="<?php echo admin_url('clients/delete/'.$client->userid); ?>" class="text-danger delete-text _delete"><i class="fa fa-remove"></i> <?php echo _l('delete'); ?>
                           </a>
                        </li>
                        <?php } ?>
                     </ul>
                  </div>
                  <?php } ?>
				  			<?php// start of content of "Activity"?>
			
				 
			
				<?php// start of content of "Activity"?>
                  #<?php echo $client->userid . ' ' . $title; ?></h4>
                  <?php $this->load->view('admin/clients/tabs'); ?>

               </div>
            </div>
         </div>
         <?php } ?>
		 
         <div class="col-md-<?php if(isset($client)){echo 9;} else {echo 12;} ?>">
            <div class="panel_s">
               <div class="panel-body">
                  <?php if(isset($client)){ ?>
                  <?php echo form_hidden( 'isedit'); ?>
                  <?php echo form_hidden( 'userid',$client->userid); ?>
                  <div class="clearfix"></div>
                  <?php } ?>
                  <div>
                     <div class="tab-content">
                        <?php $this->load->view('admin/clients/groups/'.$group); ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php if($group == 'profile'){ ?>
      <div class="btn-bottom-pusher"></div>
      <?php } ?>
	 
	 <?php 
          $this->load->view('admin/inventory/add_inventory_pro');
      ?>
   </div>

<?php init_tail(); ?>
<link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">

<script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<?php if(isset($client)){ ?>
<script>
   $(function(){
      // init_rel_tasks_table(<?php //echo $client->userid; ?>,'customer');
      init_rel_tasks_table(<?php echo $client->userid; ?>,'all');
   });
</script>
<script>
 	$( window ).load(function(){
	  $('#ts_rel_to_project').prop('checked', true);
	  $('#ts_rel_to_invoice').prop('checked', true);
	  $('#ts_rel_to_estimate').prop('checked', true);
	  $('#ts_rel_to_contract').prop('checked', true);
	  $('#ts_rel_to_ticket').prop('checked', true);
	  $('#ts_rel_to_expense').prop('checked', true);
	  $('#ts_rel_to_proposal').prop('checked', true);
	  
	});
 </script>
<?php } ?>
<?php if(!empty($google_api_key) && !empty($client->latitude) && !empty($client->longitude)){ ?>
<script>
   var latitude = '<?php echo $client->latitude; ?>';
   var longitude = '<?php echo $client->longitude; ?>';
   var mapMarkerTitle = '<?php echo $client->company; ?>';
</script>
<?php echo app_script('assets/js','map.js'); ?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key; ?>&callback=initMap"></script>
<?php } ?>
<?php $this->load->view('admin/clients/client_js'); ?>
</body>
</html>
<style>
.clearCSS {
	opacity: 0 !important;
}
.clearCSS2 {
	width: 104% !important;
}
</style>
<script>

 $( document ).ready(function() {
   
    $(".input-group-addon").first().addClass("clearCSS");
    $(".input-group").first().addClass("clearCSS2");
}); 

	//  $('.customer-form-submiter').on('click',function(){
	//  	 // $(".filter-option").first().text();
	//  	 console.log($(".pull-left").text());
	// });

 // zee code start 
	function myFunction() 
	{
		$("#myDiv").toggle();
		load_notes('<?php echo $this->uri->segment(4);?>','<?php echo $tabname;?>');	
	}
	function load_notes( company_id, tabname )
	{	
		$.ajax({
				type:'POST',
				url:'<?php echo base_url(); ?>admin/clients/get_notes',
				data:{'company_id':company_id, tabname:tabname },
				 success:function(data)
				 {	
					 $("tbody#not").html(data);
				 }	
		});
	}
	
	function form_submit(e)
	{
		if($("#notes").val()=='')
			{
				$("#notes").css('border','1px solid red');
				$("#danger_alert").show();	
				$("#danger_alert").fadeOut(3000);
			}
		else
			{			
				var currentdate = new Date();
				var datetime =                 
						+ (currentdate.getMonth()+1)  + "/"                
						+ currentdate.getDate() + "/"                
						+ currentdate.getFullYear() + " "                  
						+ currentdate.getHours() + ":"                  
						+ currentdate.getMinutes() + ":"                 
						+ currentdate.getSeconds();
				var form = $('form#'+e);
				var formdata = false;
				var notes = $("#notes").val();
				var company_id = $(".company_id").val();
				var tabname = $(".tabname").val();	
				$.ajax({
						type:'post',
						url:'<?php echo base_url(); ?>admin/Clients/add_notes',
						data:{datetime:datetime, notes:notes,  company_id:company_id, tabname:tabname },
						success:function(data) 
							{
								load_notes(company_id, tabname);
								$("#notes").val('');
								$("#success_alert").show();	
								$("#success_alert").fadeOut(3000);
							}
					  });
			}
	}
	$(function() {
	$( "#myDiv" ).draggable();
	$( "#activity" ).draggable();
 });
// zee code end	11 july
// zee code start 12 july 
	function myActivity()
	{
		$('#activity').toggle();
		load_history('<?php echo $this->uri->segment(4);?>','<?php echo $tabname;?>');	
	  
	}
	function load_history( company_id, tabname)
	{	
		$.ajax({
				type 	:"post",
				url 	:'<?php echo base_url();?>admin/clients/get_history',
				data	:{company_id:company_id, tabname:tabname},
				success:function(data)
					{
						$("table#his ").html(data);
					}
		});
	}
	

// zee code end 12 july




	// $('.save-and-add-contact').on('click',function(){
 //    alert('#services_in'+'['+']'+'-error');
 //    console.log('#services_in'+'['+']'+'-error');
 //    console.log('.select-services_in'+'['+']');
 //    console.log($('.select-services_in'+'['+']').find('#services_in'+'['+']'+'-error').text());
 //    	//find('#services_in[]-error').html());      
 //    // console.log($(this).html());
    
 //  });  
</script>
