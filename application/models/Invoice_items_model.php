<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Invoice_items_model extends CRM_Model
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
    public function get($id = '')
    {
        $columns = $this->db->list_fields('tblitems');
        $rateCurrencyColumns = '';
        foreach($columns as $column){
            if(strpos($column,'rate_currency_') !== FALSE){
                $rateCurrencyColumns .= $column.',';
            }
        }
        $this->db->select($rateCurrencyColumns.'tblitems.id as itemid,rate,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            t2.taxrate as taxrate_2,t2.id as taxid_2,t2.name as taxname_2,
            description,long_description,group_id,tblitems_groups.name as group_name,unit');
        $this->db->from('tblitems');
        $this->db->join('tbltaxes t1', 't1.id = tblitems.tax', 'left');
        $this->db->join('tbltaxes t2', 't2.id = tblitems.tax2', 'left');
        $this->db->join('tblitems_groups', 'tblitems_groups.id = tblitems.group_id', 'left');
        $this->db->order_by('description', 'asc');
        if (is_numeric($id)) {
            $this->db->where('tblitems.id', $id);

            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
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
		$this->db->where('portfilio_id', $this->session->userdata('portfilio_id'));
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
			$this->db->where('tblitems.portfolio_id', $this->session->userdata('portfolio_id'));
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
     * Add new invoice item
     * @param array $data Invoice item data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['itemid']);
        if ($data['tax'] == '') {
            unset($data['tax']);
        }

        if (isset($data['tax2']) && $data['tax2'] == '') {
            unset($data['tax2']);
        }

        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
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
		
        $this->db->insert('tblitems', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields, true);
            }
            logActivity('New Invoice Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');

            return $insert_id;
        }

        return false;
    }
	
	
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
        $this->db->where('id', $id);
        $this->db->delete('tblitems');
        if ($this->db->affected_rows() > 0) {

            $this->db->where('relid',$id);
            $this->db->where('fieldto','items_pr');
            $this->db->delete('tblcustomfieldsvalues');

            logActivity('Invoice Item Deleted [ID: ' . $id . ']');

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
		$this->db->where('portfolio_id', $this->session->userdata('portfolio_id'));	
        return $this->db->get('tblitems_groups')->result_array();
    }

    public function add_group($data)
    {
        $this->db->insert('tblitems_groups', $data);
        logActivity('Items Group Created [Name: ' . $data['name'] . ']');

        return $this->db->insert_id();
    }

    public function edit_group($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblitems_groups', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Items Group Updated [Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function delete_group($id)
    {
        $this->db->where('id', $id);
        $group = $this->db->get('tblitems_groups')->row();

        if ($group) {
            $this->db->where('group_id', $id);
            $this->db->update('tblitems', array(
                'group_id' => 0
            ));

            $this->db->where('id', $id);
            $this->db->delete('tblitems_groups');

            logActivity('Item Group Deleted [Name: ' . $group->name . ']');

            return true;
        }

        return false;
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
	
	public function get_product_so($id = '')
    {
        $columns = $this->db->list_fields('tblproductsso');
        $rateCurrencyColumns = '';
        foreach($columns as $column){
            if(strpos($column,'rate_currency_') !== FALSE){
                $rateCurrencyColumns .= $column.',';
            }
        }
        $this->db->select('*');
        $this->db->from('tblproductsso');
        $this->db->order_by('description', 'asc');
        if (is_numeric($id)) {
            $this->db->where('tblproductsso.id', $id);

            return $this->db->get()->row();
        }
		$this->db->where('portfolio_id', $this->session->userdata('portfolio_id'));
        return $this->db->get()->result_array();
    }

	public function edit_product_so($data)
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

        $affectedRows = 0;
        $this->db->where('id', $itemid);
        $this->db->update('tblproductsso', $data);
		
		if ($this->db->affected_rows() > 0) {
            logActivity('Product [ID: ' . $itemid . ', ' . $data['description'] . ']');
            $affectedRows++;
			$this->db->where('product_id', $itemid);
			$this->db->delete('tblproductstasksso');
        }
		$this->delete_product_tasks_so($itemid);
		foreach($taskspassed as $task)
		{
			if($task)
			{
				$this->db->insert('tblproductstasksso', array('product_id'=>$itemid, 'task_id'=>$task));
			}
		}

        if(isset($custom_fields)) {
            if(handle_custom_fields_post($itemid, $custom_fields, true)) {
                $affectedRows++;
            }
        }
        return $affectedRows > 0 ? true : false;
    }
	
	public function delete_product_tasks_so($id)
    {
        $this->db->where('product_id', $id);
        $this->db->delete('tblproductstasksso');
        if ($this->db->affected_rows() > 0)
        {
            $this->db->where('product_id',$id);
            $this->db->delete('tblproductstasksso');
            logActivity('Sales Opportunity Product Task Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }
	
	//bilal code end	
	
	
}
