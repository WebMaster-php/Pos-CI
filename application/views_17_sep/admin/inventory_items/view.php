<?php init_head(); ?>
<style>
#ribbon_project_<?php echo $project->id;
?> span::before {
border-top: 3px solid <?php echo $project_status['color'];
?>;
border-left: 3px solid <?php echo $project_status['color'];
?>;
}
center {
	padding-left: 80px;
}
.datestyle {
	font-size: 80%;
	color: #D7D7CF;
}
.vertically {
	width: 20px;
	word-break: break-all !important;
	background-color: #ee892b;
	color: #ffffff;
	margin-top: 125px;
	margin-right: -20px;
	padding: 7px;
	float: right;
	clear: both;
	font-weight: 500;
}
;
#myDiv {
	position: absolute;
	z-index: 9;
	background-color: #f1f1f1;
	text-align: center;
	border: 1px solid #d3d3d3;
}
#myDivheader {
	cursor: move;
	z-index: 10;
}
#his thead th {
	border: 1px solid #dcdcdc !important;
	background-color: #F2F3F3 !important;
}
.datastyle {
	font-size: 80%;
	color: #89867D;
}
#his tbody td {
	border: 1px solid #dcdcdc !important;
}
#heading {
	padding-top: 10px !important;
	background: #F8F8F8 !important;
}
</style>
<?php $this->load->view('admin/inventory_items/add_inventory_pro'); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s project-top-panel panel-full">
          <div class="panel-body _buttons">
            <div class="row">
              <div class="col-md-8 project-heading">
                <div id="project_view_name">
                  <div class="btn-group bootstrap-select fit-width">
                    <button type="button" class="btn-default" data-toggle=" " role="button" data-id="project_top" title="Visible all Tabs - Inventory Items "> <span class="filter-option pull-left">Inventory Item -
                    <?php  echo $inventory[0]['serial_number']?>
                    </span>&nbsp; </button>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-right"> <a href="#" class="btn btn-info" onclick="edit_inventory_clone(<?php echo $inventory[0]['inventory_id']; ?>)" data-toggle="modal" data-target="#sales_inventory_modal"><?php echo _l('clone'); ?></a>
                <?php if (has_permission('inventory_items', '', 'edit')) {?>
                <a href="#" class="btn btn-info" onclick="edit_inventory(<?php echo $inventory[0]['inventory_id']; ?>)" data-toggle="modal" data-target="#sales_inventory_modal"><?php echo _l('edit'); ?></a>
                <?php } if (has_permission('inventory_items', '', 'delete')) {?>
                <a href="<?php echo base_url('admin/inventory/delete/'.$inventory[0]['inventory_id'])?>" class="invoice-project btn btn-danger" onclick="return confirm('Are you sure you want to delete this inventory?')";>Delete</a>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
        <div class="panel_s project-menu-panel">
          <div class="panel-body">
            <ul class="nav nav-tabs no-margin project-tabs" role="tablist">
              <li class="active project_tab_project_overview"> <a data-group="project_overview" href="javascript:0;" role="tab"> <i class="fa fa-th" aria-hidden="true"></i> Inventory Overview </a> </li>
            </ul>
          </div>
        </div>
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-6 border-right project-overview-left">
                <div class="row">
                  <div class="col-md-12">
                    <p class="project-info bold font-size-14">Overview</p>
                  </div>
                  <div class="col-md-7">
                    <table class="table no-margin project-overview-table">
                      <tbody>
                        <tr class="project-overview-customer">
                          <td class="bold">Account</td>
                          <td><a href="<?php echo base_url('admin/clients/client/'.$inventory[0]['account']);?>"><?php echo $inventory[0]['company']?></a></td>
                        </tr>
                        <tr class="project-overview-billing">
                          <td class="bold">Type Of Hardware</td>
                          <td><?php  echo $inventory[0]['hardwarevalue'];?></td>
                        </tr>
                        <tr>
                          <td class="bold">Date In</td>
                          <td><?php  echo date('m-d-Y', strtotime($inventory[0]['date_in']));?></td>
                        </tr>
                        <tr class="project-overview-status">
                          <td class="bold">Status</td>
                          <td><?php  echo $inventory[0]['statusvalue'];?></td>
                        </tr>
                        <tr class="project-overview-date-created">
                          <td class="bold">Origin</td>
                          <td><?php  echo $inventory[0]['originvalue'];?></td>
                        </tr>
                        <tr class="project-overview-start-date">
                          <td class="bold">Who own's this Equipment?</td>
                          <td><?php  echo $inventory[0]['ownervalue'];?></td>
                        </tr>
                        <tr class="project-overview-estimated-hours">
                          <td class="bold">Manufacturer</td>
                          <td><?php  echo $inventory[0]['manufacturer'];?></td>
                        </tr>
                        <tr class="project-overview-total-logged-hours">
							  <td class="bold">Warranty - Expiration date</td>
							  <td>
									<?php 
									if ($inventory[0]['exp_date'] == 'No Warranty')
										{
											echo $inventory[0]['exp_date']; 
										}
									else 
										{
											echo date('m-d-Y', strtotime($inventory[0]['exp_date']));
										}
									?>	
							  </td>
                        </tr>
						
						<?php if(total_rows('tblcustomfields',array('fieldto'=>'inventory','active'=>1)) > 0 ){ 
						$custom_fields = get_custom_fields('inventory');						
						foreach ($custom_fields as $field) {
							$value = get_custom_field_value($inventory[0]['inventory_id'], $field['id'], $field['fieldto']);
							if($field['type'] != 'textarea'){
							?>
							<tr class="project-overview-total-logged-hours">
								<td class="bold">
									<?php echo $field['name']; ?>
								</td>
								<td>
									<?php  echo $value ?>
								</td>
							</tr>
							<?php } } ?>
						<?php } ?>
						
                      </tbody>
                    </table>
                  </div>
                  <div class="col-md-5 text-center project-percent-col mtop10">
                    <p class="bold">Serial Number -
                      <?php  echo $inventory[0]['serial_number']?>
                    </p>
                    <div class="project-progress relative mtop15" data-value="1" data-size="150" data-thickness="22" data-reverse="true"> <img style="max-height:200px" class="img-responsive" src="<?php echo $inventory[0]['image'];?>" > </div>
                  </div>
                </div>
                <div class="tc-content project-overview-description">
                  <hr class="hr-panel-heading project-area-separation">
                  <p class="bold font-size-14 project-info">Description</p>
                  <p>
                    <?php  echo $inventory[0]['description']?>
                  </p>
                </div>
				
				<?php if(total_rows('tblcustomfields',array('fieldto'=>'inventory','active'=>1)) > 0 ){ 
						$custom_fields = get_custom_fields('inventory');
						foreach ($custom_fields as $field) {
							$value = get_custom_field_value($inventory[0]['inventory_id'], $field['id'], $field['fieldto']);
							if($field['type'] == 'textarea'){
							?>
							<div class="tc-content project-overview-description">
							  <hr class="hr-panel-heading project-area-separation">
							  <p class="bold font-size-14 project-info"><?php echo trim($field['name']); ?></p>
							  <p>
								<?php  echo $value ?>
							  </p>
							</div>
					<?php } } ?>
				<?php } ?>
				
                <div class="team-members project-overview-team-members">
                  <hr class="hr-panel-heading project-area-separation">
                  <div class="clearfix"></div>
                  <div class="media">
                    <div class="media-body">
                      <h5 class="media-heading mtop5">
                        <p class="bold">CREATED BY <a href="<?php echo base_url('admin/profile/'.$inventory[0]['user_id'].'')?>"><?php echo strtoupper($inventory[0]['firstname']).' '.strtoupper($inventory[0]['lastname']); ?></a> ON <?php echo date('m/d/Y', strtotime($inventory[0]['datecreated'])); ?> </p>
                        <br>
                        <?php if($lastupdate[0]['user_id']){?>
                        <p class="bold"> LAST MODIFIED BY <a href="<?php echo base_url('admin/profile/'.$lastupdate[0]['user_id'].'')?>"><?php echo strtoupper($lastupdate[0]['firstname']).' '.strtoupper($lastupdate[0]['lastname']); ?></a> ON <?php echo date('m/d/Y', strtotime($lastupdate[0]['date_in']));?> </p>
                        <?php }?>
                        </p>
                      </h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 project-overview-right">
                <div class ="row">
                  <div class ="col-md-11">
                    <?php// this div containing content of "notes"?>
                    <div id="myDiv" class='draggable' style="display:none;">
                      <div class="row">
                        <div id ="myDivheader" class= "col-md-8 col-centered ">
                          <div class="modal-content" style="border: 3px solid #D2E0F4;">
                            <div class="modal-header" style="padding: 5px 10px; background: linear-gradient(to bottom, #dbe8f9 0%, #c7d7ee 100%);">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick= "myFunction()"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title" id="myModalLabel"> <span class="add-title"><i style="color:#5A9F43;" class="fa fa-file-text" aria-hidden="true"></i> <?php echo _l('add_notes'); ?></span> </h4>
                            </div>
                            <?php $inventory_id	 = $inventory[0]["inventory_id"];?>
                            <form enctype="multipart/form-data" id="formPostnote" method="post">
                              <input type="hidden" name="inventory_id" value="<?php echo $inventory_id?>">
                              <input type="hidden" name="inventory_id_id" id="inventory_id_id" value="<?php echo $inventory_id?>">
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
                      <?php// end of content of "notes"?>
                    </div>
                    <?php  //// for activity button starts?>
                    <div id="activity" style="cursor: move; display:none; margin-top: 20px; " >
                      <div class="row">
                        <div class= "col-md-12 col-centered ">
                          <div   class="modal-content">
                            <div id = "heading" class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick = "myFunction1(<?php echo $inventory[0]['inventory_id']; ?>)"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title" id="myModalLabel"> <span class="add-title-1"><?php echo _l('inventory_history'); ?></span> </h4>
                              <div class="modal-body" style="padding:5px 0px; height:200px; overflow-y: scroll;">
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
                    <?php //// activity button code end ?>
                  </div>
                  <div class ="col-md-1"> <a style="cursor:pointer;" class="vertically" value="none" onclick="myFunction()" class="invoice-project">NOTES</a> <a  style="margin-top:2px; cursor:pointer;"class="vertically" value="none" onclick="myFunction1(<?php echo $inventory[0]['inventory_id']; ?>)" class="invoice-project">ACTIVITY</a> </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
<script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script> 
<script>
var values ='';
var validation_input, validation_textarea, validation_select = 0;
	 var validation_input, validation_textarea, validation_select = 0;
	function validate_form(){
		var required_field = '';
		$("input[type=text]").each(function(){
			if($(this).attr('data-fieldto') == 'inventory')
			{
				$(this).next().remove();
				required_field = $(this).attr('data-custom-field-required');
				if($(this).val() == ''){
					if(required_field){
						$(this).parent().addClass('has-error');
						$(this).after('<p id="'+ $(this).attr('id') +'" class="text-danger">This field is required.</p>');
						validation_input = 1;
					}
				}else{
					validation_input = 0;
					$(this).parent().removeClass('has-error');
					$(this).next().remove();
				}			
			}
		});
		
		$(".chk").each(function(key, value){
			var x = 0;
			var req_field_flg = 0;
			$(value).next().find(".errorMessage").remove();
			var chkId = $(value).find('.custom_field_checkbox');
			console.log($(chkId));
			var csChk = $(chkId).attr('data-fieldid');
			$("input[data-fieldid="+csChk+"]").each(function(key, val){
				required_field = $(this).attr('data-custom-field-required');
				if(required_field == 1){
					req_field_flg = 1;
					if($(this).is(':checked')){
						x = x+1;
					}
				}
			});
			if(x < 1 && req_field_flg == 1){
				x = 0;
				required_field = '';
				validation_input = 1;
				$("input[data-fieldid="+csChk+"]").parent().parent().addClass('has-error');
				$("input[data-fieldid="+csChk+"]").parent().parent().after('<p id="'+ $(this).attr('id') +'" class="text-danger errorMessage">This field is required.</p>');
				req_field_flg = 0;
			 }else{
				x = 0;
				required_field = '';
				validation_input = 0;
				req_field_flg = '';
				console.log('Hi');
				$("input[data-fieldid="+csChk+"]").parent().parent().removeClass('has-error');
				$("input[data-fieldid="+csChk+"]").parent().siblings().find(".errorMessage").remove();
			}
		});
		
		$("textarea").each(function() {
			if ($(this).attr('data-custom-field-required'))
			{
				$(this).next().remove();
				if($(this).val() == ''){
					$(this).parent().addClass('has-error');
					$(this).after('<p id="'+ $(this).attr('id') +'" class="text-danger">This field is required.</p>');
					validation_textarea = 1;
				}else{
					validation_textarea = 0;
					$(this).parent().removeClass('has-error');
					$(this).next().remove();
				}	
			}
		});
		
		$("select").each(function() {
			if ($(this).attr('data-custom-field-required'))
			{
				$(this).next().remove();
				if($(this).change().val() == ''){
					$(this).parent().parent().addClass('has-error');
					$(this).after('<p id="'+ $(this).attr('id') +'" class="text-danger">This field is required.</p>');
					validation_select = 1;
				}else{
					validation_select = 0;
					$(this).parent().parent().removeClass('has-error');
					$(this).next().remove();
				}
			}
		});
		return false;
	}

function add_validate_form(){
	var required_field = '';
	$("input[type=text]").each(function(){
		if($(this).attr('data-fieldto') == 'inventory')
		{
			required_field = $(this).attr('data-custom-field-required');
			if(required_field){
				$(this).prev().prepend('<small class="req text-danger">* </small>');
			}				
		}
	});
	
	$("input[type=checkbox]").each(function(){
		if($(this).attr('data-fieldto') == 'inventory')
		{
			required_field = $(this).attr('data-custom-field-required');
			if(required_field){
				$(this).parent().prev().prepend('<small class="req text-danger">* </small>');
			}				
		}
	});
	
	
	$( "select" ).each(function() {
		if ($(this).attr('data-custom-field-required'))
		{
			$(this).parent().prev().prepend('<small class="req text-danger">* </small>');
		}
	});
	
	$( "textarea" ).each(function() {
		if ($(this).attr('data-custom-field-required'))
		{
			$(this).prev().prepend('<small class="req text-danger">* </small>');
		}
	});
	
}

function load_notes(inventory_id)
{
    $.ajax({
            type:'POST',
            url:'<?php echo base_url(); ?>admin/Inventory_items/get_notes',
            data:{'inventory_id':inventory_id},
            success:function(data){
                    $("tbody#not").html(data);
            }	
    });
}
 $(function() {
	$( "#myDiv" ).draggable();
	$( "#activity" ).draggable();
 });
 
function form_submit(e)
{
if($("#notes").val()=='')
{
	$("#notes").css('border','1px solid red');
	$("#danger_alert").show();	
	$("#danger_alert").fadeOut(3000);
}else
{
	var currentdate = new Date(); 
	var datetime =                 
			+ (currentdate.getMonth()+1)  + "/"                
			+ currentdate.getDate() + "/"                
			+ currentdate.getFullYear() + " "                  
			+ currentdate.getHours() + ":"                  
			+ currentdate.getMinutes() + ":"                 
			+ currentdate.getSeconds();
	//alert(datetime);
	var form = $('form#'+e);
	var formdata = false;
	var notes = $("#notes").val();
	var inventory_id = $("#inventory_id_id").val();
	$.ajax({type:'post',
	url:'<?php echo base_url(); ?>admin/Inventory_items/add_notes',
	//data:new FormData(form[0]),
	data:{notes:notes, datetime:datetime, inventory_id:inventory_id},
	success:function (data) {
        load_notes(inventory_id);
	$("#notes").val('');
	$("#success_alert").show();	
	$("#success_alert").fadeOut(3000);
	}
	});
}
}

function myFunction() {
	$("#myDiv").toggle();
	load_notes(<?php  echo $inventory[0]['inventory_id']; ?>);
}


function myFunction1(e) {
	$.ajax({
	type:'post',
	url:'<?php echo base_url(); ?>admin/inventory_items/inventory_history',
	data:{inventory_id:e},
	success:function (data) {
		$("table#his ").html(data);
		}
	});
	$("#activity").toggle();
	//$("#myDIV").hide();
}

function umer(a)
    {
        if(a==10)
        {
            $("#exp_no").val('');
            $("#exp_type").val(1);
            $("div#expiration_fields").show();
        }else
        {
            $("div#expiration_fields").hide();
        }
    }

    //***umer farooq chattha*****//
    function edit_inventory(e)
    {
		add_validate_form();
		$('#titleModal').addClass('add-title').removeClass('add-title-1');
		$('#titleModal').addClass('add-title').removeClass('add-title-1');
		$(".modal-title .add-title").text('Edit Inventory');
        $.ajax({
            type:"post",
            url:'<?php echo base_url(); ?>admin/inventory/for_model',
            data:{"inventory_id":e},
            dataType:'json',
            success:function(dt){
				setTimeout(function(){
				if(dt.inventory_custom_res.length > 0)
				{
					for(var r=0; r<dt.inventory_custom_res.length; r++)
					{
						var class_chkbox = $('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').attr('class');
						var custom_mlt_slc_cls = $('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').hasClass('custom-field-multi-select');
						
						if(class_chkbox != "custom_field_checkbox"){
							$('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').val(dt.inventory_custom_res[r].value).change();
						}
						
						if(custom_mlt_slc_cls)
						{
							
							split_mltbx_ = dt.inventory_custom_res[r].value.split(",");
									
							opt_slec_mulit = 0;
							$('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').val(dt.inventory_custom_res[r].value).siblings().find('ul.dropdown-menu li').each(function(){
								
								currnt_sp_mlt = $(this).find('span').text();
								
								cstm_slctd_multibx = $(this);
								cstm_slctd_multibx_anc_atr = $(this).find('a');
								cstm_slctd_multibx_parent = $(this).parent().parent().prev().find('span:first');
								cstm_slctd_multibx_parent_btn = $(this).parent().parent().prev();
									
								$.each(split_mltbx_,function(i){									
									if(currnt_sp_mlt == split_mltbx_[i].trim()){
										
										values += split_mltbx_[i]+',';
										cstm_slctd_multibx_anc_atr.attr('aria-selected',true);
										cstm_slctd_multibx.addClass('selected');
										if(cstm_slctd_multibx.attr('data-original-index') == '0')
										{
											cstm_slctd_multibx.removeClass('selected');
										}
										
										$('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']  option:eq('+opt_slec_mulit+')').attr('selected','selected');
									}										
								});
								opt_slec_mulit++;
							});
							values = values.slice(0,-1);
							cstm_slctd_multibx_parent.text(values);
							cstm_slctd_multibx_parent_btn.attr('title',values);
						}
												
						if(class_chkbox == "custom_field_checkbox"){

							split_checkbx_ = dt.inventory_custom_res[r].value.split(",");
							$('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').each(function(){
								currnt_bo_chk = $(this).val();
								currnt_bo_chk_id = $(this).attr('id');
								
								$.each(split_checkbx_,function(i){									
									if(currnt_bo_chk == split_checkbx_[i].trim()){
										$('#'+currnt_bo_chk_id).prop('checked',true);
									}
								});								
							});
						}
					}	
				}
                $("#inventory_id").val(dt.inventory_res.inventory_id);
                $("#account123").val(dt.inventory_res.account).change();
                $("select[name='type_of_hardware']").val(dt.inventory_res.type_of_hardware).change();
                $("input[name='serial_number']").val(dt.inventory_res.serial_number);
                $("select[name='type_of_hardware']").val(dt.inventory_res.type_of_hardware).change();
                $("input[name='date_in']").val(dt.inventory_res.date_in);
                $("select[name='status']").val(dt.inventory_res.status).change();
                $("select[name='origin']").val(dt.inventory_res.origin).change();
                $("img.image").show();
                $("img.image").attr('src',dt.inventory_res.image);
                $("#img_clone").val(0);
                $("select[name='equipment_owner']").val(dt.inventory_res.equipment_owner).change();
                $("input[name='manufacturer']").val(dt.inventory_res.manufacturer);
                $("select[name='warranty_expiration_date']").val(dt.inventory_res.warranty_expiration_date).change();
                if(dt.inventory_res.warranty_expiration_date==10)
                {
                    $("#exp_no").val(dt.inventory_res.custome_nu);
                    $("#exp_type").val(dt.inventory_res.custome_type);
                    $("div#expiration_fields").show();
                }
                $("#description").val(dt.inventory_res.description);
                $("input[name='inventory_id']").val(dt.inventory_res.inventory_id);
                $("input[name='inventory_id']").val(dt.inventory_res.inventory_id);
                $("input[name='inventory_id']").val(dt.inventory_res.inventory_id);
				},1000);
            }
        });
    }

function edit_inventory_clone(e)
{
	add_validate_form();
	$('#titleModal').addClass('add-title-1').removeClass('add-title');
	$(".modal-title .add-title-1").text('Add New Inventory');
	$.ajax({
	type:"post",
	url:'<?php echo base_url(); ?>admin/inventory/for_model',
	data:{"inventory_id":e},
	dataType:'json',
	success:function(dt){
		setTimeout(function(){
			if(dt.inventory_custom_res.length > 0)
			{
				for(var r=0; r<dt.inventory_custom_res.length; r++)
				{
					var class_chkbox = $('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').attr('class');
					var custom_mlt_slc_cls = $('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').hasClass('custom-field-multi-select');
					
					if(class_chkbox != "custom_field_checkbox"){
						$('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').val(dt.inventory_custom_res[r].value).change();
					}
					
					if(custom_mlt_slc_cls)
					{
						
						split_mltbx_ = dt.inventory_custom_res[r].value.split(",");
								
						opt_slec_mulit = 0;
						$('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').val(dt.inventory_custom_res[r].value).siblings().find('ul.dropdown-menu li').each(function(){
							
							currnt_sp_mlt = $(this).find('span').text();
							
							cstm_slctd_multibx = $(this);
							cstm_slctd_multibx_anc_atr = $(this).find('a');
							cstm_slctd_multibx_parent = $(this).parent().parent().prev().find('span:first');
							cstm_slctd_multibx_parent_btn = $(this).parent().parent().prev();
								
							$.each(split_mltbx_,function(i){									
								if(currnt_sp_mlt == split_mltbx_[i]){
									
									values += split_mltbx_[i]+',';
									cstm_slctd_multibx_anc_atr.attr('aria-selected',true);
									cstm_slctd_multibx.addClass('selected');
									if(cstm_slctd_multibx.attr('data-original-index') == '0')
									{
										cstm_slctd_multibx.removeClass('selected');
									}
									
									$('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']  option:eq('+opt_slec_mulit+')').attr('selected','selected');
								}										
							});
							opt_slec_mulit++;
						});
						values = values.slice(0,-1);
						cstm_slctd_multibx_parent.text(values);
						cstm_slctd_multibx_parent_btn.attr('title',values);
					}
											
					if(class_chkbox == "custom_field_checkbox"){

						split_checkbx_ = dt.inventory_custom_res[r].value.split(",");
						$('[data-fieldid='+dt.inventory_custom_res[r].fieldid+']').each(function(){
							currnt_bo_chk = $(this).val();
							currnt_bo_chk_id = $(this).attr('id');
							
							$.each(split_checkbx_,function(i){									
								if(currnt_bo_chk == split_checkbx_[i]){
									$('#'+currnt_bo_chk_id).prop('checked',true);
								}
							});								
						});
					}
				}	
			}
		$("#inventory_id").val(0);
		$("select#account123").val(dt.inventory_res.account).change();
		$("select[name='type_of_hardware']").val(dt.inventory_res.type_of_hardware).change();
		$("input[name='date_in']").val(dt.inventory_res.date_in);
		$("select[name='status']").val(dt.inventory_res.status).change();
		$("select[name='origin']").val(dt.inventory_res.origin).change();
		$("img.image").show();
		$("img.image").attr('src',dt.inventory_res.image);
		$("#img_clone").val(dt.inventory_res.image);
		$("select[name='equipment_owner']").val(dt.inventory_res.equipment_owner).change();
		$("input[name='manufacturer']").val(dt.inventory_res.manufacturer);
		$("select[name='warranty_expiration_date']").val(dt.inventory_res.warranty_expiration_date).change();
			if(dt.inventory_res.warranty_expiration_date==10)
			{
				$("#exp_no").val(dt.inventory_res.custome_nu);
				$("#exp_type").val(dt.inventory_res.custome_type);
				$("div#expiration_fields").show();
			}
		$("#description").val(dt.inventory_res.description);
		},1000);
		}
		
	});
	setTimeout(function (){
		$("#serial_number").val('');
		$('#serial_number').focus();
	}, 1000);
}

</script>