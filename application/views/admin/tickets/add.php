<?php  
// $cont = get_all_contact_company($contact['userid'], $contact['contact_id']);
// echo "<pre>";print_r($cont);exit('iiiiii');
// echo "<pre>";print_r($contact);exit('iiiiii');
 $id=$_GET['userid'];
init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div id="contact_data"></div>
		<?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'new_ticket_form')); ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<?php echo render_input('subject','ticket_settings_subject','','text',array('required'=>'true')); ?>
								<div class="form-group select-placeholder">
									<label for="userid">DBA Name</label>
									<?php if(isset($contact)){?>
										<select class="selectpicker form-control company" data-live-search="true" name="userid">
											<option>Select Company</option>
											<?php foreach($compnay_data as $company){?>

											<option value="<?php echo $company['userid']; ?>" <?php if($company['userid'] ==  $contact['userid']){ echo "selected"; }?>><?php echo $company['company']; ?></option>
											<?php }?>
										</select>
									<?php } else{?>
										<select class="selectpicker form-control company" data-live-search="true" name="userid">
											<option>Select Comapny</option>
											<?php foreach($compnay_data as $company){?>

											<option value="<?php echo $company['userid']; ?>"><?php echo $company['company']; ?></option>
										<?php }?>
										</select>
									<?php } ?>
									<!-- <select name="contactid" required="true" id="contactid" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<?php //if(isset($contact)) { ?>
									<option value="<?php //echo $contact['id']."_".$contact['userid']; ?>" selected><?php //echo $contact['firstname']. ' ' .$contact['lastname']; ?></option>

									<!--<option value="<?php //echo $contact['id']; ?>" selected><?php //echo $contact['firstname']. ' ' .$contact['lastname']; ?></option>-->
									<?php //} ?>
									<!-- <option value=""></option> -->
									<!-- </select> -->
									<?php //echo form_hidden('userid'); ?>
								</div> 
								<div class="row">
									<!-- <div class="col-md-6"> -->
									<div class="col-md-6" id="hiden_contact_id">
										<label for="contactid">Contact</label>
										<select class="selectpicker form-control contacts_id" id="contactid" multiple data-actions-box="true" >
											<?php if($contact){
												$cont = get_all_contact_company($contact['userid']);
												foreach ($cont as $con) {?>
													<option data-email = "<?php echo $con['contact_id'] ?>" value = "<?php echo $con['email'] ?>" <?php if($con['contact_id'] == $contact['contact_id']){ echo "selected";}?>>
														<?php echo $con['firstname'] . ' '. $con['lastname'];?>
													</option> 	
												 <?php } 
												}?>
										</select>
									<?php //echo render_input('to','ticket_settings_to','','text',array('disabled'=>true)); ?>
									</div>
										<?php //echo render_input('to','ticket_settings_to','','text',array('disabled'=>true)); ?>
									<!-- </div> -->
									<!-- <div class="col-md-6"> -->
										<?php //echo render_input('email','ticket_settings_email','','email',array('disabled'=>true)); ?>
									<!-- </div> -->
									<div class="col-md-6" >
										<label for="contacts_shows_now">Email address</label>
										<?php if($contact){?>
											<input type='text' class="form-control" value="<?php echo $contact['email'];?>" readonly id="contacts_shows_now" style=" color: #323a45; background-color: #fff !important">
											<input type='hidden' class="form-control" value="<?php echo $contact['contact_id'];?>" id="contacts_hidden_id" name="contactid[]">
										<?php }
										else{?>
												<input type='text' class="form-control" value="" readonly id="contacts_shows_now" style=" color: #323a45; background-color: #fff !important">
												<input type='hidden' class="form-control" value="" id="contacts_hidden_id" name="contactid[]">
										<?php }?>
										
									<?php //echo render_input('email','ticket_settings_email','','email',array('disabled'=>true)); ?>
									</div>
								</div>
								<br>
								<?php 
								// echo "<pre>"; print_r($departments); exit('huji');   ?> 
								<div class="row">
									<div class="col-md-6">
										<?php 
										echo render_select('department',$departments,array('departmentid','name'),'ticket_settings_departments',(count($departments) == 1) ? $departments[0]['departmentid'] : $departments[1]['departmentid'],array('required'=>'true')); ?>
									</div>
									<div class="col-md-6">
										<?php echo render_input('cc','CC'); ?>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
									<input type="text" class="tagsinput" id="tags" name="tags" data-role="tagsinput">
								</div>

								<div class="form-group select-placeholder">
									<label for="assigned" class="control-label">
										<?php echo _l('ticket_settings_assign_to'); ?>
									</label>
									<?php
					                  $selected = ''; 
					                  foreach($staff_multi as $member){ ?>
										<?php if($member['staffid'] == get_staff_user_id()){$selected = $member['staffid'];} }
										$assignee_nameee = array(
					                  		0 => 'firstname',
					                  		1 => 'lastname');

					                	echo render_select('assigned[]',$staff_multi,array('staffid',$assignee_nameee),'',$selected,array('multiple'=>true,'data-actions-box'=>true, 'selected'=>true),array(),'','form-control selectpicker',false);?>
								</div>
								<!-- new code -->
								<div class="form-group select-placeholder">
									<?php
					                  $selected = ''; 
					                  $req = required_followers($this->session->userdata('portfolio_id')); 
					                  if($req  ==  1){
					                  	$followers_nameee = array(
					                  		0 => 'firstname',
					                  		1 => 'lastname');
					                  	echo render_select('followers[]',$staff_multi,array('staffid',$followers_nameee),'Followers',$selected,array('multiple'=>true,'data-actions-box'=>true, 'required'=>true, 'selected'=>true),array(),'','form-control selectpicker',false);
					                  }
					                  else{
											echo render_select('followers[]',$staff,array('staffid','firstname', 'lastname'),'Followers',$selected,array('multiple'=>true,'data-actions-box'=>true, 'selected'=>true),array(),'','form-control selectpicker',false);					                  	
					                  }?>

								</div>
								<!-- new code end -->
								<div class="row">
									<div class="col-md-<?php if(get_option('services') == 1){ echo 6; }else{echo 12;} ?>">
										<?php $priorities['callback_translate'] = 'ticket_priority_translate';
										echo render_select('priority',$priorities,array('priorityid','name'),'ticket_settings_priority',do_action('new_ticket_priority_selected',2),array('required'=>'true')); ?>
									</div>
									<?php if(get_option('services') == 1){ ?>
										<div class="col-md-6">
											<?php if(is_admin() || get_option('staff_members_create_inline_ticket_services') == '1'){
												echo render_select_with_input_group('service',$services,array('serviceid','name'),'ticket_settings_service','','<a href="#" onclick="new_service();return false;"><i class="fa fa-plus"></i></a>');
											} else {
												echo render_select('service',$services,array('serviceid','name'),'ticket_settings_service');
											}
											?>
										</div>
									<?php } ?>
									
									<div class="col-md-6">
										<?php 
										// echo "<pre>"; print_r($statuses); 
										echo render_select('status',$statuses,array('ticketstatusid','name'),'status',(count($statuses) == 1) ? $statuses[0]['ticketstatusid'] : $statuses[0]['ticketstatusid'],array('required'=>'true')); 
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo render_select('source',$sources,array('source_id','source_name'),'Source','',array('required'=>'true')); 
										?>
									</div>
								</div>
								<div class="form-group">
									<div class="checkbox">
										<input  type="checkbox" name="notify_ticket_body" value="1">
										<label for="notify_ticket_body">Display Ticket Body</label>
									</div>
								</div>
								<div class="form-group projects-wrapper hide">
									<label for="project_id"><?php echo _l('project'); ?></label>
									<div id="project_ajax_search_wrapper">
										<select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"<?php if(isset($project_id)){ ?> data-auto-project="true" data-project-userid="<?php echo $userid; ?>"<?php } ?>>
											<?php if(isset($project_id)){ ?>
												<option value="<?php echo $project_id; ?>" selected><?php echo '#'.$project_id. ' - ' . get_project_name_by_id($project_id); ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<?php echo render_custom_fields('tickets'); ?>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-heading">
								<?php echo _l('ticket_add_body'); ?>
							</div>
							<div class="panel-body">
								<div class="btn-bottom-toolbar text-right">
									<button type="submit" data-form="#new_ticket_form" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info"><?php echo _l('open_ticket'); ?></button>
								</div>
								<div class="row">
									<div class="col-md-12 mbot20 before-ticket-message">
										<select id="insert_predefined_reply" data-live-search="true" class="selectpicker mleft10 pull-right" data-title="<?php echo _l('ticket_single_insert_predefined_reply'); ?>">
											<?php foreach($predefined_replies as $predefined_reply){ ?>
												<option value="<?php echo $predefined_reply['id']; ?>"><?php echo $predefined_reply['name']; ?></option>
											<?php } ?>
										</select>
										<?php if(get_option('use_knowledge_base') == 1){ ?>
											<?php $groups = get_all_knowledge_base_articles_grouped($only_customers=FALSE); ?>
											<select id="insert_knowledge_base_link" class="selectpicker pull-right" data-live-search="true" onchange="insert_ticket_knowledgebase_link(this);" data-title="<?php echo _l('ticket_single_insert_knowledge_base_link'); ?>">
												<option value=""></option>
												<?php foreach($groups as $group){ ?>
													<?php if(count($group['articles']) > 0){ ?>
														<optgroup label="<?php echo $group['name']; ?>">
															<?php foreach($group['articles'] as $article) { ?>
																<option value="<?php echo $article['articleid']; ?>">
																	<?php echo $article['subject']; ?>
																</option>
															<?php } ?>
														</optgroup>
													<?php } ?>
												<?php } ?>
											</select>
										<?php } ?>
									</div>
								</div>
								<div class="clearfix"></div>
								<?php echo render_textarea('message','','',array(),array(),'','tinymce'); ?>
							</div>
							<div class="panel-footer attachments_area">
								<div class="row attachments">
									<div class="attachment">
										<div class="col-md-4 col-md-offset-4 mbot15">
											<div class="form-group">
												<label for="attachment" class="control-label"><?php echo _l('ticket_add_attachments'); ?></label>
												<div class="input-group">
													<input type="file" id = "preview1" extension="<?php echo str_replace('.','',get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control attachments_d" name="attachments[]" onchange="readURL(this)" accept="<?php echo get_ticket_form_accepted_mimes(); ?>" >
													<img class="prev1" id="prev1" src="#" name="img_attachments[]" style="display:none;">
													<span class="input-group-btn">
														<button class="btn btn-success add_more_attachments_d p8-half" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
													</span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
	<?php $this->load->view('admin/tickets/services/service'); ?>
	<?php init_tail(); ?>
	<?php echo app_script('assets/js','tickets.js'); ?>
	<?php $this->load->view('admin/clients/client_js'); ?>
	<?php do_action('new_ticket_admin_page_loaded'); ?>
	<script>			
		var val = 0;
		var addMoreAttachmentsInputKey = 1;
		$("body").on('click', '.add_more_attachments_d', function() {
		val = $('.attachments_d').length +1 ;
		if ($(this).hasClass('disabled')) { return false; }
		var total_attachments = $('.attachments input[name*="attachments"]').length;
		if ($(this).data('ticket') && total_attachments >= app_maximum_allowed_ticket_attachments) {
		return false;
		}
		var newattachment = $('.attachments').find('.attachment').eq(0).clone().appendTo('.attachments');
		newattachment.find('input').removeAttr('aria-describedby aria-invalid');
		newattachment.find('.has-error').removeClass('has-error');
		newattachment.find('input').attr('name', 'attachments[' + addMoreAttachmentsInputKey + ']').val('');

		newattachment.find('input').attr('id', "preview" + val);

		newattachment.find('img').attr('style', 'display:none');
		newattachment.find('img').attr('id', 'prev'+val);
		newattachment.find('img').attr('src',''); 
		newattachment.find('p[id*="error"]').remove();
		newattachment.find('i').removeClass('fa-plus').addClass('fa-minus');

		//$('element').attr('id', 'value');

		newattachment.find('button').removeClass('add_more_attachments_d').addClass('remove_attachment_d').removeClass('btn-success').addClass('btn-danger');
		addMoreAttachmentsInputKey++;
		//readURL(this);
		});
		// Remove attachment
		$("body").on('click', '.remove_attachment_d', function() {
		$(this).parents('.attachment').remove();
		});


		function readURL(input) 
		{
		var idv = input.id; 
		var sa = idv[idv.length -1]; 
		$('#prev'+sa).show();
		$.each(input.files,function(i){
		var reader = new FileReader();
		reader.onload = function (e) {
		$('#prev'+sa)
		.attr('src', e.target.result)
		.width(160)
		.height(180);
		};
		reader.readAsDataURL(input.files[i]);
		});
		} 
		$(".attachments_d").on('change',function() {
		readURL(this);
		});

		$(function(){

			init_ajax_search('contact','#contactid.ajax-search',{
				tickets_contacts:true,
				contact_userid:function(){
					// when ticket is directly linked to project only search project client id contacts
					var uid = $('select[data-auto-project="true"]').attr('data-project-userid');
					if(uid){
						return uid;
					} else {
						return '';
					}
				}
			});

			$('#new_ticket_form').validate();

			<?php if(isset($project_id) || isset($contact)){ ?>
				$('body.ticket select[name="contactid"]').change();
			<?php } ?>
			<?php if(isset($project_id)){ ?>
				$('body').on('selected.cleared.ajax.bootstrap.select','select[data-auto-project="true"]',function(e){
					$('input[name="userid"]').val('');
					$(this).parents('.projects-wrapper').addClass('hide');
					$(this).prop('disabled',false);
					$(this).removeAttr('data-auto-project');
					$('body.ticket select[name="contactid"]').change();
				});
			<?php } ?>
		});
	</script>
	<script type="text/javascript">
			$(document).ready(function(){
			$("select.company").change(function(){
				var company_Id = $(this).children("option:selected").val();
				var contacts='';
				$.ajax({
						url:'<?php echo base_url(); ?>admin/tickets/company_contacts/'+company_Id,
						type:"POST",
						dataType: "json",
						success:function (response) {
							$("#contactid").empty();
							// alert();
							$("#contactid").val("");
							var favorite = [];
							$("#contacts_shows_now").val("");
							$.each(response, function(index, value) {
								if(value['title'] !=''){
									$("#contactid").append("<option data-email="+value['contact_id']+" value="+value['email']+">"+value['firstname']+' '+value['lastname'] +' '+'|'+' '+value['title']+"</option>");
								}
								else{
									$("#contactid").append("<option data-email="+value['contact_id']+" value="+value['email']+">"+value['firstname']+' '+value['lastname'] +"</option>");	
								}
								
							})
							$('.selectpicker').selectpicker('refresh');
						}
					});
			});
				$("#contactid").on("changed.bs.select", function() {
				    var val2 = 0;
				    var val = $(this).val();

					    $.ajax({
					      type:'post',
					      url:'<?php echo base_url();?>admin/tickets/get_company_contacts_id',
					      data:{val},
					      success:function(dt){
					      val2 = dt;
					      $("#contacts_shows_now").val('');
					      $("#contacts_hidden_id").val('');
					      $("#contacts_shows_now").val(val);
					      $("#contacts_hidden_id").val(val2);
					      }
				    	});
					});
			$('.selectpicker').on('change', function(){
			var selected = '';
			 selected = $(this).find("option:selected").val();
			 //alert(selected);
			 if(selected == ''){
			 	$("#contacts_shows_now").val('');
			 }
			});

			// $("select.contacts_id").change(function(){
			// var contact_add = $(this).find('option').attr('value') ;
			// // $(this).children("option:selected").attr("data-email");
			// alert(contact_add);
			// // $(this).find(':selected').attr("data-email").removeClass('selected');
			// return false;
			// $.each($("select.contacts_id"), function(){ 
			// favorite.push(contact_add);
			// });
			// $("#contacts_shows_now").val(favorite);
			// });

			});
	</script>
</body>
</html>
