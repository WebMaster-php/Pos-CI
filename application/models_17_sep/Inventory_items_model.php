<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Inventory_items_model extends CRM_Model{

	//new change zee s
	public function data($inventory_id)
	{
		$this->db->select('ia.*, ts.firstname, ts.lastname');		
		$this->db->from('tblinventorynotes AS ia');                               // I use aliasing make joins easier		
		$this->db->join('tblstaff AS ts', 'ts.staffid = ia.user_id', 'INNER');		
		$this->db->where('ia.inventory_id', $inventory_id);
		$this->db->order_by('id','DESC');
		$data = $this->db->get()->result_array();
		return $data ;
	}
	// new change zee e
	
	
	public function add($data){
		$this->db->insert('tblinventorynotes', $data);
		if($this->db->insert_id()>0){
			return true;
		}
		else{
			return FALSE;
		}		
	}
	
        // new change zee s
	 public function inventory_get($inventory_id){		  
		$this->db->select('ia.*, ts.firstname, ts.lastname');		
		$this->db->from('tblinventoryhistory AS ia');                               // I use aliasing make joins easier		
		$this->db->join('tblstaff AS ts', 'ts.staffid = ia.user_id', 'LEFT');		
		$this->db->where('ia.inventory_id', $inventory_id);
		$this->db->order_by('history_id','DESC');
		$data = $this->db->get()->result_array();	

		if($data)
		{
			return $data;
		}
		else
		{
			return FALSE;
		}
	  }
        // new change zee	  e
	
}
?>