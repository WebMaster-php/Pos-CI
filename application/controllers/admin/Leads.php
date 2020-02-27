<?php
header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');
class Leads extends Admin_controller
{
    private $not_importable_leads_fields;

    public function __construct()
    {
        parent::__construct();
        check_portfolio_id();
        $this->not_importable_leads_fields = do_action('not_importable_leads_fields', array('id', 'source', 'assigned', 'status', 'dateadded', 'last_status_change', 'addedfrom', 'leadorder', 'date_converted', 'lost', 'junk', 'is_imported_from_email_integration', 'email_integration_uid', 'is_public', 'dateassigned', 'client_id', 'lastcontact', 'last_lead_status', 'from_form_id', 'default_language'));
        $this->load->model('leads_model');

    }

    /* List all leads */
    public function index($id = '')
    {

        close_setup_menu();

        if (!is_staff_member()) {
            access_denied('Leads');
        }

        $data['switch_kanban'] = true;

        if ($this->session->userdata('leads_kanban_view') == 'true') {
            $data['switch_kanban'] = false;
            $data['bodyclass']     = 'kan-ban-body';
        }

        $data['staff'] = $this->staff_model->get('', 1);

        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();
        $data['title']    = _l('leads');
        // in case accesed the url leads/index/ directly with id - used in search
        $data['leadid']   = $id;
		// echo "<pre>"; print_r($data); exit; 
        $this->load->view('admin/leads/manage_leads', $data);
    }

    public function table()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        $this->app->get_table_data('leads');
    }

    public function kanban()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        $data['statuses'] = $this->leads_model->get_status();
        echo $this->load->view('admin/leads/kan-ban', $data, true);
    }

    /* Add or update lead */
    public function lead($id = '')
    {
      
        if (!is_staff_member() || ($id != '' && !$this->leads_model->staff_can_access_lead($id))) {
            $this->access_denied_ajax();
        }

        

        if ($this->input->post()) {
              

         if ($id == '') {
            $data = $this->input->post();
            unset($data['existing_customer']);
            unset($data['old_existing_customer']);
            $data['assigned']=implode(',', $this->input->post('assigned'));
            $data['followers']=implode(',', $this->input->post('followers')); 
            //echo "<pre>"; print_r($data['followers']); exit('kkk');
            $data['portfolio_id'] = $this->session->userdata('portfolio_id');
            // echo "<pre>";print_r($data); exit('aaaa'); 
            $id      = $this->leads_model->add($data);
            $message = $id ? _l('added_successfully', _l('lead')) : '';

            echo json_encode(array(
                'success' => $id ? true : false,
                'id' => $id,
                'message' => $message,
                'leadView'=>$id ? $this->_get_lead_data($id) : array(),
            ));
        } else {
            $leadData= $this->leads_model->getLeadDetailsById($id);//added by salik
            $data=$this->input->post();
            unset($data['old_existing_customer']);
            $data['assigned']=implode(',', $this->input->post('assigned'));
            $data['followers']=implode(',', $this->input->post('followers'));
            $emailOriginal = $this->db->select('email')->where('id', $id)->get('tblleads')->row()->email;
            $proposalWarning = false;
            $message          = '';
            $success          = $this->leads_model->update($data, $id);
            if ($success) {
                $emailNow = $this->db->select('email')->where('id', $id)->get('tblleads')->row()->email;

                $proposalWarning = (total_rows('tblproposals', array(
                    'rel_type' => 'lead',
                    'rel_id' => $id, )) > 0 && ($emailOriginal != $emailNow) && $emailNow != '') ? true : false;

                $message = _l('updated_successfully', _l('lead'));
            }
               //salik code start
            if($leadData){
            if($leadData->status !=$data['status']){

                $leadName=$leadData->name;
                $assignedUsers=explode(',', $leadData->assigned);
                $followers=explode(',', $leadData->followers);
                $company=$leadData->company;
                $this->load->model('emails_model');
                $emailAddress="";
                $subject="Status Update";
                $message="";
                
                foreach ($assignedUsers as $key => $assign) {
                $assignedUserRecord=$this->leads_model->getTableRecordById('tblstaff',array('staffid'=>$assign));
                if($assignedUserRecord->email_notify_enable){
                   
                $emailAddress=$assignedUserRecord->email;
                $name=$assignedUserRecord->firstname." ".$assignedUserRecord->lastname;
                $status=($data['status']==1?"Customer":"Potential Customer");
                $message.="Hi ".$name." ,<br/>";
                $message.="".$leadName." has changed status to <b>".$status."</b><br/>";
                $message.="Click here to view this Lead:<a target='_blank' href='".admin_url('leads')."'>Leads</a>  <br/>";
                $message.="Best regards,<br/>";
                $message.="VersiPOS Team";
                $sendMail=$this->emails_model->report_email($emailAddress,$subject,$message);
                $message=""; 
                }
                
                }
                foreach ($followers as $key => $follow) {
                $followersUserRecord=$this->leads_model->getTableRecordById('tblstaff',array('staffid'=>$follow));
                if($followersUserRecord->email_notify_enable){
                      
                $emailAddress=$followersUserRecord->email;
                $name=$followersUserRecord->firstname." ".$followersUserRecord->lastname;
                $status=($data['status']==1?"Customer":"Potential Customer");
                $message.="Hi ".$name." ,<br/>";
                $message.="".$leadName." has changed status to <b>".$status."</b><br/>";
                $message.="Click here to view this Lead:<a target='_blank' href='".admin_url('leads')."'>Leads</a>  <br/>";
                $message.="Best regards,<br/>";
                $message.="VersiPOS Team";
                $sendMail=$this->emails_model->report_email($emailAddress,$subject,$message);
                $message="";
              }
                

                }
                }
            }
            //salik code end
            echo json_encode(array(
                'success' => $success,
                'message' => $message,
                'id'=>$id,
                'proposal_warning' => $proposalWarning,
                'leadView'=>$this->_get_lead_data($id),
            ));
        }
        die;
    }

    echo json_encode(array('leadView'=>$this->_get_lead_data($id)));

}

private function _get_lead_data($id = '')
{
    $reminder_data = '';
    $data['lead_locked'] = false;
    $data['members'] = $this->staff_model->get('', 1, array('is_not_staff' => 0));
    $data['customersdata'] = $this->leads_model->getAllTableRecord('tblclients');
    $data['status_id'] = $this->input->get('status_id') ? $this->input->get('status_id') : get_option('leads_default_status');

    if (is_numeric($id)) {
        $leadWhere = (has_permission('leads', '', 'view') ? array() : '(assigned = ' . get_staff_user_id() . ' OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');

        $lead = $this->leads_model->get($id, $leadWhere);

        if (!$lead) {
            header("HTTP/1.0 404 Not Found");
            echo _l('lead_not_found');
            die;
        }

        if (total_rows('tblclients', array('leadid' => $id )) > 0) {
            $data['lead_locked'] = ((!is_admin() && get_option('lead_lock_after_convert_to_customer') == 1) ? true : false);
        }

        $reminder_data = $this->load->view('admin/includes/modals/reminder', array(
            'id' => $lead->id,
			'client_id' => $lead->client_id,
            'name' => 'lead',
            'members' => $data['members'],
            'reminder_title' => _l('lead_set_reminder_title'),
        ), true);

        $data['lead']          = $lead;
        $data['mail_activity'] = $this->leads_model->get_mail_activity($id);
        $data['notes']         = $this->misc_model->get_notes($id, 'lead');
        $data['activity_log']  = $this->leads_model->get_lead_activity_log($id);
    }


    $data['statuses'] = $this->leads_model->get_status();
    $data['sources']  = $this->leads_model->get_source();

    $data = do_action('lead_view_data', $data);

    return array(
        'data' => $this->load->view('admin/leads/lead', $data, true),
        'reminder_data' => $reminder_data,
    );
}

public function leads_kanban_load_more()
{
    if (!is_staff_member()) {
        $this->access_denied_ajax();
    }

    $status = $this->input->get('status');
    $page   = $this->input->get('page');

    $this->db->where('id', $status);
    $status = $this->db->get('tblleadsstatus')->row_array();

    $leads = $this->leads_model->do_kanban_query($status['id'], $this->input->get('search'), $page, array(
        'sort_by' => $this->input->get('sort_by'),
        'sort' => $this->input->get('sort'),
    ));

    foreach ($leads as $lead) {
        $this->load->view('admin/leads/_kan_ban_card', array(
            'lead' => $lead,
            'status' => $status,
        ));
    }
}

public function switch_kanban($set = 0)
{
    if ($set == 1) {
        $set = 'true';
    } else {
        $set = 'false';
    }
    $this->session->set_userdata(array(
        'leads_kanban_view' => $set,
    ));
    redirect($_SERVER['HTTP_REFERER']);
}

/* Delete lead from database */
public function delete($id)
{
    if (!$id) {
        redirect(admin_url('leads'));
    }

    if (!is_lead_creator($id) && !has_permission('leads', '', 'delete')) {
        access_denied('Delte Lead');
    }

    $response = $this->leads_model->delete($id);
    if (is_array($response) && isset($response['referenced'])) {
        set_alert('warning', _l('is_referenced', _l('lead_lowercase')));
    } elseif ($response === true) {
        set_alert('success', _l('deleted', _l('lead')));
    } else {
        set_alert('warning', _l('problem_deleting', _l('lead_lowercase')));
    }
    $ref = $_SERVER['HTTP_REFERER'];

        // if user access leads/inded/ID to prevent redirecting on the same url because will throw 404
    if(!$ref || strpos($ref,'index/'.$id) !== FALSE) {
        redirect(admin_url('leads'));
    }

    redirect($ref);
}

public function mark_as_lost($id)
{
    if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
        $this->access_denied_ajax();
    }
    $message = '';
    $success = $this->leads_model->mark_as_lost($id);
    if ($success) {
        $message = _l('lead_marked_as_lost');
    }
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'leadView'=>$this->_get_lead_data($id),
        'id'=>$id,
    ));
}

public function unmark_as_lost($id)
{
    if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
        $this->access_denied_ajax();
    }
    $message = '';
    $success = $this->leads_model->unmark_as_lost($id);
    if ($success) {
        $message = _l('lead_unmarked_as_lost');
    }
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'leadView'=>$this->_get_lead_data($id),
        'id'=>$id,
    ));
}

public function mark_as_junk($id)
{
    if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
        $this->access_denied_ajax();
    }
    $message = '';
    $success = $this->leads_model->mark_as_junk($id);
    if ($success) {
        $message = _l('lead_marked_as_junk');
    }
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'leadView'=>$this->_get_lead_data($id),
        'id'=>$id,
    ));
}

public function unmark_as_junk($id)
{
    if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
        $this->access_denied_ajax();
    }
    $message = '';
    $success = $this->leads_model->unmark_as_junk($id);
    if ($success) {
        $message = _l('lead_unmarked_as_junk');
    }
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'leadView'=>$this->_get_lead_data($id),
        'id'=>$id,
    ));
}

public function add_activity()
{
    $leadid = $this->input->post('leadid');
    if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($leadid)) {
        $this->access_denied_ajax();
    }
    if ($this->input->post()) {
        $message = $this->input->post('activity');
        $aId = $this->leads_model->log_lead_activity($leadid, $message);
        if ($aId) {
            $this->db->where('id', $aId);
            $this->db->update('tblleadactivitylog', array('custom_activity'=>1));
        }
        echo json_encode(array('leadView'=>$this->_get_lead_data($leadid), 'id'=>$leadid));
    }
}

public function get_convert_data($id)
{
    if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
        $this->access_denied_ajax();
    }
    $data['lead'] = $this->leads_model->get($id);
    $this->load->view('admin/leads/convert_to_customer', $data);
}

    /**
     * Convert lead to client
     * @since  version 1.0.1
     * @return mixed
     */
    public function convert_to_customer()
    {
         
            
        if (!is_staff_member()) {
            access_denied('Lead Convert to Customer');
        }
//echo "<pre>"; print_r($_POST); exit('hhhdfdh');
        if ($this->input->post()) {
           
            $default_country              = get_option('customer_default_country');
            $data                         = $this->input->post();
           // $data['password'] = $this->input->post('password', false);
            $notes = $this->misc_model->get_notes($data['leadid'], 'lead');
            $original_lead_email          = $data['original_lead_email'];
            unset($data['original_lead_email']);

            if (isset($data['transfer_notes'])) {
                $notes = $this->misc_model->get_notes($data['leadid'], 'lead');
                unset($data['transfer_notes']);
            }

            if (isset($data['merge_db_fields'])) {
                $merge_db_fields = $data['merge_db_fields'];
                unset($data['merge_db_fields']);
            }

            if (isset($data['merge_db_contact_fields'])) {
                $merge_db_contact_fields = $data['merge_db_contact_fields'];
                unset($data['merge_db_contact_fields']);
            }

            if (isset($data['include_leads_custom_fields'])) {
                $include_leads_custom_fields = $data['include_leads_custom_fields'];
                unset($data['include_leads_custom_fields']);
            }

            if ($data['country'] == '' && $default_country != '') {
                $data['country'] = $default_country;
            }

            $data['billing_street'] = $data['address'];
            $data['billing_city'] = $data['city'];
            $data['billing_state'] = $data['state'];
            $data['billing_zip'] = $data['zip'];
            $data['billing_country'] = $data['country'];
            $data['portfolio_id'] = $this->session->userdata('portfolio_id');

            $data['is_primary'] = 1;
            $data['donotsendwelcomeemail']=1;
            $id = $this->clients_model->add($data, true);
            //echo "<pre>"; print_r($id); exit('ffff');
            $contactData=$this->leads_model->getTableRecordById('tblcontacts',array('userid'=>$id));
           $contactId=$contactData->id;
           // echo $id; exit('bb');

            if ($id) {

                if (isset($notes)) {
                    foreach ($notes as $note) {
                        $this->db->insert('tblnotes', array(
                            'rel_id'=>$id,
                            'rel_type'=>'customer',
                            'dateadded'=>$note['dateadded'],
                            'addedfrom'=>$note['addedfrom'],
                            'description'=>$note['description'],
                            'date_contacted'=>$note['date_contacted'],
                        ));
                    }
                }
                if (!has_permission('customers', '', 'view') && get_option('auto_assign_customer_admin_after_lead_convert') == 1) {
                    $this->db->insert('tblcustomeradmins', array(
                        'date_assigned' => date('Y-m-d H:i:s'),
                        'customer_id' => $id,
                        'staff_id' => get_staff_user_id(),
                    ));
                }
                $this->leads_model->log_lead_activity($data['leadid'], 'not_lead_activity_converted', false, serialize(array(
                    get_staff_full_name(),
                )));
                $default_status = $this->leads_model->get_status('', array(
                    'isdefault' => 1,
                ));
                $this->db->where('id', $data['leadid']);
                $this->db->update('tblleads', array(
                    'date_converted' => date('Y-m-d H:i:s'),
                    'status' => $default_status[0]['id'],
                    'junk' => 0,
                    'lost' => 0,
                ));
                // Check if lead email is different then client email
                $contact = $this->clients_model->get_contact(get_primary_contact_user_id($id));
                if ($contact->email != $original_lead_email) {
                    if ($original_lead_email != '') {
                        $this->leads_model->log_lead_activity($data['leadid'], 'not_lead_activity_converted_email', false, serialize(array(
                            $original_lead_email,
                            $contact->email,
                        )));
                    }
                }
                if (isset($include_leads_custom_fields)) {
                    foreach ($include_leads_custom_fields as $fieldid => $value) {
                        // checked don't merge
                        if ($value == 5) {
                            continue;
                        }
                        // get the value of this leads custom fiel
                        $this->db->where('relid', $data['leadid']);
                        $this->db->where('fieldto', 'leads');
                        $this->db->where('fieldid', $fieldid);
                        $lead_custom_field_value = $this->db->get('tblcustomfieldsvalues')->row()->value;
                        // Is custom field for contact ot customer
                        if ($value == 1 || $value == 4) {
                            if ($value == 4) {
                                $field_to = 'contacts';
                            } else {
                                $field_to = 'customers';
                            }
                            $this->db->where('id', $fieldid);
                            $field = $this->db->get('tblcustomfields')->row();
                            // check if this field exists for custom fields
                            $this->db->where('fieldto', $field_to);
                            $this->db->where('name', $field->name);
                            $exists               = $this->db->get('tblcustomfields')->row();
                            $copy_custom_field_id = null;
                            if ($exists) {
                                $copy_custom_field_id = $exists->id;
                            } else {
                                // there is no name with the same custom field for leads at the custom side create the custom field now
                                $this->db->insert('tblcustomfields', array(
                                    'fieldto' => $field_to,
                                    'name' => $field->name,
                                    'required' => $field->required,
                                    'type' => $field->type,
                                    'options' => $field->options,
                                    'display_inline' => $field->display_inline,
                                    'field_order' => $field->field_order,
                                    'slug' => slug_it($field_to . '_' . $field->name, array(
                                        'separator' => '_',
                                    )),
                                    'active' => $field->active,
                                    'only_admin' => $field->only_admin,
                                    'show_on_table' => $field->show_on_table,
                                    'bs_column' => $field->bs_column,
                                ));
                                $new_customer_field_id = $this->db->insert_id();
                                if ($new_customer_field_id) {
                                    $copy_custom_field_id = $new_customer_field_id;
                                }
                            }
                            if ($copy_custom_field_id != null) {
                                $insert_to_custom_field_id = $id;
                                if ($value == 4) {
                                    $insert_to_custom_field_id = get_primary_contact_user_id($id);
                                }
                                $this->db->insert('tblcustomfieldsvalues', array(
                                    'relid' => $insert_to_custom_field_id,
                                    'fieldid' => $copy_custom_field_id,
                                    'fieldto' => $field_to,
                                    'value' => $lead_custom_field_value,
                                ));
                            }
                        } elseif ($value == 2) {
                            if (isset($merge_db_fields)) {
                                $db_field = $merge_db_fields[$fieldid];
                                // in case user don't select anything from the db fields
                                if ($db_field == '') {
                                    continue;
                                }
                                if ($db_field == 'country' || $db_field == 'shipping_country' || $db_field == 'billing_country') {
                                    $this->db->where('iso2', $lead_custom_field_value);
                                    $this->db->or_where('short_name', $lead_custom_field_value);
                                    $this->db->or_like('long_name', $lead_custom_field_value);
                                    $country = $this->db->get('tblcountries')->row();
                                    if ($country) {
                                        $lead_custom_field_value = $country->country_id;
                                    } else {
                                        $lead_custom_field_value = 0;
                                    }
                                }
                                $this->db->where('userid', $id);
                                $this->db->update('tblclients', array(
                                    $db_field => $lead_custom_field_value,
                                ));
                            }
                        } elseif ($value == 3) {
                            if (isset($merge_db_contact_fields)) {
                                $db_field = $merge_db_contact_fields[$fieldid];
                                if ($db_field == '') {
                                    continue;
                                }
                                $primary_contact_id = get_primary_contact_user_id($id);
                                $this->db->where('id', $primary_contact_id);
                                $this->db->update('tblcontacts', array(
                                    $db_field => $lead_custom_field_value,
                                ));
                            }
                        }
                    }
                }
                // set the lead to status client in case is not status client
                $this->db->where('isdefault', 1);
                $status_client_id = $this->db->get('tblleadsstatus')->row()->id;
                $this->db->where('id', $data['leadid']);
                $this->db->update('tblleads', array(
                    'status' => $status_client_id,
                ));
                                 //convert contacts to customers
                 $lead_related_contacts=$this->leads_model->getTableRecords('tbl_lead_contacts_rel_clients',array('company_id'=>$_POST['leadid']));
                if(isset($lead_related_contacts)){
                    foreach ($lead_related_contacts as $key => $newCust) {
                    $tblcontacts_rel_clients_data['company_id']=$id;
                    $tblcontacts_rel_clients_data['contact_id'] =$contactId;
                    $tblcontacts_rel_clients_data['is_primary'] =$newCust->is_primary;
                    $tblcontacts_rel_clients_data['invoice_emails'] =$newCust->invoice_emails;
                    $tblcontacts_rel_clients_data['estimate_emails'] =$newCust->estimate_emails;
                    $tblcontacts_rel_clients_data['credit_note_emails'] =$newCust->credit_note_emails;
                    $tblcontacts_rel_clients_data['project_emails'] =$newCust->project_emails;
                    $tblcontacts_rel_clients_data['task_emails'] =$newCust->task_emails;
                    $tblcontacts_rel_clients_data['contract_emails'] =$newCust->contract_emails;
                    $tblcontacts_rel_clients_data['datecreated'] =$newCust->datecreated;
                    $conver_contacts_to_cusmter=$this->leads_model->add_contacts_to_customer($tblcontacts_rel_clients_data);
                    }
                    
                }
               
				$assignedNdFollowers=$this->leads_model->getLeadDetailsById($data['leadid']);
				$leadName=$assignedNdFollowers->name;
				if($assignedNdFollowers){
				$assignedUsers=explode(',', $assignedNdFollowers->assigned);
				$followers=explode(',', $assignedNdFollowers->followers);
				$company=$assignedNdFollowers->company;
				$this->load->model('emails_model');
				$emailAddress="";
				$subject="Changed to customer";
				$message="";
				foreach ($assignedUsers as $key => $assign) {
				$assignedUserRecord=$this->leads_model->getTableRecordById('tblstaff',array('staffid'=>$assign));
				$emailAddress=$assignedUserRecord->email;
				$name=$assignedUserRecord->firstname." ".$assignedUserRecord->lastname;
				$message.="Hi ".$name." ,<br/>";
				$message.="Congratulations! <br/>";
				$message.="".$leadName." has been converted to customer. <br/>";
				$message.="To view Customer click here: <a target='_blank' href='".admin_url('clients/client/'.$id)."'>".$data['company']."</a>  <br/>";
				$message.="Best regards,<br/>";
				$message.="VersiPOS Team";
				$sendMail=$this->emails_model->report_email($emailAddress,$subject,$message);
				$message="";

				}

				foreach ($followers as $key => $follow) {
				$followersUserRecord=$this->leads_model->getTableRecordById('tblstaff',array('staffid'=>$follow));
				$emailAddress=$followersUserRecord->email;
				$name=$followersUserRecord->firstname." ".$followersUserRecord->lastname;
				$message.="Hi ".$name." ,<br/>";
				$message.="Congratulations! <br/>";
				$message.="".$leadName." has been converted to customer. <br/>";
				$message.="To view Customer click here: <a target='_blank' href='".admin_url('clients/client/'.$id)."'>".$data['company']."</a>  <br/>";
				$message.="Best regards,<br/>";
				$message.="VersiPOS Team";
				$sendMail=$this->emails_model->report_email($emailAddress,$subject,$message);
				$message="";

				}
				}
				//salik code end
                set_alert('success', _l('lead_to_client_base_converted_success'));
                logActivity('Created Lead Client Profile [LeadID: ' . $data['leadid'] . ', ClientID: ' . $id . ']');
                do_action('lead_converted_to_customer', array('lead_id'=>$data['leadid'], 'customer_id'=>$id));
                redirect(admin_url('clients/client/' . $id));
            }
        }
    }

    // Ajax
    /* Used in kanban when dragging */
    public function update_kan_ban_lead_status()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->leads_model->update_lead_status($this->input->post());
        }
    }

    public function update_status_order()
    {
        if ($post_data = $this->input->post()) {
            $this->leads_model->update_status_order($post_data);
        }
    }

    public function add_lead_attachment()
    {
        $id = $this->input->post('id');
        $lastFile = $this->input->post('last_file');

        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
            $this->access_denied_ajax();
        }

        handle_lead_attachments($id);
        echo json_encode(array('leadView'=>$lastFile ? $this->_get_lead_data($id) : array(), 'id'=>$id));
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->leads_model->add_attachment_to_database($this->input->post('lead_id'), $this->input->post('files'), $this->input->post('external'));
        }
    }

    public function delete_attachment($id)
    {
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
            $this->access_denied_ajax();
        }
        echo json_encode(array(
            'success' => $this->leads_model->delete_lead_attachment($id),
        ));
    }

    public function delete_note($id)
    {
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
            $this->access_denied_ajax();
        }
        echo json_encode(array(
            'success' => $this->misc_model->delete_note($id),
        ));
    }

    public function update_all_proposal_emails_linked_to_lead($id)
    {
        $success = false;
        $email   = '';
        if ($this->input->post('update')) {
            $this->load->model('proposals_model');

            $this->db->select('email');
            $this->db->where('id', $id);
            $email = $this->db->get('tblleads')->row()->email;

            $proposals     = $this->proposals_model->get('', array(
                'rel_type' => 'lead',
                'rel_id' => $id,
            ));
            $affected_rows = 0;

            foreach ($proposals as $proposal) {
                $this->db->where('id', $proposal['id']);
                $this->db->update('tblproposals', array(
                    'email' => $email,
                ));
                if ($this->db->affected_rows() > 0) {
                    $affected_rows++;
                }
            }

            if ($affected_rows > 0) {
                $success = true;
            }
        }

        echo json_encode(array(
            'success' => $success,
            'message' => _l('proposals_emails_updated', array(
                _l('lead_lowercase'),
                $email,
            )),
        ));
    }

    public function save_form_data()
    {
        $data = $this->input->post();

        // form data should be always sent to the request and never should be empty
        // this code is added to prevent losing the old form in case any errors
        if (!isset($data['formData']) || isset($data['formData']) && !$data['formData']) {
            echo json_encode(array(
                'success' => false,
            ));
            die;
        }
        $this->db->where('id', $data['id']);
        $this->db->update('tblwebtolead', array(
            'form_data' => $data['formData'],
        ));
        if ($this->db->affected_rows() > 0) {
            echo json_encode(array(
                'success' => true,
                'message' => _l('updated_successfully', _l('web_to_lead_form')),
            ));
        } else {
            echo json_encode(array(
                'success' => false,
            ));
        }
    }

    public function form($id = '')
    {
        if (!is_admin()) {
            access_denied('Web To Lead Access');
        }
        if ($this->input->post()) {
            if ($id == '') {
                $data = $this->input->post();
                if($this->session->userdata('portfolio_id'))
                {
                   $data['portfolio_id']= $this->session->userdata('portfolio_id');
               }
               $id   = $this->leads_model->add_form($data);
               if ($id) {
                set_alert('success', _l('added_successfully', _l('web_to_lead_form')));
                redirect(admin_url('leads/form/' . $id));
            }
        } else {
            $success = $this->leads_model->update_form($id, $this->input->post());
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('web_to_lead_form')));
            }
            redirect(admin_url('leads/form/' . $id));
        }
    }

    $data['formData'] = array();
    $custom_fields    = get_custom_fields('leads', 'type != "link"');

    $cfields          = format_external_form_custom_fields($custom_fields);
    $data['title']    = _l('web_to_lead');

    if ($id != '') {
        $data['form']     = $this->leads_model->get_form(array(
            'id' => $id,
        ));
        $data['title']    = $data['form']->name . ' - ' . _l('web_to_lead_form');
        $data['formData'] = $data['form']->form_data;
    }

    $this->load->model('roles_model');
    $data['roles']    = $this->roles_model->get();
    $data['sources']  = $this->leads_model->get_source();
    $data['statuses'] = $this->leads_model->get_status();

    $data['members'] = $this->staff_model->get('', 1, array(
        'is_not_staff' => 0,
    ));

    $data['languages']           = $this->app->get_available_languages();
    $data['cfields']             = $cfields;
    $data['form_builder_assets'] = true;

    $db_fields = array();
    $fields    = array(
        'name',
        'title',
        'email',
        'phonenumber',
        'company',
        'address',
        'city',
        'state',
        'country',
        'zip',
        'description',
        'website',
    );

    $fields = do_action('lead_form_available_database_fields', $fields);

    $className = 'form-control';

    foreach ($fields as $f) {
        $_field_object = new stdClass();
        $type          = 'text';

        if ($f == 'email') {
            $type = 'email';
        } elseif ($f == 'description' || $f == 'address') {
            $type = 'textarea';
        } elseif ($f == 'country') {
            $type = 'select';
        }

        if ($f == 'name') {
            $label = _l('lead_add_edit_name');
        } elseif ($f == 'email') {
            $label = _l('lead_add_edit_email');
        } elseif ($f == 'phonenumber') {
            $label = _l('lead_add_edit_phonenumber');
        } else {
            $label = _l('lead_' . $f);
        }

        $field_array = array(
            'type' => $type,
            'label' => $label,
            'className' => $className,
            'name' => $f,
        );

        if ($f == 'country') {
            $field_array['values'] = array();
            $countries             = get_all_countries();
            foreach ($countries as $country) {
                $selected = false;
                if (get_option('customer_default_country') == $country['country_id']) {
                    $selected = true;
                }
                array_push($field_array['values'], array(
                    'label' => $country['short_name'],
                    'value' => (int) $country['country_id'],
                    'selected' => $selected,
                ));
            }
        }

        if ($f == 'name') {
            $field_array['required'] = true;
        }

        $_field_object->label    = $label;
        $_field_object->name     = $f;
        $_field_object->fields   = array();
        $_field_object->fields[] = $field_array;
        $db_fields[]             = $_field_object;
    }
    $data['bodyclass'] = 'web-to-lead-form';
    $data['db_fields'] = $db_fields;
    $this->load->view('admin/leads/formbuilder', $data);
}

public function forms($id = '')
{
    if (!is_admin()) {
        access_denied('Web To Lead Access');
    }

    if ($this->input->is_ajax_request()) {
        $this->app->get_table_data('web_to_lead');
    }

    $data['title'] = _l('web_to_lead');
    $this->load->view('admin/leads/forms', $data);
}

public function delete_form($id)
{
    if (!is_admin()) {
        access_denied('Web To Lead Access');
    }

    $success = $this->leads_model->delete_form($id);
    if ($success) {
        set_alert('success', _l('deleted', _l('web_to_lead_form')));
    }

    redirect(admin_url('leads/forms'));
}

    // Sources
/* Manage leads sources */
public function sources()
{
    if (!is_admin()) {
        access_denied('Leads Sources');
    }
    $data['sources'] = $this->leads_model->get_source();
    $data['title']   = 'Leads sources';
    $this->load->view('admin/leads/manage_sources', $data);
}

/* Add or update leads sources */
public function source()
{
    if (!is_admin() && get_option('staff_members_create_inline_lead_source') == '0') {
        access_denied('Leads Sources');
    }
    if ($this->input->post()) {
        $data = $this->input->post();
        if (!$this->input->post('id')) {
            $inline = isset($data['inline']);
            if (isset($data['inline'])) {
                unset($data['inline']);
            }
            if($this->session->userdata('portfolio_id'))
            {
               $data['portfolio_id'] = $this->session->userdata('portfolio_id');	
           }	

           $id = $this->leads_model->add_source($data);

           if (!$inline) {
            if ($id) {
                set_alert('success', _l('added_successfully', _l('lead_source')));
            }
        } else {
            echo json_encode(array('success'=>$id ? true : fales, 'id'=>$id));
        }
    } else {
        $id   = $data['id'];
        unset($data['id']);
        $success = $this->leads_model->update_source($data, $id);
        if ($success) {
            set_alert('success', _l('updated_successfully', _l('lead_source')));
        }
    }
}
}

/* Delete leads source */
public function delete_source($id)
{
    if (!is_admin()) {
        access_denied('Delete Lead Source');
    }
    if (!$id) {
        redirect(admin_url('leads/sources'));
    }
    $response = $this->leads_model->delete_source($id);
    if (is_array($response) && isset($response['referenced'])) {
        set_alert('warning', _l('is_referenced', _l('lead_source_lowercase')));
    } elseif ($response == true) {
        set_alert('success', _l('deleted', _l('lead_source')));
    } else {
        set_alert('warning', _l('problem_deleting', _l('lead_source_lowercase')));
    }
    redirect(admin_url('leads/sources'));
}

    // Statuses
/* View leads statuses */
public function statuses()
{
    if (!is_admin()) {
        access_denied('Leads Statuses');
    }
    $data['statuses'] = $this->leads_model->get_status();
    $data['title']    = 'Leads statuses';
    $this->load->view('admin/leads/manage_statuses', $data);
}

/* Add or update leads status */
public function status()
{
    if (!is_admin() && get_option('staff_members_create_inline_lead_status') == '0') {
        access_denied('Leads Statuses');
    }
    if ($this->input->post()) {
        $data = $this->input->post();
        if (!$this->input->post('id')) {
            $inline = isset($data['inline']);
            if (isset($data['inline'])) {
                unset($data['inline']);
            }

            if($this->session->userdata('portfolio_id'))
            {
               $data['portfolio_id'] = $this->session->userdata('portfolio_id');	
           }	

           $id = $this->leads_model->add_status($data);
           if (!$inline) {
            if ($id) {
                set_alert('success', _l('added_successfully', _l('lead_status')));
            }
        } else {
            echo json_encode(array('success'=>$id ? true : fales, 'id'=>$id));
        }
    } else {
        $id   = $data['id'];
        unset($data['id']);
        $success = $this->leads_model->update_status($data, $id);
        if ($success) {
            set_alert('success', _l('updated_successfully', _l('lead_status')));
        }
    }
}
}

/* Delete leads status from databae */
public function delete_status($id)
{
    if (!is_admin()) {
        access_denied('Leads Statuses');
    }
    if (!$id) {
        redirect(admin_url('leads/statuses'));
    }
    $response = $this->leads_model->delete_status($id);
    if (is_array($response) && isset($response['referenced'])) {
        set_alert('warning', _l('is_referenced', _l('lead_status_lowercase')));
    } elseif ($response == true) {
        set_alert('success', _l('deleted', _l('lead_status')));
    } else {
        set_alert('warning', _l('problem_deleting', _l('lead_status_lowercase')));
    }
    redirect(admin_url('leads/statuses'));
}

/* Add new lead note */
public function add_note($rel_id)
{
    if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($rel_id)) {
        $this->access_denied_ajax();
    }

    $assignedNdFollowers=$this->leads_model->getLeadDetailsById($rel_id);
    if ($this->input->post()) {
        $data = $this->input->post();

        if ($data['contacted_indicator'] == 'yes') {
            $contacted_date         = to_sql_date($data['custom_contact_date'], true);
            $data['date_contacted'] = $contacted_date;
        }

        unset($data['contacted_indicator']);
        unset($data['custom_contact_date']);

            // Causing issues with duplicate ID or if my prefixed file for lead.php is used
        $data['description'] = isset($data['lead_note_description']) ? $data['lead_note_description'] : $data['description'];

        if (isset($data['lead_note_description'])) {
            unset($data['lead_note_description']);
        }

        $note_id = $this->misc_model->add_note($data, 'lead', $rel_id);

        if ($note_id) {
            if (isset($contacted_date)) {
                $this->db->where('id', $rel_id);
                $this->db->update('tblleads', array(
                    'lastcontact' => $contacted_date,
                ));
                if ($this->db->affected_rows() > 0) {
                    $this->leads_model->log_lead_activity($rel_id, 'not_lead_activity_contacted', false, serialize(array(
                        get_staff_full_name(get_staff_user_id()),
                        _dt($contacted_date),
                    )));
                }
            }
        }

if($assignedNdFollowers){
        $assignedUsers=explode(',', $assignedNdFollowers->assigned);
        $followers=explode(',', $assignedNdFollowers->followers);
        $company=$assignedNdFollowers->name;
        $this->load->model('emails_model');
        $emailAddress="";
        $subject="Note Added";
        $message="";
        foreach ($assignedUsers as $key => $assign) {
            $assignedUserRecord=$this->leads_model->getTableRecordById('tblstaff',array('staffid'=>$assign));
            $emailAddress=$assignedUserRecord->email;
            $name=$assignedUserRecord->firstname." ".$assignedUserRecord->lastname;
            $message.="Hi ".$name." ,<br/>";
            $message.="A new message have been added to the Lead  [".$company."] <br/>";
            $message.="To view the lead please click  <a target='_blank' href='".admin_url('leads/index/'.$rel_id)."'>here</a>  <br/>";
            $message.="Note : ".$_POST['lead_note_description']." <br/>";
            $message.="Best regards,<br/>";
            $message.="VersiPOS Team";
            $sendMail=$this->emails_model->report_email($emailAddress,$subject,$message);
            $message="";
            
        }
        
        foreach ($followers as $key => $follow) {
            $followersUserRecord=$this->leads_model->getTableRecordById('tblstaff',array('staffid'=>$follow));
            $emailAddress=$followersUserRecord->email;
            $name=$followersUserRecord->firstname." ".$followersUserRecord->lastname;
            $message.="Hi ".$name." ,<br/>";
            $message.="A new message have been added to the Lead  [".$company."] <br/>";
            $message.="To view the lead please click <a target='_blank' href='".admin_url('leads/index/'.$rel_id)."'>here</a> <br/>";
            $message.="Note : ".$_POST['lead_note_description']." <br/>";
            $message.="Best regards,<br/>";
            $message.="VersiPOS Team";
            $sendMail=$this->emails_model->report_email($emailAddress,$subject,$message);
            $message="";
            
        }
    }

    }
    echo json_encode(array('leadView'=>$this->_get_lead_data($rel_id), 'id'=>$rel_id));
}

public function test_email_integration()
{
    if (!is_admin()) {
        access_denied('Leads Test Email Integration');
    }

    require_once(APPPATH . 'third_party/php-imap/Imap.php');

    $mail = $this->leads_model->get_email_integration();
    $ps   = $mail->password;
    if (false == $this->encryption->decrypt($ps)) {
        set_alert('danger', _l('failed_to_decrypt_password'));
        redirect(admin_url('leads/email_integration'));
    }
    $mailbox    = $mail->imap_server;
    $username   = $mail->email;
    $password   = $this->encryption->decrypt($ps);
    $encryption = $mail->encryption;
        // open connection
    $imap       = new Imap($mailbox, $username, $password, $encryption);

    if ($imap->isConnected() === false) {
        set_alert('danger', _l('lead_email_connection_not_ok') . '<br /><b>' . $imap->getError() . '</b>');
    } else {
        set_alert('success', _l('lead_email_connection_ok'));
    }

    redirect(admin_url('leads/email_integration'));
}

public function email_integration()
{
    if (!is_admin()) {
        access_denied('Leads Email Intregration');
    }
    if ($this->input->post()) {
        $data = $this->input->post();

			// if($this->session->userdata('portfolio_id'))
			// {
				// $data['portfolio_id'] = $this->session->userdata('portfolio_id');	
			// }	

        $data['password'] = $this->input->post('password', false);

        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        $success = $this->leads_model->update_email_integration($data);
        if ($success) {
            set_alert('success', _l('leads_email_integration_updated'));
        }
        redirect(admin_url('leads/email_integration'));
    }
    $data['roles']    = $this->roles_model->get();
    $data['sources']  = $this->leads_model->get_source();
    $data['statuses'] = $this->leads_model->get_status();

    $data['members'] = $this->staff_model->get('', 1, array(
        'is_not_staff' => 0,
    ));

    $data['title']   = _l('leads_email_integration');
    $data['mail']    = $this->leads_model->get_email_integration();
    $data['bodyclass']    = 'leads-email-integration';
    $this->load->view('admin/leads/email_integration', $data);
}

public function change_status_color()
{
    if ($this->input->post()) {
        $this->leads_model->change_status_color($this->input->post());
    }
}

public function import()
{
    if (!is_admin() && get_option('allow_non_admin_members_to_import_leads') != '1'){
        access_denied('Leads Import');
    }

    $simulate_data  = array();
    $total_imported = 0;
    if ($this->input->post()) {
        $simulate = $this->input->post('simulate');
        if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                // Get the temp file path
            $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Setup our new file path
                $newFilePath = TEMP_FOLDER . $_FILES['file_csv']['name'];
                if (!file_exists(TEMP_FOLDER)) {
                    mkdir(TEMP_FOLDER, 777);
                }
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $import_result = true;
                    $fd            = fopen($newFilePath, 'r');
                    $rows          = array();
                    while ($row = fgetcsv($fd)) {
                        $rows[] = $row;
                    }
                    fclose($fd);
                    $data['total_rows_post'] = count($rows);
                    if (count($rows) <= 1) {
                        set_alert('warning', 'Not enought rows for importing');
                        redirect(admin_url('leads/import'));
                    }

                    unset($rows[0]);
                    if ($simulate) {
                        if (count($rows) > 500) {
                            set_alert('warning', 'Recommended splitting the CSV file into smaller files. Our recomendation is 500 row, your CSV file has ' . count($rows));
                        }
                    }
                    $db_temp_fields = $this->db->list_fields('tblleads');
                    array_push($db_temp_fields, 'tags');

                    $db_fields      = array();
                    foreach ($db_temp_fields as $field) {
                        if (in_array($field, $this->not_importable_leads_fields)) {
                            continue;
                        }
                        $db_fields[] = $field;
                    }
                    $custom_fields = get_custom_fields('leads');
                    $_row_simulate = 0;
                    foreach ($rows as $row) {
                            // do for db fields
                        $insert = array();
                        for ($i = 0; $i < count($db_fields); $i++) {
                                // Avoid errors on nema field. is required in database
                            if ($db_fields[$i] == 'name' && $row[$i] == '') {
                                $row[$i] = '/';
                            } elseif ($db_fields[$i] == 'country') {
                                if ($row[$i] != '') {
                                    if (!is_numeric($row[$i])) {
                                        $this->db->where('iso2', $row[$i]);
                                        $this->db->or_where('short_name', $row[$i]);
                                        $this->db->or_where('long_name', $row[$i]);
                                        $country = $this->db->get('tblcountries')->row();
                                        if ($country) {
                                            $row[$i] = $country->country_id;
                                        } else {
                                            $row[$i] = 0;
                                        }
                                    }
                                } else {
                                    $row[$i] = 0;
                                }
                            }
                            if($row[$i] === 'NULL' || $row[$i] === 'null') {
                                $row[$i] = '';
                            }
                            $insert[$db_fields[$i]] = $row[$i];
                        }

                        if (count($insert) > 0) {
                            if (isset($insert['email']) && $insert['email'] != '') {
                                if (total_rows('tblleads', array('email'=>$insert['email'])) > 0) {
                                    continue;
                                }
                            }
                            $total_imported++;
                            $insert['dateadded']   = date('Y-m-d H:i:s');
                            $insert['addedfrom']   = get_staff_user_id();
                                //   $insert['lastcontact'] = null;
                            $insert['status']      = $this->input->post('status');
                            $insert['source']      = $this->input->post('source');
                            if ($this->input->post('responsible')) {
                                $insert['assigned'] = $this->input->post('responsible');
                            }
                            if (!$simulate) {
                                foreach ($insert as $key=>$val) {
                                    $insert[$key] = trim($val);
                                }
                                if (isset($insert['tags'])) {
                                    $tags = $insert['tags'];
                                    unset($insert['tags']);
                                }
                                $this->db->insert('tblleads', $insert);
                                $leadid = $this->db->insert_id();
                            } else {
                                if ($insert['country'] != 0) {
                                    $c = get_country($insert['country']);
                                    if ($c) {
                                        $insert['country'] = $c->short_name;
                                    }
                                } else {
                                    $insert['country'] = '';
                                }
                                $simulate_data[$_row_simulate] = $insert;
                                $leadid                        = true;
                            }
                            if ($leadid) {
                                if (!$simulate) {
                                    handle_tags_save($tags, $leadid, 'lead');
                                }
                                $insert = array();
                                foreach ($custom_fields as $field) {
                                    if (!$simulate) {
                                        if ($row[$i] != '' && $row[$i] !== 'NULL' && $row[$i] !== 'null') {
                                            $this->db->insert('tblcustomfieldsvalues', array(
                                                'relid' => $leadid,
                                                'fieldid' => $field['id'],
                                                'value' => trim($row[$i]),
                                                'fieldto' => 'leads',
                                            ));
                                        }
                                    } else {
                                        $simulate_data[$_row_simulate][$field['name']] = $row[$i];
                                    }
                                    $i++;
                                }
                            }
                        }
                        $_row_simulate++;
                        if ($simulate && $_row_simulate >= 100) {
                            break;
                        }
                    }
                    unlink($newFilePath);
                }
            } else {
                set_alert('warning', _l('import_upload_failed'));
            }
        }
    }
    $data['statuses'] = $this->leads_model->get_status();
    $data['sources']  = $this->leads_model->get_source();

    $data['members'] = $this->staff_model->get('', 1);

    if (count($simulate_data) > 0) {
        $data['simulate'] = $simulate_data;
    }

    if (isset($import_result)) {
        set_alert('success', _l('import_total_imported', $total_imported));
    }

    $data['not_importable'] = $this->not_importable_leads_fields;
    $data['title']          = _l('import');
    $this->load->view('admin/leads/import', $data);
}

public function email_exists()
{
    if ($this->input->post()) {
            // First we need to check if the email is the same
        $leadid = $this->input->post('leadid');

        if ($leadid != '') {
            $this->db->where('id', $leadid);
            $_current_email = $this->db->get('tblleads')->row();
            if ($_current_email->email == $this->input->post('email')) {
                echo json_encode(true);
                die();
            }
        }
        $exists = total_rows('tblleads', array(
            'email' => $this->input->post('email'),
        ));
        if ($exists > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }
}

public function bulk_action()
{
    if (!is_staff_member()) {
        $this->access_denied_ajax();
    }

    do_action('before_do_bulk_action_for_leads');
    $total_deleted = 0;
    if ($this->input->post()) {
        $ids      = $this->input->post('ids');
        $status   = $this->input->post('status');
        $source   = $this->input->post('source');
        $assigned = $this->input->post('assigned');
        $visibility = $this->input->post('visibility');
        $tags = $this->input->post('tags');
        $last_contact = $this->input->post('last_contact');
        $has_permission_delete = has_permission('leads', '', 'delete');
        if (is_array($ids)) {
            foreach ($ids as $id) {
                if ($this->input->post('mass_delete')) {
                    if ($has_permission_delete) {
                        if ($this->leads_model->delete($id)) {
                            $total_deleted++;
                        }
                    }
                } else {
                    if ($status || $source || $assigned || $last_contact || $visibility) {
                        $update = array();
                        if ($status) {
                                // We will use the same function to update the status
                            $this->leads_model->update_lead_status(array(
                                'status' => $status,
                                'leadid' => $id,
                            ));
                        }
                        if ($source) {
                            $update['source'] = $source;
                        }
                        if ($assigned) {
                            $update['assigned'] = $assigned;
                        }
                        if ($last_contact) {
                            $last_contact = to_sql_date($last_contact, true);
                            $update['lastcontact'] = $last_contact;
                        }

                        if ($visibility) {
                            if ($visibility == 'public') {
                                $update['is_public'] = 1;
                            } else {
                                $update['is_public'] = 0;
                            }
                        }

                        if (count($update) > 0) {
                            $this->db->where('id', $id);
                            $this->db->update('tblleads', $update);
                        }
                    }
                    if ($tags) {
                        handle_tags_save($tags, $id, 'lead');
                    }
                }
            }
        }
    }

    if ($this->input->post('mass_delete')) {
        set_alert('success', _l('total_leads_deleted', $total_deleted));
    }
}

private function access_denied_ajax()
{
    header("HTTP/1.0 404 Not Found");
    echo _l('access_denied');
    die;
}
    //added by Salik
public function followers(){

   if (!is_admin()) {
    access_denied('services');
}
if ($this->input->is_ajax_request()) {
    $this->app->get_table_data('followers');
}

$data['title'] = _l('Followers');

$this->load->view('admin/leads/followers_manage', $data); 
}
public function add_followers(){

    if ($this->input->is_ajax_request()) {
        $data = $this->input->post();
        if ($data['id'] == '') {
         $followers_data=array('name'=>$data['name'],'email'=>$data['email'],'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s'));


         $id = $this->leads_model->add_followers($followers_data);
         $message = $id ? _l('added_successfully', _l('Follower')) : '';

         echo json_encode(array(
            'success' => $id ? true : false,
            'message' => $message,
            'id'=>$id,
            'name'=>$data['name'],
        ));
     } else {
        $success = $this->leads_model->edit_followers($data);
        $message = '';
        if ($success == true) {
            $message = _l('updated_successfully', _l('Follower'));
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message,
        )); 
    }
}

}
public function delete_follower($id)
{
    if (!is_admin()) {
        access_denied('Delete Customer Group');
    }
    if (!$id) {
        redirect(admin_url('clients/services'));
    }
    $response = $this->leads_model->delete_follower($id);
    if ($response == true) {
        set_alert('success', _l('deleted', _l('Follower')));
    } else {
        set_alert('warning', _l('problem_deleting', _l('customer_group_lowercase')));
    }
    redirect(admin_url('leads/followers'));
}


    public function contact($customer_id, $contact_id = '')
    {   
       
        // if (!has_permission('customers', '', 'view')) {
        //     if (!is_customer_admin($customer_id)) {
        //         echo _l('access_denied');
        //         die;
        //     }
        // }
       // print_r($this->input->post()); exit; 
        $data['customer_id'] = $customer_id;
        $data['contactid']   = $contact_id;
        $data['client_id_for_page'] = $customer_id;
            
        if ($this->input->post()) {
            // echo "<pre>";
            // print_r($this->input->post()); exit; 
            $this->session->set_userdata
            (
                array
                (
                    'redirect_id_param'  => 'one'
                )
            );
                            
            $data = $this->input->post();
            $data['password'] = $this->input->post('password',false);
            unset($data['contactid']);
            if ($contact_id == '') {
                if (!has_permission('customers', '', 'create')) {
                    if (!is_customer_admin($customer_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;
                    }
                }
               // unset($_POST['old_existing_account']);
                if($this->input->post('old_existing_account')){
                    $id  = $this->leads_model->add_contact($data, $customer_id);
                }
                if($this->input->post('company_id') != '' && $this->input->post('old_existing_account') == ''){ 
                    $data['portfolio_id'] = $this->session->userdata('portfolio_id'); 

                    $id  = $this->leads_model->add_contact($data, $customer_id);
                }
                //die('there');
                //here start
                // if(!$this->input->post('old_existing_account')){
                // $data = array(
                        // 'company_id'      => $customer_id,
                        // 'contact_id'      => $id
                    // );
                // $rel_contact = $this->clients_model->contacts_rel_clients($data);
                // }
                // here end     
                
                // $updateData = array('contactsid'=>$id);
                
                // $this->clients_model->update_contact($updateData, $id);
                
                $message = '';
                $success = false;
                if ($id) {
                    $action = 'Contact "'.$data['first_name'].' '.$data['last_name'].'" Created ';
                    $tabname = "profile";
                    $heading = "Company Profile Created";
                    // $this->zip_invoices_history($customer_id, $action, $tabname, $heading);
                    
                    handle_contact_profile_image_upload($id);
                    $success = true;
                    $message = _l('added_successfully', _l('contact'));
                }
                
                 // $ch = array(
                    // 'success' => $success,
                    // 'message' => $message,
                    // 'has_primary_contact'=>(total_rowss('tblcontacts', array('userid'=>$customer_id, 'is_primary'=>1)) > 0 ? true : false),
                    // 'is_individual'=>is_empty_customer_company($customer_id) && total_rows('tblcontacts',array('userid'=>$customer_id)) == 1,
                    // );
                // echo "<pre>";
                // print_r($ch); 
                // exit; 
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'has_primary_contact'=>(total_rowss('tblleadcontacts', array('userid'=>$customer_id, 'is_primary'=>1)) > 0 ? true : false),
                    'is_individual'=>is_empty_customer_company($customer_id) && total_rowss('tblleadcontacts',array('userid'=>$customer_id)) == 1,
                ));
                die;
            } else {
                if (!has_permission('customers', '', 'edit')) {
                    if (!is_customer_admin($customer_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;
                    }
                }
                $original_contact = $this->leads_model->get_contact($contact_id);
                
                if($customer_id){
                        $success = $this->leads_model->update_contact($data, $contact_id); 
                    }   
                $message          = '';
                $proposal_warning = false;
                $original_email   = '';
                $updated          = false;
                if (is_array($success)) {
                    if (isset($success['set_password_email_sent'])) {
                        $message = _l('set_password_email_sent_to_client');
                    } elseif (isset($success['set_password_email_sent_and_profile_updated'])) {
                        $updated = true;
                        $message = _l('set_password_email_sent_to_client_and_profile_updated');
                    }
                } else {
                    if ($success == true) {
                        $updated = true;
                        $message = _l('updated_successfully', _l('contact'));
                    }
                }
                if (handle_contact_profile_image_upload($contact_id) && !$updated) {
                    $message = _l('updated_successfully', _l('contact'));
                    $success = true;
                }
                if ($updated == true) {
                    $contact = $this->leads_model->get_contact($contact_id);
                    if (total_rows('tblproposals', array(
                        'rel_type' => 'customer',
                        'rel_id' => $contact->userid,
                        'email' => $original_contact->lead_email,
                    )) > 0 && ($original_contact->lead_email != $contact->lead_email)) {
                        $proposal_warning = true;
                        $lead_email   = $original_contact->lead_email;
                    }
                }
                echo json_encode(array(
                    'success' => $success,
                    'proposal_warning' => $proposal_warning,
                    'message' => $message,
                    'original_email' => $original_email,
                    'has_primary_contact'=>(total_rowss('tblleadcontacts', array('userid'=>$customer_id)) > 0 ? true : false),
                ));
                die;
            }
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('contact_lowercase'));
        } else {
            $where = array(
                'company_id'=>$customer_id, 
                'contact_id'=>$contact_id
            );
            $data['contact'] = $this->leads_model->get_contact_rel($where);
                
            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Contact Not Found',
                ));
                die;
            }
            $title = $data['contact']->first_name . ' ' . $data['contact']->last_name;
        }

        $data['customer_permissions'] = get_contact_permissions();
        $data['title']                = $title;
        //$join = 1;
        $where = ' tblleadcontacts.portfolio_id = ' . $this->session->userdata('portfolio_id'); 
        $data['contactsdata'] = $this->inventory_model->get('tblleadcontacts', $objArr=TRUE, $where );
        
        if($data['client_id_for_page'] == 'undefined')
        {
            $this->db->select("*");
            $this->db->from('tblclients');
            $this->db->where('portfolio_id', $this->session->userdata('portfolio_id'));
            $qry__ = $this->db->get();
            if($qry__->num_rows() > 0)
            {
                $data['company_lst'] = $qry__->result_array();
            }
        }
        // echo "<pre>"; print_r($data); exit; 
        $this->load->view('admin/leads/leadContactsModal', $data);
    
    }

     public function delete_contacts_rel_clients($customer_id, $id, $ticket_page_id = '')
    {
		$company_id = $customer_id;
		
		if($ticket_page_id == 1) {
			$query = $this->leads_model->delete_contacts_rel_clients($customer_id, $id);
		} else if($ticket_page_id == 0){
			$query = $this->leads_model->delete_contact($customer_id, $id);
		} else {
			$query = $this->leads_model->delete_contacts_rel_clients($customer_id, $id);
		}
        
        redirect(admin_url('leads'));
    }
     public function change_contact_status($id, $status)
    {
        
            if ($this->input->is_ajax_request()) {
                $this->leads_model->change_contact_status($id, $status);
            }
        
    }

    public function get_existing_check()
    {
    //  echo "ech"; exit ; //echo $company_id; echo $contact_id ; exit; 
        $company_id = $this->input->post('company_id');
        $contact_id = $this->input->post('contact_id');
        $query = $this->leads_model->get_existing_check($company_id , $contact_id);
        
        if($query)
        {           
             echo 1; 
        }
        else
        {   
            echo  0;
        }
    }

    public function contacts($client_id)
    {    
        $this->app->get_table_data('leadscontacts', array(
            'userid' => $client_id,
        ));
    }
    public function get_existing_customer(){
        $userid=$_POST['userid'];
        $customer=$this->leads_model->get_customer_record($userid);
        
        $customerData=array();
        $customersdata['company']=$customer->company;
        $customersdata['phonenumber']=$customer->phonenumber;
        $customersdata['city']=$customer->city;
        $customersdata['state']=$customer->state;
        $customersdata['address']=$customer->address;
        $customersdata['website']=$customer->website;
        $customersdata['zip']=$customer->zip;
        echo json_encode($customersdata);
    }


}//end class
