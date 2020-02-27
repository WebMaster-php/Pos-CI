<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Portfolio_model extends CRM_Model
{
	public function get_all_data()
	{
		$this->db->select('tblportfolio_info.* , tblportfolio_names.names');
		$this->db->from('tblportfolio_info');
		$this->db->join('tblportfolio_names' , 'tblportfolio_names.name_id = tblportfolio_info.portfolio_name_id', 'LEFT'); 		
		return $this->db->get()->result_array();
	}
	public function get_portfolio_data($id)
	{
	//	echo $id; 
		$this->db->select('tblportfolio.portfolio_name');
		$this->db->from('tblportfolio');
		$this->db->where('tblportfolio.addedfor', $id);
		$res = $this->db->get()->row();		
		$re = json_decode($res->portfolio_name);
		
		
		$result = array();
		foreach($re as $r)
		{
			$this->db->select('tblportfolio_info.* , tblportfolio_names.*');
			$this->db->from('tblportfolio_info');
			$this->db->join('tblportfolio_names' , 'tblportfolio_names.name_id = tblportfolio_info.portfolio_name_id'); 
			$this->db->where('tblportfolio_info.portfolio_name_id', $r);
			$result[] = $this->db->get()->row();
		} 
		return $result;
	}
	
	public function get_data($id)
	{	
		$this->db->select('tblportfolio_info.* , tblportfolio_names.*');
		$this->db->from('tblportfolio_info');
		$this->db->join('tblportfolio_names' , 'tblportfolio_names.name_id = tblportfolio_info.portfolio_name_id'); 
		$this->db->where('tblportfolio_info.portfolio_name_id', $id);
		return $this->db->get()->result_array();
	}
	public function add($data, $id= '')
	{ 

		if($id != 0)
		{
			
			$this->db->where('id', $id);
			$update = $this->db->update('tblportfolio_info', $data);
			
			if($this->db->affected_rows() > 0) {
				return  true;
			}
			else
			{
				return false; 
			}
		}

		$insert = $this->db->insert('tblportfolio_info', $data);
		// echo $this->db->last_query(); 
		// echo $insert; echo "kokoko"; echo "<pre>"; print_r($data);exit('out');  		
		if($insert) {
			 
			// echo $insert; echo "kokoko"; echo "<pre>"; print_r($data);exit('jijijiji');  
			$msg = 'Portfolio Created'; 
			return  $msg;
		} 

		return false;
	}
	public function add_names($name, $id = '' )
	{
		if($id !='' )
		{	
			$data = array('names'=> $name);
			$this->db->where('name_id', $id);
			$update = $this->db->update('tblportfolio_names', $data);
			if($this->db->affected_rows() > 0) 
			{
				return  true;
			}
			else
			{
				return false; 
			}
		}		
		$data = array(
			'names'=> $name, 
			'createdat'=> date('Y-m-d H:i:s')
			);
		$insert = $this->db->insert('tblportfolio_names', $data);		
		$da = $this->db->insert_id();
		return $da; 
	}
	public function delete($id)
	{	
	//$this->db->select('portfolio_name'); 
	//$this->db->form('tblportfolio');
	//$this->db->where("FIND_IN_SET($id, REPLACE( REPLACE("portfolio_name", '[', ''), ']','') )" ); 	
	//$v = $this->db->get()->result_array(); 
	
	//echo ""; print_r($v); exit('kkk'); 
		
		   $this->db->delete('tblportfolio_names', array('name_id' => $id)); 
		   $this->db->delete('tblportfolio_info', array('portfolio_name_id'=> $id));
		if($this->db->affected_rows() > 0) 
			{
				return  true;
			}
			else
			{
				return false; 
			}
	
	}
	public function get_portfolio_permission($data)
	{	 
		foreach($data['value'] as $d)
		{		
			// $sa = explode('=', $d);
			// $val = $sa[1];	
			$this->db->select('*');
			$this->db->from('tblportfolio_info');
			$this->db->where('portfolio_name_id', $d);
			$ac = $this->db->get()->result_array();
			
			$arr[]= $ac[0];		
		}
		$arra = array();
		foreach($arr as $r)
		{
			$arra[]= json_decode($r['modules']);			
		} 
		return $arra; 
	}

	public function get_portfolio_permission_for_if($data)
	{	 
		// print_r($data); exit; 
		
		foreach($data['value'] as $d)
		{		
			// $sa = explode('=', $d);
			// $val = $sa[1];	
			$this->db->select('*');
			$this->db->from('tblportfolio_info');
			$this->db->where('portfolio_name_id', $d);
			$ac = $this->db->get()->result_array();
			
			$arr[]= $ac[0];		
		} 
		$arra = array();
		foreach($arr as $r)
		{
			$arra[]= json_decode($r['modules']);	
		}
		
		$array = array();
		$arrac = json_decode(json_encode($arra), True);		
		
		if($arrac[1])
		{
			$arrab = array();	
			$i = 0;
			$j = 1;
			foreach($arrac as $key)
			{
				if($i = 0 ){
					$i++;
					$res = $key;
					$arrab = array_merge($res, $key);
				}else{
					$arrab = array_merge($arrab, $key);
				}
			
			}
		}
		else
		{
			$arrab = $arrac[0];	
		} 
		while ($fruit_name = current($arrab)) 
		{
			
			$a =  key($arrab);
			$this->db->select('tblstaffpermissions.*, tblpermissions.*');
			$this->db->from('tblstaffpermissions');
			$this->db->join('tblpermissions', 'tblpermissions.permissionid = tblstaffpermissions.permissionid');
			$this->db->where('shortname', $a);
			$af = $this->db->get()->row(); 
			$array[] = $af;
			if($af ='')
			{	
				foreach($arr as $d)
				{	 
					$this->db->where('shortname', $d);
					$array[]  = $this->db->get('tblpermissions')->row();
				}
			}
			next($arrab);
		}	
		return $array; 
	}

	
	public function get_id_of_permissions($data)
	{
		$array = json_decode(json_encode($data), true);				
		
		
		if($array[1])
		{
			$sa = array();	
			$i = 0;
			$j = 1;
			foreach($array as $key)
			{
				if($i = 0 ){
					$i++;
					$res = $key;
					$sa = array_merge($res, $key);
				}else{
					$sa = array_merge($sa, $key);
				}
			
			} 
			$arr = array();
			while ($fruit_name = current($sa)) 
			{
				$arr[] = key($sa);
				next($sa);
			}
			$allids = array();	
			foreach($arr as $d)
			{	 
				$this->db->where('shortname', $d);
				$allids[]  = $this->db->get('tblpermissions')->row();
			}
			return $allids;
			
		}
		else
		{	
			$arr = array();
			while ($fruit_name = current($array[0])) 
			{
				$arr[] = key($array[0]);
				next($array[0]);
			}
			$allids = array();	
			foreach($arr as $d)
			{	 
				$this->db->where('shortname', $d);
				$allids[]  = $this->db->get('tblpermissions')->row();
			} 
			return $allids;
		}
	}
	public function get_data_by_id($id)
	{	
		$this->db->select('portfolio_name');
		$this->db->from('tblportfolio');
		$this->db->where('addedfor', $id);
		$res = $this->db->get()->result_array();
		
		$result = json_decode($res[0]['portfolio_name']);
		foreach($result as $r)
		{
			$this->db->select('portfolio_type');
			$this->db->from('tblportfolio_info');
			$this->db->where('id', $r);
			$res = $this->db->get()->row();
			if($res->portfolio_type == 'main')
			{
				$alldata = 'yes';
				return $alldata;				
			}
		}
		return $alldata= 'no';
	}
	public function get_main_portfolio($main)
	{  
		$this->db->select('*');
		$this->db->from('tblportfolio_info');
		$this->db->where('portfolio_type', $main);
		$res = $this->db->get()->result_array();	
		return $res[0]; 
	}
	
	public function get_data_by_value($value, $main)
	{	
		$alldata = '';
		foreach($value as $r)
		{
			$this->db->select('portfolio_type');
			$this->db->from('tblportfolio_info');
			$this->db->where('id', $r);
			$this->db->where('portfolio_type', $main);
			$res = $this->db->get()->row();
			if($res)
			{
				$alldata = 'yes';
				return $alldata;				
			}
		}
		
		return $alldata= 'no';
	
	
		$this->db->select('portfolio_name');
		$this->db->from('tblportfolio');
		$this->db->where('addedfor', $id);
		$res = $this->db->get()->result_array();
	
	}
	public function get_portfolio_type()
	{
		$this->db->select('*');
		$this->db->from('tblportfolio_info');
		$this->db->where('portfolio_type', 'main');
		$val = $this->db->get()->result_array();
		return  $val[0]; 	
	}

}
?>