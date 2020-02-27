<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Inventory_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get invoice item by ID
     * @param  mixed $id
     * @return mixed - array if not passed id, object if id passed
     */

    
    /// abu bakar saddique///
    
    public function historyGet($inventory_id){		
        $this->db->select('ia.*, ts.firstname, ts.lastname');		
        $this->db->from('tblinventoryhistory AS ia');                               // I use aliasing make joins easier		
        $this->db->join('tblstaff AS ts', 'ts.staffid = ia.user_id', 'INNER');		
        $this->db->where('ia.inventory_id', $inventory_id);
        $this->db->order_by('ia.history_id','desc');
        $this->db->limit(1);
        $data = $this->db->get()->result_array();
        return $data;
    }
    
   public function edit_group($table, $data, $id)
    {
       error_reporting(E_ALL);
       $this->db->trans_start();
	$this->db->where('id',$id);
        $this->db->update($table, $data);
        $this->db->trans_complete();
        
        // echo $this->db->last_query();
        //
		
        //if($this->db->affected_rows() > 0) {
        if($this->db->trans_status() > 0){  
            logActivity('Inventory Group Updated [Name: ' . $data['value'] . ']');
            return true;
        }else{
             return false;
        }
    }

    public function delete_group($id)
    {
        $this->db->where('id', $id);
        $group = $this->db->get('tblcustom')->row();

		if($group->group_type == 'hardware')
		{
			$field = 'type_of_hardware';
		}

		if($group->group_type == 'owner')
		{	
			$field = 'equipment_owner';
		}

		if($group->group_type == 'origin')
		{			
			$field = 'origin';
		}

		if($group->group_type == 'status')
		{
			$field = 'status';
		}
        if ($group){
            $this->db->where($field, $id);
            $this->db->update('tblinventoryadd', array($field => 0));
            $this->db->where('id', $id);
            $this->db->delete('tblcustom');
            logActivity('Inventory Group Deleted [Name: ' . $group->value . ']');
            return true;
        }
        else{
        return false;
        }
    }
    
    //abo baker saddique////

	 function get($table, $objArr=FALSE, $where=false, $fields='*', $order=false)    
	{  
		$this->db->select($fields)->from($table);  
		if($where)
		{            	
			$this->db->where($where);       
		}   
		if($order)		
		{
			foreach ($order as $key => $value) 			
			{                
				$this->db->order_by($key,$value);            
			}       
		}
		$query = $this->db->get();   
		
		//if($group_by)     
		if($query->num_rows() > 0)
		{			
			if($objArr)
			{				
				return $query->result();			
			}			
			else
			{				
				return $query->result_array();			
			}	
		}        
		return FALSE;    
	}
	
	
	//abo baker saddique////

	 function getContacts($table, $objArr=FALSE, $where=false, $fields='*', $order=false, $join='')    
	{  
		$this->db->select($fields)->from($table);        
		if($join == 1)
		{
			$this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = '.$table.'.id', 'LEFT');
		}
		if($where)
		{            	
			$this->db->where($where);       
		}   
		if($order)		
		{
			foreach ($order as $key => $value) 			
			{                
				$this->db->order_by($key,$value);            
			}       
		}
		$query = $this->db->get();   
		     
		//echo $this->db->last_query();die;        
		if($query->num_rows() > 0)
		{			
			if($objArr)
			{				
				return $query->result();			
			}			
			else
			{				
				return $query->result_array();			
			}	
		}        
		return FALSE;    
	}
	
	
	//abo baker saddique////

	 function getprofileContacts($table, $objArr=FALSE, $where=false, $fields='*', $order=false, $join='')    
	{  
		$this->db->select($fields)->from($table);        
		if($join == 1)
		{
			$this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = '.$table.'.id', 'LEFT');
		}
		if($where)
		{            	
			$this->db->where($where);       
		}   
		if($order)		
		{
			foreach ($order as $key => $value) 			
			{                
				$this->db->order_by($key,$value);            
			}       
		}
		$query = $this->db->get();   
		           
		if($query->num_rows() > 0)
		{			
			if($objArr)
			{				
				return $query->result();			
			}			
			else
			{				
				return $query->result_array();			
			}	
		}        
		return FALSE;    
	}


	
	public function get_product($id = '')
    {
        $columns = $this->db->list_fields('tblproducts');
        $rateCurrencyColumns = '';
        foreach($columns as $column){
            if(strpos($column,'rate_currency_') !== FALSE){
                $rateCurrencyColumns .= $column.',';
            }
        }
        $this->db->select('*');
        $this->db->from('tblproducts');
        $this->db->order_by('description', 'asc');
        if (is_numeric($id)) {
            $this->db->where('tblproducts.id', $id);

            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }

    public function get_grouped()
    {
        $items = array();
        $this->db->order_by('name', 'asc');
        $groups = $this->db->get('tblitems_groups')->result_array();

        array_unshift($groups, array(
            'id' => 0,
            'name' => ''
        ));

        foreach ($groups as $group) {
            $this->db->select('*,tblitems_groups.name as group_name,tblitems.id as id');
            $this->db->where('group_id', $group['id']);
            $this->db->join('tblitems_groups', 'tblitems_groups.id = tblitems.group_id', 'left');
            $this->db->order_by('description', 'asc');
            $_items = $this->db->get('tblitems')->result_array();
            if (count($_items) > 0) {
                $items[$group['id']] = array();
                foreach ($_items as $i) {
                    array_push($items[$group['id']], $i);
                }
            }
        }

        return $items;
    }

    /**
     * Add new inventory
     * @param array $data Inventory data
     * @return boolean
     */
    //umer farooq chattha///
   
   public function add($data)
    {	
		if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $this->db->insert('tblinventoryadd', $data); //echo $this->db->last_query();exit;
		$insert_id = $this->db->insert_id();
		if (isset($custom_fields)) {
			handle_custom_fields_post($insert_id, $custom_fields);
		}
        if($insert_id > 0){
            logActivity('Inventory Added [Serail Number: ' . $data['serial_number'] . ']');
            return $insert_id;
        }else 
            return false;
    }
    
    public function update($data,$where)
    {	
		if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $this->db->where("inventory_id",$where);
        if($this->db->update('tblinventoryadd', $data))
        {
			if (isset($custom_fields)) {
				handle_custom_fields_post($where, $custom_fields);
			}
            echo 'yes';
            return true;
        }
        else{ 
            return false;
        }
	
    }
    
    function geet($where)    
    { 
        $this->db->select('ia.inventory_id, ia.account, ia.serial_number, ia.type_of_hardware, ia.date_in, ia.status, ia.origin, ia.equipment_owner, ia.manufacturer, ia.exp_date, ia.image, ia.description, ia.user_id, ia.datecreated, cs1.value as statusvalue, cs2.value as originvalue, cs3.value as hardwarevalue , cs4.value as ownervalue, ts.firstname as firstname, ts.lastname as lastname, cl.company');
        $this->db->from('tblinventoryadd AS ia');// I use aliasing make joins easier
        $this->db->join('tblcustom AS cs1', 'cs1.id = ia.status', 'LEFT');
        $this->db->join('tblcustom AS cs2', 'cs2.id = ia.origin', 'LEFT');
        $this->db->join('tblcustom AS cs3', 'cs3.id = ia.type_of_hardware', 'LEFT');
        $this->db->join('tblcustom AS cs4', 'cs4.id = ia.equipment_owner', 'LEFT');
		$this->db->join('tblstaff AS ts', 'ts.staffid  = ia.user_id', 'INNER');
        $this->db->join('tblclients AS cl', 'cl.userid = ia.account', 'INNER');
        $this->db->where('ia.inventory_id', $where);
        return $this->db->get()->result_array();
    }
    
    //umer farooq chattha
	
	public function add_product($data)
    {
		$taskspassed = $data['tasks'];
        unset($data['itemid']);
        unset($data['tasks']);
        unset($data['task']);
		 unset($data['start_date']);


        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $columns = $this->db->list_fields('tblproducts');
        $this->load->dbforge();

        foreach($data as $column => $itemData){
            if(!in_array($column,$columns) && strpos($column,'rate_currency_') !== FALSE){
                $field = array(
                        $column => array(
                            'type' =>'decimal(15,'.get_decimal_places().')',
                            'null'=>true,
                        )
                );
                $this->dbforge->add_column('tblproducts', $field);
            }
        }
		
        $this->db->insert('tblproducts', $data);
        $insert_id = $this->db->insert_id();
		

        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields, true);
            }
            logActivity('Ne Product Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');

			foreach($taskspassed as $task)
			{
				if($task)
				{
					$this->db->insert('tblproductstasks', array('product_id'=>$insert_id, 'task_id'=>$task));
				}
			}
			
            return $insert_id;
        }

        return false;
    }

    /**
     * Update invoiec item
     * @param  array $data Invoice data to update
     * @return boolean
     */
    public function edit($data)
    {
        $itemid = $data['itemid'];
        unset($data['itemid']);

        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        if (isset($data['tax']) && $data['tax'] == '') {
            $data['tax'] = NULL;
        }

        if (isset($data['tax2']) && $data['tax2'] == '') {
             $data['tax2'] = NULL;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $columns = $this->db->list_fields('tblitems');
        $this->load->dbforge();

        foreach($data as $column => $itemData){
            if(!in_array($column,$columns) && strpos($column,'rate_currency_') !== FALSE){
                $field = array(
                        $column => array(
                            'type' =>'decimal(15,'.get_decimal_places().')',
                            'null'=>true,
                        )
                );
                $this->dbforge->add_column('tblitems', $field);
            }
        }

        $affectedRows = 0;
        $this->db->where('id', $itemid);
        $this->db->update('tblitems', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Invoice Item Updated [ID: ' . $itemid . ', ' . $data['description'] . ']');
            $affectedRows++;
        }

        if(isset($custom_fields)) {
            if(handle_custom_fields_post($itemid, $custom_fields, true)) {
                $affectedRows++;
            }
        }
        return $affectedRows > 0 ? true : false;
    }
	
    public function edit_product($data)
    {
		$taskspassed = $data['tasks'];
		unset($data['task']);
		unset($data['tasks']);
        $itemid = $data['itemid'];
        unset($data['itemid']);

        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        if (isset($data['tax']) && $data['tax'] == '') {
            $data['tax'] = NULL;
        }

        if (isset($data['tax2']) && $data['tax2'] == '') {
             $data['tax2'] = NULL;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $columns = $this->db->list_fields('tblproducts');
        $this->load->dbforge();

        foreach($data as $column => $itemData){
            if(!in_array($column,$columns) && strpos($column,'rate_currency_') !== FALSE){
                $field = array(
                        $column => array(
                            'type' =>'decimal(15,'.get_decimal_places().')',
                            'null'=>true,
                        )
                );
                $this->dbforge->add_column('tblproducts', $field);
            }
        }

        $affectedRows = 0;
        $this->db->where('id', $itemid);
        $this->db->update('tblproducts', $data);
		
		if ($this->db->affected_rows() > 0) {
            logActivity('Product [ID: ' . $itemid . ', ' . $data['description'] . ']');
            $affectedRows++;
			$this->db->where('product_id', $itemid);
			$this->db->delete('tblproductstasks');
        }
		$this->delete_product_tasks($itemid);
		foreach($taskspassed as $task)
		{
			if($task)
			{
				$this->db->insert('tblproductstasks', array('product_id'=>$itemid, 'task_id'=>$task));
			}
		}

        if(isset($custom_fields)) {
            if(handle_custom_fields_post($itemid, $custom_fields, true)) {
                $affectedRows++;
            }
        }
        return $affectedRows > 0 ? true : false;
    }
	
    public function search($q){

        $this->db->select('rate, id, description as name, long_description as subtext');
        $this->db->like('description',$q);
        $this->db->or_like('long_description',$q);

        $items = $this->db->get('tblitems')->result_array();

        foreach($items as $key=>$item){
            $items[$key]['subtext'] = strip_tags(mb_substr($item['subtext'],0,200)).'...';
            $items[$key]['name'] = '('._format_number($item['rate']).') ' . $item['name'];
        }

        return $items;
    }

    /**
     * Delete invoice item
     * @param  mixed $id
     * @return boolean
     */
    public function delete($id)
    {
		$this->db->where('inventory_id', $id);
        $this->db->delete('tblinventoryadd');
		
        if ($this->db->affected_rows() > 0) {
            $this->db->where('relid',$id);
            $this->db->where('fieldto','items_pr');
            $this->db->delete('tblcustomfieldsvalues');
            logActivity('Inventory Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }
    /**
     * Delete Product
     * @param  mixed $id
     * @return boolean
     */
    public function delete_product($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblproducts');
        if ($this->db->affected_rows() > 0)
		{
            $this->db->where('product_id',$id);
            $this->db->delete('tblproductstasks');
            logActivity('Invoice Item Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }
    public function delete_product_tasks($id)
    {
        $this->db->where('product_id', $id);
        $this->db->delete('tblproductstasks');
        if ($this->db->affected_rows() > 0)
        {
            $this->db->where('product_id',$id);
            $this->db->delete('tblproductstasks');
            logActivity('Product Task Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    public function get_groups()
    {
        $this->db->order_by('name', 'asc');

        return $this->db->get('tblitems_groups')->result_array();
    }


    
	
	//bilal code start
	public function add_product_so($data)
    {
		$taskspassed = $data['tasks'];
        unset($data['itemid']);
        unset($data['tasks']);
        unset($data['task']);
		unset($data['start_date']);


        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $columns = $this->db->list_fields('tblproductsso');
        $this->load->dbforge();

        foreach($data as $column => $itemData){
            if(!in_array($column,$columns) && strpos($column,'rate_currency_') !== FALSE){
                $field = array(
                        $column => array(
                            'type' =>'decimal(15,'.get_decimal_places().')',
                            'null'=>true,
                        )
                );
                $this->dbforge->add_column('tblproductsso', $field);
            }
        }
		
        $this->db->insert('tblproductsso', $data);
        $insert_id = $this->db->insert_id();
		

        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields, true);
            }
            logActivity('New Sale Opportunity Product Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');

			foreach($taskspassed as $task)
			{
				if($task)
				{
					$this->db->insert('tblproductstasksso', array('product_id'=>$insert_id, 'task_id'=>$task));
				}
			}
			
            return $insert_id;
        }

        return false;
    }
	
	public function inventoryGroup($data)
	{
		$this->db->insert('tblcustom', $data);
		logActivity('New inventory group value added');
		return $this->db->insert_id(); 
	}
        
        // now change ze s
	public function inventory_history($data){
		$query = $this->db->insert('tblinventoryhistory', $data);
		if($query > 0){
			return true;
		}
		else {
			return false;
			}
	}
	public function fields($inventory_id){
		
        $this->db->select('*');
        $this->db->from('tblinventoryadd');
		$this->db->where('inventory_id', $inventory_id);
		$fields = $this->db->get()->result_array();
		if($fields){
			return $fields;
		}
		else {
			return false;
			}
	}
	
	public function updation_add($values){
		//print_r($values); exit;							
		$query = $this->db->insert('tblinventoryhistory', $values);
		if($query > 0){
			//echo "yes"; exit;
			return true;
		}
		else {
			//echo "no"; exit;
			return false;
			}
	}
	
		public function paramvalue($param1, $param2){

		$this->db->select('value');
		$this->db->from('tblcustom');
		$this->db->where('id', $param1);
		$query1= $this->db->get()->result_array();
		
		$this->db->select('value');
		$this->db->from('tblcustom');
		$this->db->where('id', $param2);
		$query2= $this->db->get()->result_array();
		$data['query1']= $query1;
		$data['query2']= $query2;
		
		return $data;
	}
	public function paramvaluecustomer($param1, $param2){
		// for customer
		$this->db->select('company');
		$this->db->from('tblclients');
		$this->db->where('userid', $param1);
		$query1= $this->db->get()->result_array();
		
		$this->db->select('company');
		$this->db->from('tblclients');
		$this->db->where('userid', $param2);
		$query2= $this->db->get()->result_array();
		$data['query1']= $query1;
		$data['query2']= $query2;
		
		return $data;
	}
	//18 /07
	public function custom_inventory($fieldto)
	{
		$this->db->select("*");
		$this->db->from("tblcustomfields");
		$this->db->where("id", $fieldto);
		return $this->db->get()->result_array();
		
	}
	public function getcustom($inventory_id, $fieldid)
	{
		$this->db->select("*");
		$this->db->from('tblcustomfieldsvalues');
		$this->db->where('relid', $inventory_id);
		$this->db->where('fieldid', $fieldid);
		$query = $this->db->get()->result_array();
		return $query;
	}
	
	public function get_name_field($id)
	{
		$this->db->select("name");
		$this->db->from('tblcustomfields');
		$this->db->where('id', $id);
		$query = $this->db->get()->result_array();
		return $query;
	}
	
	public function get_inventory($id)
	{	
		$this->db->select("ti.*, tch.value as hardwareval, tcs.value as statusval, tcc.company as companyval");
		$this->db->from('tblinventoryadd as ti');
		$this->db->join('tblcustom as tch','ti.type_of_hardware = tch.id','LEFT');
		$this->db->join('tblcustom as tcs','ti.status = tcs.id','LEFT');
		$this->db->join('tblclients as tcc','ti.account = tcc.userid','LEFT');
		$this->db->where('account', $id);
		$this->db->order_by('ti.date_in','desc');
		$query = $this->db->get()->result_array();
		return $query;
	}

	public function get_notes($id, $check)
	{	 	
		$this->db->select("tn.*, ts.firstname, ts.lastname");
		$this->db->from('tblnotes as tn');
	//	$this->db->join('tblcontacts as cont', 'tn.rel_id = cont.userid');
		$this->db->join('tblstaff as ts', 'ts.staffid = tn.addedfrom', 'LEFT');
		$this->db->where('tn.rel_id', $id);
		$this->db->where('tn.viewcheck', $check);
		$this->db->order_by('tn.dateadded','desc');
		$query = $this->db->get()->result_array();
	

		// $this->db->select("tn.*, cont.firstname as fname, cont.lastname as lname, ts.firstname, ts.lastname");
		// $this->db->from('tblnotes as tn');
		// $this->db->join('tblcontacts as cont', 'tn.rel_id = cont.userid');
		// $this->db->join('tblstaff as ts', 'ts.staffid = tn.addedfrom');
		// $this->db->where('tn.rel_id', $id);
		// $this->db->where('tn.viewcheck', $check);
		// $this->db->order_by('tn.dateadded','desc');
		// $query = $this->db->get()->result_array();
		return $query;
	}

	public function get_announcement($user_id)
	{	  
		$this->db->select("*");
		$this->db->from('tblannouncements');
		//$this->db->join('announcement_status', 'tblannouncements.announcementid = announcement_status.announcement_id','left');
		$this->db->where('showtousers', 1);
		
		$this->db->order_by('dateadded','desc');
		$query = $this->db->get()->result_array();

		return $query;
	}
	public function get_company($id)
	{
		$this->db->select("tcl.*, cont.company_id");
		$this->db->from('tblclients as tcl');
		$this->db->join('tblcontacts_rel_clients as cont', 'tcl.userid = cont.company_id');
		$this->db->where('cont.contact_id', $id);
		$query = $this->db->get()->result_array();
		// here 9 aug ed	
		return $query;
	}
	public function company_details($data)
	{	
		$company_id = $data['company_id'];
		if (isset($data['company_id'])) {
            unset($data['company_id']);
        }
		if (isset($data['update'])) {
            unset($data['update']);
        }
		$this->db->where('userid', $company_id);
        $this->db->update('tblclients', $data);
        
		if ($this->db->affected_rows() > 0) {
		
		   return true;            
        }
		
	}

	//now change zee e
	//now change zee e

	
}
