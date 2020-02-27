<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Portfolio extends Admin_controller
{
	public function __construct()
    {
        parent::__construct();
        check_portfolio_id();
		$this->load->model('portfolio_model');
		$this->load->model('roles_model');	
    }	
	 public function index()
    {   
		 if (!has_permission('portfolio', '', 'view')) {
            access_denied('portfolio');
        }
		if ($this->input->is_ajax_request()) {
	        $this->app->get_table_data('portfolio');
	    }
		$data['allportfolios'] = $this->portfolio_model->get_all_data();	
		$data['title'] ="Portfolio";
        $this->load->view('admin/portfolio/manage', $data);
    }
	public function portfolio($id = '')
	{
		if($this->input->post())
		{
			$data = $this->input->post();	
			
			if($data['id'] != 0)
			{
					$id = $this->portfolio_model->add_names($data['portfolio_name'], $data['portfolio_name_id']);
					$right = array(
					'portfolio_name_id'		=> $data['portfolio_name_id'],
					'portfolio_type'		=> $data['portfolio_type'],
					'description'			=> $data['description'],					
					'modules'				=>json_encode($data['modules']),
					'followers'				=> $data['followers'],
					'user_picture'			=> $data['user_picture'],
					'addedby'				=> $this->session->userdata('staff_user_id'),
										
				);
				$data['save_data'] = $this->portfolio_model->add($right, $data['id']);			
				if($data['save_data'] = true)
				{
					set_alert('success', 'Portfolio  Updated');
					redirect('admin/portfolio');			
				}
			}
			else{

				
				$id 	= $this->portfolio_model->add_names($data['portfolio_name']);
				$right 	= array(
					'portfolio_name_id'		=> $id,
					'portfolio_type'		=> $data['portfolio_type'],
					'description'			=> $data['description'],
					'modules'				=> json_encode($data['modules']),
					'followers'				=> $data['followers'],
					'user_picture'			=> $data['user_picture'],
					'addedby'				=> $this->session->userdata('staff_user_id'),
					'createdat'				=> date('Y-m-d H:i:s')
				);				
				// echo "<pre>"; print_r($right);exit('jijijiji');  
				// echo "<pre>"; print_r($data);exit('jijijiji');  
				$data['save_data'] = $this->portfolio_model->add($right);			
				if($data['save_data'])
				{
					set_alert('success', 'Portfolio  Created');
					redirect('admin/portfolio');			
				}
			}
		}
		if($id != '')
		{
			$data['permissions'] 		= $this->roles_model->get_permissions();
			$data['single_portfolio_data'] =$this->portfolio_model->get_data($id);
			
			$check 	=$this->portfolio_model->get_portfolio_type();
			

			$i = 0 ; 
			if($check['id'] != $id )
			{
				foreach($data['permissions'] as $ar)
				{
					if($ar['shortname'] == 'settings')
						{
							unset($data['permissions'][$i]);
						}
					if($ar['shortname'] == 'credit_notes')
						{
							unset($data['permissions'][$i]);
						}
					if($ar['shortname'] == 'goals')
						{
							unset($data['permissions'][$i]);
						}
					if($ar['shortname'] == 'payments')
						{
							unset($data['permissions'][$i]);
						}		
					if($ar['shortname'] == 'reports')
						{
							unset($data['permissions'][$i]);
						}
					if($ar['shortname'] == 'bulk_pdf_exporter')
						{
							unset($data['permissions'][$i]);
						}			
					if($ar['shortname'] == 'expenses')
						{
							unset($data['permissions'][$i]);
						}	
					if($ar['shortname'] == 'surveys')
						{
							unset($data['permissions'][$i]);
						}		
					$i++; 								
				}
			}

		}
		else
		{
			$data['permissions'] 		= $this->roles_model->get_permissions();
			$check 	=$this->portfolio_model->get_portfolio_type();
			// echo "<pre>"; print_r($check); exit;  
			  
			$i = 0 ; 
			if(!empty($check) )
			{
				foreach($data['permissions'] as $ar)
				{
					if($ar['shortname'] == 'settings')
						{
							unset($data['permissions'][$i]);
						}
					if($ar['shortname'] == 'credit_notes')
						{
							unset($data['permissions'][$i]);
						}
					if($ar['shortname'] == 'goals')
						{
							unset($data['permissions'][$i]);
						}
					if($ar['shortname'] == 'payments')
						{
							unset($data['permissions'][$i]);
						}		
					if($ar['shortname'] == 'reports')
						{
							unset($data['permissions'][$i]);
						}
					if($ar['shortname'] == 'bulk_pdf_exporter')
						{
							unset($data['permissions'][$i]);
						}			
					if($ar['shortname'] == 'expenses')
						{
							unset($data['permissions'][$i]);
						}	
					if($ar['shortname'] == 'surveys')
						{
							unset($data['permissions'][$i]);
						}	
					$i++; 								
				}
			}
			// echo "<pre>"; print_r($data['permissions']); exit;
		}
		$data['title'] ="New Portfolio";
		$main = 'main';
		$data['created_portfolios'] = $this->portfolio_model->get_main_portfolio($main);
		
			

        $this->load->view('admin/portfolio/add', $data);	
	}
	public function delete_portfolio($id)
	{
		$del = $this->portfolio_model->delete($id); 
		if($del = 1)
			{
				set_alert('danger', 'Portfolio  Deleted');
				redirect('admin/portfolio');			
			}	
	}
	
	public function add_modules()
	{
		$data['allportfolios'] = $this->portfolio_model->get_all_data();
		$data['title'] ="Add Modules";
        $this->load->view('admin/portfolio/add_modules', $data);	
	}
	
	public function get_portfolio_permission()
	{ 
		$data = $this->input->post();
		$main = $this->portfolio_model->get_data_by_value($data['value'], 'main'); 
		$conditions = get_permission_conditions();
		// echo "<pre>"; print_r($conditions); exit; 
		if($data['id'] && $main != 'yes')
		{	
			//exit('inif'); 
			$id = $data['id'];
			$check = $this->portfolio_model->get_portfolio_permission_for_if($data);		
			//echo "<pre>"; print_r($check); exit;  
			foreach($check as $permission)
			{	
				$permission_condition = $conditions[$permission->shortname];
				$view = '';
				$view_own = '';
				$create = '';
				$edit = '';
				$delete = '';
				
				if($permission_condition['view'] == false)
				{
					$view = 'style = "display:none"'; 
				}
				if($permission_condition['view_own'] == false)
				{
					$view_own = 'style = "display:none"';
				}
				if($permission_condition['create'] == false)
				{
					$create = 'style = "display:none"';
				}
				if($permission_condition['delete'] == false)
				{
					$delete = 'style = "display:none"';
				}
				if($permission_condition['edit'] == false)
				{
					$edit = 'style = "display:none"';
				}				
				if($permission->can_view == 1)
				{
					$can_view = "checked";
				}
				else{
					$can_view = '';
				}
				
				if($permission->can_view_own == 1)
				{
					$can_view_own = "checked";
				}
				else{
					$can_view_own = '';
				}
				
				if($permission->can_edit == 1)
				{
					$can_edit = "checked";
				}
				else{
					$can_edit = '';
				}
				if($permission->can_create == 1)
				{
					$can_create = "checked";
				}
				else{
					$can_create = '';
				}
				if($permission->can_delete == 1)
				{
					$can_delete = "checked";
				}
				else{
					$can_delete = '';
				}
				
			$html .= '<tr data-id="' .  $permission->permissionid . '" data-name="'. $permission->shortname . '" >
						<td>' . $permission->name . '</td>
						<td class="text-center" >
							<div class="checkbox" ' . $view . '>
							   <input type="checkbox" data-can-view name="view[]" value="' . $permission->permissionid  . '" '.$can_view.'>
							   <label></label>
							</div>
						</td>
						<td class="text-center" >
							<div class="checkbox" ' . $view_own . '>
							   <input type="checkbox" data-can-view name="view_own[]" value="' . $permission->permissionid . '"'. $can_view_own .'>
							   <label></label>
							</div>
						</td>
						<td class="text-center" ' . $create . '>
							<div class="checkbox">
							   <input type="checkbox" data-can-view name="create[]" value="' . $permission->permissionid . '"'. $can_create .'>
							   <label></label>
							</div>
						</td>
						<td class="text-center">
							<div class="checkbox" ' . $edit . '>
							   <input type="checkbox" data-can-view name="edit[]" value="' . $permission->permissionid . '" '. $can_edit .'>
							   <label></label>
							</div>
						</td>
						<td class="text-center">
							<div class="checkbox checkbox-danger" ' . $delete . '>
							   <input type="checkbox" data-can-view name="delete[]" value="' . $permission->permissionid . '" '. $can_delete .'>
							   <label></label>
							</div>
						</td>
					</tr>';
			}	
		}
		else
		{
			
			$res = $this->portfolio_model->get_portfolio_permission($data);	
			$main_portfolio = $this->portfolio_model->get_data_by_value($data['value'], 'main');
			$check = $this->portfolio_model->get_id_of_permissions($res);
			//echo "<pre>"; print_r($check); exit;   
			if($main_portfolio == 'yes')
				{
					$administrator = "checked"; 
				}
			else
				{
					$administrator = " ";
				}		
			
			foreach($check as $permission)
			{	
			
				$permission_condition = $conditions[$permission->shortname];
				$view = '';
				$view_own = '';
				$create = '';
				$edit = '';
				$delete = '';
				
				if($permission_condition['view'] == false)
				{
					$view = 'style = "display:none"'; 
				}
				if($permission_condition['view_own'] == false)
				{
					$view_own = 'style = "display:none"';
				}
				if($permission_condition['create'] == false)
				{
					$create = 'style = "display:none"';
				}
				if($permission_condition['delete'] == false)
				{
					$delete = 'style = "display:none"';
				}
				if($permission_condition['edit'] == false)
				{
					$edit = 'style = "display:none"';
				}	
		
			
			$html .= '<tr data-id="' .  $permission->permissionid . '" data-name="'. $permission->shortname . '">
						<td>' . $permission->name . '</td>
						<td class="text-center">
							<div class="checkbox"  ' . $view . '>
							   <input type="checkbox" data-can-view name="view[]" value="' . $permission->permissionid  . '"' . $administrator . '>
							   <label></label>
							</div>
						</td>
						<td class="text-center">
							<div class="checkbox"  ' . $view_own . '>
							   <input type="checkbox" data-can-view name="view_own[]" value="' . $permission->permissionid . '"' . $administrator . '>
							   <label></label>
							</div>
						</td>
						<td class="text-center">
							<div class="checkbox"  ' . $create . '>
							   <input type="checkbox" data-can-view name="create[]" value="' . $permission->permissionid . '"' . $administrator . '>
							   <label></label>
							</div>
						</td>
						<td class="text-center">
							<div class="checkbox"  ' . $edit . '>
							   <input type="checkbox" data-can-view name="edit[]" value="' . $permission->permissionid . '"' . $administrator . '>
							   <label></label>
							</div>
						</td>
						<td class="text-center">
							<div class="checkbox checkbox-danger" ' . $delete . '>
							   <input type="checkbox" data-can-view name="delete[]" value="' . $permission->permissionid . '" ' . $administrator . '>
							   <label></label>
							</div>
						</td>
					</tr>';
					
		
			}
		}		
		echo $html;
		exit; 
		//return $check;

	}
	
}
?>