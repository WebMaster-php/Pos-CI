<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Inventory extends Admin_controller
{
    public function __construct()
    {
		parent::__construct();
		check_portfolio_id();
        $this->load->model('invoice_items_model');
		//$this->load->model('Inventory_model');
        $this->load->model('products_model');
    }
	//**umer farooq chattha****//
    public function is_unique_serial($id,$ids)
    {
        $this->db->where_not_in('inventory_id', $ids);
        $this->db->where("serial_number",$id);
        $this->db->where("portfolio_id",$this->session->userdata('portfolio_id'));
        $qt = $this->db->get("tblinventoryadd");
        //echo $this->db->last_query();
        if($qt->num_rows()>0)
        {
             $this->form_validation->set_message( __FUNCTION__ ,  'The Serial Number field must contain a unique value.');
            return FALSE;
        }else
        {
            return TRUE;
        }
                
    }
     public function is_unique_serial2()
    {
    	$this->db->where("serial_number",$_POST['serial_number']);
        $this->db->where("portfolio_id",$this->session->userdata('portfolio_id'));
        $qt = $this->db->get("tblinventoryadd");
        //echo $this->db->last_query();
        if($qt->num_rows()>0)
        {
             $this->form_validation->set_message( __FUNCTION__ ,  'The Serial Number field must contain a unique value.');
            return FALSE;
        }else
        {
            return TRUE;
        }
                
    }
    
	public function manage()
    {
		if (!has_permission('inventory', '', 'view')) {
            access_denied('Inventory');
        }        
        extract($_POST);
        $this->load->helper(array('form', 'url'));
        $this->load->helper('file');
        $this->form_validation->set_rules('account', 'Account', 'required|trim');
        if($inventory_id ==0)
        {
            // $this->form_validation->set_rules('serial_number', 'Serial Number', 'trim|is_unique[tblinventoryadd.serial_number]');
            $this->form_validation->set_rules('serial_number', 'Serial Number', 'trim|callback_is_unique_serial2[]');

        }else{
			$where = array('inventory_id' => $inventory_id);
			$data['inventory'] = $this->inventory_model->get('tblinventoryadd', $objArr=FALSE, $where);
			if($this->input->post('serial_number') != '' && $this->input->post('serial_number') != $data['inventory'][0]['serial_number']){
				$this->form_validation->set_rules('serial_number', 'Serial Number', 'trim|callback_is_unique_serial['.$inventory_id.']');
			}
        }
        $this->form_validation->set_rules('type_of_hardware', 'Type Of Hardware', 'required|trim');
        $this->form_validation->set_rules('date_in', 'Date In', 'required|trim');
        $this->form_validation->set_rules('status', 'Status', 'required|trim');
        $this->form_validation->set_rules('origin', 'Origin', 'required|trim');
        $this->form_validation->set_rules('equipment_owner', 'Equipment Owner', 'required|trim');
        $this->form_validation->set_rules('manufacturer', 'Manufacturer', 'trim');
        $this->form_validation->set_rules('warranty_expiration_date', 'Warranty Expiry Date', 'required|trim');
        if (empty($_FILES['image_name']['name']) && $inventory_id ==0 && $img_clone=='0')
        {
            $this->form_validation->set_rules('image_name', 'Item Image', 'trim');
        }else if($img_clone !='0')
        {
           
        }
        $this->form_validation->set_rules('description', 'Description', 'trim');
        if($this->form_validation->run()==false){
            echo validation_errors();
        }
        else{
            $ok=0;
            extract($_POST);
            if($inventory_id ==0 )
            {
                if($img_clone=='0')
                {
					if($_FILES['image_name']['name']){
						$img_upload_name = $_FILES['image_name']['name'];
						$img_name = time().$_FILES['image_name']['name'];
						//upload configuration
						$config['upload_path']   = 'uploads/files/';
						$config['allowed_types'] = 'gif|jpg|jpeg|png';
						$config['max_size']      = 1024;
						$config['file_name'] = $img_name;
						$img_name = base_url()."uploads/files/".$img_name;
						$this->load->library('upload', $config);
						//upload file to directory
						if($this->upload->do_upload('image_name')){
							$uploadData = $this->upload->data();
							$uploadedFile = $uploadData['image_name'];
							$ok = 0;
						}else{
							echo $data['error_msg'] = $this->upload->display_errors();
							$ok=1;
						}
					}
                }
                else
				{
                    if(empty($_FILES['image_name']['name']))
                    {
                        $img_name = $img_clone;
                        $ok = 0;
                    }else{
						if($_FILES['image_name']['name']){
							$img_upload_name = $_FILES['image_name']['name'];
							$img_name = time().$_FILES['image_name']['name'];
							//upload configuration
							$config['upload_path']   = 'uploads/files/';
							$config['allowed_types'] = 'gif|jpg|jpeg|png';
							$config['max_size']      = 1024;
							$config['file_name'] = $img_name;
							$img_name = base_url()."uploads/files/".$img_name;
							$this->load->library('upload', $config);
							//upload file to directory
							if($this->upload->do_upload('image_name')){
								$uploadData = $this->upload->data();
								$uploadedFile = $uploadData['image_name'];
								$ok = 0;
							}else{
								echo $data['error_msg'] = $this->upload->display_errors();
								$ok=1;
							}
						}
                    }
                }
				
                if($ok==0)
                {
                    $data = array(
						"account"=>$account,
						"serial_number"=>$serial_number,
						"type_of_hardware"=>$type_of_hardware,
						"date_in"=>date_format(date_create($date_in), "Y-m-d"),
						"status"=>$status,
						"origin"=>$origin,
						"image"=>$img_name,
						"imageuploadname"=>$img_upload_name,
						"equipment_owner"=>$equipment_owner,
						"manufacturer"=>$manufacturer,
						"warranty_expiration_date"=> $warranty_expiration_date,
						"description"=>$description
                    );
					if($this->input->post('custom_fields')){
						$data['custom_fields'] = $this->input->post('custom_fields');
					}
                    $data['current_date']=date("Y-m-d");
                    $data['custome_nu']=$exp_no;
                    $dateIn = date('Y-m-d', strtotime($date_in));
					$inDate = strtotime($dateIn);
                    if($warranty_expiration_date==10)
                    {
                        if($exp_type==1){
							$getExp = strtotime('+'.$exp_no.' years',$inDate);
							$data['exp_date'] = date('Y-m-d', $getExp);
                        }else if($exp_type==2){
							$getExp = strtotime('+'.$exp_no.' months',$inDate);
							$data['exp_date'] = date('Y-m-d', $getExp);
                        }else if($exp_type==3){
							$getExp = strtotime('+'.$exp_no.' days',$inDate);
                            $data['exp_date'] = date('Y-m-d', $getExp);
                        }
                        $data['custome_type']=$exp_type;
                    }
					//here 31 jul
                    elseif($warranty_expiration_date==0){
							$data['exp_date']= 'No Warranty';
						}
					// 31 july
					else{
						$getExp = strtotime('+'.$warranty_expiration_date.' years',$inDate);
                        $data['exp_date'] = date('Y-m-d', $getExp);
                    }
                    //update action s
                    $data['user_id']= $this->session->userdata('staff_user_id');
                    // now zee code start
                    $inventory_id = $this->input->post('inventory_id');
                    $user_id = $this->session->userdata('staff_user_id');

                    $action = "Created";		
					$tabname = "profile";
					
					$data['portfolio_id'] = get_client_portfolio($data['account']);
					// echo "<pre>"; print_r($data); exit; 
					// $data['portfolio_id'] = $this->session->userdata('portfolio_id');
                    $inventory_id = $this->inventory_model->add($data);//
                    if($inventory_id)
					{   
                        $this->inventory_history($inventory_id, $user_id, $action);
						$this->inventory_customer_history($inventory_id, $data, $tabname, $action);
                        //now zee code end
                        echo 'yes';
                        set_alert('success', 'Data Inserted Succesfully', 'Inventory Inserted Succesfully');
                    } 
					else 
					{
                        echo 'no data save';
                    }
                }else{
                    echo 'image Problem';
                }
            }
			//mail if close inventory 0
			else
            {
                $ok==0;
                if (empty($_FILES['image_name']['name']))
                {
                    $ok=0;
                }else{
					
					if($_FILES['image_name']['name']){
						$img_upload_name = $_FILES['image_name']['name'];
						$img_name = time().$_FILES['image_name']['name'];
						$config['upload_path']   = 'uploads/files/';
						$config['allowed_types'] = 'gif|jpg|png|pdf';
						$config['max_size']      = 1024;
						$config['file_name'] = $img_name;
						$this->load->library('upload', $config);
						if($this->upload->do_upload('image_name')){
							$uploadData = $this->upload->data();
							$uploadedFile = $uploadData['image_name'];
							$ok = 1;
							
						}else{
							echo $data['error_msg'] = $this->upload->display_errors();
							$ok=0;
						}
					}
                }
                
                $data = array(
                    "account"=>$account,
                    "serial_number"=>$serial_number,
                    "type_of_hardware"=>$type_of_hardware,
                    "date_in"=>date_format(date_create($date_in), "Y-m-d"),
                    "status"=>$status,
                    "origin"=>$origin,
                    "equipment_owner"=>$equipment_owner,
                    "manufacturer"=>$manufacturer,
                    "warranty_expiration_date"=> $warranty_expiration_date,
                    "description"=>$description
                    );
                    if($ok==1)
                    {
                        $data['image']= base_url()."uploads/files/".$img_name;
						$data['imageuploadname']= $img_upload_name;
                    }
					if($this->input->post('custom_fields')){
						$data['custom_fields'] = $this->input->post('custom_fields');
					}
					
                    $data['current_date']=date("Y-m-d");
                    $data['custome_nu']=$exp_no;
					$dateIn = date('Y-m-d', strtotime($date_in));
					$inDate = strtotime($dateIn);
                    if($warranty_expiration_date==10)
                    {
                        if($exp_type==1){
							$getExp = strtotime('+'.$exp_no.' years',$inDate);
							$data['exp_date'] = date('Y-m-d', $getExp);
                        }else if($exp_type==2){
							$getExp = strtotime('+'.$exp_no.' months',$inDate);
							$data['exp_date'] = date('Y-m-d', $getExp);
                        }else if($exp_type==3){
							$getExp = strtotime('+'.$exp_no.' days',$inDate);
                            $data['exp_date'] = date('Y-m-d', $getExp);
                        }
                        $data['custome_type']=$exp_type;
                    }
					//here 31 jul
                    elseif($warranty_expiration_date==0){
							$data['exp_date']= 'No Warranty';
						}
					// 31 july
					else{
						$getExp = strtotime('+'.$warranty_expiration_date.' years',$inDate);
                        $data['exp_date'] = date('Y-m-d', $getExp);
                    }
                    //update action s
				
                    $this->update_history($data, $inventory_id, $_FILES['image_name']['name'] );
					$this->update_costomer_history($data, $inventory_id, $_FILES['image_name']['name']);
					$data['updated_date'] = date("Y-m-d h:i:sa");
                    if($this->inventory_model->update($data,$inventory_id))
					{
						set_alert('success', 'Update the inventory', 'Inventory Group');
                    } 
					else {
                        echo 'no data update';
                    }
            }
        }
    }
	


	
//**umer farooq chattha****//
    /* List all available items */
    public function index()
    {
        if (!has_permission('inventory', '', 'view')) {	
            access_denied('Inventory');
        } 
        $where = [];
        if(!null == $this->session->userdata('portfolio_id')){
  			$check = main_portfolio($this->session->userdata('portfolio_id'));  
		    if($check == '')
		    {
		        $where= array('portfolio_id'=>$this->session->userdata('portfolio_id'));
		    }
		}
		
		$data['values'] = $this->inventory_model->get('tblcustom', $objArr=TRUE, $where);		
		$data['cust'] = $this->inventory_model->get('tblclients','', $where);
		$data['accounts'] = $this->inventory_model->get('tblclients', $objArr=TRUE,$where);
		$where['group_type'] = 'status';
		$data['statuses'] = $this->inventory_model->get('tblcustom','', $where );
		$this->load->view('admin/inventory/manage', $data);
	}
	
	public function table(){      
		// echo "<pre>";
		// print_r($_POST); 
		// exit('innnn');
		$this->app->get_table_data('tblinventoryadd');    
	}
    
    /* Edit or update items / ajax request /*/


	//for new hardware
	
	public function inventoryGroup()
	{
        if (has_permission('inventory', '', 'view')) 
		{
			$data =  array
			(
				'group_type' =>  $this->input->post('inv_group_type'),
				'value' =>  $this->input->post('name'),			
				'created_at' =>  date('Y-m-d H:i:s'),
				'portfolio_id' => 	$this->session->userdata('portfolio_id')
			);
			$save = $this->inventory_model->inventoryGroup($data);
			if($save)
			{
				set_alert('success', $this->input->post('inv_group_type').' '.'Added Successfully');
			}
			else
			{
				set_alert('warning', _l('group_type_inventory_warning', _l($this->input->post('inv_group_type'))));
			}
			
		}
	}	
	
        
    //// abu bakar saddique//////
    public function update_group($id)
    {    
        if ($this->input->post() && has_permission('inventory', '', 'edit')) {
            $data = array(
                'value' => $this->input->post('name'),
                //'updated_at' => date('Y-m-d H:i:s'),
            );
            $update = $this->inventory_model->edit_group('tblcustom', $data, $id);
            load_admin_language();
            if($update)
            {
                set_alert('success', 'Inventory Updated successfully!');  
            }
            else
            {
                set_alert('warning', 'There is problem while updating Inventory');
            }
        }
    }

    public function delete_group($id)
    {
        if (has_permission('inventory', '', 'delete')) {
            if ($this->inventory_model->delete_group($id)) {
                set_alert('success', 'Inventory Deleted Successfully');
            }
        }
        redirect(admin_url('inventory'));
    }
    
    //// abu bakar saddique//////

    /* Delete item*/
    public function delete($id, $locid = '')
    {
		 //echo "aaaa"; die;
        if (!has_permission('inventory','', 'delete')) {
            access_denied('Inventory');
        }
			
        if (!$id) {
            redirect(admin_url('inventory'));
        }

        $response = $this->inventory_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('inventory')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
        }
		if($locid){
			redirect(admin_url('tickets/ticket/'.$locid ));
		}
        else{
			redirect(admin_url('inventory'));
		}
	}	

    //**** umer farooq chattha******///
    function for_model()
    {	
		$json = array();
		$this->db->select('*');
		$this->db->from('tblinventoryadd');
		$this->db->where("inventory_id",$_POST['inventory_id']);
		$this->db->where("portfolio_id",$this->session->userdata('portfolio_id'));
		$res = $this->db->get()->result_array();
		if($res)
		{
			$json['inventory_res'] = $res[0];
			$this->db->select('*');
			$this->db->from('tblcustomfieldsvalues');
			$this->db->where("fieldto",'inventory');
			$this->db->where("relid",$res[0]['inventory_id']);
			$customRes = $this->db->get()->result_array();
			if($customRes)
			{
				$json['inventory_custom_res'] = $customRes;
			}else{
				$json['inventory_custom_res'] = '';
			}
		}
		echo json_encode($json);
    }

	// zee code starts	
	// to get value from tbl custom
	public function param_value($param1 , $param2){
		$query= $this->inventory_model->paramvalue($param1, $param2);
		return $query; 
		
	}
	public function find_user($param1 , $param2){
		$query= $this->inventory_model->getUser($param1, $param2);
		return $query; 
	}
	public function param_valuecustomer($param1 , $param2){
		$query= $this->inventory_model->paramvaluecustomer($param1, $param2);
		return $query; 
	}
	
	//for customer name ///

	public function update_history($data, $inventory_id, $img_clone)
	{
		if($data['custom_fields'])
		{ 
			$custom_inventory =$data['custom_fields'];
			$custom = $custom_inventory['inventory'];
			$inventory = "inventory";
			//$field_types=  $this->inventory_model->custom_inventory()
			while($fri = current($custom))
				{	
					$fieldid  = key($custom);
					$fields = $this->inventory_model->getcustom($inventory_id, $fieldid); 
					$cust = $this->inventory_model->custom_inventory($fieldid);
					
						if (is_array($custom[$fieldid]) && $fields[0]['value'] != implode($custom[$fieldid]))
						{	
							$custome_name = $this->inventory_model->get_name_field($fieldid);
							$fieldschanged = $custome_name[0]['name'].': "'.$fields[0]['value'].'" updated to "' .implode($custom[$fieldid]).'"';
							$values = array(
								'inventory_id' => $inventory_id,
								'user_id'	    => $this->session->userdata('staff_user_id'),
								'note'			=> 'Change of "'.$custome_name[0]['name'].'" In Inventory Number: "'.$inventory_id.'"',
								'action'       => $fieldschanged,
							 );
							 $this->inventory_model->updation_add($values);	
						} 
						else{
							if($fields[0]['value'] != $custom[$fieldid] && $cust[0]['type'] != 'checkbox' && $cust[0]['type'] != 'multiselect')
							{
								$custome_name = $this->inventory_model->get_name_field($fieldid);
								$fieldschanged = $custome_name[0]['name'].': "'.$fields[0]['value'].'" updated to "' .$custom[$fieldid].'"';
								$values = array(
								 'inventory_id' => $inventory_id,
								 'user_id'	    => $this->session->userdata('staff_user_id'),
								 'note'			=> 'Change of "'.$custome_name[0]['name'].'" In Inventory Number: "'.$inventory_id.'"',
								 'action'       => $fieldschanged,
								 );
								 $this->inventory_model->updation_add($values); 
							}
						}
					next($custom);
				}
		}	
		$fields = $this->inventory_model->fields($inventory_id);
		if($fields[0]['account'] != $data['account'])
		 {
			$param1 = $fields[0]['account'];
			$param2 = $data['account'];
			$valu_type = $this->param_valuecustomer($param1 , $param2 );
			$fieldschanged = 'Company "'.$valu_type['query1'][0]['company'].'" updated to "'.$valu_type['query2'][0]['company'].'"';
			 if ($fieldschanged){
			 $values = array(
				 'inventory_id' => $inventory_id,
				 'user_id'	    => $this->session->userdata('staff_user_id'),
				 'note'			=> 'Change of Company:',
				 'action'       => $fieldschanged,
				 );
			 $this->inventory_model->updation_add($values);
			 }
		 }
		 
		 if($fields[0]['serial_number'] != $data['serial_number'])
		 {
			 $fieldschanged = 'Serial '.$fields[0]['serial_number'].' updated to ' .$data['serial_number'];
			 if ($fieldschanged){

			 $values = array(
				 'inventory_id' => $inventory_id,
				 'user_id'	   => $this->session->userdata('staff_user_id'),
				 'note'			=> 'Change of Serial Number:',
				 'action'       => $fieldschanged,
			 );
			 $this->inventory_model->updation_add($values);
			 }
		 }
		 
		 if($fields[0]['type_of_hardware'] != $data['type_of_hardware'])
		 {
			 $param1 = $fields[0]['type_of_hardware'];
			 $param2 = $data['type_of_hardware'];
			 $valu_type = $this->param_value($param1 , $param2 );
			 $fieldschanged = 'Type Of Hardware "'.$valu_type['query1'][0]['value'].'" updated to "'.$valu_type['query2'][0]['value'].'"';
			 if ($fieldschanged){
			 $values = array(
				 'inventory_id' => $inventory_id,
				 'user_id'	   => $this->session->userdata('staff_user_id'),
				 'note'			=> 'Change of Hardware:',
				 'action'       => $fieldschanged,
				 );
			 $this->inventory_model->updation_add($values);
			 }
		 }
		 
		 if($fields[0]['status'] != $data['status'])
		 {
			 $param1 = $fields[0]['status'];
			 $param2 = $data['status'];
			 $valu_type = $this->param_value($param1 , $param2 );
			 $fieldschanged = 'Status "'.$valu_type['query1'][0]['value'].'" updated to "'.$valu_type['query2'][0]['value'].'"' ; 
			 if ($fieldschanged){
				$values = array(
					 'inventory_id' => $inventory_id,
					 'user_id'	   => $this->session->userdata('staff_user_id'),
					 'note'			=> 'Change of Status:',
					 'action'       => $fieldschanged,
				);
			 $this->inventory_model->updation_add($values);

			 }
		 }
		 
		 if($fields[0]['origin'] != $data['origin'])
		 {
			 $param1 = $fields[0]['origin'];
			 $param2 = $data['origin'];
			 $valu_type = $this->param_value($param1 , $param2 );
			 $fieldschanged = 'Origin "'.$valu_type['query1'][0]['value'].'" updated to "'.$valu_type['query2'][0]['value'].'"';
			 if ($fieldschanged){
				 
			 $values = array(
				 'inventory_id' => $inventory_id,
				 'user_id'	   => $this->session->userdata('staff_user_id'),
				 'note'			=> 'Change of Origin:',
				 'action'       => $fieldschanged,
			 );
			 $this->inventory_model->updation_add($values);
			 }
		 }
		 
		 if($fields[0]['equipment_owner'] != $data['equipment_owner'])
		 {
			 $param1 = $fields[0]['equipment_owner'];
			 $param2 = $data['equipment_owner'];
			 $valu_type = $this->param_value($param1 , $param2 );
			 $fieldschanged = 'Owner "'.$valu_type['query1'][0]['value'].'" updated to "'.$valu_type['query2'][0]['value'].'"';
			 if ($fieldschanged){
				 
			 $values = array(
				 'inventory_id' => $inventory_id,
				 'user_id'	   => $this->session->userdata('staff_user_id'),
				 'note'			=> 'Change of Owner:',
				 'action'       => $fieldschanged,
			 );
			 $this->inventory_model->updation_add($values);
			 }
		 }
		 
		 if($fields[0]['manufacturer'] != $data['manufacturer'])
		 {
			 $fieldschanged = 'Manufacturer "'.$fields[0]['manufacturer'].'" updated to "'.$data['manufacturer'].'"';
			 if ($fieldschanged){
				 $values = array(
					 'inventory_id' => $inventory_id,
					 'user_id'	   => $this->session->userdata('staff_user_id'),
					 'note'			=> 'Change of Manufacturer:',
					 'action'       => $fieldschanged,
				 );
			 $this->inventory_model->updation_add($values);
			 }
		 }
		 
		 if($fields[0]['description'] != $data['description'])
		 {
			 $fieldschanged = 'Description "'.$fields[0]['description'].'" updated to "'.$data['description'].'"';
			 if ($fieldschanged){
				 
			 $values = array(
				 'inventory_id' => $inventory_id,
				 'user_id'	   => $this->session->userdata('staff_user_id'),
				 'note'			=> 'Change of Description:',
				 'action'       => $fieldschanged,
				 );
			 $this->inventory_model->updation_add($values);
			 }
		 }
		 
		 // if($fields[0]['warranty_expiration_date'] != $data['warranty_expiration_date'])
		 // {
			 // $fieldschanged = 'Warranty Expiration "'.date('m/d/Y h:i A',strtotime($fields[0]['warranty_expiration_date'])).'" updated to "'.date('m/d/Y h:i A',strtotime($data['warranty_expiration_date'])).'"';
			 // if ($fieldschanged){
			 // $values = array(
				 // 'inventory_id' => $inventory_id,
				 // 'user_id'	   => $this->session->userdata('staff_user_id'),
				 // 'note'			=> 'Change of Warranty Expiration:',
				 // 'action'       => $fieldschanged,
				 // );
			 // $this->inventory_model->updation_add($values);
			// }
		 // }
		 
		 if($fields[0]['exp_date'] != $data['exp_date'])
		 {
			 if ($fields[0]['exp_date'] == 'No Warranty')
			 {
				 $fieldschanged = 'Expiration Date  "No Warranty" updated to "'.date('m/d/Y h:i A',strtotime($data['exp_date'])).'"';
			 }
			 elseif($data['exp_date'] == 'No Warranty')
			 {
				 $fieldschanged = 'Expiration Date "'.date('m/d/Y h:i A',strtotime($fields[0]['exp_date'])).'" updated to  "No Warranty"';
			 }
			 
			else
			{
				$fieldschanged = 'Expiration Date "'.date('m/d/Y h:i A',strtotime($fields[0]['exp_date'])).'" updated to "'.date('m/d/Y h:i A',strtotime($data['exp_date'])).'"';
			}
			if ($fieldschanged){
			 $values = array(
				 'inventory_id' => $inventory_id,
				 'user_id'	   => $this->session->userdata('staff_user_id'),
				 'note'			=> 'Change of Expiry Date:',
				 'action'       => $fieldschanged,
				 );
			 $this->inventory_model->updation_add($values);
			 }
		 }
		 
		 if($img_clone != '')
		 {
			 if($fields[0]['image'] != $data['image'])
			 {
				 $fieldschanged = 'Inventory Image "'.$fields[0]['imageuploadname'].'" updated to "'.$data['imageuploadname'].'"';
				 if ($fieldschanged){
				 $values = array(
					 'inventory_id' => $inventory_id,
					 'user_id'	    => $this->session->userdata('staff_user_id'),
					 'note'			=> 'Change of Inventory Image:',
					 'action'       => $fieldschanged,
				 );
				 
				 $this->inventory_model->updation_add($values);
				 }
			 }
		 }
	}
	public function inventory_history($inventory_id, $user_id, $action)
	{
		$data= array(
			'inventory_id'  => (int)$inventory_id,
			'user_id'   => (int)$user_id,
			'note'  => 'Inventory Created:',
			'action'  => $action,
		);
		$this->inventory_model->inventory_history($data);
	}
	// for customer 
	public function update_costomer_history($data, $inventory_id,  $img_clone)
	{
	//	print_r($data);
		$company_id = $data['account'];	
		if($data['custom_fields'])
		{ 
			$custom_inventory =$data['custom_fields'];
			$custom = $custom_inventory['inventory'];
			$inventory = "inventory";
			//$field_types=  $this->inventory_model->custom_inventory()
			
			while($fri = current($custom))
				{	
					$fieldid  = key($custom);
					$fields = $this->inventory_model->getcustom($inventory_id, $fieldid); 
					$cust = $this->inventory_model->custom_inventory($fieldid);
				
						if (is_array($custom[$fieldid]) && $fields[0]['value'] != implode($custom[$fieldid]))
						{	
							$custome_name = $this->inventory_model->get_name_field($fieldid);
							$fieldschanged = $custome_name[0]['name'].': "'.$fields[0]['value'].'" updated to "' .implode($custom[$fieldid]).'"';
							
							if ($fieldschanged)
								{
								 $history = array(
									 'company_id' => $company_id,
									 'user_id'	    => $this->session->userdata('staff_user_id'),
									 'heading'			=> 'Change of "'.$custome_name[0]['name'].'" In Inventory Number: "'.$inventory_id.'"',
									 'action'       => $fieldschanged,
									 'tabname'		=> 'profile'
									 );
									 $query = $this->clients_model->create_history($history);
																
								}
						}						
						else
						{
							if($fields[0]['value'] != $custom[$fieldid] && $cust[0]['type'] != 'checkbox' && $cust[0]['type'] != 'multiselect')
							{
								$custome_name = $this->inventory_model->get_name_field($fieldid);
								$fieldschanged = $custome_name[0]['name'].': "'.$fields[0]['value'].'" updated to "' .$custom[$fieldid].'"';
								$history = array(
								 'company_id' => $company_id,
								 'user_id'	    => $this->session->userdata('staff_user_id'),
								 'heading'			=> 'Change of "'.$custome_name[0]['name'].'" In Inventory Number: "'.$inventory_id.'"',
								 'action'       => $fieldschanged,
								 'tabname'		=>'profile'
								 );
								 $query = $this->clients_model->create_history($history);

							}
						}
					next($custom);
				}
					
		}
		
		$fields = $this->inventory_model->fields($inventory_id);
		
		if($fields[0]['account'] != $data['account'])
		 {
			$param1 = $fields[0]['account'];
			$param2 = $data['account'];
			$valu_type = $this->param_valuecustomer($param1 , $param2 );
			$fieldschanged = 'Company "'.$valu_type['query1'][0]['company'].'" updated to "'.$valu_type['query2'][0]['company'].'"';
			 if ($fieldschanged){
			 $history = array(
				 'company_id' => $company_id,
				 'user_id'	    => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Company In Inventory Number: "'.$inventory_id.'"',
				 'action'       => $fieldschanged,
				 'tabname'		=> 'profile'
				 );
				 $query = $this->clients_model->create_history($history);
				
			 } 
		 }
		 
		 if($fields[0]['serial_number'] != $data['serial_number'])
		 {
			 $fieldschanged = 'Serial '.$fields[0]['serial_number'].' updated to ' .$data['serial_number'];
			 if ($fieldschanged){
			 $history = array(
				 'company_id' => $company_id,
				 'user_id'	    => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Serial Number In Inventory Number: "'.$inventory_id.'"',
				 'action'       => $fieldschanged,
				 'tabname'		=> 'profile'
				 );
				 $query = $this->clients_model->create_history($history);
				
			 }
		 }
		 
		 if($fields[0]['type_of_hardware'] != $data['type_of_hardware'])
		 {	
			
			  $param1 = $fields[0]['type_of_hardware'];
			  $param2 = $data['type_of_hardware'];
			 
			 $valu_type = $this->param_value($param1 , $param2 );
			 
			 $fieldschanged = 'Type Of Hardware "'.$valu_type['query1'][0]['value'].'" updated to "'.$valu_type['query2'][0]['value'].'"';
			 if ($fieldschanged){
			 $history = array(
				 'company_id' => $company_id,
				 'user_id'	    => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Type of Hardware In Inventory Number: "'.$inventory_id.'"',
				 'action'       => $fieldschanged,
				 'tabname'		=> 'profile'
				 );

				 $query = $this->clients_model->create_history($history);

			 }
		 }
		 
		 if($fields[0]['status'] != $data['status'])
		 {
			 $param1 = $fields[0]['status'];
			 $param2 = $data['status'];
			 $valu_type = $this->param_value($param1 , $param2 );
			 $fieldschanged = 'Status "'.$valu_type['query1'][0]['value'].'" updated to "'.$valu_type['query2'][0]['value'].'"' ; 
			 if ($fieldschanged){
			 $history = array(
				 'company_id' => $company_id,
				 'user_id'	    => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Status In Inventory Number: "'.$inventory_id.'"',
				 'action'       => $fieldschanged,
				 'tabname'		=> 'profile'
				 );
				 			 
				 $query = $this->clients_model->create_history($history);
				
			 }
		 }
		 
		 if($fields[0]['origin'] != $data['origin'])
		 {
			  $param1 = $fields[0]['origin'];
			  $param2 = $data['origin'];
			 $valu_type = $this->param_value($param1 , $param2 );
			 $fieldschanged = 'Origin "'.$valu_type['query1'][0]['value'].'" updated to "'.$valu_type['query2'][0]['value'].'"';
			 if ($fieldschanged){
			 $history = array(
				 'company_id' => $company_id,
				 'user_id'	    => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Origin: In Inventory Number: "'.$inventory_id.'"',
				 'action'       => $fieldschanged,
				 'tabname'		=> 'profile'
				 );

				 $query = $this->clients_model->create_history($history);
								 
				
			 }
		 }
		 
		 if($fields[0]['equipment_owner'] != $data['equipment_owner'])
		 {
			 $param1 = $fields[0]['equipment_owner'];
			 $param2 = $data['equipment_owner'];
			 $valu_type = $this->param_value($param1 , $param2 );
			 $fieldschanged = 'Owner "'.$valu_type['query1'][0]['value'].'" updated to "'.$valu_type['query2'][0]['value'].'"';
			 if ($fieldschanged){
			 $history = array(
				 'company_id' => $company_id,
				 'user_id'	    => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Owner: In Inventory Number: "'.$inventory_id.'"',
				 'action'       => $fieldschanged,
				 'tabname'		=> 'profile'
				 );

				 $query = $this->clients_model->create_history($history);
								
				
			 }
		 }
		 
		 if($fields[0]['manufacturer'] != $data['manufacturer'])
		 {
			 $fieldschanged = 'Manufacturer "'.$fields[0]['manufacturer'].'" updated to "'.$data['manufacturer'].'"';
			 if ($fieldschanged){
			 $history = array(
				 'company_id' => $company_id,
				 'user_id'	    => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Manufacturer In Inventory Number: "'.$inventory_id.'"',
				 'action'       => $fieldschanged,
				 'tabname'		=> 'profile'
				 );
				  
				 $query = $this->clients_model->create_history($history);
				
			 }
		 }
		 
		 if($fields[0]['description'] != $data['description'])
		 {
			 $fieldschanged = 'Description "'.$fields[0]['description'].'" updated to "'.$data['description'].'"';
			 if ($fieldschanged){
			 $history = array(
				 'company_id' => $company_id,
				 'user_id'	    => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Description In Inventory Number: "'.$inventory_id.'"',
				 'action'       => $fieldschanged,
				 'tabname'		=> 'profile'
				 );

				 $query = $this->clients_model->create_history($history);
			
			
			 }
		 }
		  if($fields[0]['exp_date'] != $data['exp_date'])
		 {	
			 if ($fields[0]['exp_date'] == 'No Warranty')
			 {
				 $fieldschanged = 'Expiration Date  "No Warranty" updated to "'.date('m/d/Y h:i A',strtotime($data['exp_date'])).'"';
			 }
			 elseif($data['exp_date'] == 'No Warranty')
			 {
				 $fieldschanged = 'Expiration Date "'.date('m/d/Y h:i A',strtotime($fields[0]['exp_date'])).'" updated to  "No Warranty"';
			 }
			 else
			 {
				$fieldschanged = 'Expiration Date "'.date('m/d/Y h:i A',strtotime($fields[0]['exp_date'])).'" updated to "'.date('m/d/Y h:i A',strtotime($data['exp_date'])).'"';
			 }
			 
			 if ($fieldschanged){
			 $history = array(
				 'company_id' => $company_id,
				 'user_id'	    => $this->session->userdata('staff_user_id'),
				 'heading'			=> 'Change of Expiration Date In Inventory Number: "'.$inventory_id.'"',
				 'action'       => $fieldschanged,
				 'tabname'		=> 'profile'
				 );
				 				
				 $query = $this->clients_model->create_history($history);
				
			 }
		 }
		 
		 if($img_clone != '')
		 {
			 if($fields[0]['image'] != $data['image'])
			 {
				 $fieldschanged = 'Inventory Image "'.$fields[0]['imageuploadname'].'" updated to "'.$data['imageuploadname'].'"';
					if ($fieldschanged){
						 $history = array(
							 'company_id' => $company_id,
							 'user_id'	    => $this->session->userdata('staff_user_id'),
							 'heading'			=> 'Change of Inventory Image In Inventory Number: "'.$inventory_id.'"',
							 'action'       => $fieldschanged,
							 'tabname'		=> 'profile'
							 );
							 $query = $this->clients_model->create_history($history);
							
						 }
			 }
		 }
		
	}
	
	public function inventory_customer_history($inventory_id, $data, $tabname, $action)
	{	$company_id = $data['account'];
		$user_id = $this->session->userdata('staff_user_id');
		$history = array(
						'user_id' 		=>  $user_id,
						'company_id'	=>	$company_id,
						'tabname' 		=>  $tabname,
						'heading'		=> 	'Inventory Number: "'.$inventory_id.' "'. 'Created',
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
	public function init_relation_inventory($rel_id= '', $rel_type ='' )
    {
        // exit('huhuhujkdjfdbfbd');
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('tblinventoryadd'); 
            // $this->app->get_table_data('tasks_relations', array(
            //     'rel_id' => $rel_id,
            //     'rel_type' => $rel_type,
            // ));
        }
    }
//now zee code end
	
}
