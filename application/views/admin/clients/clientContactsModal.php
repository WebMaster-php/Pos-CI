<?php if (isset($contact))
		{$co = $contact; } 
?>
<!-- Modal Contact -->
<div class="modal fade" id="lead_contacts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open('admin/clients/costumer_contact/'.$customer_id.'/'.$contactid,array('id'=>'lead_contact_form','autocomplete'=>'off')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?><br /><small id=""><?php echo get_company_name($customer_id,true); ?></small></h4>
			
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
						<div id="new_contact">
							<?php if(isset($co)){ ?>
							<img src="<?php echo contact_profile_image_url($co->contact_id,'thumb'); ?>" id="contact-img" class="client-profile-image-thumb">
							<?php if(!empty($co->profile_image)){ ?>
							<a href="#" onclick="delete_contact_profile_image(<?php echo $co->contact_id; ?>); return false;" class="text-danger pull-right" id="contact-remove-img"><i class="fa fa-remove"></i></a>
							<?php } ?>
							<hr />
							<?php } ?>
							<?php if(isset($company_lst)){?>
								<div class="form-group">
									<label for="" class="control-label">
									<small class="req text-danger">* </small>
									Select Company</label>
									<select class="selectpicker display-block"   data-live-search="true" data-width="100%" class="ajax-search" id="idd" name="idd" data-none-selected-text="Please Select Company">
										<option ></option> <!--//-->
										<?php foreach($company_lst as $cLst){?>
											<option value="<?php echo $cLst['userid']; ?>" ><?php echo $cLst['company'];?></option>
										<?php } ?>
									</select>
									<p id="slectcompanydata-error" class="text-danger" style="display: none;">This field is required.</p>
								</div>
							<?php 
								} 
							?>
							
							<div id="contact-profile-image" class="form-group<?php if(isset($co) && !empty($co->profile_image)){echo ' hide';} ?>">
								<label for="profile_image" class="profile-image"><?php echo _l('client_profile_image'); ?></label>
								<input type="file" name="profile_image" class="form-control" id="profile_image">
							</div>
							<?php if(isset($contact)){ ?>
							<div class="alert alert-warning hide" role="alert" id="contact_proposal_warning">
								<?php echo _l('proposal_warning_email_change',array(_l('contact_lowercase'),_l('contact_lowercase'),_l('contact_lowercase'))); ?>
								<hr />
								<a href="#" id="contact_update_proposals_emails" data-original-email="" onclick="update_all_proposal_emails_linked_to_contact(<?php echo $co->contact_id; ?>); return false;"><?php echo _l('update_proposal_email_yes'); ?></a>
								<br />
								<a href="#" onclick="close_modal_manually('#contact'); return false;"><?php echo _l('update_proposal_email_no'); ?></a>
							</div>
							<?php } ?>
							<!-- // For email exist check -->
							<?php echo form_hidden('contactid',$contactid); ?>
							<?php $value=( isset($contact) ? $contact->firstname : ''); ?>
							<?php echo render_input( 'firstname', 'client_firstname',$value); ?>
							<?php $value=( isset($contact) ? $contact->lastname : ''); ?>
							<?php echo render_input( 'lastname', 'client_lastname',$value); ?>
							<?php $value=( isset($contact) ? $contact->title : ''); ?>
							<?php echo render_input( 'title', 'contact_position',$value); ?>
							<?php $value=( isset($contact) ? $contact->email : ''); ?>
							<?php echo render_input( 'email', 'client_email',$value, 'email'); ?>
							<?php $value=( isset($contact) ? $contact->phonenumber : ''); ?>
							<?php echo render_input( 'phonenumber', 'client_phonenumber',$value,'text',array('autocomplete'=>'off')); ?>
							<div class="form-group contact-direction-option">
							  <label for="direction"><?php echo _l('document_direction'); ?></label>
							  <select class="selectpicker" data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-width="100%" name="direction" id="direction">
								<option value="" <?php if(isset($contact) && empty($contact->direction)){echo 'selected';} ?>></option>
								<option value="ltr" <?php if(isset($contact) && $contact->direction == 'ltr'){echo 'selected';} ?>>LTR</option>
								<option value="rtl" <?php if(isset($contact) && $contact->direction == 'rtl'){echo 'selected';} ?>>RTL</option>
							</select>
							</div>
							<?php $rel_id=( isset($contact) ? $contact->id : false); ?>
							<?php echo render_custom_fields( 'contacts',$rel_id); ?>


							<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
							<input  type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1" />
							<input  type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1"/>

						<div class="client_password_set_wrapper">
							<label for="password" class="control-label">
								<?php echo _l( 'client_password'); ?>
							</label>
							<div class="input-group">

								<input type="password" class="form-control password" name="password" id="password" autocomplete="false">
								<span class="input-group-addon">
									<a href="#password" class="show_password" onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
								</span>
								<span class="input-group-addon">
									<a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
								</span>
							</div>
							<?php if(isset($contact)){ ?>
							<p class="text-muted">
								<?php echo _l( 'client_password_change_populate_note'); ?>
							</p>
							<?php if($contact->last_password_change != NULL){
								echo _l( 'client_password_last_changed');
								echo '<span class="text-has-action" data-toggle="tooltip" data-title="'._dt($contact->last_password_change).'"> ' . time_ago($contact->last_password_change) . '</span>';
							}
						} ?>
						</div>
					</div>
					<div id="old_contact" style="display:none;">
						<?php if(isset($company_lst)){?>
								<div class="form-group">
									<label for="" class="control-label">
									<small class="req text-danger">* </small>
									Select Company</label>
									<select class="selectpicker display-block"   data-live-search="true" data-width="100%" class="ajax-search" id="idd_1" name="idd_1" data-none-selected-text="Please Select Company">
										<option ></option> <!--//-->
										<?php foreach($company_lst as $cLst){?>
											<option value="<?php echo $cLst['userid']; ?>" ><?php echo $cLst['company'];?></option>
										<?php } ?>
									</select>
									<p id="slectcompany-error" class="text-danger" style="display: none;">This field is required.</p>
								</div>
							<?php 
								} 
							?>
						<div class="form-group">
							<label for="" class="control-label">
							<small class="req text-danger">* </small>
							Select Existing Contact</label>
							<input type = "hidden" id = "company_id" name = "company_id" value = "<?php echo $this->uri->segment(4); ?>"/>
							<select class="selectpicker display-block"   data-live-search="true" data-width="100%" class="ajax-search" id="old_existing_account" name="old_existing_account" data-none-selected-text="<?php echo _l('Please Choose Customer'); ?>">
								
								<option ></option> <!--//-->
								<?php foreach($contactsdata as $contdata){?>
									<option value="<?php echo $contdata->id; ?>" ><?php echo $contdata->firstname.' '.$contdata->last_name;?></option>
								<?php } ?>
							</select>
							<p id="existingfield-error" class="text-danger" style="display: none;">This field is required.</p>
						</div>
					</div>
					<div class="alert alert-danger" style = "display:none" id = "check">
							  This Contact is All Ready Selected for this Customer
					</div>	
                <hr />
                <div class="checkbox checkbox-primary">
					<!--<input type="checkbox" id="contact_primary" data-perm-id="3" class="onoffswitch-checkbox" <?php //if(isset($co) && $co->contract_emails == 1){echo 'checked';} ?>  value="contract_emails" name="contract_emails">-->
                    <input type="checkbox" name="is_primary" id="contact_primary" <?php if(isset($co) && $co->is_primary == 1){echo 'checked';}?> value="<?php if(isset($co) && $co->is_primary == 1){echo 1;}else{echo 0;}?>">
                    <label for="contact_primary">
                        <?php echo _l( 'contact_primary'); ?>
                    </label>
                </div>
                <?php if(!isset($contact) && total_rows('tblemailtemplates',array('slug'=>'new-client-created','active'=>0)) == 0){ ?>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="donotsendwelcomeemail" id="donotsendwelcomeemail">
                    <label for="donotsendwelcomeemail">
                        <?php echo _l( 'client_do_not_send_welcome_email'); ?>
                    </label>
                </div>
                <?php } ?>
                <?php if(total_rows('tblemailtemplates',array('slug'=>'contact-set-password','active'=>0)) == 0){ ?>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="send_set_password_email" id="send_set_password_email">
                    <label for="send_set_password_email">
                        <?php echo _l( 'client_send_set_password_email'); ?>
                    </label>
                </div>
                <?php } ?>
                <hr />
                <p class="bold"><?php echo _l('customer_permissions'); ?></p>
                <p class="text-danger"><?php echo _l('contact_permissions_info'); ?></p>
                <?php
                $default_contact_permissions = array();
                if(!isset($co)){
                    $default_contact_permissions = @unserialize(get_option('default_contact_permissions'));
                }
                ?>
                <?php foreach($customer_permissions as $permission){
				?>
				<div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo $permission['name']; ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="<?php echo $permission['id']; ?>" class="onoffswitch-checkbox" <?php if(isset($co) && has_contact_permission($permission['short_name'],$co->contact_id) || is_array($default_contact_permissions) && in_array($permission['id'],$default_contact_permissions)){echo 'checked';} ?> value="<?php echo $permission['id']; ?>" name="permissions[]">
                                <label class="onoffswitch-label" for="<?php echo $permission['id']; ?>"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php } ?>
                 <hr />
                <p class="bold"><?php echo _l('email_notifications'); ?><?php if(is_sms_trigger_active()){echo '/SMS';} ?></p>
                <div id="contact_email_notifications">
                <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo _l('invoice'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="invoice_emails" data-perm-id="1" class="onoffswitch-checkbox" <?php if(isset($co) && $co->invoice_emails == '1'){echo 'checked';} ?>  value="invoice_emails" name="invoice_emails">
                                <label class="onoffswitch-label" for="invoice_emails"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo _l('estimate'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="estimate_emails" data-perm-id="2" class="onoffswitch-checkbox" <?php if(isset($co) && $co->estimate_emails == '1'){echo 'checked';} ?>  value="estimate_emails" name="estimate_emails">
                                <label class="onoffswitch-label" for="estimate_emails"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo _l('credit_note'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="credit_note_emails" data-perm-id="1" class="onoffswitch-checkbox" <?php if(isset($co) && $co->credit_note_emails == '1'){echo 'checked';} ?>  value="credit_note_emails" name="credit_note_emails">
                                <label class="onoffswitch-label" for="credit_note_emails"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo _l('project'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="project_emails" data-perm-id="6" class="onoffswitch-checkbox" <?php if(isset($co) && $co->project_emails == '1'){echo 'checked';} ?>  value="project_emails" name="project_emails">
                                <label class="onoffswitch-label" for="project_emails"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('only_project_tasks'); ?>"></i> <?php echo _l('task'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="task_emails" data-perm-id="6" class="onoffswitch-checkbox" <?php if(isset($co) && $co->task_emails == '1'){echo 'checked';} ?>  value="task_emails" name="task_emails">
                                <label class="onoffswitch-label" for="task_emails"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span><?php echo _l('contract'); ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                            <div class="onoffswitch">
                                <input type="checkbox" id="contract_emails" data-perm-id="3" class="onoffswitch-checkbox" <?php if(isset($co) && $co->contract_emails == 1){echo 'checked';} ?>  value="contract_emails" name="contract_emails">
                                <label class="onoffswitch-label" for="contract_emails"></label>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
				</div>
			</div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" id ="existing_contact_save" class="btn btn-info" data-loading-text="Please wait..." autocomplete="off" data-form="#lead_contact_form"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>
		</div>
	</div>
</div>
<?php if(!isset($contact)){ ?>
    <script>
        $(function(){
            // Guess auto email notifications based on the default contact permissios
            var permInputs = $('input[name="permissions[]"]');
            $.each(permInputs,function(i,input){
                input = $(input);
                if(input.prop('checked') === true){
                    $('#contact_email_notifications [data-perm-id="'+input.val()+'"]').prop('checked',true);
                }
            });
        });
    </script>
<?php } ?>
<script>

   $('body').on('change', '#old_existing_account', function() {
		var contact_id = $(this).val(); 
		var company_id = $('#company_id').val();
	   $.ajax({
				type:'POST',
				url:'<?php echo base_url(); ?>admin/leads/get_existing_check',
				data:{'company_id':company_id, 'contact_id': contact_id},
				success:function(data){
						if(data==1){
						$("#check").show();
						$('#existing_contact_save').attr("disabled", 'disabled');
						}
						else
						{
							$('#existing_contact_save').removeAttr("disabled");
							$("#check").hide();
						}
				}	
			});
   });
   
    $('body').on('change', '#contact_primary', function() {
		if($(this).val() == 1){
			$(this).val('0');
		}else{
			$(this).val('1');
		}
	});

   
</script>