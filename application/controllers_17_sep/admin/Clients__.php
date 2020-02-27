<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Clients extends Admin_controller
{
    private $not_importable_clients_fields;
    public $pdf_zip;

    public function __construct()
    {
        parent::__construct();
        $this->not_importable_clients_fields = do_action('not_importable_clients_fields',array('userid', 'id', 'is_primary', 'password', 'datecreated', 'last_ip', 'last_login', 'last_password_change', 'active', 'new_pass_key', 'new_pass_key_requested', 'leadid', 'default_currency', 'profile_image', 'default_language', 'direction', 'show_primary_contact', 'invoice_emails', 'estimate_emails', 'project_emails', 'task_emails', 'contract_emails', 'credit_note_emails','addedfrom','last_active_time'));
        // last_active_time is from Chattr plugin, causing issue
    }

    /* List all clients */
    public function index()
    {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
        }
        $this->load->model('contracts_model');
        $data['contract_types'] = $this->contracts_model->get_contract_types();
        $data['groups']         = $this->clients_model->get_groups();
        $data['title']          = _l('clients');

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $this->load->model('invoices_model');
        $data['invoice_statuses'] = $this->invoices_model->get_statuses();

        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('projects_model');
        $data['project_statuses'] = $this->projects_model->get_project_statuses();

        $data['customer_admins'] = $this->clients_model->get_customers_admin_unique_ids();

        $whereContactsLoggedIn = '';
        if (!has_permission('customers', '', 'view')) {
            $whereContactsLoggedIn = ' AND userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id='.get_staff_user_id().')';
        }
        $data['contacts_logged_in_today'] = $this->clients_model->get_contacts('', 'last_login LIKE "'.date('Y-m-d').'%"'.$whereContactsLoggedIn);
        $this->load->view('admin/clients/manage', $data);
    }

    public function table()
    {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                ajax_access_denied();
            }
        }

        $this->app->get_table_data('clients');
    }

		public function all_contacts()
    {
        $data['client_id_for_page'] = $id;
		$data['client_contacts'] = $this->clients_model->client_contacts();
        $data['title'] = _l('customer_contacts');
        $this->load->view('admin/clients/all_contacts', $data);
    }	
	
	// public function all_contacts()
    // {
        // if ($this->input->is_ajax_request()) {
            // $this->app->get_table_data('all_contacts');
        // }
        // $data['title'] = _l('customer_contacts');
        // $this->load->view('admin/clients/all_contacts', $data);
    // }

    /* Edit client or add new client*/
    public function client($id = '')
    {						
        if (!has_permission('customers', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        $data['customer_id']=$id;
        $data['values'] = $this->inventory_model->get('tblcustom', $objArr=TRUE);		
        $data['accounts'] = $this->inventory_model->get('tblclients', $objArr=TRUE);
		$where = array('userid'=>$id);
        $data['contacts'] = $this->inventory_model->get('tblcontacts', $objArr=TRUE,$where);
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('customers', '', 'create')) {
                    access_denied('customers');
                }
				//print_r($this->input->post());exit;
                $data = $this->input->post();

                $save_and_add_contact = false;
                if (isset($data['save_and_add_contact'])) {
                    unset($data['save_and_add_contact']);
                    $save_and_add_contact = true;
                }
                $id = $this->clients_model->add($data);
				//adding  in history start zee code
				if($id)
					{	
						$action = 'Company: "'.$data['company'].'" Created ';
						$tabname = "profile";
						$heading = "Company Profile";
						$this->create_history($id, $action, $tabname, $heading);
					}
				else 
					{
                        echo 'no data save';
                    }	
				// adding in history end zee code	
                if (!has_permission('customers', '', 'view')) {
                    $assign['customer_admins']   = array();
                    $assign['customer_admins'][] = get_staff_user_id();
                    $this->clients_model->assign_admins($assign, $id);
                }
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('client')));
                    if ($save_and_add_contact == false) {
                        redirect(admin_url('clients/client/' . $id));
                    } else {
                        redirect(admin_url('clients/client/' . $id . '?new_contact=true&tab=contacts'));
                    }
                }
            } else {
                if (!has_permission('customers', '', 'edit')) {
                    if (!is_customer_admin($id)) {
                        access_denied('customers');
                    }
                }
				//zee code start
					$this->update_history($this->input->post(), $id);
					//$this->update_map($this->input->post(), $id);
				// ze code end	
                $success = $this->clients_model->update($this->input->post(), $id);
                if ($success == true) {				

                    set_alert('success', _l('updated_successfully', _l('client')));
                }
                redirect(admin_url('clients/client/' . $id));
            }
        }

        if (!$this->input->get('group')) {
            $group = 'profile';
        } else {
            $group = $this->input->get('group');
        }
        // View group
        $data['group']  = $group;
        // Customer groups
        $data['groups'] = $this->clients_model->get_groups();

        if ($id == '') {
            $title = _l('add_new', _l('client_lowercase'));
        } else {
            $client = $this->clients_model->get($id);  //ch
			if (!$client) {
                blank_page('Client Not Found');
            }

            $data['contacts']         = $this->clients_model->get_contacts($id);

            // Fetch data based on groups
            if ($group == 'profile') {
                $data['customer_groups'] = $this->clients_model->get_customer_groups($id);
                $data['customer_admins'] = $this->clients_model->get_admins($id);
            } elseif ($group == 'attachments') {
                $data['attachments']   = get_all_customer_attachments($id);
            } elseif ($group == 'vault') {
                $data['vault_entries'] = do_action('check_vault_entries_visibility', $this->clients_model->get_vault_entries($id));
                if ($data['vault_entries'] === -1) {
                    $data['vault_entries'] = array();
                }
            } elseif ($group == 'estimates') {
                $this->load->model('estimates_model');
                $data['estimate_statuses'] = $this->estimates_model->get_statuses();
            } elseif ($group == 'invoices') {
                $this->load->model('invoices_model');
                $data['invoice_statuses'] = $this->invoices_model->get_statuses();
            } elseif ($group == 'credit_notes') {
                $this->load->model('credit_notes_model');
                $data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();
                $data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($id);
            } elseif ($group == 'payments') {
                $this->load->model('payment_modes_model');
                $data['payment_modes'] = $this->payment_modes_model->get();
            } elseif ($group == 'notes') {
                $data['user_notes'] = $this->misc_model->get_notes($id, 'customer');
            } elseif ($group == 'projects') {
                $this->load->model('projects_model');
                $data['project_statuses'] = $this->projects_model->get_project_statuses();
            } 
			elseif ($group == 'saleopp') {
                $this->load->model('sales_model');
                $data['saleopp_statuses'] = $this->sales_model->get_project_statuses();
            } 
			
			elseif ($group == 'statement') {
                if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
                    set_alert('danger', _l('access_denied'));
                    redirect(admin_url('clients/client/'.$id));
                }
                $contact = $this->clients_model->get_contact(get_primary_contact_user_id($id));
                $email   = '';
                if ($contact) {
                    $email = $contact->email;
                }

                $template_name = 'client-statement';
                $data['template'] = get_email_template_for_sending($template_name, $email);

                $data['template_name']     = $template_name;
                $this->db->where('slug', $template_name);
                $this->db->where('language', 'english');
                $template_result = $this->db->get('tblemailtemplates')->row();

                $data['template_system_name'] = $template_result->name;
                $data['template_id'] = $template_result->emailtemplateid;

                $data['template_disabled'] = false;
                if (total_rows('tblemailtemplates', array('slug'=>$data['template_name'], 'active'=>0)) > 0) {
                    $data['template_disabled'] = true;
                }
            }

            $data['staff']           = $this->staff_model->get('', 1);

            $data['client']        = $client;
            $title                 = $client->company;

            // Get all active staff members (used to add reminder)
            $data['members'] = $data['staff'];

            if (!empty($data['client']->company)) {
                // Check if is realy empty client company so we can set this field to empty
                // The query where fetch the client auto populate firstname and lastname if company is empty
                if (is_empty_customer_company($data['client']->userid)) {
                    $data['client']->company = '';
                }
            }
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        if ($id != '') {
            $customer_currency = $data['client']->default_currency;

            foreach ($data['currencies'] as $currency) {
                if ($customer_currency != 0) {
                    if ($currency['id'] == $customer_currency) {
                        $customer_currency = $currency;
                        break;
                    }
                } else {
                    if ($currency['isdefault'] == 1) {
                        $customer_currency = $currency;
                        break;
                    }
                }
            }

            if (is_array($customer_currency)) {
                $customer_currency = (object) $customer_currency;
            }

            $data['customer_currency'] = $customer_currency;
        }
        
        $data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['title'] = $title;
        $data['client_id_for_page'] = $id;
        $data['client_inventory'] = $this->clients_model->client_inventory($id);
        $this->load->view('admin/clients/client', $data);
    }

    public function save_longitude_and_latitude($client_id)
    {
        if (!has_permission('customers', '', 'edit')) {
            if (!is_customer_admin($client_id)) {
                ajax_access_denied();
            }
        }
		//print_r($this->input->post('longitude'));
		////zee code start
		$this->update_map($this->input->post('longitude'), $this->input->post('latitude'), $client_id);
		////zee code end
		$this->db->where('userid', $client_id);
        $this->db->update('tblclients', array(
            'longitude'=>$this->input->post('longitude'),
            'latitude'=>$this->input->post('latitude'),
        ));
        if ($this->db->affected_rows() > 0) {
            echo 'success';
        } else {
            echo 'false';
        }
    }

    public function contact($customer_id, $contact_id = '')
    {
        if (!has_permission('customers', '', 'view')) {
            if (!is_customer_admin($customer_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['customer_id'] = $customer_id;
        $data['contactid']   = $contact_id;
		

        if ($this->input->post()) {
			if($this->input->post('old_existing_account')){
				$where = $this->input->post('old_existing_account');
				$dataContacts = $this->clients_model->getContacts($where);
				// print_r($dataContacts);
				// exit;
				$data = $this->input->post();
				$data['contactsid'] = $dataContacts[0]['contactsid'];
				$data['firstname'] = $dataContacts[0]['firstname'];
				$data['lastname'] = $dataContacts[0]['lastname'];
				$data['email'] = $dataContacts[0]['email'];
				$data['phonenumber'] = $dataContacts[0]['phonenumber'];
				$data['password'] = $dataContacts[0]['password'];
				$data['profile_image'] = $dataContacts[0]['profile_image'];
			}else{
				$data = $this->input->post();
				$data['password'] = $this->input->post('password',false);
			}
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
                $id      = $this->clients_model->add_contact($data, $customer_id);
				$updateData = array('contactsid'=>$id);
				$this->clients_model->update_contact($updateData, $id);
                $message = '';
                $success = false;
                if ($id) {
					$action = 'Contact "'.$data['firstname'].' '.$data['lastname'].'" Created ';
					$tabname = "profile";
					$heading = "Company Profile Created";
					$this->zip_invoices_history($customer_id, $action, $tabname, $heading);
					
                    handle_contact_profile_image_upload($id);
                    $success = true;
                    $message = _l('added_successfully', _l('contact'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'has_primary_contact'=>(total_rows('tblcontacts', array('userid'=>$customer_id, 'is_primary'=>1)) > 0 ? true : false),
                    'is_individual'=>is_empty_customer_company($customer_id) && total_rows('tblcontacts',array('userid'=>$customer_id)) == 1,
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
                $original_contact = $this->clients_model->get_contact($contact_id);
				
                $success          = $this->clients_model->update_contact($data, $contact_id);
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
                    $contact = $this->clients_model->get_contact($contact_id);
                    if (total_rows('tblproposals', array(
                        'rel_type' => 'customer',
                        'rel_id' => $contact->userid,
                        'email' => $original_contact->email,
                    )) > 0 && ($original_contact->email != $contact->email)) {
                        $proposal_warning = true;
                        $original_email   = $original_contact->email;
                    }
                }
                echo json_encode(array(
                    'success' => $success,
                    'proposal_warning' => $proposal_warning,
                    'message' => $message,
                    'original_email' => $original_email,
                    'has_primary_contact'=>(total_rows('tblcontacts', array('userid'=>$customer_id, 'is_primary'=>1)) > 0 ? true : false),
                ));
                die;
            }
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('contact_lowercase'));
        } else {
            $data['contact'] = $this->clients_model->get_contact($contact_id);

            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Contact Not Found',
                ));
                die;
            }
            $title = $data['contact']->firstname . ' ' . $data['contact']->lastname;
        }

        $data['customer_permissions'] = get_contact_permissions();
        $data['title']                = $title;
		$data['contacts'] = $this->inventory_model->get('tblcontacts', $objArr=TRUE);
		//print_r($data['contacts']);
		$this->load->view('admin/clients/modals/contact', $data);
    
	}

    public function update_file_share_visibility()
    {
        if ($this->input->post()) {
            $file_id           = $this->input->post('file_id');
            $share_contacts_id = array();

            if ($this->input->post('share_contacts_id')) {
                $share_contacts_id = $this->input->post('share_contacts_id');
            }

            $this->db->where('file_id', $file_id);
            $this->db->delete('tblcustomerfiles_shares');

            foreach ($share_contacts_id as $share_contact_id) {
                $this->db->insert('tblcustomerfiles_shares', array(
                    'file_id' => $file_id,
                    'contact_id' => $share_contact_id,
                ));
            }
        }
    }

    public function delete_contact_profile_image($contact_id)
    {
        do_action('before_remove_contact_profile_image');
        if (file_exists(get_upload_path_by_type('contact_profile_images') . $contact_id)) {
            delete_dir(get_upload_path_by_type('contact_profile_images') . $contact_id);
        }
        $this->db->where('id', $contact_id);
        $this->db->update('tblcontacts', array(
            'profile_image' => null,
        ));
    }

    public function mark_as_active($id)
    {
        $this->db->where('userid', $id);
        $this->db->update('tblclients', array(
            'active' => 1,
        ));
        redirect(admin_url('clients/client/' . $id));
    }

    public function update_all_proposal_emails_linked_to_customer($contact_id)
    {
        $success = false;
        $email   = '';
        if ($this->input->post('update')) {
            $this->load->model('proposals_model');

            $this->db->select('email,userid');
            $this->db->where('id', $contact_id);
            $contact = $this->db->get('tblcontacts')->row();

            $proposals     = $this->proposals_model->get('', array(
                'rel_type' => 'customer',
                'rel_id' => $contact->userid,
                'email' => $this->input->post('original_email'),
            ));
            $affected_rows = 0;

            foreach ($proposals as $proposal) {
                $this->db->where('id', $proposal['id']);
                $this->db->update('tblproposals', array(
                    'email' => $contact->email,
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
                _l('contact_lowercase'),
                $contact->email,
            )),
        ));
    }

    public function assign_admins($id)
    {
        if (!has_permission('customers', '', 'create') && !has_permission('customers', '', 'edit')) {
            access_denied('customers');
        }
        $success = $this->clients_model->assign_admins($this->input->post(), $id);
        if ($success == true) {
			
			 $heading 	= "Assignment of Admin in Profile";
			 $tabname 	= "profile";
			 $action 	= "Admin Assigned";
			$this->zip_invoices_history($id, $action, $tabname, $heading);
		
			set_alert('success', _l('updated_successfully', _l('client')));
        }

        redirect(admin_url('clients/client/' . $id . '?tab=customer_admins'));
    }

    public function delete_customer_admin($customer_id, $staff_id)
    {
        if (!has_permission('customers', '', 'create') && !has_permission('customers', '', 'edit')) {
            access_denied('customers');
        }

        $this->db->where('customer_id', $customer_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->delete('tblcustomeradmins');
        redirect(admin_url('clients/client/'.$customer_id).'?tab=customer_admins');
    }

    public function delete_contact($customer_id, $id)
    {
        if (!has_permission('customers', '', 'delete')) {
            if (!is_customer_admin($customer_id)) {
                access_denied('customers');
            }
        }

        $this->clients_model->delete_contact($id);
        redirect(admin_url('clients/client/' . $customer_id . '?tab=contacts'));
    }

    public function contacts($client_id)
    {
        $this->app->get_table_data('contacts', array(
            'client_id' => $client_id,
        ));
    }

    public function upload_attachment($id)
    {
		handle_client_attachments_upload($id);
		$company_id = $id;
		$action = "Attachments Uploaded";
		$tabname = "attachments";
		$heading = "Company Files";
		$this->zip_invoices_history($company_id, $action, $tabname, $heading );
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database($this->input->post('clientid'), 'customer', $this->input->post('files'), $this->input->post('external'));
        }
    }

    public function delete_attachment($customer_id, $id)
    {
        if (has_permission('customers', '', 'delete') || is_customer_admin($customer_id)) {
            $this->clients_model->delete_attachment($id);
			$action ="Company Files Deleted";
			$heading = "Company Attachments";
			$tabname = "attachments";
			$this->zip_invoices_history($customer_id, $action, $tabname, $heading);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    /* Delete client */
    public function delete($id)
    {
        if (!has_permission('customers', '', 'delete')) {
            access_denied('customers');
        }
        if (!$id) {
            redirect(admin_url('clients'));
        }
        $response = $this->clients_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('customer_delete_transactions_warning',_l('invoices').', '._l('estimates').', '._l('credit_notes')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('client')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('client_lowercase')));
        }
        redirect(admin_url('clients'));
    }

    /* Staff can login as client */
    public function login_as_client($id)
    {
        if (is_admin()) {
            login_as_client($id);
        }
        do_action('after_contact_login');
        redirect(site_url());
    }

    public function get_customer_billing_and_shipping_details($id)
    {
        echo json_encode($this->clients_model->get_customer_billing_and_shipping_details($id));
    }

    /* Change client status / active / inactive */
    public function change_contact_status($id, $status)
    {
        if (has_permission('customers', '', 'edit') || is_customer_admin(get_user_id_by_contact_id($id))) {
            if ($this->input->is_ajax_request()) {
                $this->clients_model->change_contact_status($id, $status);
            }
        }
    }

    /* Change client status / active / inactive */
    public function change_client_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->clients_model->change_client_status($id, $status);
        }
    }

    /* Zip function for credit notes */
    public function zip_credit_notes($id)
    {
        $has_permission_view = has_permission('credit_notes', '', 'view');

        if (!$has_permission_view && !has_permission('credit_notes', '', 'view_own')) {
            access_denied('Zip Customer Credit Notes');
        }

        if ($this->input->post()) {
            $status        = $this->input->post('credit_note_zip_status');
            $zip_file_name = $this->input->post('file_name');
            if ($this->input->post('zip-to') && $this->input->post('zip-from')) {
                $from_date = to_sql_date($this->input->post('zip-from'));
                $to_date   = to_sql_date($this->input->post('zip-to'));
                if ($from_date == $to_date) {
                    $this->db->where('date', $from_date);
                } else {
                    $this->db->where('date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
                }
            }
            $this->db->select('id');
            $this->db->from('tblcreditnotes');
            if ($status != 'all') {
                $this->db->where('status', $status);
            }
            $this->db->where('clientid', $id);
            $this->db->order_by('number', 'desc');

            if (!$has_permission_view) {
                $this->db->where('addedfrom', get_staff_user_id());
            }
            $credit_notes = $this->db->get()->result_array();

            $this->load->model('credit_notes_model');

            $this->load->helper('file');
            if (!is_really_writable(TEMP_FOLDER)) {
                show_error('/temp folder is not writable. You need to change the permissions to 755');
            }

            $dir = TEMP_FOLDER . $zip_file_name;

            if (is_dir($dir)) {
                delete_dir($dir);
            }

            if (count($credit_notes) == 0) {
                set_alert('warning', _l('client_zip_no_data_found', _l('credit_notes')));
                redirect(admin_url('clients/client/' . $id . '?group=credit_notes'));
            }

            mkdir($dir, 0777);

            foreach ($credit_notes as $credit_note) {
                $credit_note    = $this->credit_notes_model->get($credit_note['id']);
                $this->pdf_zip   = credit_note_pdf($credit_note);
                $_temp_file_name = slug_it(format_credit_note_number($credit_note->id));
                $file_name       = $dir . '/' . strtoupper($_temp_file_name);
                $this->pdf_zip->Output($file_name . '.pdf', 'F');
            }

            $this->load->library('zip');
            // Read the credit notes
            $this->zip->read_dir($dir, false);
            // Delete the temp directory for the client
			$action = "Zip of Credit Notes Created";
			$tabname = "credit_notes";
			$heading = "Company Credit Notes";
			$this->zip_invoices_history($id, $action, $tabname, $heading);
            delete_dir($dir);
            $this->zip->download(slug_it(get_option('companyname')) . '-credit-notes-' . $zip_file_name . '.zip');
            $this->zip->clear_data();
        }
    }

    public function zip_invoices($id)
    {
        $has_permission_view = has_permission('invoices', '', 'view');
        if (!$has_permission_view && !has_permission('invoices', '', 'view_own')) {
            access_denied('Zip Customer Invoices');
        }
        if ($this->input->post()) {
            $status        = $this->input->post('invoice_zip_status');
            $zip_file_name = $this->input->post('file_name');
            if ($this->input->post('zip-to') && $this->input->post('zip-from')) {
                $from_date = to_sql_date($this->input->post('zip-from'));
                $to_date   = to_sql_date($this->input->post('zip-to'));
                if ($from_date == $to_date) {
                    $this->db->where('date', $from_date);
                } else {
                    $this->db->where('date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
                }
            }
            $this->db->select('id');
            $this->db->from('tblinvoices');
            if ($status != 'all') {
                $this->db->where('status', $status);
            }
            $this->db->where('clientid', $id);
            $this->db->order_by('number,YEAR(date)', 'desc');

            if (!$has_permission_view) {
                $this->db->where('addedfrom', get_staff_user_id());
            }

            $invoices = $this->db->get()->result_array();
            $this->load->model('invoices_model');
            $this->load->helper('file');
            if (!is_really_writable(TEMP_FOLDER)) {
                show_error('/temp folder is not writable. You need to change the permissions to 755');
            }
            $dir = TEMP_FOLDER . $zip_file_name;
            if (is_dir($dir)) {
                delete_dir($dir);
            }
            if (count($invoices) == 0) {
                set_alert('warning', _l('client_zip_no_data_found', _l('invoices')));
                redirect(admin_url('clients/client/' . $id . '?group=invoices'));
            }
            mkdir($dir, 0777);
            foreach ($invoices as $invoice) {
                $invoice_data    = $this->invoices_model->get($invoice['id']);
                $this->pdf_zip   = invoice_pdf($invoice_data);
                $_temp_file_name = slug_it(format_invoice_number($invoice_data->id));
                $file_name       = $dir . '/' . strtoupper($_temp_file_name);
                $this->pdf_zip->Output($file_name . '.pdf', 'F');
            }
            $this->load->library('zip');
            // Read the invoices
            $this->zip->read_dir($dir, false);
			$action = "Zip of Invoices Created";
			$tabname = "invoices";
			$heading =  "Company Invoices";
			$this->zip_invoices_history($id, $action, $tabname, $heading);
            // Delete the temp directory for the client
            delete_dir($dir);
            $this->zip->download(slug_it(get_option('companyname')) . '-invoices-' . $zip_file_name . '.zip');
            $this->zip->clear_data();
        }
    }

    /* Since version 1.0.2 zip client invoices */
    public function zip_estimates($id)
    {
        $has_permission_view = has_permission('estimates', '', 'view');
        if (!$has_permission_view && !has_permission('estimates', '', 'view_own')) {
            access_denied('Zip Customer Estimates');
        }


        if ($this->input->post()) {
            $status        = $this->input->post('estimate_zip_status');
            $zip_file_name = $this->input->post('file_name');
            if ($this->input->post('zip-to') && $this->input->post('zip-from')) {
                $from_date = to_sql_date($this->input->post('zip-from'));
                $to_date   = to_sql_date($this->input->post('zip-to'));
                if ($from_date == $to_date) {
                    $this->db->where('date', $from_date);
                } else {
                    $this->db->where('date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
                }
            }
            $this->db->select('id');
            $this->db->from('tblestimates');
            if ($status != 'all') {
                $this->db->where('status', $status);
            }
            if (!$has_permission_view) {
                $this->db->where('addedfrom', get_staff_user_id());
            }
            $this->db->where('clientid', $id);
            $this->db->order_by('number,YEAR(date)', 'desc');
            $estimates = $this->db->get()->result_array();
            $this->load->helper('file');
            if (!is_really_writable(TEMP_FOLDER)) {
                show_error('/temp folder is not writable. You need to change the permissions to 777');
            }
            $this->load->model('estimates_model');
            $dir = TEMP_FOLDER . $zip_file_name;
            if (is_dir($dir)) {
                delete_dir($dir);
            }
            if (count($estimates) == 0) {
                set_alert('warning', _l('client_zip_no_data_found', _l('estimates')));
                redirect(admin_url('clients/client/' . $id . '?group=estimates'));
            }
            mkdir($dir, 0777);
            foreach ($estimates as $estimate) {
                $estimate_data   = $this->estimates_model->get($estimate['id']);
                $this->pdf_zip   = estimate_pdf($estimate_data);
                $_temp_file_name = slug_it(format_estimate_number($estimate_data->id));
                $file_name       = $dir . '/' . strtoupper($_temp_file_name);
                $this->pdf_zip->Output($file_name . '.pdf', 'F');
            }
            $this->load->library('zip');
            // Read the invoices
            $this->zip->read_dir($dir, false);
            // Delete the temp directory for the client
            delete_dir($dir);
            $this->zip->download(slug_it(get_option('companyname')) . '-estimates-' . $zip_file_name . '.zip');
            $this->zip->clear_data();
        }
    }

    public function zip_payments($id)
    {
        if (!$id) {
            die('No user id');
        }

        $has_permission_view = has_permission('payments', '', 'view');
        if (!$has_permission_view && !has_permission('invoices', '', 'view_own')) {
            access_denied('Zip Customer Payments');
        }

        if ($this->input->post('zip-to') && $this->input->post('zip-from')) {
            $from_date = to_sql_date($this->input->post('zip-from'));
            $to_date   = to_sql_date($this->input->post('zip-to'));
            if ($from_date == $to_date) {
                $this->db->where('tblinvoicepaymentrecords.date', $from_date);
            } else {
                $this->db->where('tblinvoicepaymentrecords.date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
            }
        }
        $this->db->select('tblinvoicepaymentrecords.id as paymentid');
        $this->db->from('tblinvoicepaymentrecords');
        $this->db->where('tblclients.userid', $id);
        if (!$has_permission_view) {
            $this->db->where('invoiceid IN (SELECT id FROM tblinvoices WHERE addedfrom=' . get_staff_user_id() . ')');
        }
        $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tblinvoices.clientid', 'left');
        if ($this->input->post('paymentmode')) {
            $this->db->where('paymentmode', $this->input->post('paymentmode'));
        }
        $payments      = $this->db->get()->result_array();
        $zip_file_name = $this->input->post('file_name');
        $this->load->helper('file');
        if (!is_really_writable(TEMP_FOLDER)) {
            show_error('/temp folder is not writable. You need to change the permissions to 777');
        }
        $dir = TEMP_FOLDER . $zip_file_name;
        if (is_dir($dir)) {
            delete_dir($dir);
        }
        if (count($payments) == 0) {
            set_alert('warning', _l('client_zip_no_data_found', _l('payments')));
            redirect(admin_url('clients/client/' . $id . '?group=payments'));
        }
        mkdir($dir, 0777);
        $this->load->model('payments_model');
        $this->load->model('invoices_model');
        foreach ($payments as $payment) {
            $payment_data               = $this->payments_model->get($payment['paymentid']);
            $payment_data->invoice_data = $this->invoices_model->get($payment_data->invoiceid);
            $this->pdf_zip              = payment_pdf($payment_data);
            $file_name                  = $dir;
            $file_name .= '/' . strtoupper(_l('payment'));
            $file_name .= '-' . strtoupper($payment_data->paymentid) . '.pdf';
            $this->pdf_zip->Output($file_name, 'F');
        }
        $this->load->library('zip');
        // Read the invoices
        $this->zip->read_dir($dir, false);
        // Delete the temp directory for the client
        delete_dir($dir);
        $this->zip->download(slug_it(get_option('companyname')) . '-payments-' . $zip_file_name . '.zip');
        $this->zip->clear_data();
    }

    public function import()
    {
        if (!has_permission('customers', '', 'create')) {
            access_denied('customers');
        }
        $country_fields = array('country', 'billing_country', 'shipping_country');

        $simulate_data  = array();
        $total_imported = 0;
        if ($this->input->post()) {

            // Used when checking existing company to merge contact
            $contactFields = $this->db->list_fields('tblcontacts');

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

                        $data['total_rows_post'] = count($rows);
                        fclose($fd);
                        if (count($rows) <= 1) {
                            set_alert('warning', 'Not enought rows for importing');
                            redirect(admin_url('clients/import'));
                        }
                        unset($rows[0]);
                        if ($this->input->post('simulate')) {
                            if (count($rows) > 500) {
                                set_alert('warning', 'Recommended splitting the CSV file into smaller files. Our recomendation is 500 row, your CSV file has ' . count($rows));
                            }
                        }
                        $client_contacts_fields = $this->db->list_fields('tblcontacts');
                        $i                      = 0;
                        foreach ($client_contacts_fields as $cf) {
                            if ($cf == 'phonenumber') {
                                $client_contacts_fields[$i] = 'contact_phonenumber';
                            }
                            $i++;
                        }
                        $db_temp_fields = $this->db->list_fields('tblclients');
                        $db_temp_fields = array_merge($client_contacts_fields, $db_temp_fields);
                        $db_fields      = array();
                        foreach ($db_temp_fields as $field) {
                            if (in_array($field, $this->not_importable_clients_fields)) {
                                continue;
                            }
                            $db_fields[] = $field;
                        }
                        $custom_fields = get_custom_fields('customers');
                        $_row_simulate = 0;

                        $required = array(
                            'firstname',
                            'lastname',
                            'email',
                        );

                        if (get_option('company_is_required') == 1) {
                            array_push($required, 'company');
                        }

                        foreach ($rows as $row) {
                            // do for db fields
                            $insert    = array();
                            $duplicate = false;
                            for ($i = 0; $i < count($db_fields); $i++) {
                                if (!isset($row[$i])) {
                                    continue;
                                }
                                if ($db_fields[$i] == 'email') {
                                    $email_exists = total_rows('tblcontacts', array(
                                        'email' => $row[$i],
                                    ));
                                    // don't insert duplicate emails
                                    if ($email_exists > 0) {
                                        $duplicate = true;
                                    }
                                }
                                // Avoid errors on required fields;
                                if (in_array($db_fields[$i], $required) && $row[$i] == '' && $db_fields[$i] != 'company') {
                                    $row[$i] = '/';
                                } elseif (in_array($db_fields[$i], $country_fields)) {
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


                            if ($duplicate == true) {
                                continue;
                            }
                            if (count($insert) > 0) {
                                $total_imported++;
                                $insert['datecreated'] = date('Y-m-d H:i:s');
                                if ($this->input->post('default_pass_all')) {
                                    $insert['password'] = $this->input->post('default_pass_all',false);
                                }
                                if (!$this->input->post('simulate')) {
                                    $insert['donotsendwelcomeemail'] = true;
                                    foreach ($insert as $key =>$val) {
                                        $insert[$key] = trim($val);
                                    }

                                    if (isset($insert['company']) && $insert['company'] != '' && $insert['company'] != '/') {
                                        if (total_rows('tblclients', array('company'=>$insert['company'])) === 1) {
                                            $this->db->where('company', $insert['company']);
                                            $existingCompany = $this->db->get('tblclients')->row();
                                            $tmpInsert = array();

                                            foreach ($insert as $key=>$val) {
                                                foreach ($contactFields as $tmpContactField) {
                                                    if (isset($insert[$tmpContactField])) {
                                                        $tmpInsert[$tmpContactField] = $insert[$tmpContactField];
                                                    }
                                                }
                                            }
                                            $tmpInsert['donotsendwelcomeemail'] = true;
                                            if (isset($insert['contact_phonenumber'])) {
                                                $tmpInsert['phonenumber'] = $insert['contact_phonenumber'];
                                            }

                                            $contactid = $this->clients_model->add_contact($tmpInsert, $existingCompany->userid, true);

                                            continue;
                                        }
                                    }
                                    $insert['is_primary'] = 1;

                                    $clientid                        = $this->clients_model->add($insert, true);
                                    if ($clientid) {
                                        if ($this->input->post('groups_in[]')) {
                                            $groups_in = $this->input->post('groups_in[]');
                                            foreach ($groups_in as $group) {
                                                $this->db->insert('tblcustomergroups_in', array(
                                                    'customer_id' => $clientid,
                                                    'groupid' => $group,
                                                ));
                                            }
                                        }
                                        if (!has_permission('customers', '', 'view')) {
                                            $assign['customer_admins']   = array();
                                            $assign['customer_admins'][] = get_staff_user_id();
                                            $this->clients_model->assign_admins($assign, $clientid);
                                        }
                                    }
                                } else {
                                    foreach ($country_fields as $country_field) {
                                        if (array_key_exists($country_field, $insert)) {
                                            if ($insert[$country_field] != 0) {
                                                $c = get_country($insert[$country_field]);
                                                if ($c) {
                                                    $insert[$country_field] = $c->short_name;
                                                }
                                            } elseif ($insert[$country_field] == 0) {
                                                $insert[$country_field] = '';
                                            }
                                        }
                                    }
                                    $simulate_data[$_row_simulate] = $insert;
                                    $clientid                      = true;
                                }
                                if ($clientid) {
                                    $insert = array();
                                    foreach ($custom_fields as $field) {
                                        if (!$this->input->post('simulate')) {
                                            if ($row[$i] != '' && $row[$i] !== 'NULL' && $row[$i] !== 'null') {
                                                $this->db->insert('tblcustomfieldsvalues', array(
                                                    'relid' => $clientid,
                                                    'fieldid' => $field['id'],
                                                    'value' => $row[$i],
                                                    'fieldto' => 'customers',
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
                            if ($this->input->post('simulate') && $_row_simulate >= 100) {
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
        if (count($simulate_data) > 0) {
            $data['simulate'] = $simulate_data;
        }
        if (isset($import_result)) {
            set_alert('success', _l('import_total_imported', $total_imported));
        }
        $data['groups']         = $this->clients_model->get_groups();
        $data['not_importable'] = $this->not_importable_clients_fields;
        $data['title']          = _l('import');
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('admin/clients/import', $data);
    }

    public function groups()
    {
        if (!is_admin()) {
            access_denied('Customer Groups');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('customers_groups');
        }
        $data['title'] = _l('customer_groups');
        $this->load->view('admin/clients/groups_manage', $data);
    }

    public function group()
    {
        if (!is_admin() && get_option('staff_members_create_inline_customer_groups') == '0') {
            access_denied('Customer Groups');
        }

        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $id = $this->clients_model->add_group($data);
                $message = $id ? _l('added_successfully', _l('customer_group')) : '';
                echo json_encode(array(
                    'success' => $id ? true : false,
                    'message' => $message,
                    'id'=>$id,
                    'name'=>$data['name'],
                ));
            } else {
                $success = $this->clients_model->edit_group($data);
                $message = '';
                if ($success == true) {
                    $message = _l('updated_successfully', _l('customer_group'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

    public function delete_group($id)
    {
        if (!is_admin()) {
            access_denied('Delete Customer Group');
        }
        if (!$id) {
            redirect(admin_url('clients/groups'));
        }
        $response = $this->clients_model->delete_group($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('customer_group')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('customer_group_lowercase')));
        }
        redirect(admin_url('clients/groups'));
    }

    public function bulk_action()
    {
        do_action('before_do_bulk_action_for_customers');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids    = $this->input->post('ids');
            $groups = $this->input->post('groups');

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($this->clients_model->delete($id)) {
                            $total_deleted++;
                        }
                    } else {
                        if (!is_array($groups)) {
                            $groups = false;
                        }
                        $this->client_groups_model->sync_customer_groups($id, $groups);
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_clients_deleted', $total_deleted));
        }
    }

    public function vault_entry_create($customer_id)
    {
        $data = $this->input->post();

        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }

        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        unset($data['id']);
        $data['creator'] = get_staff_user_id();
        $data['creator_name'] = get_staff_full_name($data['creator']);
        $data['description'] = nl2br($data['description']);
        $data['password'] = $this->encryption->encrypt($this->input->post('password',false));

        if (empty($data['port'])) {
            unset($data['port']);
        }

        $this->clients_model->vault_entry_create($data, $customer_id);
		////zee code start
		$action ='Company Vault "'.$data['server_address'].'" Created ';
		$tabname = "vault";
		$heading = "Company Vaults";
		$this->vault_history($customer_id, $action, $tabname, $heading);
        ////zee code start
		set_alert('success', _l('added_successfully', _l('vault_entry')));
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function vault_entry_update($entry_id)
    {	
        $entry = $this->clients_model->get_vault_entry($entry_id);

        if ($entry->creator == get_staff_user_id() || is_admin()) {
            $data = $this->input->post();

            if (isset($data['fakeusernameremembered'])) {
                unset($data['fakeusernameremembered']);
            }
            if (isset($data['fakepasswordremembered'])) {
                unset($data['fakepasswordremembered']);
            }

            $data['last_updated_from'] = get_staff_full_name(get_staff_user_id());
            $data['description'] = nl2br($data['description']);

            if (!empty($data['password'])) {
                $data['password'] = $this->encryption->encrypt($this->input->post('password',false));
            } else {
                unset($data['password']);
            }

            if (empty($data['port'])) {
                unset($data['port']);
            }
			
			$company_id = $this->input->post('customer_id');
			$this->vault_history_update($company_id, $data, $entry_id);
			$this->clients_model->vault_entry_update($entry_id, $data);
            
			set_alert('success', _l('updated_successfully', _l('vault_entry')));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function vault_entry_delete($id, $company_id="")
    {
        $entry = $this->clients_model->get_vault_entry($id);
        if ($entry->creator == get_staff_user_id() || is_admin()) {
            $this->clients_model->vault_entry_delete($id);
			////zee code start
			$action = "Company Vault Deleted";
			$tabname = "vault";
			$heading = "Company Vaults";
			$this->zip_invoices_history($company_id,  $action , $tabname , $heading );
			//zee code end
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function vault_encrypt_password()
    {
        $id = $this->input->post('id');
        $user_password = $this->input->post('user_password', false);
        $user = $this->staff_model->get(get_staff_user_id());

        $this->load->helper('phpass');

        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        if (!$hasher->CheckPassword($user_password, $user->password)) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(array('error_msg'=>_l('vault_password_user_not_correct')));
            die;
        }

        $vault = $this->clients_model->get_vault_entry($id);
        $password = $this->encryption->decrypt($vault->password);

        $password = html_escape($password);

        // Failed to decrypt
        if (!$password) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode(array('error_msg'=>_l('failed_to_decrypt_password')));
            die;
        }

        echo json_encode(array('password'=>$password));
    }

    public function get_vault_entry($id)
    {
        $entry = $this->clients_model->get_vault_entry($id);
        unset($entry->password);
        $entry->description = clear_textarea_breaks($entry->description);
        echo json_encode($entry);
    }

    public function statement_pdf()
    {
        $customer_id = $this->input->get('customer_id');

        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('clients/client/'.$customer_id));
        }

        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $data['statement'] = $this->clients_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));

        try 
			{
				$pdf   = statement_pdf($data['statement']);
				//zee code start
				//$this->statement_history($customer_id);
				//zee code end
			} 
		catch (Exception $e) 
		{
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type           = 'D';
		
		if ($this->input->get('print')) {
            $type = 'I';
			////zee code start
			$this->statement_print($customer_id);
			////zee code end
        }

        $pdf->Output(slug_it(_l('customer_statement').'-'.$data['statement']['client']->company) . '.pdf', $type);
		
		
    }

    public function send_statement()
    {
        $customer_id = $this->input->get('customer_id');

        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('clients/client/'.$customer_id));
        }

        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $send_to = $this->input->post('send_to');
        $cc = $this->input->post('cc');

        $success = $this->clients_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);
        // In case client use another language
        load_admin_language();
        if ($success) 
		{
			$email_to = $this->input->get('to');
			$email_cc = $this->input->post('cc');
			// zee code start
			$this->email($customer_id, $email_to, $email_cc);
			// zee code start
			set_alert('success', _l('statement_sent_to_client_success'));
        } else {
            set_alert('danger', _l('statement_sent_to_client_fail'));
        }

        redirect(admin_url('clients/client/' . $customer_id.'?group=statement'));
    }

    public function statement()
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }

        $customer_id = $this->input->get('customer_id');
        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $data['statement'] = $this->clients_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));

        $data['from'] = $from;
        $data['to'] = $to;

        $viewData['html'] = $this->load->view('admin/clients/groups/_statement', $data, true);

        echo json_encode($viewData);
    }
	// zee code start 11/7/18
	public function get_notes()
	{
		extract($_POST);
		$company_id = $_POST['company_id'];
		$tabname 	= $_POST['tabname'];
        $notes = $this->clients_model->data($company_id, $tabname); //here in function pass company_id and tab name
		$notes_result = '';
		if(count($notes) > 0)
        {
            foreach($notes as $not){					
				$notes_result .='
								<tr>
									<td>
										<div style="border-bottom: 1px solid #9c9c9c; padding: 0px 5px;">
											<div class= "datastyle">' .$not['firstname'].' '.$not['lastname'].' - '.date('m-d-Y h:i A',strtotime($not['date'])).'</div> 
											<div>' .$not['notes'].'</div>		
										</div>
									</td>
								</tr>';		
            }
           echo $notes_result;           
        }
		
	}
	public function add_notes()
	{
		$data =array(
			'notes'				=> 	$this->input->post('notes'),
			'user_id'		    =>	$this->session->userdata('staff_user_id'),
			'company_id'		=>	$this->input->post('company_id'),
			'tabname'			=> 	$this->input->post('tabname'),
			'date' 				=>	date_format(date_create($this->input->post('datetime')),"Y-m-d H:i:s")
        );
		
        $query = $this->clients_model->addnotes($data);
		if ($query)
		{
			$this->notes_history($data);	
			$this->session->set_flashdata('inventory_items', 'Note added successfuly ');
			echo "save";
        }
		else 
		{
			echo "no";
        }
	}
	public function get_history()
		{	
		
		$company_id 	= 	$_POST['company_id'];
		$tabname    	=  	$_POST['tabname'];
		$history 		= $this->clients_model->get_history($company_id, $tabname);
		if($history)
			{
				$result = ' ';
				$result .= '<thead><th>Date</th><th>User</th><th>Action</th></thead>';
				foreach($history as $his)
					{					
						$result .='<tr>
										<td colspan="3">
											<b>'.$his['heading'].'</b>
										</td>
									</tr>
									<tr><td>'.date('m/d/Y',strtotime($his['date'])).'</td><td>'.$his['firstname'].' '.$his['lastname'].'</td><td>'.$his['action'].'</td></tr>';		
					}
				echo $result;           
			}
		else 
			{	
				$result   = '';
				$result	 .= '<thead><th>Date</th><th>User</th><th>Action</th></thead>';
				$message  = "No History";
				$result .='<tr>
								<td colspan="3"> 
									<center>
										<b>'. $message.'</b>
									</center>
								</td>
							</tr>';
				echo $result;
			}		
		}
		// for history when element created.
	public function create_history($id, $action, $tabname, $heading)
		{
			$user_id = $this->session->userdata('staff_user_id');
			$history = array(
							'user_id' 		=>  $user_id,
							'company_id'	=>	$id,
							'tabname' 		=>  $tabname,
							'heading'		=> 	$heading,
							'action'		=>	$action
						);
		 $query = $this->clients_model->create_history($history);
			if($query)
				{
					return true;
				}	
			else 
				{
					return false;
				}	
		}
	public function update_history($data, $id )
		{	
							
			
			if (isset($data['DataTables_Table_2_length'])) {
				unset($data['DataTables_Table_2_length']);
				}
			if (isset($data['inventory_id'])) {
				unset($data['inventory_id']);
				}
			if (isset($data['account'])) {
				unset($data['account']);
				}	
			if (isset($data['serial_number'])) {
				unset($data['serial_number']);
				}
			if (isset($data['type_of_hardware'])) {
				unset($data['type_of_hardware']);
				}	
			if (isset($data['date_in'])) {
				unset($data['date_in']);
				}	
			if (isset($data['status'])) {
				unset($data['status']);
				}	
			if (isset($data['origin'])) {
				unset($data['origin']);
				}			
			if (isset($data['equipment_owner'])) {
				unset($data['equipment_owner']);
				}			
				
			if (isset($data['manufacturer'])) {
				unset($data['manufacturer']);
				}			
			if (isset($data['warranty_expiration_date'])) {
				unset($data['warranty_expiration_date']);
				}	
			if (isset($data['exp_no'])) {
				unset($data['exp_no']);
				}	
			if (isset($data['exp_type'])) {
				unset($data['exp_type']);
				}		
			if (isset($data['image_name'])) {
				unset($data['image_name']);
				}		
			if (isset($data['img_clone'])) {
				unset($data['img_clone']);
				}		
			if (isset($data['description'])) {
				unset($data['description']);
				}
			// matching if's
		$fields = $this->clients_model->fields($id);
		
		
		if($fields[0]['company'] != $data['company'])
		 {
			 $fieldschanged = 'Company Name  "'.$fields[0]['company'].'"  updated to  "'.$data['company'].'"';
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Company Name in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }			
		if($fields[0]['phonenumber'] != $data['phonenumber'])
		 {
			 $fieldschanged = 'Phone Number '.$fields[0]['phonenumber'].' updated to ' .$data['phonenumber'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Phone Number in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }			
		if($fields[0]['website'] != $data['website'])
		 {
			 $fieldschanged = 'Website '.$fields[0]['website'].' updated to ' .$data['website'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Website:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }
		if($fields[0]['default_currency'] != $data['default_currency']&& $fields[0]['shipping_country'] != 0)
		 {
			 $param1 = $fields[0]['default_currency'];
			 $param2 = $data['default_currency'];
			 $valu_type = $this->param_valuecurrency($param1 , $param2 );
			 $fieldschanged = 'Default Currency "'.$valu_type['query1'][0]['name'].'" updated to "'.$valu_type['query2'][0]['name'].'"';
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Default Currency in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }

		if($fields[0]['address'] != $data['address'])
		 {
			 $fieldschanged = 'Address '.$fields[0]['address'].' updated to ' .$data['address'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Address in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }
		if($fields[0]['city'] != $data['city'])
		 {
			 $fieldschanged = 'City '.$fields[0]['city'].' updated to ' .$data['city'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of City:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }
		if($fields[0]['state'] != $data['state'])
		 {
			 $fieldschanged = 'State '.$fields[0]['state'].' updated to ' .$data['state'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of State in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }		 
		if($fields[0]['zip'] != $data['zip'])
		 {
			 $fieldschanged = 'Zip Code '.$fields[0]['state'].' updated to ' .$data['zip'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Zip Code in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }		 

		if($fields[0]['country'] != $data['country'])
		 {		
			 $param1 =$fields[0]['country'];
			 $param2 =$data['country'];
			 $valu_type = $this->param_valuecustomer($param1 , $param2 );
			 $fieldschanged = 'Country "'.$valu_type['query1'][0]['short_name'].'" updated to "'.$valu_type['query2'][0]['short_name'].'"';
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Country in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }	
			
		if($fields[0]['billing_street'] != $data['billing_street'])
		 {	
			 $fieldschanged = 'Billing Street '.$fields[0]['billing_street'].' updated to ' .$data['billing_street'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Billing Street in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }
		if($fields[0]['billing_city'] != $data['billing_city'])
		 {
			 $fieldschanged = 'Billing City '.$fields[0]['billing_city'].' updated to ' .$data['billing_city'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Billing City in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }
		if($fields[0]['billing_state'] != $data['billing_state'])
		 {
			 $fieldschanged = 'Billing State '.$fields[0]['billing_state'].' updated to ' .$data['billing_state'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Billing State in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }
		if($fields[0]['billing_zip'] != $data['billing_zip'])
		 {
			 $fieldschanged = 'Billing Zip Code '.$fields[0]['billing_zip'].' updated to ' .$data['billing_zip'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Billing Zip Code in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }

		if($fields[0]['billing_country'] != $data['billing_country']&& $fields[0]['billing_country'] != 0)
		 {
			  $param1 = $fields[0]['billing_country'];
			  $param2 = $data['billing_country'];
			 $valu_type = $this->param_valuecustomer($param1 , $param2 );
			 $fieldschanged = 'Billing Country "'.$valu_type['query1'][0]['short_name'].'" updated to "'.$valu_type['query2'][0]['short_name'].'"';
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Billing Country in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }

		if($fields[0]['shipping_street'] != $data['shipping_street'])
		 {
			 $fieldschanged = 'Shipping Street '.$fields[0]['shipping_street'].' updated to ' .$data['shipping_street'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Shipping Street in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }
		if($fields[0]['shipping_city'] != $data['shipping_city'])
		 {
			 $fieldschanged = 'Shipping City '.$fields[0]['shipping_city'].' updated to ' .$data['shipping_city'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Shipping City in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }		 
		if($fields[0]['shipping_state'] != $data['shipping_state'])
		 {
			 $fieldschanged = 'Shipping State '.$fields[0]['shipping_state'].' updated to ' .$data['shipping_state'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Shipping State in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }
		if($fields[0]['shipping_zip'] != $data['shipping_zip'])
		 {
			 $fieldschanged = 'Shipping Zip Code '.$fields[0]['shipping_zip'].' updated to ' .$data['shipping_zip'];
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Shipping Zip Code in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }
		if($fields[0]['shipping_country'] != $data['shipping_country']&& $fields[0]['shipping_country'] != 0)
		 {
			 $param1 = $fields[0]['billing_country'];
			 $param2 = $data['billing_country'];
			 $valu_type = $this->param_valuecustomer($param1 , $param2 );
			 $fieldschanged = 'Shipping Country "'.$valu_type['query1'][0]['short_name'].'" updated to "'.$valu_type['query2'][0]['short_name'].'"';
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Shipping Country in Profile:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'profile',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }		 			
		//	$fields ; // to get fileds from db 
		}

		public function param_valuecustomer($param1 , $param2)
			{	
				$query= $this->clients_model->paramvaluecountries($param1, $param2);
				return $query; 
			}
		
		public function param_valuecurrency($param1 , $param2)
			{	
				$query= $this->clients_model->param_valuecurrency($param1, $param2);
				
				return $query; 
			}
		public function statement_history($company_id)
		{	$action= "PDF Created";
			$user_id = $this->session->userdata('staff_user_id');
			$history = array(
							'user_id' 		=>  $user_id,
							'company_id'	=>	$company_id,
							'tabname' 		=>  "statement",
							'heading'		=> 	'Company Statement',
							'action'		=>	$action
						);
		 $query = $this->clients_model->create_history($history);
			if($query)
				{
					return true;
				}	
			else 
				{
					return false;
				}
			
		}
		public function statement_print($company_id)
		{	$action= "Statement Printed";
			$user_id = $this->session->userdata('staff_user_id');
			$history = array(
							'user_id' 		=>  $user_id,
							'company_id'	=>	$company_id,
							'tabname' 		=>  "statement",
							'heading'		=> 	'Company Statement',
							'action'		=>	$action
						);
		 $query = $this->clients_model->create_history($history);
			if($query)
				{
					return true;
				}	
			else 
				{
					return false;
				}
			
		}
	
		public function email($company_id, $email_to, $email_cc= '')
		{	
			if($email_cc != '')
			{
				
				$action = 'Default Currency "'.$email_to.'" and CC "'.$email_cc.'"';
				
			}
			else
			{
				$action='Email sent to "'.$email_to.'"';
			}
			$user_id = $this->session->userdata('staff_user_id');
			$history = array(
							'user_id' 		=>  $user_id,
							'company_id'	=>	$company_id,
							'tabname' 		=>  "statement",
							'heading'		=> 	'Email Sent',
							'action'		=>	$action
						);
		 $query = $this->clients_model->create_history($history);
			if($query)
				{
					return true;
				}	
			else 
				{
					return false;
				}
			
		}
		public function vault_history($company_id, $action, $tabname, $heading)
		{
			$user_id = $this->session->userdata('staff_user_id');
			$history = array(
							'user_id' 		=>  $user_id,
							'company_id'	=>	$company_id,
							'tabname' 		=>  $tabname,
							'heading'		=> 	$heading,
							'action'		=>	$action
						);
			 $query = $this->clients_model->create_history($history);
				if($query)
					{
						return true;
					}	
				else 
					{
						return false;
					}
			
		}
	public function zip_invoices_history($company_id, $action, $tabname, $heading)
	{
	
		$user_id = $this->session->userdata('staff_user_id');
		$history = array(
						'user_id' 		=>  $user_id,
						'company_id'	=>	$company_id,
						'tabname' 		=>  $tabname,
						'heading'		=> 	$heading,
						'action'		=>	$action
					);
		 $query = $this->clients_model->create_history($history);
			if($query)
				{
					return true;
				}	
			else 
				{
					return false;
				}
	}	
	public function update_map($longitude, $latitude, $id)
	{
		$fields = $this->clients_model->get($id);
			
		if($fields->latitude != $latitude)
		 {
			 $fieldschanged = 'Latitude  "'.$fields->latitude.'"  updated to  "'.$latitude.'"';
			 
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Latitude:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'map',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 }
		 }
		 if($fields->longitude != $longitude){
			 $fieldschanged = 'Longitude  "'.$fields->longitude.'"  updated to  "'.$longitude.'"';
			 
			 if ($fieldschanged){

				$values = array(
					'user_id'	   	=> $this->session->userdata('staff_user_id'),
					'heading'		=> 'Change of Longitude:',
					'action'       	=> $fieldschanged,
					'tabname'		=> 'map',
					'company_id'	=> $id,	
					);
			 $this->clients_model->update_history($values);
			 } 
		 }
	}
	public function vault_history_update($company_id,  $data, $id)
	{
		$fields = $this->clients_model->vault_fields($id);
		 if($fields[0]['server_address'] != $data['server_address'])
		 {
			 $fieldschanged = 'Server Address "'.$fields[0]['server_address'].'" updated to "'.$data['server_address'].'"';
			 if ($fieldschanged){

			 $values = array(
				 'company_id' => $company_id,
				 'user_id'	   => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Server Address in Vaults:',
				 'tabname'		=> "vault",
				 'action'       => $fieldschanged,
			 );
			 $this->clients_model->create_history($values);
			 }
		 }
		if($fields[0]['port'] != $data['port'])
		 {
			 $fieldschanged = 'Port  "'.$fields[0]['port'].'" updated to "'.$data['port'].'"';
			 if ($fieldschanged){

			 $values = array(
				 'company_id' => $company_id,
				 'user_id'	   => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Port in Vaults:',
				 'tabname'		=> "vault",
				 'action'       => $fieldschanged,
			 );
			 $this->clients_model->create_history($values);
			 }
		 }
		if($fields[0]['username'] != $data['username'])
		 {
			 $fieldschanged = 'Username  "'.$fields[0]['username'].'" updated to "'.$data['username'].'"';
			 if ($fieldschanged){

			 $values = array(
				 'company_id' => $company_id,
				 'user_id'	   => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Username in Vaults:',
				 'tabname'		=> "vault",
				 'action'       => $fieldschanged,
			 );
			 $this->clients_model->create_history($values);
			 }
		 }
		if($fields[0]['description'] != $data['description'])
		 {
			 $fieldschanged = 'Description  "'.$fields[0]['description'].'" updated to "'.$data['description'].'"';
			 if ($fieldschanged){

			 $values = array(
				 'company_id' => $company_id,
				 'user_id'	   => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Description in Vaults:',
				 'tabname'		=> "vault",
				 'action'       => $fieldschanged,
			 );
			 $this->clients_model->create_history($values);
			 }
		 }
		if ($data['password'])
		{	
			if($fields[0]['password'] != $data['password'])
			 {
				 $fieldschanged = "Password Updated";
				 if ($fieldschanged){

				 $values = array(
					 'company_id' => $company_id,
					 'user_id'	   => $this->session->userdata('staff_user_id'),
					 'heading'			=> 'Change of Password in Vaults:',
					 'tabname'		=> "vault",
					 'action'       => $fieldschanged,
				 );
				 $this->clients_model->create_history($values);
				 }
			 }
		}
	}
	public function notes_history($data)
	{	
		$action = 'Note Created: "'.$data['notes'].'"'; 
		$history = array(
							'user_id' 		=>  $data['user_id'],
							'company_id'	=>	$data['company_id'],
							'tabname' 		=>  $data['tabname'],
							'heading'		=> 	"Notes",
							'action'		=>	$action
						);
		
		$query = $this->clients_model->create_history($history);
		if($query)
				{
					return true;
				}	
			else 
				{
					return false;
				}	
	}
	//zee code end.11/7/18
}
