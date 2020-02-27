<h4 class="customer-profile-group-heading"><?php echo _l('client_add_edit_profile'); ?></h4>
<div class="row">
   <?php echo form_open($this->uri->uri_string(),array('class'=>'client-form','autocomplete'=>'off')); ?>
   <div class="additional"></div>
   <div class="col-md-12">
      <ul class="nav nav-tabs profile-tabs row customer-profile-tabs" role="tablist">
         <li role="presentation" class="<?php if(!$this->input->get('tab')){echo 'active';}; ?>">
            <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
               <?php echo _l( 'customer_profile_details'); ?>
            </a>
         </li>
         <?php
         $customer_custom_fields = false;
         if(total_rows('tblcustomfields',array('fieldto'=>'customers','active'=>1)) > 0 ){
              $customer_custom_fields = true;
          ?>
          <li role="presentation" class="<?php if($this->input->get('tab') == 'custom_fields'){echo 'active';}; ?>">
            <a href="#custom_fields" aria-controls="custom_fields" role="tab" data-toggle="tab">
               <?php echo do_action('customer_profile_tab_custom_fields_text',_l( 'custom_fields')); ?>
            </a>
         </li>
         <?php } ?>
         <li role="presentation">
            <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab" data-toggle="tab">
               <?php echo _l( 'billing_shipping'); ?>
            </a>
         </li>
         <?php do_action('after_customer_billing_and_shipping_tab',isset($client) ? $client : false); ?>
         <?php if(isset($client)){ ?>
         <li role="presentation<?php if($this->input->get('tab') && $this->input->get('tab') == 'contacts'){echo ' active';}; ?>">
            <a href="#contacts" aria-controls="contacts" role="tab" data-toggle="tab">
                <?php if(is_empty_customer_company($client->userid)) {
                  echo _l('contact');
                } else {
                  echo _l( 'customer_contacts');
                }
                ?>
            </a>
         </li>
         <li role="presentation">
            <a href="#customer_admins" aria-controls="customer_admins" role="tab" data-toggle="tab">
               <?php echo _l( 'customer_admins'); ?>
            </a>
         </li>
		 
		 <li role="presentation">
            <a href="#inventory_profile" aria-controls="inventory_profile" role="tab" data-toggle="tab">
               <?php echo  _l( 'Inventory'); ?>
            </a>
         </li>
		 
         <?php do_action('after_customer_admins_tab',$client); ?>
         <?php } ?>
      </ul>
      <div class="tab-content">
         <?php do_action('after_custom_profile_tab_content',isset($client) ? $client : false); ?>
         <?php if($customer_custom_fields) { ?>
         <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') == 'custom_fields'){echo ' active';}; ?>" id="custom_fields">
               <?php $rel_id=( isset($client) ? $client->userid : false); ?>
               <?php echo render_custom_fields( 'customers',$rel_id); ?>
         </div>
         <?php } ?>
         <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab')){echo ' active';}; ?>" id="contact_info">
            <div class="row">
               <div class="col-md-12<?php if(isset($client) && (!is_empty_customer_company($client->userid) && total_rowss('tblcontacts',array('userid'=>$client->userid)) > 0)) { echo ''; } else {echo ' hide';} ?>" id="client-show-primary-contact-wrapper">
                  <div class="checkbox checkbox-info mbot20 no-mtop">
                     <input type="checkbox" name="show_primary_contact"<?php if(isset($client) && $client->show_primary_contact == 1){echo ' checked';}?> value="1" id="show_primary_contact">
                     <label for="show_primary_contact"><?php echo _l('show_primary_contact',_l('invoices').', '._l('estimates').', '._l('payments').', '._l('credit_notes')); ?></label>
                  </div>
               </div>
               <div class="col-md-6">
                  <?php $value=( isset($client) ? $client->company : ''); ?>
                  <?php $attrs = (isset($client) ? array() : array('autofocus'=>true)); ?>
                  <?php echo render_input( 'company', 'client_company',$value,'text',$attrs); ?>
                  <?php if(get_option('company_requires_vat_number_field') == 1){
                    $value=( isset($client) ? $client->vat : '');
                    echo render_input( 'vat', 'client_vat_number',$value);
                 } ?>
                 <?php $value=( isset($client) ? $client->phonenumber : ''); ?>
                 <?php echo render_input( 'phonenumber', 'client_phonenumber',$value); ?>
                 <?php if((isset($client) && empty($client->website)) || !isset($client)){
                   $value=( isset($client) ? $client->website : '');
                   echo render_input( 'website', 'client_website',$value);
                } else { ?>
                <div class="form-group">
                  <label for="website"><?php echo _l('client_website'); ?></label>
                  <div class="input-group">
                     <input type="text" name="website" id="website" value="<?php echo $client->website; ?>" class="form-control">
                     <div class="input-group-addon">
                        <span><a href="<?php echo maybe_add_http($client->website); ?>" target="_blank" tabindex="-1"><i class="fa fa-globe"></i></a></span>
                     </div>
                  </div>
               </div>
               <?php }
               $selected = array();
               if(isset($customer_groups)){
                 foreach($customer_groups as $group){
                    array_push($selected,$group['groupid']);
                 }
              }
              if(is_admin() || get_option('staff_members_create_inline_customer_groups') == '1'){
                echo render_select_with_input_group('groups_in[]',$groups,array('id','name'),'customer_groups',$selected,'<a href="#" data-toggle="modal" data-target="#customer_group_modal"><i class="fa fa-plus"></i></a>',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                } else {
                  echo render_select('groups_in[]',$groups,array('id','name'),'customer_groups',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                }
              ?>
              <?php if(!isset($client)){ ?>
              <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('customer_currency_change_notice'); ?>"></i>
              <?php }
              $s_attrs = array('data-none-selected-text'=>_l('system_default_string'));
              $selected = '';
              if(isset($client) && client_have_transactions($client->userid)){
                 $s_attrs['disabled'] = true;
              }
              foreach($currencies as $currency){
                 if(isset($client)){
                   if($currency['id'] == $client->default_currency){
                     $selected = $currency['id'];
                  }
               }
            }
                     // Do not remove the currency field from the customer profile!
            echo render_select('default_currency',$currencies,array('id','name','symbol'),'invoice_add_edit_currency',$selected,$s_attrs); ?>
            <?php if(get_option('disable_language') == 0){ ?>
            <div class="form-group select-placeholder">
               <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
               </label>
               <select name="default_language" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                  <option value=""><?php echo _l('system_default_string'); ?></option>
                  <?php foreach(list_folders(APPPATH .'language') as $language){
                     $selected = '';
                     if(isset($client)){
                        if($client->default_language == $language){
                           $selected = 'selected';
                        }
                     }
                     ?>
                     <option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
                     <?php } ?>
                  </select>
               </div>
               <?php } ?>

            </div>
            <div class="col-md-6">
               <?php $value=( isset($client) ? $client->address : ''); ?>
               <?php echo render_textarea( 'address', 'client_address',$value); ?>
               <?php $value=( isset($client) ? $client->city : ''); ?>
               <?php echo render_input( 'city', 'client_city',$value); ?>
               <?php $value=( isset($client) ? $client->state : ''); ?>
               <?php echo render_input( 'state', 'client_state',$value); ?>
               <?php $value=( isset($client) ? $client->zip : ''); ?>
               <?php echo render_input( 'zip', 'client_postal_code',$value); ?>
               <?php $countries= get_all_countries();
               $customer_default_country = get_option('customer_default_country');
               $selected =( isset($client) ? $client->country : $customer_default_country);
               echo render_select( 'country',$countries,array( 'country_id',array( 'short_name')), 'clients_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
               ?>
            </div>
         </div>
      </div>
      <?php if(isset($client)){ ?>
      <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') && $this->input->get('tab') == 'contacts'){echo ' active';}; ?>" id="contacts">
         <?php if(has_permission('customers','','create') || is_customer_admin($client->userid)){
            $disable_new_contacts = false;
            if(is_empty_customer_company($client->userid) && total_rows('tblcontacts',array('userid'=>$client->userid)) == 1){
               $disable_new_contacts = true;
            }
            ?>
            <div class="inline-block new-contact-wrapper" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>"<?php if($disable_new_contacts){ ?> data-toggle="tooltip"<?php } ?>>
               <a href="#" onclick="contact(<?php echo $client->userid; ?>); return false;" class="btn btn-info new-contact mbot25<?php if($disable_new_contacts){echo ' disabled';} ?>"><?php echo _l('new_contact'); ?></a>
            </div>
            <?php } ?>
            <?php
            $table_data = array(_l('client_firstname'),_l('client_lastname'),_l('client_email'),_l('contact_position'),_l('client_phonenumber'),_l('contact_active'),_l('clients_list_last_login'));
            $custom_fields = get_custom_fields('contacts',array('show_on_table'=>1));
            foreach($custom_fields as $field){
               array_push($table_data,$field['name']);
            }
            array_push($table_data,_l('options'));
            echo render_datatable($table_data,'contacts'); ?>
         </div>
          
         <div role="tabpanel" class="tab-pane" id="customer_admins">
            <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
            <a href="#" data-toggle="modal" data-target="#customer_admins_assign" class="btn btn-info mbot30"><?php echo _l('assign_admin'); ?></a>
            <?php } ?>
            <table class="table dt-table">
               <thead>
                  <tr>
                     <th><?php echo _l('staff_member'); ?></th>
                     <th><?php echo _l('customer_admin_date_assigned'); ?></th>
                     <?php if(has_permission('customers','','create') || has_permission('customers','','edit')){ ?>
                     <th><?php echo _l('options'); ?></th>
                     <?php } ?>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach($customer_admins as $c_admin){ ?>
                  <tr>
                     <td><a href="<?php echo admin_url('profile/'.$c_admin['staff_id']); ?>">
                        <?php echo staff_profile_image($c_admin['staff_id'], array(
                           'staff-profile-image-small',
                           'mright5'
                        ));
                        echo get_staff_full_name($c_admin['staff_id']); ?></a>
                     </td>
                     <td data-order="<?php echo $c_admin['date_assigned']; ?>"><?php echo _dt($c_admin['date_assigned']); ?></td>
                     <?php if(has_permission('customers','','create') || has_permission('customers','','edit')){ ?>
                     <td>
                        <a href="<?php echo admin_url('clients/delete_customer_admin/'.$client->userid.'/'.$c_admin['staff_id']); ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                     </td>
                     <?php } ?>
                  </tr>
                  <?php } ?>
               </tbody>
            </table>
         </div>
		 
		 <div role="tabpanel" class="tab-pane" id="inventory_profile">
            <?php if (getAdmin() || has_permission('inventory_items', '', 'create')) { ?>
            <a data-toggle="modal" data-target="#sales_inventory_modal" onclick="add_inventory(<?php echo $customer_id; ?>)" class="btn btn-info"><?php echo _l('Add Inventory'); ?></a>
            <div class="clearfix"></div>
          <hr class="hr-panel-heading" />
            <table class="table dt-table">
               <thead>
                   <tr>
                     <th><?php echo _l('type_Of_hardware'); ?></th>
                     <th><?php echo _l('serial_number'); ?></th>
                     <th><?php echo _l('who_owns_the_equipment'); ?></th>
                     <th><?php echo _l('status'); ?></th>
                     <th><?php echo _l('date_in'); ?></th>
                     <th><?php echo _l('waranty_expiration_date'); ?></th>
                     <?php if(getAdmin() || has_permission('inventory_items','','delete') || has_permission('inventory_items','','edit')){ ?>
                     <th><?php echo _l('options'); ?></th>
                     <?php } ?>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach($client_inventory as $c_admin){ ?>
                  <tr>
                      <td><?php echo $c_admin['hardwarevalue']; ?></td>
                      <td><a href="<?php echo base_url();?>admin/inventory_items/notes/<?php echo $c_admin['inventory_id']; ?>"><?php echo $c_admin['serial_number']; ?></a></td>
                      <td><?php echo $c_admin['ownervalue']; ?></td>
                      <td><?php echo $c_admin['statusvalue']; ?></td>
                      <td><?php echo date('m/d/Y', strtotime($c_admin['date_in'])); ?></td>
                      <td><?php echo date('m/d/Y', strtotime($c_admin['exp_date'])); ?></td>
                     <?php if(getAdmin() || has_permission('inventory_items','','edit')){ ?>
                     <td>
                        <?php if (getAdmin() || has_permission('inventory_items', '', 'edit')) { ?>
									 <a href="" class="btn btn-default btn-icon" data-toggle="modal" data-target="#sales_inventory_modal" onclick="edit_inventory(this.id)" data-id="<?php echo $c_admin['inventory_id']; ?>" id="<?php echo $c_admin['inventory_id']; ?>"><i class="fa fa-pencil-square-o"></i></a>
                        <?php } ?>
                        <?php if (getAdmin() || has_permission('inventory_items', '', 'delete')) { ?>
                         <a href="<?php echo base_url()?>admin/inventory/delete/<?php echo $c_admin['inventory_id']; ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                        <?php } ?>
                     </td>
                     <?php } ?>
                  </tr>
                  <?php } ?>
               </tbody>
            </table>
            <?php }   
               
			   // $this->load->view('admin/inventory/add_inventory_pro');
            ?>
           
</script>

         </div>
		 
         <?php } ?>
         <div role="tabpanel" class="tab-pane" id="billing_and_shipping">
            <div class="row">
               <div class="col-md-12">
                  <div class="row">
                     <div class="col-md-6">
                        <h4 class="no-mtop"><?php echo _l('billing_address'); ?> <a href="#" class="pull-right billing-same-as-customer"><small class="font-medium-xs"><?php echo _l('customer_billing_same_as_profile'); ?></small></a></h4>
                        <hr />
                        <?php $value=( isset($client) ? $client->billing_street : ''); ?>
                        <?php echo render_textarea( 'billing_street', 'billing_street',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_city : ''); ?>
                        <?php echo render_input( 'billing_city', 'billing_city',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_state : ''); ?>
                        <?php echo render_input( 'billing_state', 'billing_state',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_zip : ''); ?>
                        <?php echo render_input( 'billing_zip', 'billing_zip',$value); ?>
                        <?php $selected=( isset($client) ? $client->billing_country : '' ); ?>
                        <?php echo render_select( 'billing_country',$countries,array( 'country_id',array( 'short_name')), 'billing_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                     </div>
                     <div class="col-md-6">
                        <h4 class="no-mtop">
                           <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('customer_shipping_address_notice'); ?>"></i>
                           <?php echo _l('shipping_address'); ?> <a href="#" class="pull-right customer-copy-billing-address"><small class="font-medium-xs"><?php echo _l('customer_billing_copy'); ?></small></a>
                        </h4>
                        <hr />
                        <?php $value=( isset($client) ? $client->shipping_street : ''); ?>
                        <?php echo render_textarea( 'shipping_street', 'shipping_street',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_city : ''); ?>
                        <?php echo render_input( 'shipping_city', 'shipping_city',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_state : ''); ?>
                        <?php echo render_input( 'shipping_state', 'shipping_state',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_zip : ''); ?>
                        <?php echo render_input( 'shipping_zip', 'shipping_zip',$value); ?>
                        <?php $selected=( isset($client) ? $client->shipping_country : '' ); ?>
                        <?php echo render_select( 'shipping_country',$countries,array( 'country_id',array( 'short_name')), 'shipping_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                     </div>
                     <?php if(isset($client) &&
                     (total_rows('tblinvoices',array('clientid'=>$client->userid)) > 0 || total_rows('tblestimates',array('clientid'=>$client->userid)) > 0 || total_rows('tblcreditnotes',array('clientid'=>$client->userid)) > 0)){ ?>
                     <div class="col-md-12">
                        <div class="alert alert-warning">
                           <div class="checkbox checkbox-default">
                              <input type="checkbox" name="update_all_other_transactions" id="update_all_other_transactions">
                              <label for="update_all_other_transactions">
                                 <?php echo _l('customer_update_address_info_on_invoices'); ?><br />
                              </label>
                           </div>
                           <b><?php echo _l('customer_update_address_info_on_invoices_help'); ?></b>
                           <div class="checkbox checkbox-default">
                              <input type="checkbox" name="update_credit_notes" id="update_credit_notes">
                              <label for="update_credit_notes">
                                 <?php echo _l('customer_profile_update_credit_notes'); ?><br />
                              </label>
                           </div>
                        </div>
                     </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php echo form_close(); ?>
</div>
<div id="contact_data"></div>
<?php if(isset($client)){ ?>
<?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
<div class="modal fade" id="customer_admins_assign" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('clients/assign_admins/'.$client->userid)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('assign_admin'); ?></h4>
         </div>
         <div class="modal-body">
            <?php
            $selected = array();
            foreach($customer_admins as $c_admin){
               array_push($selected,$c_admin['staff_id']);
            }
            echo render_select('customer_admins[]',$staff,array('staffid',array('firstname','lastname')),'',$selected,array('multiple'=>true),array(),'','',false); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
      </div>
      <!-- /.modal-content -->
      <?php echo form_close(); ?>
   </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php } ?>
<?php } ?>
<?php $this->load->view('admin/clients/client_group'); ?>
<script>
var values ='';
//**** umer farooq chattha Start****//
    function add_inventory(e)
    {
        $("#inventory_id").val(0);
        $("#account123").val(e).change();
        //$("select[name='type_of_hardware']").val(dt[0].type_of_hardware).change();
        $("#serial_number").val('');
        $("select[name='type_of_hardware']").val('').change();
        $("input[name='date_in']").val('');
        $("select[name='status']").val('').change();
        $("select[name='origin']").val('').change();
        $("img.image").hide();
        $("img.image").attr('src','');
        $("select[name='equipment_owner']").val('').change();
        $("input[name='manufacturer']").val('');
        $("select[name='warranty_expiration_date']").val('').change();
        $("#description").val('');
        $("input[name='inventory_id']").val('');
        $("input[name='inventory_id']").val('');
        $("input[name='inventory_id']").val('');
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
                $("#serial_number").val(dt.inventory_res.serial_number);
                $("select[name='type_of_hardware']").val(dt.inventory_res.type_of_hardware).change();
                $("input[name='date_in']").val(dt.inventory_res.date_in);
                $("select[name='status']").val(dt.inventory_res.status).change();
                $("select[name='origin']").val(dt.inventory_res.origin).change();
                $("img.image").show();
                $("img.image").attr('src',dt.inventory_res.image);
                $("select[name='equipment_owner']").val(dt.inventory_res.equipment_owner).change();
                $("input[name='manufacturer']").val(dt.inventory_res.manufacturer);
                $("select[name='warranty_expiration_date']").val(dt.inventory_res.warranty_expiration_date).change();
                if(dt.inventory_res.warranty_expiration_date==10)
                {
                    $("#exp_no").val(dt.inventory_res.custome_nu);
                    $("#exp_type").val(dt.inventory_res.custome_type);
                    $("div#expiration_fields").show();
                }
                $("input[name='img_clone']").val(0);
                $("#description").val(dt.inventory_res.description);
                $("input[name='inventory_id']").val(dt.inventory_res.inventory_id);
                $("input[name='inventory_id']").val(dt.inventory_res.inventory_id);
                $("input[name='inventory_id']").val(dt.inventory_res.inventory_id);
				},1000);
            }
        });
    }
</script>
