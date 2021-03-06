<?php 
init_head(); 
?>
<style>
#ribbon_<?php echo $ticket->ticketid; ?> span::before {
  border-top: 3px solid <?php echo $ticket->statuscolor; ?>;
  border-left: 3px solid <?php echo $ticket->statuscolor; ?>;
}
</style>
<?php set_ticket_open($ticket->adminread,$ticket->ticketid); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
	  <?php 
    // $co = get_company_contact_tickets($ticket->userid);
//      
//     $all_rec = explode(',', $ticket->contactid);
// echo "<pre>"; print_r($all_rec); exit('lplp');
      // echo "<pre>"; print_r($co); exit('lplp');
    // echo "<pre>"; print_r($ticket); exit('lplp');?>
        <div class="panel_s">
          <div class="panel-body">
            <?php //echo "<pre>"; print_r($ticket);exit;?>
            <?php //echo '<div class="ribbon" id="ribbon_'.$ticket->ticketid.'"><span style="background:'.$ticket->statuscolor.'">'.ticket_status_translate($ticket->ticketstatusid).'</span></div>'; ?>
            <?php echo '<div class="ribbon" id="ribbon_'.$ticket->ticketid.'"><span style="background:'.$ticket->statuscolor.'">'.ticket_status_translates($ticket->ticketstatusid).'</span></div>'; ?>
            <ul class="nav nav-tabs no-margin" role="tablist">
              <li role="presentation" class="<?php if(!$this->session->flashdata('active_tab')){echo 'active';} ?>">
                <a href="#addreply" aria-controls="addreply" role="tab" data-toggle="tab">
                  <?php echo _l('ticket_single_add_reply'); ?>
                </a>
              </li>
              <!-- <li role="presentation">
                <a href="#note" aria-controls="note" role="tab" data-toggle="tab">
                  <?php //echo _l('ticket_single_add_note'); ?>
                </a>
              </li> -->
              <li role="presentation">
                <a href="#othertickets" onclick="init_table_tickets(true);" aria-controls="othertickets" role="tab" data-toggle="tab">
                  <?php echo _l('ticket_single_other_user_tickets'); ?>
                </a>
              </li>
              <li role="presentation">
                <a href="#tasks" onclick="init_rel_tasks_table(<?php echo $ticket->ticketid; ?>,'ticket'); return false;" aria-controls="tasks" role="tab" data-toggle="tab">
                  <?php echo _l('tasks'); ?>
                </a>
              </li>
              <!-- <li role="presentation" >
                <a href="#contacts"  aria-controls="tasks" role="tab" data-toggle="tab" onclick="init_rel_contacts_table('contacts'); return false;">

                  Contacts<?php //echo _l('ticket_single_settings'); ?>
                </a>
              </li> -->
              <!-- contacts -->
              <!-- sajid -->
              <?php 
              // echo "<pre>"; print_r($replies); exit('inin'); 
              if(isset($client))
                 // echo '<pre>';print_r($client ); exit;
              { ?>
                <li role="presentation<?php if($this->input->get('tab') && $this->input->get('tab') == 'contacts'){echo ' active';}; ?>">
                  <a href="#contacts" aria-controls="contacts" role="tab" data-toggle="tab">
                    <?php if(is_empty_customer_company($client->userid)) {
                      echo _l('Contacts');
                    } else {
                      echo _l( 'customer_contacts');
                    }
                  }
                  ?>
                </a>
              </li>
              <li role="presentation" >
                <a href="#inventory"  aria-controls="tasks" role="tab" data-toggle="tab">
                  <!-- <a href="#settings" aria-controls="settings" role="tab" data-toggle="tab"> -->
                    Inventory<?php //echo _l('ticket_single_settings'); ?>
                  </a>
                </li>
                 <li role="presentation">
                  <a href="#reminders" aria-controls="reminder" role="tab" data-toggle="tab">
                    <?php //echo _l('ticket_single_add_note'); ?> Reminders
                  </a>
                </li>
                <li role="presentation" class="<?php if($this->session->flashdata('active_tab_settings')){echo 'active';} ?>">
                  <a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">
                    <?php echo _l('ticket_single_settings'); ?>
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div class="panel_s">
            <div class="panel-body">
              <div class="row">
                <div class="col-md-8">
                  <h3 class="mtop5 mbot10">
                    <span id="ticket_subject">
                     <a href="<?php echo admin_url('clients/client/'.$ticket->userid)?>">
                      <?php echo $ticket->company; ?></a> - #<?php echo $ticket->ticketid; ?> <?php echo $ticket->subject; ?> 
                    </span>
                    <?php if($ticket->project_id != 0){
                      echo '<br /><small>'._l('ticket_linked_to_project','<a href="'.admin_url('projects/view/'.$ticket->project_id).'">'.get_project_name_by_id($ticket->project_id).'</a>') .'</small>';
                    } ?>
                  </h3>
                  <!-- sajid -->
                  <h4 class="mtop5 mbot10">
                    <p>Ticket Creation date: <?php echo date('m/d/Y',strtotime($ticket->date)); ?> | Merchant's Phone Number: <?php echo  $ticket->merchantsPhonenumber ;?></p>
                  </h4>
                    
                    <!-- abrar -->
                    <?php
                    $contactid=explode(',', $ticket->contactid);
                    foreach($company_contacts as $contacts){
                    if(in_array($contacts->id, $contactid)){?>
                    <p> <?php echo '<b>'.$contacts->firstname.' '.$contacts->lastname.'<b>'.' - '.$contacts->title. ' - '.$contacts->phonenumber.' - '.$contacts->email;?></p>
                    <?php } }?>
                    <!--<p><?php
                    // echo "<pre>";print_r($ticket);exit();
                    // echo '<b>'.$ticket->firstname.' '.$ticket->lastname.'<b>'.' - '.$ticket->title. ' - '.$ticket->phonenumber.' - '.$ticket->email; ?></p>-->
                    <!-- abrar -->
                  
                  </div>
                  <!-- sajid -->
                  <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'single-ticket-form','novalidate'=>true)); ?>
                  <div class="col-md-4 text-right">
                    <div class="row">
                      <div class="col-md-6 col-md-offset-6">
                        <?php echo render_select('status',$statuses,array('ticketstatusid','name'),'ticket_single_change_status',$ticket->status,array(),array(),'','',false); //exit;?>
                      </div>
                      <?php //echo render_select('status_top',$statuses,array('ticketstatusid','name'),'',$ticket->status,array(),array(),'','',false); ?>

                    </div>
                  </div>
                  <div class="clearfix"></div>
                </div>
                <div class="tab-content">
                <div role="tabpanel" class="tab-pane <?php if(!$this->session->flashdata('active_tab')){echo 'active';} ?>" id="addreply">
                    <hr class="no-mtop" />
					<h4><b>Important Ticket Notes</b></h4>
                   
                  <?php if(!empty($replies)){?>
                    <div class="panel_s">
                    <div class="panel-body" style="background-color: #ffffe6;">
                    <?php foreach($replies as $reply){ ?>
                    <p style="color:blue;"><?php echo get_pic_icon($reply['admin']); ?> Ticket note by <?php echo get_staff_full_name($reply['admin']);?> 
                    <a style="float:right;" href="<?php echo admin_url('tickets/delete_ticket_reply/'.$reply['ticketid'] .'/'.$reply['id']); ?>" class="btn btn-danger _delete btn-ticket-label mright5"><i class="fa fa-remove"></i></a>
                    <a style="float:right;" href="#" class="btn btn-default btn-ticket-label btn-icon" onclick="edit_ticket_message(<?php echo $reply['id']; ?>,'reply'); return false;"><i class="fa fa-pencil-square-o"></i></a>
                    <div data-reply-id="<?php echo $reply['id']; ?>" class="tc-content">
                    <?php echo check_for_links($reply['message']); ?>
                    </div>
                    <small>Note added: <?php echo date('m/d/Y',strtotime($reply['date'])); ?></small><p></p>
                    <?php } ?>
                    </div>
                    </div>
                  <?php } ?>

                    <?php $tags = get_tags_in($ticket->ticketid,'ticket');  ?>
                    <?php if(count($tags) > 0){ ?>
                      <div class="row">
                        <div class="col-md-12">
                          <?php echo '<b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b><br /><br /> ' . render_tags($tags); ?>
                          <hr />
                        </div>
                      </div>
                    <?php } ?>
                    <?php if(sizeof($ticket->ticket_notes) > 0){ ?>
                      <div class="row">
                        <div class="col-md-12 mbot15">
                          <h4 class="bold"><?php echo _l('ticket_single_private_staff_notes'); ?></h4>
                          <div class="ticketstaffnotes">
                            <div class="table-responsive">
                              <table>
                                <tbody>
                                  <?php foreach($ticket->ticket_notes as $note){ ?>
                                    <tr>
                                      <td>
                                        <span class="bold">
                                          <?php echo staff_profile_image($note['addedfrom'],array('staff-profile-xs-image')); ?> <a href="<?php echo admin_url('staff/profile/'.$note['addedfrom']); ?>"><?php echo _l('ticket_single_ticket_note_by',get_staff_full_name($note['addedfrom'])); ?>
                                        </a>
                                      </span>
                                      <?php
                                      if($note['addedfrom'] == get_staff_user_id() || is_admin()){ ?>
                                        <div class="pull-right">
                                          <a href="#" class="btn btn-default btn-icon" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
                                          <a href="<?php echo admin_url('misc/delete_note/'.$note["id"]); ?>" class="mright10 _delete btn btn-danger btn-icon">
                                            <i class="fa fa-remove"></i>
                                          </a>
                                        </div>
                                      <?php } ?>
                                      <hr class="hr-10" />
                                      <div data-note-description="<?php echo $note['id']; ?>">
                                        <?php echo $note['description']; ?>
                                      </div>
                                      <div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide inline-block full-width">
                                        <textarea name="description" class="form-control" rows="4"><?php echo clear_textarea_breaks($note['description']); ?></textarea>
                                        <div class="text-right mtop15">
                                          <button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                          <button type="button" class="btn btn-info" onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                                        </div>
                                      </div>
                                      <small class="bold">
                                        <?php echo _l('ticket_single_note_added',_dt($note['dateadded'])); ?>
                                      </small>
                                    </td>
                                  </tr>
                                <?php } ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                  <div>

                    <input type="hidden" value="0" id="knowledgeBase" name="knowledgeBase">
                    <a href="<?php echo admin_url('tickets/delete/'.$ticket->ticketid); ?>" class="btn btn-danger _delete btn-ticket-label mright5">
                      <i class="fa fa-remove"></i>
                    </a>
                    <?php if(!empty($ticket->priority_name)){ ?>
                      <span class="ticket-label label label-default inline-block">
                        <?php echo _l('ticket_single_priority',ticket_priority_translate($ticket->priorityid)); ?>
                      </span>
                    <?php } ?>
                    <?php if(!empty($ticket->service_name)){ ?>
                      <span class="ticket-label label label-default inline-block">
                        <?php echo _l('service'). ': ' . $ticket->service_name; ?>
                      </span>
                    <?php } ?>
                    <?php echo form_hidden('ticketid',$ticket->ticketid); ?>
                    <span class="ticket-label label label-default inline-block">
                      <?php echo _l('department') . ': '. $ticket->department_name; ?>
                    </span>
                    <?php if($ticket->assigned != 0){ ?>
                      <span class="ticket-label label label-info inline-block">
                        <?php echo _l('ticket_assigned'); ?>: <?php echo get_staff_full_name_for_tickets($ticket->assigned); ?>
                      </span>
                    <?php } ?>
                      <span class="ticket-label label label-info inline-block">
                        <?php $val = ''; 
                        $val = get_ticket_followers_full_name($ticket->followers); 
                        if($val){?>
                            Followers: <?php echo $val; ?>
                        <?php ;}
                        else{?>
                            No followers selected:
                        <?php ; }?>
                        
                        
                      </span>
                    
                    <?php if($ticket->lastreply !== NULL){ ?>
                      <span class="ticket-label label label-success inline-block" data-toggle="tooltip" title="<?php echo _dt($ticket->lastreply); ?>">
                        <span class="text-has-action">
                          <?php echo _l('ticket_single_last_reply',time_ago($ticket->lastreply)); ?>
                        </span>
                      </span>
                    <?php } ?>
                    <div class="mtop15">
                      <?php echo render_textarea('message','','',array(),array(),'','tinymce'); ?>
                    </div>
                    <div class="panel_s ticket-reply-tools">
                      <div class="btn-bottom-toolbar text-right">
                        <button type="button" class="btn btn-info" id="single-ticket-form-knowledge" data-form="#single-ticket-form-knowledge" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>">
                          <?php echo _l('ticket_single_add_response_knowledge'); ?>
                        </button>
                        <button type="submit" class="btn btn-info" id="single-ticket-form-submit" data-form="#single-ticket-form" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>">
                          <?php echo _l('ticket_single_add_response'); ?>
                        </button>
                      </div>
                      <div class="panel-body">
                        <div class="row">
                          <!-- abrar -->
                          <div id="categories">
                          <input type="hidden" id="checkbox_id" name="email_reply[]" value="">
                          </div>
                          <!-- abrar -->
                          <div class="col-md-5">
                            <!-- abrar -->
                            <h3><?php echo _l('Email To'); ?><h3>
                            <?php
                            $contactid=explode(',', $ticket->contactid);
                            foreach($company_contacts as $contacts){
                            if(in_array($contacts->id, $contactid)){?>
                            <div class="checkbox">
                            <input type="checkbox" name="email_contacts_ids[]" id="<?php echo $contacts->id; ?>" onclick="send_email_reply('<?php echo $contacts->id;?>');" value="<?php echo $contacts->email ?>"> <label for="Email to"><?php echo _l('<b>'.$contacts->firstname.' '.$contacts->lastname.' - '.$contacts->title. ' - '.$contacts->phonenumber.' - '.$contacts->email.'</b>'.'<br>'); ?></label> 


                            </div>
                            <?php } }?>

                            <!-- abrar -->

                            <?php echo render_input('cc','CC'); ?>
                            <?php if($ticket->assigned !== get_staff_user_id()){ ?>
                              <div class="checkbox">
                                <input type="checkbox" name="assign_to_current_user" id="assign_to_current_user">
                                <label for="assign_to_current_user"><?php echo _l('ticket_single_assign_to_me_on_update'); ?></label>
                              </div>
                            <?php } ?>
                            <div class="checkbox">
                              <input type="checkbox" <?php echo do_action('ticket_add_response_and_back_to_list_default','checked'); ?> name="ticket_add_response_and_back_to_list" value="1" id="ticket_add_response_and_back_to_list">
                              <label for="ticket_add_response_and_back_to_list"><?php echo _l('ticket_add_response_and_back_to_list'); ?></label>
                            </div>
                            <div class="checkbox">
                              <input type="checkbox" value="1" name="pin_to_top" id="pin_to_top">
                              <label for="pin_to_top">Pin to the top</label>
                            </div>
                              <!-- <div class="checkbox">
                              <input type="checkbox" name="status_show_or_not" id="status_show_or_not" value="1">
                              <label for="Notify the client"><?php //echo _l('Notify the client'); ?></label>
                              </div> -->
                          </div>
                          <?php
                          $use_knowledge_base = get_option('use_knowledge_base');
                          ?>
                          <div class="col-md-7 _buttons mtop20">
                            <?php
                            $use_knowledge_base = get_option('use_knowledge_base');
                            ?>
                            <select id="insert_predefined_reply" data-live-search="true" class="selectpicker mleft10 pull-right" data-title="<?php echo _l('ticket_single_insert_predefined_reply'); ?>">

                              <?php foreach($predefined_replies as $predefined_reply){ ?>
                                <option value="<?php echo $predefined_reply['id']; ?>"><?php echo $predefined_reply['name']; ?></option>
                              <?php } ?>
                            </select>
                            <?php if($use_knowledge_base == 1){ ?>
                              <?php $groups = get_all_knowledge_base_articles_grouped($only_customers=FALSE); ?>
                              <select id="insert_knowledge_base_link" class="selectpicker pull-right" data-live-search="true" onchange="insert_ticket_knowledgebase_link(this);" data-title="<?php echo _l('ticket_single_insert_knowledge_base_link'); ?>">
                                <option value=""></option>
                                <?php foreach($groups as $group){ ?>
                                  <?php if(count($group['articles']) > 0){ ?>
                                    <optgroup label="<?php echo $group['name']; ?>">
                                      <?php foreach($group['articles'] as $article) { ?>
                                        <option value="<?php echo $article['articleid']; ?>">
                                          <?php 
                                          echo $article['subject']; 
                                          $articleDesc = strip_tags($article['description']);

                                          echo substr($articleDesc, 0, 20);
                                    //$pieces = explode(" ", $article['description']);
                                    //echo $pieces[0].' '.$pieces[1].' '.$pieces[2].'.....';
                                          ?>
                                        </option>
                                      <?php } ?>
                                    </optgroup>
                                  <?php } ?>
                                <?php } ?>
                              </select>
                            <?php } ?>
                          </div>
                        </div>
                        <hr />
                        <div class="row attachments">
                          <div class="attachment">
                            <div class="col-md-5 mbot15">
                              <div class="form-group">
                                <label for="attachment" class="control-label">
                                  <?php echo _l('ticket_single_attachments'); ?>
                                </label>
                                <div class="input-group">
                                  <input type="file" id = "preview1" extension="<?php echo str_replace('.','',get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control attachments_d" name="attachments[]" onchange="readURL(this)" accept="<?php echo get_ticket_form_accepted_mimes(); ?>" >
                                  <img class="prev1" id="prev1" src="#" name="img_attachments[]" style="display:none;">
                                  <span class="input-group-btn">
                                    <button class="btn btn-success add_more_attachments_d p8-half" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                  </span>
                                </div>
                              </div>
                            </div>
                            <div class="clearfix"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php echo form_close(); ?>
                  </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="note">
                  <hr class="no-mtop" />
                  <div class="form-group">
                    <label for="note_description"><?php echo _l('ticket_single_note_heading'); ?></label>
                    <textarea class="form-control" name="note_description" rows="5"></textarea>
                  </div>
                  <a class="btn btn-info pull-right add_note_ticket"><?php echo _l('ticket_single_add_note'); ?></a>
                </div>
                <div role="tabpanel" class="tab-pane" id="reminders">
                  <a href="#" data-toggle="modal" data-target=".reminder-modal-customer-<?php echo $ticket->userid; ?>" class="btn btn-info mbot25"><i class="fa fa-bell-o"></i> <?php echo _l('set_reminder'); ?></a>
                  <?php 
                    $this->load->view('admin/includes/modals/reminder',array('id'=>$ticket->userid,'name'=>'customer','members'=>$staff,'reminder_title'=>_l('set_reminder')));
                   //echo admin_url('tickets/ticket?107'); echo "<pre>"; print_r($reminders); 
                   ?>
                  <hr class="no-mtop" />
                  <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified'), _l( 'options'), ), 'reminders');
                        // $this->load->view('admin/includes/modals/reminder',array('id'=>$ticket->userid,'name'=>'customer','members'=>$members,'reminder_title'=>_l('set_reminder')));
                    ?>
                </div>

                <div role="tabpanel" class="tab-pane" id="othertickets">
                  <hr class="no-mtop" />
                  <div class="_filters _hidden_inputs hidden tickets_filters">
                    <?php echo form_hidden('filters_ticket_id',$ticket->ticketid); ?>
                    <?php echo form_hidden('filters_email',$ticket->email); ?>
                    <?php echo form_hidden('filters_userid',$ticket->userid); ?>
                  </div>
                  <?php echo AdminTicketsTableStructure(); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="tasks">
                  <hr class="no-mtop" />
                  <?php init_relation_tasks_table(array('data-new-rel-id'=>$ticket->ticketid,'data-new-rel-type'=>'ticket')); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="contacts">

                  <!-- sajid -->
                  <hr class="no-mtop" />
                  <div class="inline-block new-contact-wrapper" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>"<?php if($disable_new_contacts){ ?> data-toggle="tooltip"<?php } ?>>
                    <a href="contact" onclick="contact(<?php echo $ticket->userid; ?>); return false;" class="btn btn-info new-contact mbot25<?php if($disable_new_contacts){echo ' disabled';} ?>"><?php echo _l('new_contact'); ?></a>
                  </div>

                  <table class="table dt-table" id="contacts">
                    <thead>
                      <tr>
                        <th><?php echo _l('client_firstname'); ?></th>
                        <th><?php echo _l('client_lastname'); ?></th>
                        <th><?php echo _l('client_email'); ?></th>
                        <th><?php echo _l('contact_position'); ?></th>
                        <th><?php echo _l('client_phonenumber'); ?></th>
                        <!-- <th><?php //echo _l('contact_active'); ?></th> -->
                        <th><?php echo _l('clients_list_last_login'); ?></th>
                        <th><?php echo _l('options'); ?></th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($client_contacts as $clients){ ?>
                        <tr>
                          <td><?php echo $clients['firstname']; ?></td>
                          <td><?php echo $clients['lastname']; ?></td>
                          <td><?php echo $clients['email']; ?></td>
                          <td><?php echo $clients['title']; ?></td>
                          <td><?php echo $clients['phonenumber']; ?></td>
                          <!-- <td><?php //echo $clients['active']; ?></td> -->
                          <td>
							  <?php 
									if($clients['last_login'] !=''){
                        echo time_ago($clients['last_login']);
										//echo date('m/d/Y',strtotime($clients['last_login']));
									}							  
							  ?>
						  </td>
                          <td><a href="#" class="btn btn-default btn-icon" onclick="contact(<?php echo $clients['userid'] ?> 
                          ,<?php echo $clients['id']?>);return false;"><i class="fa fa-pencil-square-o"></i></a><a href="
                          <?php echo admin_url('clients/delete_contacts_rel_clients/'. $clients['company_id'].'/'.$clients['contact_id'].'/'.$ticket->ticketid ); ?>
                          " class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a></td>
                        </div>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
                <!-- sajid -->

                <?php 

                  //init_relation_tasks_table(array('data-new-rel-id'=>$ticket->ticketid,'data-new-rel-type'=>'ticket')); //exit; 
                  //render_datatable($table_data,'contacts');?>
                  <?php $this->load->view('admin/clients/client_js'); ?>

                  <hr class="no-mtop" />
                </div>
                <div role="tabpanel" class="tab-pane" id="inventory">
                  <hr class="no-mtop" />
                  <?php if(getAdmin() || has_permission('inventory', '', 'create')) { ?>

                    <a href="#" class="btn btn-info pull-left" onclick="add_inventory(<?php echo $ticket->userid ; ?>)" data-toggle="modal" data-target="#sales_inventory_modal"><?php echo _l('New Inventory'); ?></a>
                  <?php }?>
                  <div class="clearfix"></div>
                  <hr class="hr-panel-heading" />
                  <!-- here -->

                  <table class="table dt-table" id="inventory">
                    <thead>
                      <tr>
                        <th><?php echo _l('inventory_add_typeofhardware'); ?></th>
                        <th><?php echo _l('serial_number'); ?></th>
                        <th><?php echo _l('inventory_add_whoownstheequipment'); ?></th>
                        <th><?php echo _l('status'); ?></th>
                        <th><?php echo _l('date_in'); ?></th>
                        <th><?php echo _l('inventory_add_warrantyexpirationdate'); ?></th>
                        <th><?php echo _l('actions'); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($s_inventory as $clients){ ?>
                        <tr>
                          <td><?php echo $clients['hardwarevalue']; ?></td>
                          <td>
							 <a href= "<?php echo base_url()?>admin/inventory_items/notes/<?php echo $clients['inventory_id'];?>">
							  	<?php echo $clients['serial_number']; ?>
							  </a>
						  </td>
                          <td><?php echo $clients['ownervalue']; ?></td>
                          <td><?php echo $clients['statusvalue']; ?></td>
                          <td><?php echo date('m/d/Y',strtotime($clients['date_in'])); ?></td>
                          <td>
							  <?php 
								  if ($clients['exp_date'] == 'No Warranty')
									{
									  echo $clients['exp_date']; 
									}
								  else 
									{
									  echo date('m-d-Y', strtotime($clients['exp_date']));
									}						  
							  ?> 
						  </td>
                          <td><a href="#" class="btn btn-default btn-icon" data-toggle="modal" data-target="#sales_inventory_modal" onclick="edit_inventory(<?php echo $clients['inventory_id']; ?>)"><i class="fa fa-pencil-square-o"></i></a> 
                            <a href="<?php echo base_url(); ?>admin/inventory/delete/<?php echo $clients['inventory_id']; ?>/<?php echo $ticket->ticketid; ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a></td>
                          </div>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                  <!-- sajid -->

                  <!-- to there -->
                  
                  <?php 
                  if (getAdmin() || has_permission('inventory', '', 'edit') || has_permission('inventory', '', 'delete') ) 
                  {
                    $table_data = array(
                      _l('serial_number'),
                      _l('type_Of_hardware'),
                      _l("who_owns_the_equipment"),
                      _l('account'),
                      _l('notes'),
                      _l('Last Activity'),
                      _l('status'),
                      _l('date_in'),
                      _l("origin"),
                      _l("actions"));
                           // print_r($table_data); exit; 
                    //render_datatable($table_data,'sale-inventory-items');
                  }
                  else
                  {
                    $table_data = array(
                      _l('serial_number'),
                      _l('type_Of_hardware'),
                      _l("who_owns_the_equipment"),
                      _l('account'),
                      _l('notes'),
                      _l('status'),
                      _l('date_in'),
                      _l("origin"),
                    );
                    // render_datatable($table_data,'sale-inventory-items'); 
                  }

                  ?>
                  <?php $this->load->view('admin/inventory/add_inventory'); ?>

                </div>
                <div role="tabpanel" class="tab-pane <?php if($this->session->flashdata('active_tab_settings')){echo 'active';} ?>" id="settings">
                  <hr class="no-mtop" />
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo render_input('subject','ticket_settings_subject',$ticket->subject); ?>
					  <!-- waqas -->
					  <div class="form-group select-placeholder">
						<label for="userid">DBA Name</label>
						<select class="selectpicker form-control company" data-live-search="true" name="userid">
						<option></option>
						<?php foreach($compnay_data as $company){?>

						<option value="<?php echo $company['userid']; ?>" <?php if($company['userid'] == $ticket->userid) { echo 'selected';} ?>><?php echo $company['company']; ?></option>
						<?php }?>
						</select>
						<!-- <select name="contactid" required="true" id="contactid" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
						<?php //if(isset($contact)) { ?>
						<option value="<?php //echo $contact['id']."_".$contact['userid']; ?>" selected><?php //echo $contact['firstname']. ' ' .$contact['lastname']; ?></option>

						<!--<option value="<?php //echo $contact['id']; ?>" selected><?php //echo $contact['firstname']. ' ' .$contact['lastname']; ?></option>-->
						<?php //} ?>
						<!-- <option value=""></option> -->
						<!-- </select> -->
						<?php //echo form_hidden('userid'); ?>
					  </div>
					  
					  
                      <!--<div class="form-group select-placeholder">
                        <label for="contactid" class="control-label"><?php// echo _l('contact'); ?></label>
                        <select name="contactid" id="contactid" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php// echo _l('dropdown_non_selected_tex'); ?>">
                          <?php
                          //$rel_data = get_relation_data('contact',$ticket->contactid);
                          //$rel_val = get_relation_values($rel_data,'contact');
                          //echo '<option value="'.$rel_val['id'].'" selected data-subtext="'.$rel_val['subtext'].'">'.$rel_val['name'].'</option>';
                          ?>
                        </select>
                        <?php// echo form_hidden('userid',$ticket->userid); ?>
                      </div>-->
					  <?php //echo "<pre>"; print_r($ticket); exit('qwerty'); ?>
					  <div class="row">
						<!-- <div class="col-md-6"> -->
						<div class="col-md-6" id="hiden_contact_id">
							<label for="contactid">Contact</label>
							<select class="selectpicker form-control contacts_id" id="contactid" multiple data-actions-box="true">
                  <?php if(!empty($ticket->contactid)){
                  $all_rec = explode(',', $ticket->contactid);
                  // foreach($all_rec as $rec)
                  // {
                     $all_contacts_company = get_company_contact_tickets($ticket->userid);
                    if($all_contacts_company){
                      foreach ($all_contacts_company as $cont_val) {?>
                         <!-- cont_val() -->
                        <option data-email="<?php echo $cont_val['contact_id'];?>" value="<?php echo $cont_val['email'];?>" 
                          <?php foreach ($all_rec as $rec) {
                                    if(trim($rec) == trim($cont_val['contact_id']) ){ echo "selected";};} ?>
                          >
                          <?php if($cont_val['title'] != ''){
                                    echo $cont_val['firstname'].' '.$cont_val['lastname'] .' '.'|'.' '.$cont_val['title'];
                                  }
                                  else{
                                    echo $cont_val['firstname'].' '.$cont_val['lastname'] ;
                                  }
                       } ?>
                      
                   <?php  ; } 
                    // get_company_contact_tickets($ticket->userid);
                    //$stri = get_contacts_firstname($rec); ?>
                    <!-- <option data-email="<?php //echo $rec;?>" value="<?php //echo $stri['email'];?>" selected><?php //echo $stri['firstname'];?></option> -->
                    <?php
                  // }

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
						<?php 
							$all_email = explode(',', $ticket->contactid);
							//print_r($all_email); exit('qqq'); 
							$stri = '';
							foreach($all_email as $emails){
								$stri .= get_contacts_email_tickets($emails) . ',';
							}	
							$stri = trim($stri, ',' );
							//print_r($stri); exit('ok');
						
						?>
							<label for="contacts_shows_now">Email address</label>
							<input type='text' class="form-control" value="<?php if($stri !=''){ echo $stri; }?>" readonly id="contacts_shows_now" >
							<input type='hidden' class="form-control" value="<?php if($stri !=''){ echo $ticket->contactid; }?>" id="contacts_hidden_id" name="contactid[]">
						<?php //echo render_input('email','ticket_settings_email','','email',array('disabled'=>true)); ?>
						</div>
					  </div>
						<br>
                     <!-- <div class="row">
                        <div class="col-md-6">
                          <?php //echo render_input('to','ticket_settings_to',$ticket->submitter,'text',array('disabled'=>true)); ?>
                        </div>
                        <div class="col-md-6">
                          <?php
                          //if($ticket->userid != 0){
                            //echo render_input('email','ticket_settings_email',$ticket->email,'email',array('disabled'=>true));
                          //} else {
                            //echo render_input('email','ticket_settings_email',$ticket->ticket_email,'email',array('disabled'=>true));
                          //}
                          ?>
                        </div>
                      </div>-->
                      <div class="row">
						  <div class="col-md-6">
							<?php echo render_select('department',$departments,array('departmentid','name'),'ticket_settings_departments',$ticket->department); ?>
						  </div>
						  <div class="col-md-6">
							  <?php echo render_input('cc','CC',$ticket->cc); ?>
						  </div>
					  </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group mbot20">
                        <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                        <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo prep_tags_input(get_tags_in($ticket->ticketid,'ticket')); ?>" data-role="tagsinput">
                      </div>
						
                      <!--<div class="form-group select-placeholder">
                        <label for="assigned" class="control-label">
                          <?php// echo _l('ticket_settings_assign_to'); ?>
                        </label>
                        <select name="assigned" id="assigned" class="form-control selectpicker" data-none-selected-text="<?php// echo _l('dropdown_non_selected_tex'); ?>">
                          <option value=""><?php// echo _l('ticket_settings_none_assigned'); ?></option>
                          <?php// foreach($staff as $member){ ?>
                            <option value="<?php// echo $member['staffid']; ?>" <?php// if($ticket->assigned == $member['staffid']){echo 'selected';} ?>>
                              <?php// echo $member['firstname'] . ' ' . $member['lastname'] ; ?>
                            </option>
                          <?php// } ?>
                        </select>
                      </div>-->
					  <!-- waqas code -->
					  <div class="form-group select-placeholder">
						<label for="assigned" class="control-label">
							<?php echo _l('ticket_settings_assign_to'); ?>
						</label>
						<?php 
						   
							$selected_assignee = explode(',', $ticket->assigned );
              // print_r($selected_assignee);
              $sl_assignee = array(); 
              foreach ($selected_assignee as $sa) {
                     $sl = trim($sa);
                     array_push($sl_assignee, $sl);
                      
                
              }
              $assignee_nameee = array(
                                0 => 'firstname',
                                1 => 'lastname');

               // $selected_assignee = trim($selected_assignee);
							 echo render_select('assigned[]',$staff_multi,array('staffid', $assignee_nameee),'',$sl_assignee,array('multiple'=>true,'data-actions-box'=>true),array(),'','form-control selectpicker',false);?>
					  </div>
					  <div class="form-group select-placeholder">
						<?php
						
							  $selected_followers = array();
							  $selected_followers = explode(',',$ticket->followers);

                $followers_nameee = array(
                                0 => 'firstname',
                                1 => 'lastname');

							echo render_select('followers[]',$staff_multi,array('staffid',$followers_nameee),'Followers',$selected_followers,array('multiple'=>true,'data-actions-box'=>true, 'required'=>true, 'selected'=>true),array(),'','form-control selectpicker',false);
						 ?>

					  </div>

                      <div class="row">
                        <div class="col-md-<?php if(get_option('services') == 1){ echo 6; }else{echo 12;} ?>">
                          <?php
                          $priorities['callback_translate'] = 'ticket_priority_translate';
                          echo render_select('priority',$priorities,array('priorityid','name'),'ticket_settings_priority',$ticket->priority); ?>
                        </div>
                        <?php if(get_option('services') == 1){ ?>
                          <div class="col-md-6">
                            <?php if(is_admin() || get_option('staff_members_create_inline_ticket_services') == '1'){
                              echo render_select_with_input_group('service',$services,array('serviceid','name'),'ticket_settings_service',$ticket->service,'<a href="#" onclick="new_service();return false;"><i class="fa fa-plus"></i></a>');
                            } else {
                              echo render_select('service',$services,array('serviceid','name'),'ticket_settings_service',$ticket->service);
                            }
                            ?>
                          </div>
                        <?php } ?>
                      </div>
					  <div class="row">
						  <div class="col-md-6">
							<?php 
							//echo "<pre>"; print_r($statuses);exit(); 
							echo render_select('status',$statuses,array('ticketstatusid','name'),'status',$ticket->status,array('required'=>'true')); 
							?>
						  </div>
						  <div class="col-md-6">
							<?php
								echo render_select('source',$sources,array('source_id','source_name'),'Source',$ticket->source,array('required'=>'true')); 
							?>
						  </div>
					  </div>
                      <!--<div class="form-group select-placeholder projects-wrapper<?php// if($ticket->userid == 0){echo ' hide';} ?>">
                        <label for="project_id"><?php// echo _l('project'); ?></label>
                        <div id="project_ajax_search_wrapper">
                          <select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="<?php// echo _l('dropdown_non_selected_tex'); ?>">
                            <?php //if($ticket->project_id != 0){ ?>
                              <option value="<?php// echo $ticket->project_id; ?>"><?php// echo get_project_name_by_id($ticket->project_id); ?></option>
                            <?php// } ?>
                          </select>
                        </div>
                      </div>-->

                    </div>
                    <div class="col-md-12">
                      <?php echo render_custom_fields('tickets',$ticket->ticketid); ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 text-center">
                      <hr />
                      <a href="#" class="btn btn-info save_changes_settings_single_ticket">
                        <?php echo _l('submit'); ?>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="panel_s mtop20">
            <div class="panel-body <?php if($ticket->admin == NULL){echo 'client-reply';} ?>">
              <div class="row">
                <div class="col-md-3 border-right">
                  <p>
                    <?php if($ticket->admin == NULL || $ticket->admin == 0){ ?>
                      <?php if($ticket->userid != 0){ ?>
                        <a href="<?php echo admin_url('clients/client/'.$ticket->userid.'?contactid='.$ticket->contactid); ?>"
                          ><?php echo $ticket->submitter; ?>
                        </a>
                      <?php } else {
                        echo $ticket->submitter;
                        ?>
                        <br />
                        <a href="mailto:<?php echo $ticket->ticket_email; ?>"><?php echo $ticket->ticket_email; ?></a>
                        <hr />
                        <?php
                        if(total_rows('tblticketsspamcontrol',array('type'=>'sender','value'=>$ticket->ticket_email)) == 0){ ?>
                          <button type="button" data-sender="<?php echo $ticket->ticket_email; ?>" class="btn btn-danger block-sender btn-xs"><?php echo _l('block_sender'); ?></button>
                          <?php
                        } else {
                          echo '<span class="label label-danger">'._l('sender_blocked').'</span>';
                        }
                      }
                    } else {  ?>
                      <a href="<?php echo admin_url('profile/'.$ticket->admin); ?>"><?php echo $ticket->opened_by; ?></a>
                    <?php } ?>
                  </p>
                  <p class="text-muted">
                    <?php if($ticket->admin !== NULL || $ticket->admin != 0){
                      echo _l('ticket_staff_string');
                    } else {
                      if($ticket->userid != 0){
                        echo _l('ticket_client_string');
                      }
                    }
                    ?>
                  </p>
                  <?php if(has_permission('tasks','','create')){ ?>
                    <a href="#" class="btn btn-default btn-xs" onclick="convert_ticket_to_task(<?php echo $ticket->ticketid; ?>,'ticket'); return false;"><?php echo _l('convert_to_task'); ?></a>
                  <?php } ?>
                </div>



                <div class="col-md-9">
                  <div class="row">
                    <div class="col-md-12 text-right">
                      <a href=""></a>
                      <a href="#" onclick="edit_ticket_message(<?php echo $ticket->ticketid; ?>,'ticket'); return false;"><i class="fa fa-pencil-square-o"></i></a>

                    </div>
                  </div>
                  <div data-ticket-id="<?php echo $ticket->ticketid; ?>" class="tc-content">
                    <?php echo check_for_links($ticket->message); ?>
                  </div><br />
                  <p>-----------------------------</p>
                  <?php if(filter_var($ticket->ip, FILTER_VALIDATE_IP)){ ?>
                    <p>IP: <?php echo $ticket->ip; ?></p>
                  <?php } ?>

                  <?php if(count($ticket->attachments) > 0){
                    echo '<hr />';
                    foreach($ticket->attachments as $attachment){

                      $path = get_upload_path_by_type('ticket').$ticket->ticketid.'/'.$attachment['file_name'];
                      $is_image = is_image($path);

                      if($is_image){
                        echo '<div class="preview_image">';
                      }
                      ?>
                      <a href="<?php echo site_url('download/file/ticket/'. $attachment['id']); ?>" class="display-block mbot5"<?php if($is_image){ ?> data-lightbox="attachment-ticket-<?php echo $ticket->ticketid; ?>" <?php } ?>>
                        <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i> <?php echo $attachment['file_name']; ?>
                        <?php if($is_image){ ?>
                          <img class="mtop5" src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path($path).'&type='.$attachment['filetype']); ?>">
                        <?php } ?>

                      </a>
                      <?php if($is_image){
                        echo '</div>';
                      }
                      echo '<hr />';
                      ?>
                    <?php }
                  } ?>
                </div>
              </div>
            </div>
            <div class="panel-footer">
              <?php echo _l('ticket_posted',_dt($ticket->date)); ?>
            </div>
          </div>
          <?php foreach($ticket_replies as $reply){ ?>
            <div class="panel_s">
              <div class="panel-body <?php if($reply['admin'] == NULL){echo 'client-reply';} ?>">
                <div class="row">
                  <div class="col-md-3 border-right">
                    <p>
                      <?php if($reply['admin'] == NULL || $reply['admin'] == 0){ ?>
                        <?php if($reply['userid'] != 0){ ?>
                          <a href="<?php echo admin_url('clients/client/'.$reply['userid'].'?contactid='.$reply['contactid']); ?>"><?php echo $reply['submitter']; ?></a>
                        <?php } else { ?>
                          <?php echo $reply['submitter']; ?>
                          <br />
                          <a href="mailto:<?php echo $reply['reply_email']; ?>"><?php echo $reply['reply_email']; ?></a>
                        <?php } ?>
                      <?php }  else { ?>
                        <a href="<?php echo admin_url('profile/'.$reply['admin']); ?>"><?php echo $reply['submitter']; ?></a>
                      <?php } ?>
                    </p>
                    <p class="text-muted">
                      <?php if($reply['admin'] !== NULL || $reply['admin'] != 0){
                        echo _l('ticket_staff_string');
                      } else {
                        if($reply['userid'] != 0){
                          echo _l('ticket_client_string');
                        }
                      }
                      ?>
                    </p>
                    <hr />
                    <a href="<?php echo admin_url('tickets/delete_ticket_reply/'.$ticket->ticketid .'/'.$reply['id']); ?>" class="btn btn-danger pull-left _delete mright5 btn-xs"><?php echo _l('delete_ticket_reply'); ?></a>
                    <div class="clearfix"></div>
                    <?php if(has_permission('tasks','','create')){ ?>
                      <a href="#" class="pull-left btn btn-default mtop5 btn-xs" onclick="convert_ticket_to_task(<?php echo $reply['id']; ?>,'reply'); return false;"><?php echo _l('convert_to_task'); ?>
                    </a>
                    <div class="clearfix"></div>
                  <?php } ?>
                   <div class="clearfix"></div>
                      <p></p> 
                      <div style="font-size:10px;">
                        <?php
                            $reply_email = explode(',',$reply['reply_email']);
                          foreach ($reply_email as $email)
                          {
                            $res = get_email_reply_contact($email); 
                            echo $res[0]['firstname'].' '.$res[0]['lastname'].' - '.$res[0]['title'].' - '.$res[0]['email'].'<br>';
                          } 
                        ?>
                    </div>
                </div>
                <div class="col-md-9">
                  <div class="row">
                    <div class="col-md-12 text-right">
                      <a href="#" onclick="edit_ticket_message(<?php echo $reply['id']; ?>,'reply'); return false;"><i class="fa fa-pencil-square-o"></i></a>

                    </div>
                  </div>
                  <div class="clearfix"></div>
                  <div data-reply-id="<?php echo $reply['id']; ?>" class="tc-content">
                    <?php echo check_for_links($reply['message']); ?>
                  </div><br />
                  <p>-----------------------------</p>
                  <?php if(filter_var($reply['ip'], FILTER_VALIDATE_IP)){ ?>
                    <p>IP: <?php echo $reply['ip']; ?></p>
                  <?php } ?>
                  <?php if(count($reply['attachments']) > 0){
                    echo '<hr />';
                    foreach($reply['attachments'] as $attachment){
                      $path = get_upload_path_by_type('ticket').$ticket->ticketid.'/'.$attachment['file_name'];
                      $is_image = is_image($path);

                      if($is_image){
                        echo '<div class="preview_image">';
                      }
                      ?>
                      <a href="<?php echo site_url('download/file/ticket/'. $attachment['id']); ?>" class="display-block mbot5"<?php if($is_image){ ?> data-lightbox="attachment-reply-<?php echo $reply['id']; ?>" <?php } ?>>
                        <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i> <?php echo $attachment['file_name']; ?>
                        <?php if($is_image){ ?>
                          <img class="mtop5" src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path($path).'&type='.$attachment['filetype']); ?>">
                        <?php } ?>

                      </a>
                      <?php if($is_image){
                        echo '</div>';
                      }
                      echo '<hr />';
                    }
                  } ?>
                </div>
              </div>
            </div>
            <div class="panel-footer">
              <span><?php echo _l('ticket_posted',_dt($reply['date'])); ?></span>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
    <div class="btn-bottom-pusher"></div>
    <?php if(count($ticket_replies) > 1){ ?>
      <a href="#top" id="toplink">↑</a>
      <a href="#bot" id="botlink">↓</a>
    <?php } ?>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="ticket-message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <?php echo form_open(admin_url('tickets/edit_message')); ?>
    <input type="hidden" id="client_customer_id" value="<?php echo $this->uri->segment(5);?>"/>
    <div class="modal-content">
      <div id="edit-ticket-message-additional"></div>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo _l('ticket_message_edit'); ?></h4>
      </div>
      <div class="modal-body">
        <?php echo render_textarea('data','','',array(),array(),'','tinymce-ticket-edit'); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
<div id="contact_data"></div>
<!-- sajid -->

<?php $this->load->view('admin/clients/client_js'); ?>
<!-- sajid -->
<script>
  var _ticket_message;
</script>
<?php $this->load->view('admin/tickets/services/service'); ?>
<?php init_tail(); ?>
<?php echo app_script('assets/js','tickets.js'); ?>
<?php do_action('ticket_admin_single_page_loaded',$ticket); ?>
<script>

$(function(){
   initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + '<?php echo $ticket->userid; ?>' + '/' + 'customer', [4], [4], undefined);
   });

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
    $('#single-ticket-form').validate();
    init_ajax_search('contact','#contactid.ajax-search',{tickets_contacts:true});
    init_ajax_search('project', 'select[name="project_id"]', {
      customer_id: function() {
        return $('input[name="userid"]').val();
      }
    });
    $('body').on('shown.bs.modal', '#_task_modal', function() {
      if(typeof(_ticket_message) != 'undefined') {
        // Init the task description editor
        $(this).find('textarea[name="description"]').click();
        tinymce.activeEditor.execCommand('mceInsertContent', false, _ticket_message);
        $('body #_task_modal input[name="name"]').val($('#ticket_subject').text().trim());
      }
    });

    $('body').on('click', '#single-ticket-form-knowledge', function() {
      $('#knowledgeBase').val('1');
      $('#single-ticket-form-submit').trigger('click');
    });

    $("a[href='#top']").on("click", function(e) {
      e.preventDefault();
      $("html,body").animate({scrollTop:0}, 1000);
      e.preventDefault();
    });

    // Smooth scroll to bottom.
    $("a[href='#bot']").on("click", function(e) {
      e.preventDefault();
      $("html,body").animate({scrollTop:$(document).height()}, 1000);
      e.preventDefault();
    });
    
  });


  var Ticket_message_editor;
  var edit_ticket_message_additional = $('#edit-ticket-message-additional');


  function edit_ticket_message(id,type){
    edit_ticket_message_additional.empty();
    if(type == 'ticket'){
      _ticket_message = $('[data-ticket-id="'+id+'"]').html();
    } else {
      _ticket_message = $('[data-reply-id="'+id+'"]').html();
    }

    init_ticket_edit_editor();
    tinyMCE.activeEditor.setContent(_ticket_message);
    $('#ticket-message').modal('show');
    edit_ticket_message_additional.append(hidden_input('type',type));
    edit_ticket_message_additional.append(hidden_input('id',id));
    edit_ticket_message_additional.append(hidden_input('main_ticket',$('input[name="ticketid"]').val()));
  }

  function init_ticket_edit_editor(){
    if(typeof(Ticket_message_editor) !== 'undefined'){
      return true;
    }
    Ticket_message_editor = init_editor('.tinymce-ticket-edit');
  }
  <?php if(has_permission('tasks','','create')){ ?>
    function convert_ticket_to_task(id, type){
      if(type == 'ticket'){
        _ticket_message = $('[data-ticket-id="'+id+'"]').html();
      } else {
        _ticket_message = $('[data-reply-id="'+id+'"]').html();
      }
      var new_task_url = admin_url + 'tasks/task?rel_id=<?php echo $ticket->ticketid; ?>&rel_type=ticket&ticket_to_task=true';
      new_task(new_task_url);
    }
  <?php } ?>


</script>
<script>
  var values ='';
    //**** umer farooq chattha Start****//
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



    //**** umer farooq chattha End****//
    $(function(){    
      var not_sortable_items;    
      not_sortable_items = [($('.table-invoice-items').find('th').length - 1)];    
      not_sortable_items = [($('.table-sale-inventory-items').find('th').length - 1)];    
      //here
      initDataTable('.table-sale-inventory-items', admin_url+'inventory/table', not_sortable_items, not_sortable_items,'undefined',[0,'ASC']);    
      if(get_url_param('groups_modal'))
      {       
      // Set time out user to see the message       
      setTimeout(function(){         
        $('#groups').modal('show');       
      },1000);    
    }

    $('.btn-inv-action').on('click',function(){
      var action = $(this).parent().parent().find('#action').val();
      var inv_group_type = $(this).parent().parent().find('#inv_group_type').val();
      var name = $(this).parent().parent().find('#name').val();
      if(name != ''){
        $.post(admin_url+'inventory/'+action,{name:name,inv_group_type:inv_group_type}).done(function(){
          window.location.href = admin_url+'inventory?inventory_modal=true';

        });
      }
    });


    $('body').on('click','.edit-item-group',function(){
      var tr = $(this).parents('tr'),
      group_id = tr.attr('data-group-row-id');
      tr.find('.group_name_plain_text').toggleClass('hide');
      tr.find('.group_edit').toggleClass('hide');
      tr.find('.group_edit input').val(tr.find('.group_name_plain_text').text());
    });

    $('body').on('click','.update-inventory-group',function(){
      var tr = $(this).parents('tr');
      var group_id = tr.attr('data-group-row-id');
      name = tr.find('.group_edit input').val();
      if(name != ''){
        $.post(admin_url+'inventory/update_group/'+group_id,{name:name}).done(function(){
          window.location.href = admin_url+'inventory';
        });
      }
    });
  });  
    //sajid code start
    $('body').on('click', '#existing_contact', function(){
      exiscontacttogle = $("#existing_contact").val();
      if(exiscontacttogle == 1)
      {
        $("#existing_contact").val('0');
        $("#old_existing_account").removeClass("required");
        $("#idd_1").removeClass("required");
        $("#idd").addClass("required");
        $("#firstname").val("");
        $("#lastname").val("");
        $("#email").val("");
        $("#password").val("");
        $("#old_contact").hide();
        $("#new_contact").show();
        $('#existing_contact_save').removeAttr("disabled");
        $("#check").hide();
      }
      else
      {
        $("#idd").removeClass("required");
        $("#old_existing_account").addClass("required");
        $("#idd_1").addClass("required");
        $("#firstname").attr("aria-invalid","false");
        $("#firstname").val("AA");
        $("#lastname").attr("aria-invalid","false");
        $("#lastname").val("AA");
        $("#email").attr("aria-invalid","false");
        $("#email").val("aa@mail.com");
        $("#password").val("AA");
        $("#existing_contact").val('1');
        $("#new_contact").hide();
        $("#old_contact").css({'display':'inline-block', 'width':'100%'});    
      }
      console.log($("#firstname").val());

    });
    //sajid code end
  </script>
  <script>
   $('body').on('change', '#old_existing_account', function() {

    var contact_id = $(this).val(); 
    var company_id = $('input:hidden').val();
    $.ajax({
      type:'POST',
      url:'<?php echo base_url(); ?>admin/clients/get_existing_check',
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
  // $('body').on('click', '#existing_contact_save', function() {
    // alert('hi'); 
    // $('#existing_contact_save').addAttr('disabled');
  // });

</script>
<script>
  $(function(){
            // Guess auto email notifications based on the default contact permissios
            var permInputs = $('input[name="permissions[]"]');
            $.each(permInputs,function(i,input){
              input = $(input);
              if(input.prop('checked') === true){
                $('#contact_active_new [data-perm-id="'+input.val()+'"]').prop('checked',true);
              }
            });
          });
</script>

  <!-- abrar -->
<script type="text/javascript">
var checkbox_values = [];
var check_previous_categories=0;
function send_email_reply(email){
var checked_checkbox=$("input[type=checkbox]:checked").length;

if(check_previous_categories==0){
var checked_id=$('#checkbox_id').val();
// alert(checked_id);
}
var checked_cate=$('#'+email+'').is(':checked');
// alert(checked_cate);
// return false;
if(checked_cate){
if(checked_id!=='' && check_previous_categories==0){
var checked_id=checked_id.split(',');
$.each(checked_id,function(i,row){
checkbox_values.push(row);
});
}
checkbox_values.push(email);
check_previous_categories++;
}else{
if(checked_id!=='' && check_previous_categories==0){
var checked_id=checked_id.split(',');
$.each(checked_id,function(i,row){
checkbox_values.push(row);
});
check_previous_categories++;
}

checkbox_values = $.grep(checkbox_values, function(data) {
return data !=email;
});
}
checkbox_values.join();

$('#checkbox_id').val(checkbox_values);

}
</script>
<!-- abrar -->
<!-- waqas -->
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
	var favorite = [];
	$("#contacts_shows_now").val("");
	$.each(response, function(index, value) {

	$("#contactid").append("<option data-email="+value['contact_id']+" value="+value['email']+">"+value['firstname']+"</option>");
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
	var selected = $(this).find("option:selected").val();

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
