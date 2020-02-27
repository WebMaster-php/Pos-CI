<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Clients_model extends CRM_Model
{
    private $contact_columns;

    
    public function __construct()
    {
        parent::__construct();
		// $this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = tblcontacts.id' ,'LEFT');
        // $this->contact_columns = do_action('contact_columns', array('firstname', 'lastname', 'email', 'phonenumber', 'title', 'password', 'send_set_password_email', 'donotsendwelcomeemail', 'permissions', 'direction', 'cs1.invoice_emails', 'cs1.estimate_emails', 'cs1.credit_note_emails', 'cs1.contract_emails', 'cs1.task_emails', 'cs1.project_emails', 'cs1.is_primary'));
        $this->contact_columns = do_action('contact_columns', array('firstname', 'lastname', 'email', 'phonenumber', 'title', 'password', 'send_set_password_email', 'donotsendwelcomeemail', 'permissions', 'direction', 'invoice_emails', 'estimate_emails', 'credit_note_emails', 'contract_emails', 'task_emails', 'project_emails', 'is_primary'));
        $this->load->model(array('client_vault_entries_model', 'client_groups_model', 'statement_model', 'inventory_model'));
                // if(!isset($this->versi_con)){
           
        // }
    }

    /**
     * Get client object based on passed clientid if not passed clientid return array of all clients
     * @param  mixed $id    client id
     * @param  array  $where
     * @return mixed
     */
    public function get($id = '', $where = array())
    {
        $this->db->select(implode(',', prefixed_table_fields_array('tblclients')) . ','.get_sql_select_client_company());

        $this->db->join('tblcountries', 'tblcountries.country_id = tblclients.country', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.userid = tblclients.userid', 'left');
		$this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = tblcontacts.id AND is_primary = 1', 'LEFT');
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('tblclients.userid', $id);
            $client = $this->db->get('tblclients')->row();

            if (get_option('company_requires_vat_number_field') == 0) {
                $client->vat = null;
            }

            return $client;
        }

        $this->db->order_by('company', 'asc');
        return $this->db->get('tblclients')->result_array();
    }
    
    function client_inventory($where)    
    { 
        $this->db->select('ia.inventory_id, ia.account, ia.serial_number, ia.type_of_hardware, ia.date_in, ia.status, ia.origin, ia.equipment_owner, ia.manufacturer, ia.exp_date, ia.image, ia.description, cs1.value as statusvalue, cs3.value as hardwarevalue , cs4.value as ownervalue');
        $this->db->from('tblinventoryadd AS ia');// I use aliasing make joins easier
        $this->db->join('tblcustom AS cs1', 'cs1.id = ia.status', 'LEFT');
        $this->db->join('tblcustom AS cs3', 'cs3.id = ia.type_of_hardware', 'LEFT');
        $this->db->join('tblcustom AS cs4', 'cs4.id = ia.equipment_owner', 'LEFT');
        $this->db->where('ia.account', $where);
         return $this->db->get()->result_array();
         //echo $this->db->last_query();
    }

    public function get_all_tblcontacts_rel_clients($where = '' ){
       $this->db->select('*');
        $this->db->from('tblcontacts');
        $this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = tblcontacts.id', 'LEFT');
        // $this->db->where($where);
        if ($where) {
            $this->db->where($where);
        //     $this->db->where('conpany_id', $customer_id);
        }
        $this->db->order_by('cs1.is_primary', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Get customers contacts
     * @param  mixed $customer_id
     * @param  array  $where       perform where in query
     * @return array
     */
    public function get_contacts($customer_id = '', $where = array('active' => 1))
    {
		$this->db->select('*');
		$this->db->from('tblcontacts');
		$this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = tblcontacts.id', 'LEFT');
        $this->db->where($where);
        if ($customer_id != '') {
            $this->db->where('userid', $customer_id);
        }
        $this->db->order_by('cs1.is_primary', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Get single contacts
     * @param  mixed $id contact id
     * @return object
     */
    public function get_contact($id)
    {
        $this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = tblcontacts.id', 'LEFT');
        $this->db->where('cs1.contact_id', $id);
        return $this->db->get('tblcontacts')->row();
    }
	
	public function get_contact_company($where)
    {
        // echo $where; exit('io'); 
        $this->db->select('tblcontacts.*,cs1.company_id as userid, cs1.contact_id as contact_id');
		$this->db->from('tblcontacts');
		$this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = tblcontacts.id', 'LEFT');
        $this->db->where($where); 
        return $res =  $this->db->get()->result_array()[0];
    }
	
	
	/**
     * Get single contacts
     * @param  mixed $id contact id
     * @return object
     */
    public function get_contact_rel($where)
    { 
		$this->db->select('tblcontacts.*, cs1.*');
		$this->db->from('tblcontacts');
		$this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = tblcontacts.id', 'LEFT');
        $this->db->where($where);
        return $this->db->get()->row();		
    }

    /**
     * @param array $_POST data
     * @param client_request is this request from the customer area
     * @return integer Insert ID
     * Add new client to database
     */
public function add($data, $client_or_lead_convert_request = false)
{
        $contact_data = array();
        foreach ($this->contact_columns as $field) {
        if (isset($data[$field])) {
        $contact_data[$field] = $data[$field];
        // Phonenumber is also used for the company profile
        if ($field != 'phonenumber') {
        unset($data[$field]);
        }
        }
        }

        // From customer profile register
        if (isset($data['contact_phonenumber'])) {
        $contact_data['phonenumber'] = $data['contact_phonenumber'];
        unset($data['contact_phonenumber']);
        }

        if (isset($data['custom_fields'])) {
        $custom_fields = $data['custom_fields'];
        unset($data['custom_fields']);
        }

        if (isset($data['groups_in'])) {
        $groups_in = $data['groups_in'];
        unset($data['groups_in']);
        }
        if (isset($data['services_in'])) {
        $services_in = $data['services_in'];
        unset($data['services_in']);
        }

        $data = $this->check_zero_columns($data);

        $data['datecreated'] = date('Y-m-d H:i:s');

        if (is_staff_logged_in()) {
        $data['addedfrom'] = get_staff_user_id();
        }

        $hook_data = do_action('before_client_added', array('data'=>$data));
        $data = $hook_data['data'];


        $this->db->insert('tblclients', $data);

        $userid = $this->db->insert_id();
        if ($userid) {
        if (isset($custom_fields)) {
        $_custom_fields = $custom_fields;
        // Possible request from the register area with 2 types of custom fields for contact and for comapny/customer
        if (count($custom_fields) == 2) {
        unset($custom_fields);
        $custom_fields['customers'] = $_custom_fields['customers'];
        $contact_data['custom_fields']['contacts'] = $_custom_fields['contacts'];
        } elseif (count($custom_fields) == 1) {
        if (isset($_custom_fields['contacts'])) {
        $contact_data['custom_fields']['contacts'] = $_custom_fields['contacts'];
        unset($custom_fields);
        }
        }
        handle_custom_fields_post($userid, $custom_fields);
        }
        /**
        * Used in Import, Lead Convert, Register
        */
        if ($client_or_lead_convert_request == true) {
        $contact_id = $this->add_contact($contact_data, $userid, $client_or_lead_convert_request);
        }
        if (isset($groups_in)) {
        foreach ($groups_in as $group) {
        $this->db->insert('tblcustomergroups_in', array(
        'customer_id' => $userid,
        'groupid' => $group,
        ));
        }
        }
        if (isset($services_in)) {
        foreach ($services_in as $service) {
        $this->db->insert('tblcustomerservices_in', array(
        'customer_id' => $userid,
        'serviceid' => $service,
        ));
        }
        }
        do_action('after_client_added', $userid);
        $log = $data['company'];

        if ($log == '' && isset($contact_id)) {
        $log = get_contact_full_name($contact_id);
        }

        $isStaff = null;
        if (!is_client_logged_in() && is_staff_logged_in()) {
        $log .= ' From Staff: ' . get_staff_user_id();
        $isStaff = get_staff_user_id();
        }

        logActivity('New Client Created [' . $log . ']', $isStaff);
        }

        return $userid;
    }
    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update client informations
     */
    public function update($data, $id, $client_request = false)
    {
	
        if (isset($data['update_all_other_transactions'])) {
            $update_all_other_transactions = true;
            unset($data['update_all_other_transactions']);
        }


        if (isset($data['update_credit_notes'])) {
            $update_credit_notes = true;
            unset($data['update_credit_notes']);
        }

        $affectedRows = 0;
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
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
		if (isset($data['groups_in'])) {
            $groups_in = $data['groups_in'];
            unset($data['groups_in']);
        }
        if (isset($data['services_in'])) {
            $services_in = $data['services_in'];
            unset($data['services_in']);
        }

        $data = $this->check_zero_columns($data);

        $_data = do_action('before_client_updated', array(
            'userid' => $id,
            'data' => $data,
        ));
       
         
        $data  = $_data['data'];
        $this->db->where('userid', $id);
        $this->db->update('tblclients', $data);		
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }


        if (isset($update_all_other_transactions) || isset($update_credit_notes)) {
            $transactions_update = array(
                    'billing_street' => $data['billing_street'],
                    'billing_city' => $data['billing_city'],
                    'billing_state' => $data['billing_state'],
                    'billing_zip' => $data['billing_zip'],
                    'billing_country' => $data['billing_country'],
                    'shipping_street' => $data['shipping_street'],
                    'shipping_city' => $data['shipping_city'],
                    'shipping_state' => $data['shipping_state'],
                    'shipping_zip' => $data['shipping_zip'],
                    'shipping_country' => $data['shipping_country'],
                );
            if (isset($update_all_other_transactions)) {

                // Update all invoices except paid ones.
                $this->db->where('clientid', $id);
                $this->db->where('status !=', 2);
                $this->db->update('tblinvoices', $transactions_update);
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }

                // Update all estimates
                $this->db->where('clientid', $id);
                $this->db->update('tblestimates', $transactions_update);
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            }
            if (isset($update_credit_notes)) {
                $this->db->where('clientid', $id);
                $this->db->where('status !=', 2);
                $this->db->update('tblcreditnotes', $transactions_update);
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            }
        }

        if (!isset($groups_in)) {
            $groups_in = false;
        }
        if ($this->client_groups_model->sync_customer_groups($id, $groups_in)) {
            $affectedRows++;
        }
        if (!isset($services_in)) {
            $services_in = false;
        }
        if ($this->client_services_model->sync_customer_services($id, $services_in)) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            do_action('after_client_updated', $id);
            logActivity('Customer Info Updated [' . $data['company'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Update contact data
     * @param  array  $data           $_POST data
     * @param  mixed  $id             contact id
     * @param  boolean $client_request is request from customers area
     * @return mixed
     */
    
	public function update_contact($data, $id, $client_request = false)
    {	
		$affectedRows = 0;
        $contact = $this->get_contact($id);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $this->load->helper('phpass');
            $hasher                       = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $data['password']             = $hasher->HashPassword($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }

        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;
        $set_password_email_sent = false;
		
		if (isset($data['old_existing_account'])) {
            unset($data['old_existing_account']);
        }else{
			unset($data['old_existing_account']);
		}
		
		if (isset($data['existing_contact'])) {
            unset($data['existing_contact']);
        }else{
			unset($data['existing_contact']);
		}
		if (isset($data['company_id'])) {
            $company_id = $data['company_id'];
			unset($data['company_id']);
        }else{
			$company_id = $data['company_id'];
			unset($data['company_id']);
		}
		
		
        $permissions = isset($data['permissions']) ? $data['permissions'] : array();
        
        // Contact cant change if is primary or not
        if ($client_request == true) {
            unset($data['is_primary']);
            if (isset($data['email'])) {
                unset($data['email']);
            }
        }
		if (isset($data['permissions'])) {
            $permissions= $data['permissions'];
            unset($data['permissions']);
        }else{
			unset($data['permissions']);
		}		

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
			
		if (isset($data['invoice_emails'])) {
            $invoice_emails= $data['invoice_emails'];
            unset($data['invoice_emails']);
        }else{
			unset($data['invoice_emails']);
		}
		if (isset($data['estimate_emails'])) {
            $estimate_emails= $data['estimate_emails'];
            unset($data['estimate_emails']);
        }else{
			unset($data['estimate_emails']);
		}
		if (isset($data['credit_note_emails'])) {
            $credit_note_emails= $data['credit_note_emails'];
            unset($data['credit_note_emails']);
        }else{
			unset($data['credit_note_emails']);
		}
		if (isset($data['project_emails'])) {
            $project_emails= $data['project_emails'];
            unset($data['project_emails']);
        }else{
			unset($data['project_emails']);
		}
		if (isset($data['task_emails'])) {
            $task_emails= $data['task_emails'];
            unset($data['task_emails']);
        }else{
			unset($data['task_emails']);
		}
		if (isset($data['contract_emails'])) {
            $contract_emails= $data['contract_emails'];
            unset($data['contract_emails']);
        }else{
			unset($data['contract_emails']);
		}
		if (isset($data['is_primary'])) {
            $is_primary= $data['is_primary'];
            unset($data['is_primary']);
        }else{
			unset($data['is_primary']);
		}
		//print_r($data); exit; 
		if(isset($is_primary) ==1){
			$this->db->where('company_id',$company_id);
			$this->db->where('is_primary', 1);
			$this->db->update('tblcontacts_rel_clients', array('is_primary'=>0));
			}
		else
			{
				$this->db->where('company_id',$company_id);
				$this->db->where('contact_id',$id);
				$this->db->where('is_primary', 1);
				$this->db->update('tblcontacts_rel_clients', array('is_primary'=>0));				
				if ($this->db->affected_rows() > 0) 
					{	
						$affectedRows++;
					}
			}	
		$query['is_primary'] 			= isset($is_primary);
		$query['invoice_emails']		= isset($invoice_emails);
		$query['estimate_emails'] 		= isset($estimate_emails);
		$query['credit_note_emails']	= isset($credit_note_emails);
		$query['project_emails'] 		= isset($project_emails);
		$query['task_emails'] 			= isset($task_emails);
		$query['contract_emails'] 		= isset($contract_emails);				
		
		$this->db->where('company_id', $company_id);
		$this->db->where('contact_id', $id);
		$this->db->update('tblcontacts_rel_clients', $query);
		if ($this->db->affected_rows() > 0) 
		{	
			$affectedRows++;
		}	 	
        $hook_data = do_action('before_update_contact', array('data'=>$data, 'id'=>$id));
        $data = $hook_data['data']; 
        $this->db->where('id', $id);
        $this->db->update('tblcontacts', $data);	
        
		if ($this->db->affected_rows() > 0 || $affectedRows == 1) {
			$affectedRows++;
	   }
        if ($client_request == false) {
            $customer_permissions = $this->roles_model->get_contact_permissions($id);
            if (sizeof($customer_permissions) > 0) {
                foreach ($customer_permissions as $customer_permission) {
                    if (!in_array($customer_permission['permission_id'], $permissions)) {
                        $this->db->where('userid', $id);
                        $this->db->where('permission_id', $customer_permission['permission_id']);
                        $this->db->delete('tblcontactpermissions');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
                foreach ($permissions as $permission) {
                    $this->db->where('userid', $id);
                    $this->db->where('permission_id', $permission);
                    $_exists = $this->db->get('tblcontactpermissions')->row();
                    if (!$_exists) {
                        $this->db->insert('tblcontactpermissions', array(
                            'userid' => $id,
                            'permission_id' => $permission,
                        ));
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            } else {
                foreach ($permissions as $permission) {
                    $this->db->insert('tblcontactpermissions', array(
                        'userid' => $id,
                        'permission_id' => $permission,
                    ));
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if ($send_set_password_email) {
                $set_password_email_sent = $this->authentication_model->set_password_email($data['email'], 0);
            }
        }
        if ($affectedRows > 0 && !$set_password_email_sent) {
            logActivity('Contact Updated [' . $data['firstname'] . ' ' . $data['lastname'] . ']');

            return true;
        } elseif ($affectedRows > 0 && $set_password_email_sent) {
            return array(
                'set_password_email_sent_and_profile_updated' => true,
            );
        } elseif ($affectedRows == 0 && $set_password_email_sent) {
            return array(
                'set_password_email_sent' => true,
            );
        }

        return false;
    }

    /**
     * Add new contact
     * @param array  $data               $_POST data
     * @param mixed  $customer_id        customer id
     * @param boolean $not_manual_request is manual from admin area customer profile or register, convert to lead
     */
    
	public function add_contact($data, $customer_id, $not_manual_request = false)
    {
        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }
		
		if (isset($data['old_existing_account'])) {
            $old_existing_account = $data['old_existing_account'];
			unset($data['old_existing_account']);
        }else{
			$old_existing_account = $data['old_existing_account'];
			unset($data['old_existing_account']);
		}
		
		if (isset($data['existing_contact'])) {
            unset($data['existing_contact']);
        }else{
			unset($data['existing_contact']);
		}
		
	
		
		if (isset($data['company_id'])) {
            unset($data['company_id']);
        }else{
			unset($data['company_id']);
		}
		
        $send_welcome_email = true;
        if (isset($data['donotsendwelcomeemail'])) {
            $send_welcome_email = false;
        } elseif (strpos($_SERVER['HTTP_REFERER'], 'register') !== false) {
            $send_welcome_email = true;
            // If client register set this auto contact as primary
            $data['is_primary'] = 1;
        }
		

        $password_before_hash  = '';
        $data['userid'] = $customer_id;        
		if(isset($data['idd']) || isset($data['idd_1']))
		 {
			 if(isset($data['idd']) != ''){
				$data['userid'] = $data['idd'];
				unset($data['idd']);
				unset($data['idd_1']);
			 }			
			else{
				$data['userid'] = $data['idd_1'];
				unset($data['idd_1']);
				unset($data['idd']);
			}
		 }
		if (isset($data['password'])) {
            $password_before_hash = $data['password'];
            $this->load->helper('phpass');
            $hasher              = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $data['password']    = $hasher->HashPassword($data['password']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');

        if (!$not_manual_request) {
            $data['invoice_emails'] = isset($data['invoice_emails']) ? 1 :0;
            $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 :0;
            $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 :0;
            $data['contract_emails'] = isset($data['contract_emails']) ? 1 :0;
            $data['task_emails'] = isset($data['task_emails']) ? 1 :0;
            $data['project_emails'] = isset($data['project_emails']) ? 1 :0;
        }

        $hook_data = array(
            'data' => $data,
            'not_manual_request' => $not_manual_request,
        );

        $hook_data = do_action('before_create_contact', $hook_data);
        $data  = $hook_data['data'];
		
        $data['email'] = trim($data['email']);
		if (isset($data['is_primary'])) {
			$is_primary= isset($data['is_primary']) ? 1 : 0;
            unset($data['is_primary']);
        }else{
			$is_primary= isset($data['is_primary']) ? 1 : 0;
			unset($data['is_primary']);
		}	
		if (isset($data['invoice_emails'])) {
            $invoice_emails= $data['invoice_emails'];
            unset($data['invoice_emails']);
        }else{
			unset($data['invoice_emails']);
		}
		if (isset($data['estimate_emails'])) {
            $estimate_emails= $data['estimate_emails'];
            unset($data['estimate_emails']);
        }else{
			unset($data['estimate_emails']);
		}
		if (isset($data['credit_note_emails'])) {
            $credit_note_emails= $data['credit_note_emails'];
            unset($data['credit_note_emails']);
        }else{
			unset($data['credit_note_emails']);
		}
		if (isset($data['project_emails'])) {
            $project_emails= $data['project_emails'];
            unset($data['project_emails']);
        }else{
			unset($data['project_emails']);
		}
		if (isset($data['task_emails'])) {
            $task_emails= $data['task_emails'];
            unset($data['task_emails']);
        }else{
			unset($data['task_emails']);
		}
		if (isset($data['contract_emails'])) {
            $contract_emails= $data['contract_emails'];
            unset($data['contract_emails']);
        }else{
			unset($data['contract_emails']);
		}
		
			/////////////////////////////////
			if (isset($data['is_primary_'])) {
			$is_primary= isset($data['is_primary_']) ? 1 : 0;
			unset($data['is_primary_']);
			}	
			if (isset($data['invoice_emails_'])) {
			$invoice_emails= $data['invoice_emails_'];
			unset($data['invoice_emails_']);
			}
			if (isset($data['estimate_emails_'])) {
			$estimate_emails= $data['estimate_emails_'];
			unset($data['estimate_emails_']);
			}
			if (isset($data['credit_note_emails_'])) {
			$credit_note_emails= $data['credit_note_emails_'];
			unset($data['credit_note_emails_']);
			}
			if (isset($data['project_emails_'])) {
			$project_emails= $data['project_emails_'];
			unset($data['project_emails_']);
			}
			if (isset($data['task_emails_'])) {
			$task_emails= $data['task_emails_'];
			unset($data['task_emails_']);
			}
			if (isset($data['contract_emails_'])) {
			$contract_emails= $data['contract_emails_'];
			unset($data['contract_emails_']);
			}
			/////////////////////////////////	
		if($old_existing_account)
		{	if($is_primary ==1){
				
				$this->db->where('company_id',$data['userid']);
				$this->db->where('is_primary', 1);
				$this->db->update('tblcontacts_rel_clients', array('is_primary'=>0));
			} 
			$query['company_id'] 			= $data['userid'];
			$query['contact_id']			= $old_existing_account;
			$query['is_primary'] 			= $is_primary;
			$query['invoice_emails']		= $invoice_emails;
			$query['estimate_emails'] 		= $estimate_emails;
			$query['credit_note_emails']	= $credit_note_emails;
			$query['project_emails'] 		= $project_emails;
			$query['task_emails'] 			= $task_emails;
			$query['contract_emails'] 		= $contract_emails;						
			$this->db->insert('tblcontacts_rel_clients', $query);
			$contacts_rel_clients_id = $this->db->insert_id(); 
			$contact_id = $old_existing_account;
		}
		else
		{ 	 
			if($is_primary ==1){
				
				$this->db->where('company_id',$data['userid']);
				$this->db->where('is_primary', 1);
				$this->db->update('tblcontacts_rel_clients', array('is_primary'=>0));
			}
			$this->db->insert('tblcontacts', $data);
			$contact_id = $this->db->insert_id();
			
			$query['company_id'] 			= $data['userid'];
			$query['contact_id']			= $contact_id;
			$query['is_primary'] 			= $is_primary;
			$query['invoice_emails']		= $invoice_emails;
			$query['estimate_emails'] 		= $estimate_emails;
			$query['credit_note_emails']	= $credit_note_emails;
			$query['project_emails'] 		= $project_emails;
			$query['task_emails'] 			= $task_emails;
			$query['contract_emails'] 		= $contract_emails;				
			$this->db->insert('tblcontacts_rel_clients', $query);
			$contacts_rel_clients_id = $this->db->insert_id();
		}

        if ($contact_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($contact_id, $custom_fields);
            }
            // request from admin area
            if (!isset($permissions) && $not_manual_request == false) {
                $permissions = array();
            } elseif ($not_manual_request == true) {
                $permissions         = array();
                $_permissions        = get_contact_permissions();
                $default_permissions = @unserialize(get_option('default_contact_permissions'));
                if (is_array($default_permissions)) {
                    foreach ($_permissions as $permission) {
                        if (in_array($permission['id'], $default_permissions)) {
                            array_push($permissions, $permission['id']);
                        }
                    }
                }
            }

            if ($not_manual_request == true) {
                // update all email notifications to 0
                $this->db->where('id', $contact_id);
                $this->db->update('tblcontacts', array(
                    'invoice_emails'=>0,
                    'estimate_emails'=>0,
                    'credit_note_emails'=>0,
                    'contract_emails'=>0,
                    'task_emails'=>0,
                    'project_emails'=>0,
                ));
            }
            foreach ($permissions as $permission) {

                $this->db->insert('tblcontactpermissions', array(
                    'userid' => $contact_id,
                    'permission_id' => $permission,
                ));

                // Auto set email notifications based on permissions
                if ($not_manual_request == true) {
                    if ($permission == 6) {
                        $this->db->where('id', $contact_id);
                        $this->db->update('tblcontacts', array('project_emails'=>1, 'task_emails'=>1));
                    } elseif ($permission == 3) {
                        $this->db->where('id', $contact_id);
                        $this->db->update('tblcontacts', array('contract_emails'=>1));
                    } elseif ($permission == 2) {
                        $this->db->where('id', $contact_id);
                        $this->db->update('tblcontacts', array('estimate_emails'=>1));
                    } elseif ($permission == 1) {
                        $this->db->where('id', $contact_id);
                        $this->db->update('tblcontacts', array('invoice_emails'=>1, 'credit_note_emails'=>1));
                    }
                }
            }

            $lastAnnouncement = $this->db->query("SELECT announcementid FROM tblannouncements WHERE showtousers = 1 AND announcementid = (SELECT MAX(announcementid) FROM tblannouncements)")->row();
            if ($lastAnnouncement) {
                // Get all announcements and set it to read.
                $this->db->select('announcementid')
                ->from('tblannouncements')
                ->where('showtousers', 1)
                ->where('announcementid !=', $lastAnnouncement->announcementid);

                $announcements = $this->db->get()->result_array();
                foreach ($announcements as $announcement) {
                    $this->db->insert('tbldismissedannouncements', array(
                        'announcementid' => $announcement['announcementid'],
                        'staff' => 0,
                        'userid' => $contact_id,
                    ));
                }
            }
            if ($send_welcome_email == true) {
                $this->load->model('emails_model');
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($data['userid'], $contact_id, $password_before_hash));
                $this->emails_model->send_email_template('new-client-created', $data['email'], $merge_fields);
            }

            if ($send_set_password_email) {
                $this->authentication_model->set_password_email($data['email'], 0);
            }

            logActivity('Contact Created [' . $data['firstname'] . ' ' . $data['lastname'] . ']');
            do_action('contact_created', $contact_id);

            return $contact_id;
        }

        return false;
    }

    /**
     * Used to update company details from customers area
     * @param  array $data $_POST data
     * @param  mixed $id
     * @return boolean
     */
    public function update_company_details($data, $id)
    {	
        $affectedRows = 0;
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }
        if (isset($data['billing_country']) && $data['billing_country'] == '') {
            $data['billing_country'] = 0;
        }
        if (isset($data['shipping_country']) && $data['shipping_country'] == '') {
            $data['shipping_country'] = 0;
        }

        // From v.1.9.4 these fields are textareas
        $data['address'] = trim($data['address']);
        $data['address'] = nl2br($data['address']);
        if (isset($data['billing_street'])) {
            $data['billing_street'] = trim($data['billing_street']);
            $data['billing_street'] = nl2br($data['billing_street']);
        }
        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }
		$company_id = $data['company_id'];
		if (isset($data['company_id'])){
			unset($data['company_id']);
		}
        $this->db->where('userid', $company_id);
        $this->db->update('tblclients', $data);
         
		if($this->db->affected_rows() > 0){

			$affectedRows++;
        }

        if ($affectedRows > 0) {
            do_action('customer_updated_company_info', $id);
            logActivity('Customer Info Updated From Clients Area [' . $data['company'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Get customer staff members that are added as customer admins
     * @param  mixed $id customer id
     * @return array
     */
    public function get_admins($id)
    {
        $this->db->where('customer_id', $id);

        return $this->db->get('tblcustomeradmins')->result_array();
    }

    /**
     * Get unique staff id's of customer admins
     * @return array
     */
    public function get_customers_admin_unique_ids()
    {
        return $this->db->query('SELECT DISTINCT(staff_id) FROM tblcustomeradmins')->result_array();
    }

    /**
     * Assign staff members as admin to customers
     * @param  array $data $_POST data
     * @param  mixed $id   customer id
     * @return boolean
     */
    public function assign_admins($data, $id)
    {
        $affectedRows = 0;

        if (count($data) == 0) {
            $this->db->where('customer_id', $id);
            $this->db->delete('tblcustomeradmins');
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        } else {
            $current_admins     = $this->get_admins($id);
            $current_admins_ids = array();
            foreach ($current_admins as $c_admin) {
                array_push($current_admins_ids, $c_admin['staff_id']);
            }
            foreach ($current_admins_ids as $c_admin_id) {
                if (!in_array($c_admin_id, $data['customer_admins'])) {
                    $this->db->where('staff_id', $c_admin_id);
                    $this->db->where('customer_id', $id);
                    $this->db->delete('tblcustomeradmins');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            foreach ($data['customer_admins'] as $n_admin_id) {
                if (total_rows('tblcustomeradmins', array(
                    'customer_id' => $id,
                    'staff_id' => $n_admin_id,
                )) == 0) {
                    $this->db->insert('tblcustomeradmins', array(
                        'customer_id' => $id,
                        'staff_id' => $n_admin_id,
                        'date_assigned' => date('Y-m-d H:i:s'),
                    ));
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete client, also deleting rows from, dismissed client announcements, ticket replies, tickets, autologin, user notes
     */
    public function delete($id)
    {
        $affectedRows = 0;

        if (is_reference_in_table('clientid', 'tblinvoices', $id)
            || is_reference_in_table('clientid', 'tblestimates', $id)
            || is_reference_in_table('clientid', 'tblcreditnotes', $id)) {
            return array(
                'referenced' => true,
            );
        }

        do_action('before_client_deleted', $id);
       
       //get for hidden
        $hidden = $this->get_for_hidden($id);    
        // print_r($hidden); exit; 
        

        $this->db->where('userid', $id);
        $this->db->delete('tblclients');

        if ($this->db->affected_rows() > 0) {
           //put in hidden table
            $this->put_in_hidden($hidden);     
           
            $affectedRows++;

            // Delete all tickets start here
            // $this->db->where('userid', $id);
            // $tickets = $this->db->get('tbltickets')->result_array();
            // $this->load->model('tickets_model');
            // foreach ($tickets as $ticket) {
            //     $this->tickets_model->delete($ticket['ticketid']);
            // }

            // $this->db->where('rel_id', $id);
            // $this->db->where('rel_type', 'customer');
            // $this->db->delete('tblnotes');

            // // Delete all user contacts
            // $this->db->where('userid', $id);
            // $contacts = $this->db->get('tblcontacts')->result_array();
            // foreach ($contacts as $contact) {
            //     $this->delete_contact($contact['id']);
            // }
            // // Get all client contracts
            // $this->load->model('contracts_model');
            // $this->db->where('client', $id);
            // $contracts = $this->db->get('tblcontracts')->result_array();
            // foreach ($contracts as $contract) {
            //     $this->contracts_model->delete($contract['id']);
            // }
            // // Delete the custom field values
            // $this->db->where('relid', $id);
            // $this->db->where('fieldto', 'customers');
            // $this->db->delete('tblcustomfieldsvalues');

            // // Get customer related tasks
            // $this->db->where('rel_type', 'customer');
            // $this->db->where('rel_id', $id);
            // $tasks = $this->db->get('tblstafftasks')->result_array();

            // foreach ($tasks as $task) {
            //     $this->tasks_model->delete_task($task['id']);
            // }
            // $this->db->where('rel_type', 'customer');
            // $this->db->where('rel_id', $id);
            // $this->db->delete('tblreminders');

            // $this->db->where('customer_id', $id);
            // $this->db->delete('tblcustomeradmins');

            // $this->db->where('customer_id', $id);
            // $this->db->delete('tblvault');

            // $this->db->where('customer_id', $id);
            // $this->db->delete('tblcustomergroups_in');

            // // Delete all projects
            // $this->load->model('projects_model');
            // $this->db->where('clientid', $id);
            // $projects = $this->db->get('tblprojects')->result_array();
            // foreach ($projects as $project) {
            //     $this->projects_model->delete($project['id']);
            // }
            // $this->load->model('proposals_model');
            // $this->db->where('rel_id', $id);
            // $this->db->where('rel_type', 'customer');
            // $proposals = $this->db->get('tblproposals')->result_array();
            // foreach ($proposals as $proposal) {
            //     $this->proposals_model->delete($proposal['id']);
            // }
            // $this->db->where('rel_id', $id);
            // $this->db->where('rel_type', 'customer');
            // $attachments = $this->db->get('tblfiles')->result_array();
            // foreach ($attachments as $attachment) {
            //     $this->delete_attachment($attachment['id']);
            // }

            // $this->db->where('clientid', $id);
            // $expenses = $this->db->get('tblexpenses')->result_array();

            // $this->load->model('expenses_model');
            // foreach ($expenses as $expense) {
            //     $this->expenses_model->delete($expense['id']);
            // }
        }
        if ($affectedRows > 0) {
            do_action('after_client_deleted', $id);
            logActivity('Client Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete customer contact
     * @param  mixed $id contact id
     * @return boolean
     */
    public function delete_contact($id)
    {
        // exit('innjijijijijijop');
        $this->db->select('userid');
        $this->db->where('id', $id);
        $result      = $this->db->get('tblcontacts')->row();
        $customer_id = $result->userid;
        do_action('before_delete_contact', $id);
        $this->db->where('id', $id);
        $this->db->delete('tblcontacts');
        if ($this->db->affected_rows() > 0) {
            if (is_dir(get_upload_path_by_type('contact_profile_images') . $id)) {
                delete_dir(get_upload_path_by_type('contact_profile_images') . $id);
            }

            $this->db->where('contact_id', $id);
            $this->db->delete('tblcustomerfiles_shares');

            $this->db->where('userid', $id);
            $this->db->where('staff', 0);
            $this->db->delete('tbldismissedannouncements');

            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'contacts');
            $this->db->delete('tblcustomfieldsvalues');

            $this->db->where('userid', $id);
            $this->db->delete('tblcontactpermissions');

            // Delete autologin if found
            $this->db->where('user_id', $id);
            $this->db->where('staff', 0);
            $this->db->delete('tbluserautologin');

            $this->db->select('ticketid');
            $this->db->where('contactid', $id);
            $this->db->where('userid', $customer_id);
            $tickets = $this->db->get('tbltickets')->result_array();

            $this->load->model('tickets_model');
            foreach ($tickets as $ticket) {
                $this->tickets_model->delete($ticket['ticketid']);
            }
			// to delete setting of contact from tblrad start 
			$this->db->where('userid', $id);
			$this->db->delete('tblrad');
			// to delete setting of contact from tblrad end
			
            $this->db->where('contactid', $id);
            $this->db->where('userid', $customer_id);
            $this->db->delete('tblticketreplies');

            
			return true;
        }

        return false;
    }

    /**
     * Get customer default currency
     * @param  mixed $id customer id
     * @return mixed
     */
    public function get_customer_default_currency($id)
    {
        $this->db->select('default_currency');
        $this->db->where('userid', $id);
        $result = $this->db->get('tblclients')->row();
        if ($result) {
            return $result->default_currency;
        }

        return false;
    }

    /**
     *  Get customer billing details
     * @param   mixed $id   customer id
     * @return  array
     */
    public function get_customer_billing_and_shipping_details($id)
    {
        $this->db->select('billing_street,billing_city,billing_state,billing_zip,billing_country,shipping_street,shipping_city,shipping_state,shipping_zip,shipping_country');
        $this->db->from('tblclients');
        $this->db->where('userid', $id);

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $result[0]['billing_street'] = clear_textarea_breaks($result[0]['billing_street']);
            $result[0]['shipping_street'] = clear_textarea_breaks($result[0]['shipping_street']);
        }

        return $result;
    }

    /**
     * Get customer files uploaded in the customer profile
     * @param  mixed $id    customer id
     * @param  array  $where perform where
     * @return array
     */
    public function get_customer_files($id, $where = array())
    {
        $this->db->where($where);
        $this->db->where('contact_id', $id);
        $this->db->where('rel_type', 'customer');
        $this->db->order_by('dateadded', 'desc');
		$this->db->limit(6);
		
        return $this->db->get('tblfiles')->result_array();
		// print_r($this->db->last_query());
		// exit;
	}

    /**
     * Delete customer attachment uploaded from the customer profile
     * @param  mixed $id attachment id
     * @return boolean
     */
    public function delete_attachment($id)
    {
        $this->db->where('id', $id);
        $attachment = $this->db->get('tblfiles')->row();
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                $relPath = get_upload_path_by_type('customer') . $attachment->rel_id . '/';
                $fullPath =$relPath.$attachment->file_name;
                unlink($fullPath);
                $fname = pathinfo($fullPath, PATHINFO_FILENAME);
                $fext = pathinfo($fullPath, PATHINFO_EXTENSION);
                $thumbPath = $relPath.$fname.'_thumb.'.$fext;
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

            $this->db->where('id', $id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                $this->db->where('file_id', $id);
                $this->db->delete('tblcustomerfiles_shares');
                logActivity('Customer Attachment Deleted [CustomerID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('customer') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('customer') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    delete_dir(get_upload_path_by_type('customer') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update contact status Active/Inactive
     */
    public function change_contact_status($id, $status)
    {
        $hook_data['id']     = $id;
        $hook_data['status'] = $status;
        $hook_data           = do_action('change_contact_status', $hook_data);
        $status              = $hook_data['status'];
        $id                  = $hook_data['id'];
        $this->db->where('id', $id);
        $this->db->update('tblcontacts', array(
            'active' => $status,
        ));
        if ($this->db->affected_rows() > 0) {
            logActivity('Contact Status Changed [ContactID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update client status Active/Inactive
     */
    public function change_client_status($id, $status)
    {
        $this->db->where('userid', $id);
        $this->db->update('tblclients', array(
            'active' => $status,
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Customer Status Changed [CustomerID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  mixed $_POST data
     * @return mixed
     * Change contact password, used from client area
     */
    public function change_contact_password($data)
    {
        $hook_data['data'] = $data;
        $hook_data         = do_action('before_contact_change_password', $hook_data);
        $data              = $hook_data['data'];

        // Get current password
        $this->db->where('id', get_contact_user_id());
        $client = $this->db->get('tblcontacts')->row();
        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        if (!$hasher->CheckPassword($data['oldpassword'], $client->password)) {
            return array(
                'old_password_not_match' => true,
            );
        }
        $update_data['password']             = $hasher->HashPassword($data['newpasswordr']);
        $update_data['last_password_change'] = date('Y-m-d H:i:s');
        $this->db->where('id', get_contact_user_id());
        $this->db->update('tblcontacts', $update_data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Contact Password Changed [ContactID: ' . get_contact_user_id() . ']');

            return true;
        }

        return false;
    }

    /**
     * Get customer groups where customer belongs
     * @param  mixed $id customer id
     * @return array
     */
    public function get_customer_groups($id)
    {
        return $this->client_groups_model->get_customer_groups($id);
    }

    /**
     * Get all customer groups
     * @param  string $id
     * @return mixed
     */
    public function get_groups($id = '')
    {
        return $this->client_groups_model->get_groups($id);
    }

    /**
     * Delete customer groups
     * @param  mixed $id group id
     * @return boolean
     */
    public function delete_group($id)
    {
        return $this->client_groups_model->delete($id);
    }

    /**
     * Add new customer groups
     * @param array $data $_POST data
     */
    public function add_group($data)
    {
        return $this->client_groups_model->add($data);
    }

    /**
     * Edit customer group
     * @param  array $data $_POST data
     * @return boolean
     */
    public function edit_group($data)
    {
        return $this->client_groups_model->edit($data);
    }

    /**
    * Create new vault entry
    * @param  array $data        $_POST data
    * @param  mixed $customer_id customer id
    * @return boolean
    */
    public function vault_entry_create($data, $customer_id)
    {
        return $this->client_vault_entries_model->create($data, $customer_id);
    }

    /**
     * Update vault entry
     * @param  mixed $id   vault entry id
     * @param  array $data $_POST data
     * @return boolean
     */
    public function vault_entry_update($id, $data)
    {	
		return $this->client_vault_entries_model->update($id, $data);
    }

    /**
     * Delete vault entry
     * @param  mixed $id entry id
     * @return boolean
     */
    public function vault_entry_delete($id)
    {
        return $this->client_vault_entries_model->delete($id);
    }

    /**
     * Get customer vault entries
     * @param  mixed $customer_id
     * @param  array  $where       additional wher
     * @return array
     */
    public function get_vault_entries($customer_id, $where = array())
    {
        return $this->client_vault_entries_model->get_by_customer_id($customer_id, $where);
    }

    /**
     * Get single vault entry
     * @param  mixed $id vault entry id
     * @return object
     */
    public function get_vault_entry($id)
    {
        return $this->client_vault_entries_model->get($id);
    }

    /**
    * Get customer statement formatted
    * @param  mixed $customer_id customer id
    * @param  string $from        date from
    * @param  string $to          date to
    * @return array
    */
    public function get_statement($customer_id, $from, $to)
    {
        return $this->statement_model->get_statement($customer_id, $from, $to);
    }

    /**
    * Send customer statement to email
    * @param  mixed $customer_id customer id
    * @param  array $send_to     array of contact emails to send
    * @param  string $from        date from
    * @param  string $to          date to
    * @param  string $cc          email CC
    * @return boolean
    */
    public function send_statement_to_email($customer_id, $send_to, $from, $to, $cc = '')
    {
        return $this->statement_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);
    }

    private function check_zero_columns($data)
    {
        if (!isset($data['show_primary_contact'])) {
            $data['show_primary_contact'] = 0;
        }

        if (isset($data['default_currency']) && $data['default_currency'] == '' || !isset($data['default_currency'])) {
            $data['default_currency'] = 0;
        }

        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }

        if (isset($data['billing_country']) && $data['billing_country'] == '' || !isset($data['billing_country'])) {
            $data['billing_country'] = 0;
        }

        if (isset($data['shipping_country']) && $data['shipping_country'] == '' || !isset($data['shipping_country'])) {
            $data['shipping_country'] = 0;
        }

        return $data;
    }
	// zee code start 11 july
	public function data($company_id, $tabname) // add company id + tabname
	{
		$this->db->select('ia.*, ts.firstname, ts.lastname');		
		$this->db->from('tblcompanynotes AS ia');                            
		$this->db->join('tblstaff AS ts', 'ts.staffid = ia.user_id', 'INNER');		
		$this->db->where('ia.company_id', $company_id);
		$this->db->order_by('id','DESC');
		$data = $this->db->get()->result_array();		
		if($data)
			{
				return $data ;
			}
		else
			{
				return false;
			}		
	}
	
	public function addnotes($data)
	{ 
		$this->db->insert('tblcompanynotes', $data);
		if($this->db->insert_id()>0)
			{
				return true;
			}
		else
			{
				return FALSE;
			}		
	}
	public function get_history($company_id, $tabname)
	{
        $this->db->select('ia.*, ts.firstname, ts.lastname');		
        $this->db->from('tblcompanyhistory AS ia');                               // I use aliasing make joins easier		
        $this->db->join('tblstaff AS ts', 'ts.staffid = ia.user_id', 'INNER');		
        $this->db->where('ia.company_id', $company_id);
        $this->db->order_by('ia.id','desc');
        //$this->db->limit(1);
        $data = $this->db->get()->result_array();
		//print_r($data);exit;
		if($data)
			{
				return $data ;
			}
		else
			{
				return false;
			}		
	}
	// method for element's history created !
	public function create_history($history)
	{	
		$query = $this->db->insert('tblcompanyhistory', $history);
		if($query > 0){
			return true;
		}
		else {
			return false;
			}
	}
	public function fields($id)
	{
		$this->db->select('*');
		$this->db->from('tblclients');
		$this->db->where('userid', $id);
		$fields = $this->db->get()->result_array();
		if($fields)
			{
				return $fields;
			}
		else 
			{
				return false;
			}
	}
	public function paramvaluecountries($param1, $param2)
	{	
		//print_r($data);
		$this->db->select('short_name');
		$this->db->from('tblcountries');
		$this->db->where('country_id', $param1);
		$query1= $this->db->get()->result_array();
		
		$this->db->select('short_name');
		$this->db->from('tblcountries');
		$this->db->where('country_id', $param2);
		$query2= $this->db->get()->result_array();
		$data['query1']= $query1;
		$data['query2']= $query2;
		return $data;
	}
	
	public function param_valuecurrency($param1, $param2)
	{	
		$this->db->select('name');
		$this->db->from('tblcurrencies');
		$this->db->where('id', $param1);
		$query1= $this->db->get()->result_array();
		
		$this->db->select('name');
		$this->db->from('tblcurrencies');
		$this->db->where('id', $param2);
		$query2= $this->db->get()->result_array();
		$data['query1']= $query1;
		$data['query2']= $query2;
		return $data;
	}
	
	public function update_history($data){
		$query = $this->db->insert('tblcompanyhistory', $data);
		if($query > 0){
			return true;
		}
		else {
			return false;
			}
	}
	public function notes_fields($id){
		$this->db->select('*');
		$this->db->from('tblnotes');
		$this->db->where('id', $id);
		$fields = $this->db->get()->result_array();
		if($fields)
			{
				return $fields;
			}
		else 
			{
				return false;
			}
	}
	// zee code end 	11 july

	public function getContacts($where){
		$this->db->select('*');
		$this->db->from('tblcontacts');
		$this->db->where('id',$where);
		return $this->db->get()->result_array();
	}
	
	function client_contacts()    
    { 
        $this->db->select('tco.firstname, tco.lastname, tco.email, tco.phonenumber, tco.title, tco.last_login, tco.active, tco.id, tco.userid, cs1.is_primary, tcl.company as company');
        $this->db->from('tblcontacts AS tco');// I use aliasing make joins easier
        $this->db->join('tblclients AS tcl', 'tcl.userid = tco.userid', 'LEFT');
		$this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = tco.id', 'LEFT');
		$this->db->group_by('tco.email');
         return $this->db->get()->result_array();
    }
	
	public function vault_fields($id)
	{
		$this->db->select('*');
		$this->db->from('tblvault');
		$this->db->where('id',$id);
		return $this->db->get()->result_array();
	}
	public function get_staff_update($param1, $param2){

		$this->db->select('*');
		$this->db->from('tblstaff');
		$this->db->where('staffid', $param1);
		$query1= $this->db->get()->result_array();
		
		$this->db->select('*');
		$this->db->from('tblstaff');
		$this->db->where('staffid', $param2);
		$query2= $this->db->get()->result_array();
		$data['query1']= $query1;
		$data['query2']= $query2;
		
		return $data;
	}
	public function update_contact_new($data, $id)
	{	
		$this->db->where('id', $id);
        $this->db->update('tblcontacts', $data);	
        if ($this->db->affected_rows() > 0) 
		{
			return true;	
		}
	}
		// here 9 8 
	public function contacts_rel_clients($data)
	{
		
		$query = $this->db->insert('tblcontacts_rel_clients', $data);
		if($query)
			{
				return ;
			}
		else
			{
				return ; 
			}	
	}
	
	public function delete_contacts_rel_clients($company_id, $contact_id)
	{
		$this->db->where("contact_id", $contact_id);
		$this->db->where("company_id", $company_id);
		$query = $this->db->delete('tblcontacts_rel_clients');
		if ($query)
		{ 
			return true;
		}
		else
		{
			return false; 
		}
	}
	// here 9 8 end 
	
	public function get_existing_check($company_id , $contact_id)
	{	 
		$this->db->select("*"); 
		$this->db->from("tblcontacts_rel_clients"); 
		$this->db->where('company_id', $company_id );
		$this->db->where('contact_id', $contact_id ); 
		$query = $this->db->get()->result_array();
		return $query;
	} 
	public function update_contact_rel($table, $data, $where)
	{	
		$this->db->where($where);
		$this->db->update($table, $data);
		if ($this->db->affected_rows() > 0) 
		{
			return true;	
		}
	}
	public function get_customer_more_files($id, $where = array(), $filesid)
	{
		$this->db->where($where);
		$this->db->where('contact_id', $id);
		$this->db->where('rel_type', 'customer');
		$this->db->where('id <', $filesid);
		$this->db->order_by('dateadded', 'desc');
		$this->db->limit(6);

		return $this->db->get('tblfiles')->result_array();
	}
	public function rad_save($user_id , $data)
	{	
		
		$name = $data['name'];
		$this->db->select("*");
		$this->db->from('tblrad');
		$this->db->where('userid', $user_id);
		$this->db->where('name', $name);
		$result = $this->db->get()->result_array();	
		
		if($result)
		 {	
			$dat = array(
				'value' => $data['value'],
			);
			$this->db->where('userid', $user_id);
			$this->db->where('name', $data['name']);
			$this->db->update('tblrad', $dat);		
			if ($this->db->affected_rows() > 0) 
				{					
					return true;	
				}
		}
		else
		{	
			$save= array(
			'name' => $data['name'],
			'value' =>$data['value'],
			'userid' =>$user_id,	
			);
			$this->db->insert('tblrad', $save);
			$id = $this->db->insert_id();
			return $id;
		}
		// echo "out";exit;
		// echo $user_id; 
		// print_r($data); 
		// exit ;
		
	}
	public function rad_get($user_id)
	{	
		$this->db->select("*");
		$this->db->from('tblrad');
		$this->db->where('userid', $user_id);
		$result = $this->db->get()->result_array();	
		return $result;  
	}
    public function get_for_hidden($id)
    {   
        $this->db->select("*");
        $this->db->from('tblclients');
        $this->db->where('userid', $id);
        $result = $this->db->get()->result_array(); 
        return $result;  
    }
    public function put_in_hidden($hidden)
    { 
        $data = $hidden[0];
        $data['deleted_by'] =$this->session->userdata('staff_user_id'); 
        $val = $this->db->insert('tblhidden_items', $data);        
        if($val){
             return $val;     
        }
        else{
            return false; 
        }
         
    }
    public function client_portfolio_data(){
        $this->db->select()->from('tblportfolio_names');
        $query = $this->db->get();
        return $result = $query->result();
    }
    public function get_company_versieats_related($where){
        $this->db->select("tblclients.*, tblcontacts_rel_clients.company_id");
        $this->db->from('tblclients ');
        $this->db->join('tblcontacts_rel_clients ', 'tblclients.userid = tblcontacts_rel_clients.company_id');
        $this->db->where($where);
        $query = $this->db->get()->result_array();
        // here 9 aug ed    
        return $query;
    } 
    public function load_versieats_db(){
         $this->versi_con = $this->load->database('versieats_db', TRUE);    
         if($this->versi_con){ }
         else{
            return false; 
         }
    }
 public function reports($formdata){
        $this->load_versieats_db();
        $date_start  = date('Y-m-d') . " " . "00:00:00"; 
        $date_end    =  date('Y-m-d') . " ". "23:59:59";
        
        if(is_array($formdata)){
            $arr =$formdata['restaurant_name'];       
            $query_time_zone = "SELECT option_value FROM `mt_option` WHERE `mt_option`.`option_name` = 'merchant_timezone' AND `mt_option`.`merchant_id` = " . $arr ; 
            $qtime_zone = $this->versi_con->query($query_time_zone)->result_array();
            
            if($qtime_zone){     
                 date_default_timezone_set($qtime_zone[0]['option_value']);
                  $date_start  = date('Y-m-d') . " " . "00:00:00"; 
                  $date_end    =  date('Y-m-d') . " ". "23:59:59";   
            }
            else{
                 date_default_timezone_set('Pacific/Midway');
                  $date_start  = date('Y-m-d') . " " . "00:00:00"; 
                  $date_end    =  date('Y-m-d') . " ". "23:59:59";                   
            }
            $day =$formdata['day'];
            if(isset($formdata['datefrom']) && isset($formdata['dateto'])){
                $date_start = $formdata['datefrom']." "."00:00:00";
                $date_end = $formdata['dateto'] ." "."23:59:59";
            }
        }
        else{
            
            $arr = $formdata;
            $query_time_zone = "SELECT option_value FROM `mt_option` WHERE `mt_option`.`option_name` = 'merchant_timezone' AND `mt_option`.`merchant_id` = " . $arr ; 
            $qtime_zone = $this->versi_con->query($query_time_zone)->result_array();
            
            if($qtime_zone){
                
                  date_default_timezone_set($qtime_zone[0]['option_value']);
                  $date_start  = date('Y-m-d') . " " . "00:00:00"; 
                  $date_end    =  date('Y-m-d') . " ". "23:59:59";

            }
            else{
                 date_default_timezone_set('Pacific/Midway');
                  $date_start  = date('Y-m-d') . " " . "00:00:00"; 
                  $date_end    =  date('Y-m-d') . " ". "23:59:59";                   
            }
        }
        
        if($day){
            if($day  ==   2){
                $currentDate  = date('Y-m-d');
                $date_starts  = date('Y-m-d', strtotime("-1 day", strtotime(date('Y-m-d'))));
                $date_start   = $date_starts . " " . "00:00:00";
                $date_end     = $date_starts . " " . "23:59:59"; 

            } 
            elseif($day == 7){
                $currentDate  = date('Y-m-d');
                $date_starts  = date('Y-m-d', strtotime("-7 day", strtotime(date('Y-m-d'))));
                $date_start   = $date_starts . " " . "00:00:00";
                $currentDate2 = $currentDate . " " . "23:59:59";
                $date_end     = $currentDate2;
            }  
            elseif($day == 30){
                $currentDate  = date('Y-m-d');
                $date_starts  = date('Y-m-d', strtotime("-30 day", strtotime(date('Y-m-d'))));
                $date_start   = $date_starts . " " . "00:00:00";
                $currentDate2 = $currentDate . " " . "23:59:59";
                $date_end     = $currentDate2;
            }   
        }
        // echo $date_start;  echo $date_end; exit('ioo'); 

        if($this->versi_con){
           
            $result = array();
            $date = date('Y-m-d ');
            //echo $date ; exit('date'); 
            $payment_tip = array();
            $tip_pay = 0;
            $paymentgate = "SELECT option_value FROM mt_option WHERE `option_name` = 'paymentgateway' "; 
            $paymentgateway = $this->versi_con->query($paymentgate)->result_array();
            $paymentgateways = json_decode(stripslashes($paymentgateway[0]['option_value']), true); 
            // $res_ptype = $paymentgateways;
            //here db contain 4 paymentgateways but only two are using as per client request! 
            $res_ptype = array(0=>'cod', 1=>'mxm');
            
            foreach ($res_ptype as $ptype){
                $query_ptip =  "SELECT SUM(cart_tip_value) FROM mt_order WHERE `date_created` BETWEEN  '".$date_start ."'  AND  '". $date_end ."'  AND merchant_id = ".$arr."   AND  payment_type = '". $ptype ."' AND cart_tip_value !=''  AND status = 'paid' ";
                // echo  $query_ptip; exit('l'); 
                $resltip = $this->versi_con->query($query_ptip)->result_array();
                // echo $this->versi_con->last_query(); exit('lq');
                foreach ($resltip as  $resltips) {
                    $tip_pay += $resltips['SUM(cart_tip_value)']; 
                }
                
                // array_push($payment_tip, $resltip[0]);

                // $payment_tip = $resltip[0];
                // echo "<pre>"; print_r($tip_pay);  exit('lol'); 
            }
            // echo "<pre>"; print_r($payment_tip);  exit('ll'); 
            $payment_with_type = array(); $i= 0;            
            foreach ($res_ptype as $ptype){
                $query_pt= '';
               
                $query_pt =  "SELECT SUM(total_w_tax) , order_id, count(order_id) FROM mt_order WHERE `date_created` BETWEEN  '".$date_start ."'  AND  '". $date_end ."'  AND merchant_id = ".$arr."  AND status = 'paid'  AND  payment_type = '". $ptype ."'"; 
                $resl = $this->versi_con->query($query_pt)->result_array();
                $payment_with_type[$i] = $resl[0];
                if($ptype == 'cod'){
                    $ptype = 'Cash';
                }
                if($ptype == 'mxm'){
                    $ptype = 'Card';
                }
                array_push($payment_with_type[$i], $ptype);
                $i++; 
            }
            $payment_with_type[$i]['SUM(total_w_tax)'] = $tip_pay;
            array_push($payment_with_type[$i], 'Payment Tips');   
            $result[0] = $payment_with_type;
            //discount 
            $query_discount =  "SELECT count(voucher_code),  voucher_code, SUM(voucher_amount)  FROM mt_order WHERE `date_created` BETWEEN  '".$date_start ."'  AND  '". $date_end ."'  AND merchant_id = ".$arr . " AND voucher_code != '' GROUP BY voucher_code ";
            $qdiscount = $this->versi_con->query($query_discount)->result_array();
            $result[4] = $qdiscount;
            // discount end
            // sales by categoies:
            $cat_arr = array(); $j = 0; $svala = array();
            $query_sales =  "SELECT json_details   FROM mt_order WHERE `date_created` BETWEEN  '".$date_start ."'  AND  '". $date_end ."'  AND merchant_id = ".$arr . " AND status = 'paid' " ; 
            $qsales = $this->versi_con->query($query_sales)->result_array();
              //merchant tax 
             $query_merchant_tax = "SELECT option_value FROM `mt_option` WHERE `mt_option`.`option_name` = 'merchant_tax' AND `mt_option`.`merchant_id` = " . $arr ; 
             $merchant_tax = $this->versi_con->query($query_merchant_tax)->result_array();
                //for putting all jsons in one array . 
                $jarr = array();
                $sv  = array();
                $taxarr =  array();
                // $taxables  = 0;
                foreach ($qsales as $QSval) {
                        $QSarr = json_decode($QSval['json_details'], true) ; 
                         foreach ($QSarr as $QSarrval) {
                            // $taxables += $QSval['taxable_total'];
                            // array_push($taxarr,$QSval['taxable_total']);
                            array_push($jarr, $QSarrval);
                          }
                        }
                $group = array();       
                foreach ( $jarr as $jarrv ) {
                        $group[$jarrv['category_id']][] = $jarrv;
                    } 
                    // echo "<pre>"; print_r($group); exit('baggo');  
                    $z=1; $attr = array(); $prr = array(); 
               
                foreach ($group as  $key => $groupv) {
                            // echo "<pre>"; print_r($groupv); exit('oo'); 
                            //get cat name 
                              if($key != ''){
                                    // echo "<pre>"; echo $key; echo '</b>'; 
                                    $query_cat =  "SELECT category_name FROM mt_category WHERE cat_id = ".$key ; 
                                    $qcat = $this->versi_con->query($query_cat)->result_array();
                                }
                              else{
                                continue;
                                // exit('here');
                              }                              
                              if($qcat[0]['category_name'] != ''){
                                $cat_name = $qcat[0]['category_name'];    
                                 }   
                                  else{ $cat_name = ' ' ;}
                               $price = 0; $qty = 0;
                               $no = count($groupv);
                               foreach ($groupv as $gva) {
                                // echo "<pre>"; print_r($gva); exit('barg');
                                $price += $gva['price'];
                                if(isset($gva['sub_item'])){
                                        foreach ($gva['sub_item'] as  $innercat){
                                            foreach ($innercat as  $innercatprice){
                                                $cater = explode('|', $innercatprice);
                                                $price += $cater[1]; 
                                                // echo "<pre>"; print_r($cater); exit('thi');        
                                            }
                                            
                                        }
                                        
                                    } 
                                 if(isset($gva['addonitem_categories'])){
                                        foreach ($gva['addonitem_categories'] as  $innersubcat){
                                            // echo "<pre>"; print_r($innersubcat); exit('thi'); 
                                            foreach ($innersubcat as  $subcatkey => $innersubcatprice){
                                                // echo "<pre>"; print_r($innersubcatprice); exit('thi');        
                                                $subcater = explode('|', $subcatkey);
                                                $price += $subcater[1]; 
                                                // echo "<pre>"; print_r($subcater); exit('thi');        
                                            }   
                                        }
                                    }    
                                   $qty   += $gva['qty'];
                               }

                // echo "<pre>"; print_r($merchant_tax[0]['option_value']); exit('baggo');
                               $prr[$key] = array('no' => $no, 'price' =>$price, 'qty'=>$qty, 'name' =>  $cat_name, 'tax'=> $merchant_tax[0]['option_value']); 
                    }
                    // print_r($prr); exit('barg'); 

            $result[1] =   $prr;     
            //sales by category end
            //sales by food items by category
                    $group_sales = array();
                    foreach ($jarr as  $gv2) {
                        $group_sales[$gv2['category_id']][$gv2['item_id']][] = $gv2;
                    }
                    // echo "<pre>"; print_r($group_sales); exit('done '); 
                    foreach ($group_sales as  $keygs => $gsv) {
                                
                            //get cat name
                            foreach ($gsv as $keygsv => $gsv2) { 
                                
                                if($keygs != ''){
                                    $query_catgs =  "SELECT category_name FROM mt_category WHERE cat_id = ".$keygs ; 
                                    $qcatgs = $this->versi_con->query($query_catgs)->result_array();
                                }
                                else{
                                    continue;
                                }   
                                if($keygsv != ''){
                                    $query_catgsv =  "SELECT item_name , price FROM mt_item WHERE item_id = ".$keygsv ; 
                                    $qcatgsv = $this->versi_con->query($query_catgsv)->result_array();
                                }
                                $item_price = '';    

                              if($qcat[0]['category_name'] != ''){
                                $cat_name2 = $qcatgs[0]['category_name']; 
                                $item_price =  $qcatgsv[0]['price'];
                                 }   
                                  else{ $cat_name2 = ' ' ;}
                               $price2 = 0; $qty2 = 0;
                               $no2 = count($gsv2);
                               $o = 1;  
                               foreach ($gsv2 as $gva2) {         
                                    $o++;   
                                    $price2 += $gva2['price'];
                                    if(isset($gva2['sub_item'])){
                                            foreach ($gva2['sub_item'] as  $gva2innercat){
                                                foreach ($gva2innercat as  $gva2innercatprice){
                                                    $gva2cater = explode('|', $gva2innercatprice);
                                                    $price2 += $gva2cater[1]; 
                                                    // echo "<pre>"; print_r($cater); exit('thi');        
                                                }
                                                
                                            }
                                            
                                    } 
                                    if(isset($gva2['addonitem_categories'])){
                                        foreach ($gva2['addonitem_categories'] as  $gva2innersubcat){
                                            // echo "<pre>"; print_r($innersubcat); exit('thi'); 
                                            foreach ($gva2innersubcat as  $gva2subcatkey => $gva2innersubcatprice){
                                                // echo "<pre>"; print_r($innersubcatprice); exit('thi');        
                                                $gva2subcater = explode('|', $gva2subcatkey);
                                                $price2 += $gva2subcater[1]; 
                                                // echo "<pre>"; print_r($subcater); exit('thi');        
                                            }
                                            
                                        }
                                        
                                    }    

                                    // echo "<pre>"; print_r($gva2); exit('pr');  
                                    
                                       $qty2   += $gva2['qty'];
                               }
                     
                               $prrr[$cat_name2][$keygsv] = array('no' => $no2, 'item_price'=>$item_price, 'price' =>$price2, 'qty'=>$qty2, 'cat_name' =>  $cat_name2, 'cat_id'=>$keygs , 'item_name' => $qcatgsv[0]['item_name'], 'tax'=>$merchant_tax[0]['option_value']); 
                           }
                    }
                    // echo "<pre>"; print_r($prrr); exit('pr');  
            $result[2] =   $prrr;
            //sales by food items by category end

            //hourly sales 
            $query_hourly_sales_open = "SELECT option_value FROM `mt_option` WHERE `mt_option`.`option_name` = 'stores_open_starts' AND `mt_option`.`merchant_id` = " . $arr ; 
            $q_hourly_sales_open = $this->versi_con->query($query_hourly_sales_open)->result_array();
            $openhour =0; $closehour= 0; 
             $todayopen = ''; $day = ''; $todayclose =''; 
            if($q_hourly_sales_open[0]['option_value'] ){
                $opentimings = json_decode(stripslashes($q_hourly_sales_open[0]['option_value']), true); 
                $opentiming = array(); 
                foreach ($opentimings as $key => $valuew) {
                    if($valuew){
                    $opentiming[$key] =  date("H", strtotime($valuew));
                    }
                }
                // echo "<pre>"; print_r($opentimings); exit('koooo'); 
                $openhour = min($opentiming); 
            }

            $query_hourly_sales_end = "SELECT option_value FROM `mt_option` WHERE `mt_option`.`option_name` = 'stores_open_ends' AND `mt_option`.`merchant_id` = " . $arr ; 
            $q_hourly_sales_end = $this->versi_con->query($query_hourly_sales_end)->result_array();
            if($q_hourly_sales_end[0]['option_value'] != ''){
                $closingtimings = json_decode(stripslashes($q_hourly_sales_end[0]['option_value']), true);
                $closingtiming = array(); 
                 foreach ($closingtimings as $ckeytiming => $valueq) { 
                    if($valueq){
                        $closingtiming[$ckeytiming] =  date("H", strtotime($valueq));
                    }
                }
                $closehour = max($closingtiming); 
            }

            $openhour ; 
            $dif = $closehour - $openhour ; 

            $a = $openhour +1;
            $hoval = array(); $m = 1; 
            for ($q=0 ; $q <= $dif ; $q++) {
                
                 $date_diff = strtotime($date_end) - strtotime($date_start); 
                 $days_dif  = round($date_diff / (60 * 60 * 24));//exit('jj'); 
                 $openhourb = $openhour+$m; 
                 $orders = 0; 
                 $sub_total = 0;
                 $total_w_tax = 0;
                 $openhourcur  =     $openhour+$q; 
                 for ($r=0 ; $r < $days_dif ; $r++) {
                    // echo $date_start; 
                    $date_startsaaa = date('Y-m-d', strtotime("+". $r. " day", strtotime($date_start)));   
                     
                    $vakkk = "'". $date_startsaaa . " ". $openhourcur  . ":00:00'";
                    $vs = "'". $date_startsaaa . "  ". $openhourcur . ":59:59'";
                    // echo $vakkk; echo "  "; 
                    // echo $vs;
                     // exit('kp');

                   $daa = "SELECT count(order_id), date_created, order_id, SUM(sub_total), SUM(total_w_tax)  FROM `mt_order` WHERE (`date_created` BETWEEN ".$vakkk ." AND ". $vs .") AND `merchant_id` = " . $arr ." AND  status = 'paid' " ;
                   // echo $daa ; //exit('ko');  
                    $dab = $this->versi_con->query($daa)->result_array();
                    $orders = $orders +$dab[0]['count(order_id)'];
                    $sub_total = $sub_total + $dab[0]['SUM(sub_total)'];
                    $total_w_tax = $total_w_tax + $dab[0]['SUM(total_w_tax)'];
                    // echo $r; 
                 }
                 // echo $days_dif; 
                 // exit('o'); 
                 // $time_in_12_hour_format  = date("g:i A", strtotime( $openhourb . ":00" ));
                 // echo $time_in_12_hour_format; exit; 
                 array_push($hoval, array('count(order_id)'=>$orders, 'sub_total'=>$sub_total , 'total_w_tax'=>$total_w_tax , 'openhoura' => date("g:i A", strtotime( $openhourb . ":00" )) ));
                $m++; 

            }
            // print_r($hoval); exit('all'); 
            $result[3] = $hoval; 
            //hourly sales  ends
            
            if($result){
                return $result; 
            }    
        }
    }
    public function merchant($val = ''){ 
        $this->load_versieats_db();
        $this->versi_con->select('merchant_id, restaurant_name');
        $this->versi_con->from('mt_merchant');
        if(!is_array($val)){
            $this->versi_con->where('merchant_id',$val);    
        }
        else{
            $comp = ''; 
            foreach ($val as $id){
               $comp .= $id.',';
            }   
            $ids = rtrim($comp,",");
            $where = 'merchant_id IN ('. $ids . ')';
            $this->versi_con->where($where);       
        }
        return $this->versi_con->get()->result_array();
    }
	public function get_all_clients_for_mail(){ 
        $this->db->select('*');
        $this->db->from('tblclients');
        return $this->db->get()->result_array();

    }
	 public function get_closing_hours($arr){
        $this->load_versieats_db(); $val; 
        $query_hourly_sales_end = "SELECT option_value FROM `mt_option` WHERE `mt_option`.`option_name` = 'stores_open_ends' AND `mt_option`.`merchant_id` = " . $arr ; 
             $q_hourly_sales_end = $this->versi_con->query($query_hourly_sales_end)->result_array();
            if($q_hourly_sales_end[0]['option_value'] ){
            $closingtimings = json_decode(stripslashes($q_hourly_sales_end[0]['option_value']), true);
            foreach ($closingtimings as $key => $clt) {
                if($key == strtolower(date('l'))){
                    $val = $clt;
                }
            }
            return  $val; 
        }
    }
	 public function report_sended($id = '', $mail, $clientid , $date){
        $query_report = "SELECT flag FROM `tblreport_sended` WHERE `tblreport_sended`.`merchant_id` = ". $id . " AND `tblreport_sended`.`sended_email` =  '" . $mail. "'  AND `tblreport_sended`.`client_id` =" . $clientid. " AND `tblreport_sended`.`date_of_send` = '" . $date . "'"; 
        // echo $query_report; exit('jaz'); 
        $res_query = $this->db->query($query_report)->result_array();
        if($res_query){
            return $res_query[0]['flag']; 
            }
    }
    public function add_reports_flag($id, $mail, $clientid , $date){
        //  echo date('Y-m-d H:i');
        // exit('om');
        $data = array(
        'flag'=>1,
        'merchant_id' =>$id,
        'sended_email' =>$mail,
        'client_id' =>$clientid,
        'date_of_send'  => $date,
        );
        $insert = $this->db->insert('tblreport_sended', $data);
        return $insert; 
    }

    public function get_time_zone($arr){
        $this->load_versieats_db();

        $query_time_zone = "SELECT option_value FROM `mt_option` WHERE `mt_option`.`option_name` = 'merchant_timezone' AND `mt_option`.`merchant_id` = " . $arr ; 
        // echo $query_time_zone; 
        // exit('p');
        $qtime_zone = $this->versi_con->query($query_time_zone)->result_array();
        
        if($qtime_zone){ 
            return $qtime_zone[0]['option_value']; 
        }

    }
    public function get_versiPOS_api_configuration($id = ''){
        $this->db->select('versiPOS_username, versiPOS_password, versiPOS_merchant_id, versiPOS_store_id');
        $this->db->from('tblclients');
        $this->db->where('userid', $id);
        $result = $this->db->get()->result_array();
        // echo "<pre>"; print_r($this->db->get()->result_array()); exit('fo'); 
        return $result[0]; 
    }
    public function get_versiPOS_api_data($id = ''){
        $config = $this->get_versiPOS_api_configuration($id);
        // echo "<pre>"; print_r($config);  echo $id ; exit('hjhjhjhjhjh'); 

        // $url = 'https://reports.versipos.net:1502/reports/GET_DSR';
        $fields = array(
        "ApiKey"=> "d17aaf42f76047339de1c749e9cf3bbc",
        "MerchantID"=> $config['versiPOS_merchant_id'],
        "StoreID"   => $config['versiPOS_store_id'],
        "Username"  => $config['versiPOS_username'],
        "Password"  => $config['versiPOS_password'],
        "Format"=> "text"

        ); 
        $ch = curl_init('https://reports.versipos.net:1502/reports/GET_DSR');
        curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,

        CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        ),
        CURLOPT_POSTFIELDS => json_encode($fields)
        ));
        $response = curl_exec($ch);
        $rep = json_decode($response);
        return $rep->ReportTxt; 
        // //close connection
        
    }

    public function get_versiPos_merchants($id = ''){
           $result = $this->inventory_model->get_company($id);
           $api_company = array(); 
           foreach ($result as $res) {
              if($res['versiPOS_username'] !='' && $res['versiPOS_password'] !='' && $res['versiPOS_merchant_id'] !='' && $res['versiPOS_store_id'] !=''){
                    $api_result = $this->get_versiPOS_api_data($res['userid']); 
                    $api_result_config =  $this->get_versiPOS_api_configuration($res['userid']); 
                    $merchant_name_versiPos = explode(':', $api_result[0]);
                    if($api_result){
                        array_push($api_company, array('client_merchant_versiPos' =>$res['userid'], 'merchant_name_versiPos' => $merchant_name_versiPos[1]));      
                    }
                }
           }
           if($api_company){  
                return $api_company;  
            }
    }
	 public function get_size_for_price($id = ''){
        $this->load_versieats_db();
        $query_size = "SELECT size_name FROM `mt_size` WHERE `mt_size`.`size_id` = " . $id ;
        $size = $this->versi_con->query($query_size)->result_array();
        if($size){ 
                return $size[0]['size_name']; 
            }

    }
    public function api_create(){
        $this->load_versieats_db();
        $query_size = "SELECT * FROM `mt_order`" ;
        $size = $this->versi_con->query($query_size)->result_array();
        if($size){ 
                return $size; 
            }

    }
    public function get_1()
    {
        $chech_portfolio = main_portfolio($this->session->userdata('portfolio_id'));

        $this->db->select('*');
        $this->db->from('tblclients');
        if($chech_portfolio == '') {
            $this->db->where('tblclients.portfolio_id', $this->session->userdata('portfolio_id'));    
        }
        
        $this->db->order_by('company', 'asc');

        return $this->db->get()->result_array();
    }
    public function get_compny_contacts($id)
    {
        $this->db->select('tblcontacts.email,tblcontacts.firstname,tblcontacts.lastname,tblcontacts.title,cs1.contact_id');
        $this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = tblcontacts.id', 'LEFT');
        $this->db->where('cs1.company_id',$id);
        return $this->db->get('tblcontacts')->result_array();
    }
    
        public function add_customer_leads($data)
    {
        if (isset($data['custom_contact_date']) || isset($data['custom_contact_date'])) {
            if (isset($data['contacted_today'])) {
                $data['lastcontact'] = date('Y-m-d H:i:s');
                unset($data['contacted_today']);
            } else {
                $data['lastcontact'] = to_sql_date($data['custom_contact_date'], true);
            }
        }

        if (isset($data['is_public']) && ($data['is_public'] == 1 || $data['is_public'] === 'on')) {
            $data['is_public'] = 1;
        } else {
            $data['is_public'] = 0;
        }

        if (!isset($data['country']) || isset($data['country']) && $data['country'] == '') {
            $data['country'] = 0;
        }

        if (isset($data['custom_contact_date'])) {
            unset($data['custom_contact_date']);
        }

        $data['description'] = nl2br($data['description']);
        $data['dateadded']   = date('Y-m-d H:i:s');
        $data['addedfrom']   = get_staff_user_id();

        $data                = do_action('before_lead_added', $data);

        $tags = '';
        if (isset($data['tags'])) {
            $tags  = $data['tags'];
            unset($data['tags']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $data['address'] = trim($data['address']);
        $data['address'] = nl2br($data['address']);

        $data['email'] = trim($data['email']);
        // echo "<pre>"; print_r($data); exit; 
        $this->db->insert('tblcustomerleads', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
        return $insert_id;
        }

        return false;
    }

        public function update_customer_leads($data, $id)
    {
        $current_lead_data = $this->get($id);
        $current_status    = $this->get_status($current_lead_data->status);
        if ($current_status) {
            $current_status_id = $current_status->id;
            $current_status    = $current_status->name;
        } else {
            if ($current_lead_data->junk == 1) {
                $current_status = _l('lead_junk');
            } elseif ($current_lead_data->lost == 1) {
                $current_status = _l('lead_lost');
            } else {
                $current_status = '';
            }
            $current_status_id = 0;
        }

        $affectedRows = 0;
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        if (!defined('API')) {
            if (isset($data['is_public'])) {
                $data['is_public'] = 1;
            } else {
                $data['is_public'] = 0;
            }

            if (!isset($data['country']) || isset($data['country']) && $data['country'] == '') {
                $data['country'] = 0;
            }

            $data['description'] = nl2br($data['description']);
        }

        if (isset($data['lastcontact']) && $data['lastcontact'] == '' || isset($data['lastcontact']) && $data['lastcontact'] == null) {
            $data['lastcontact'] = null;
        } elseif (isset($data['lastcontact'])) {
            $data['lastcontact'] = to_sql_date($data['lastcontact'], true);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'lead')) {
                $affectedRows++;
            }
            unset($data['tags']);
        }

        $data['address'] = trim($data['address']);
        $data['address'] = nl2br($data['address']);

        $data['email'] = trim($data['email']);

        $this->db->where('id', $id);
        $this->db->update('tblcustomerleads', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if (isset($data['status']) && $current_status_id != $data['status']) {
                $this->db->where('id', $id);
                $this->db->update('tblcustomerleads', array(
                    'last_status_change' => date('Y-m-d H:i:s'),
                ));
                $new_status_name = $this->get_status($data['status'])->name;
                $this->log_lead_activity($id, 'not_lead_activity_status_updated', false, serialize(array(
                    get_staff_full_name(),
                    $current_status,
                    $new_status_name,
                )));

                do_action('lead_status_changed', array('lead_id'=>$id, 'old_status'=>$current_status_id, 'new_status'=>$data['status']));
            }

            if (($current_lead_data->junk == 1 || $current_lead_data->lost == 1) && $data['status'] != 0) {
                $this->db->where('id', $id);
                $this->db->update('tblcustomerleads', array(
                    'junk' => 0,
                    'lost' => 0,
                ));
            }

            if (isset($data['assigned'])) {
                if ($current_lead_data->assigned != $data['assigned'] && (!empty($data['assigned']) && $data['assigned'] != 0)) {
                    $this->lead_assigned_member_notification($id, $data['assigned']);
                }
            }
            logActivity('Lead Updated [Name: ' . $data['name'] . ']');

            return true;
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    public function delete_customer_lead($id = ''){
        $this->db->where('id', $id);
        $this->db->delete('tblcustomerleads');
         if ($this->db->affected_rows() > 0) {
            return true; 
         }

    }
public function get_customers_lead($id = '', $where = array())
{
$this->db->select('*');
// $this->db->join('tblleadsstatus', 'tblleadsstatus.id=tblleads.status', 'left');
// $this->db->join('tblleadssources', 'tblleadssources.id=tblleads.source', 'left');
if($where){
$this->db->where('id', $id); 
$this->db->where($where); 

}


// if (is_numeric($id)) {
// $this->db->where('tblcustomerleads.id', $id);
// $lead = $this->db->get('tblcustomerleads')->row();
// // if ($lead) {
// // if ($lead->from_form_id != 0) {
// // $lead->form_data = $this->get_form(array(
// // 'id' => $lead->from_form_id,
// // ));
// // }
// // $lead->attachments = $this->get_lead_attachments($id);
// // }

// return $lead;
// }

return $this->db->get('tblcustomerleads')->row();
}
}
