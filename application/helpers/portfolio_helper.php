<?php
defined('BASEPATH') or exit('No direct script access allowed');

function get_portfolio_data_member($id)
{
	$CI = &get_instance();
	$CI->load->model('Staff_model'); 
	$port = $CI->Staff_model->get_portfolio($id);
	return $port;	
}

function get_all_portfolio()
{
	$CI = &get_instance();
	$CI->load->model('Staff_model');
	$port = $CI->Staff_model->get_all_portfolio();
	return $port;
}
function check_portfolio_id()
{
	$CI = &get_instance();	
	$CI->load->model('Staff_model');
	$id = $CI->session->userdata('portfolio_id');
	if($id =='' ) 
	{
		redirect(admin_url(), 'refresh');
	}
	else
	{
		$staff_user_id = $CI->session->userdata('staff_user_id');
		
		$port = $CI->Staff_model->get_portfolio_check($staff_user_id);
		$right = '';
		foreach($port as $p)
		{
			 
			if($p == $id)
			{
				$right = 'true'; 	
			}
		
		}
		if($right != 'true')
		{
			redirect(admin_url(), 'refresh');
		}
		
	}

}
function contacts_by_portfolio($id= "")
{
	$CI =& get_instance();
	$CI->db->select('portfolio_id');
	$CI->db->from('tblcontacts');
	if($id != '')
		{
			$CI->db->where('tblcontacts.id' , $id);			
		}
	else
		{
			$CI->db->where('tblcontacts.id' , $_SESSION['contact_user_id']);
		}	
	
	$sa = $CI->db->get()->result_array();
	return $sa[0]['portfolio_id'];
}
function main_portfolio($id)
{
	$CI =& get_instance();
	$CI->db->select('portfolio_name_id');
	$CI->db->from('tblportfolio_info');
	$CI->db->where('portfolio_name_id' ,$id);
	$CI->db->where('portfolio_type' ,'main');				
	$sa = $CI->db->get()->result_array();
	return $sa[0]['portfolio_name_id'];
}

?>