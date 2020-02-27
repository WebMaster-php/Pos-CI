<?php
// echo "index"; die;
error_reporting(1);
defined('BASEPATH') or exit('No direct script access allowed');
class Inventory_items extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('date');
		$this->load->model('Inventory_items_model');
    }
    public function get_notes()
    {	
        extract($_POST);
        $notes = $this->Inventory_items_model->data($inventory_id);
        $notes_result = '';
        if(count($notes) > 0)
        {
            foreach($notes as $not){					
				$notes_result .='
								<tr>
									<td>
										<div style="border-bottom: 1px solid #9c9c9c; padding: 0px 5px;">
											<div class= "datastyle">' .$not['firstname'].' '.$not['lastname'].' - '.date('m-d-Y h:i A',strtotime($not['date_in'])).'</div> 
											<div>' .$not['notes'].'</div>		
										</div>
									</td>
								</tr>';		
            }
           echo $notes_result;           
        }
    }
	
    public function notes($id)
    {
		if (!has_permission('inventory_items', '', 'view')) {
            access_denied('Inventory Items');
        } 
        $data['inventory'] = $this->inventory_model->geet($id);
        $data['values'] = $this->inventory_model->get('tblcustom', $objArr=TRUE);		
        $data['accounts'] = $this->inventory_model->get('tblclients', $objArr=TRUE);
        $data['lastupdate'] = $this->inventory_model->historyGet($id);
        $this->load->view('admin/inventory_items/view', $data);
    }
    public function add_notes()
    {
        $data =array(
			'date_in'			=>	date_format(date_create($this->input->post('datetime')),"Y-m-d H:i:s"),
			'inventory_id'		=>	$this->input->post('inventory_id'),
			'user_id'		    =>	$this->session->userdata('staff_user_id'),
			'notes'				=> 	$this->input->post('notes')
        );
        $query = $this->Inventory_items_model->add($data);
        if ($query)
		{
			$this->session->set_flashdata('inventory_items', 'Note added successfuly ');
        }
		else 
		{
			echo "no";
        }
    }
        public function inventory_history()
		{
			$inventory_id = $this->input->post('inventory_id');
			$query = $this->Inventory_items_model->inventory_get($inventory_id);

			if ($query)
			{
				$result = '';
				$result .= '<thead><th>Date</th><th>User</th><th>Action</th></thead>';

				foreach($query as $q)
				{
					$result .='<tr>
									<td colspan="3">
										<b>'.$q['note'].'</b>
									</td>
								</tr>
								<tr><td>'.date('m/d/Y',strtotime($q['date_in'])).'</td><td>'.$q['firstname'].' '.$q['lastname'].'</td><td>'.$q['action'].'</td></tr>';
				}
				echo $result;
			}
			else
			{
				$result   = '';
				$result	 .= '<thead><th>Date</th><th>User</th><th>Action</th></thead>';
				$massage  = "No Inventory History";
				$result .='<tr>
								<td colspan="3"> 
									<center>
										<b>'. $massage.'</b>
									</center>
								</td>
							</tr>';
				echo $result;
			}

		}
	  //new change zee
}