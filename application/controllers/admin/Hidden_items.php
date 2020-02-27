<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Hidden_items extends Admin_controller
{
	public function __construct()
    {
    	parent::__construct();
		check_portfolio_id();
		$this->load->model('hidden_items_model');
		
    }
    public function hidden()
    {
    	$hidden = has_permission_hidden($_SESSION['staff_user_id']); 
        if($hidden['hidden_items'] ==0){
              access_denied('Hidden Items'); 
           	};
    	
	    if ($this->input->is_ajax_request()) {
	        $this->app->get_table_data('hidden_items');
	    }
	    $data['clients']	= $this->hidden_items_model->get();
	    $data['title'] = _l('hidden_items');
    	$this->load->view('admin/hidden_items/manage', $data);   	
    }

 //    public function table(){      
	// 	$this->app->get_table_data('hidden_items');    
	// }

	public function restore($id = ''){      
		$res = $this->hidden_items_model->restore($id);
		if($res == true){
			set_alert('success', 'Customer Restored');
			redirect('admin/hidden_items/hidden');
		}
		else{
			set_alert('danger', 'Customer not Restored');
			redirect('admin/hidden_items/hidden');	
		}
	}
	public function delete($id, $userid= ''){      
		$res = $this->hidden_items_model->delete($id, $userid);
		if($res == true){
			// exit('in');
			set_alert('success', 'Customer Deleted');
			redirect('admin/hidden_items/hidden');
		}
		else{
			// exit('out');
			set_alert('danger', 'Customer not Deleted');
			redirect('admin/hidden_items/hidden');	
		}
	}
	public function hidden_items_popup(){

		// exit('nko');
		// print_r($_POST['id']); //exit; 
		$data = $this->hidden_items_model->get($_POST['id']);
		// echo "<pre>"; print_r($data); exit;
		$primary = getPrimary($data->userid); 
		// echo "<pre>"; print_r($primary); exit;
		$html.='<div> Company : ' . $data->company . '</div><div> Portfolio : ' . $data->names . '</div><div> Primary Contact : ' . $primary->firstname . ' ' . $primary->lastname . '</div><div> Primary Email : ' . $primary->email . '</div><div> Country : ' . $data->short_name . '</div><div> Address : ' . $data->address . '</div><div> Website : ' . $data->website . '</div><div> Created Date : ' . date('m-d-Y  h:i A', strtotime($data->datecreated)) . '</div><div> Deleted Date : ' . date('m-d-Y  h:i A', strtotime($data->hidden_created_date)) . '</div><div> Who Deleted Customer : ' . $data->firstname .' ' . $data->lastname . '</div>';
		
		echo $html;
		exit;
		// exit; 

	}


}