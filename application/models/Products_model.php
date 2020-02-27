<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Products_model extends CRM_Model
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
	
	public function get_product_new($id = '')
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
	
	public function get_product_tasks($id = '')
    {
        $this->db->select(' * ');
        $this->db->from('tblproductstasks');
        $this->db->where('tblproductstasks.product_id', $id);
        return $this->db->get()->result_array();
    }

	 public function delete_product($id)
    {
		// echo "in model"; die;
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
	
	public function getTasks()
	{
		$this->db->select('id, name');
		$this->db->where('portfolio_id', $this->session->userdata('portfolio_id'));
        $query = $this->db->get('tblstafftasks');
		return $query->result_array();
	}
    public function getProducts()
    {	
        $this->db->select('id,description, long_description');
		$this->db->where('portfolio_id', $this->session->userdata('portfolio_id'));
        $query = $this->db->get('tblproducts');
        return $query->result_array();
    }
    public function getTasksDetails($itemid)
    {
        $this->db->select('*');
        $this->db->from('tblproductstasks');
        $this->db->join('tbl_tasks t1', 't1.id = tblproductstasks.task_id', 'left');
        if (is_numeric($itemid)) {
            $this->db->where('tblproductstasks.product_id', $itemid);
            return $this->db->get()->result_array();
        }
    }

    /**
     * Add new invoice item
     * @param array $data Invoice item data
     * @return boolean
     */
    
	
	public function add_product($data)
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
		// print_r($data); exit; 	
        $this->db->insert('tblproducts', $data);
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

    /**
     * Update invoiec item
     * @param  array $data Invoice data to update
     * @return boolean
     */

	
	public function edit_product($data)
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
	
	//bilal code start
	
	public function get_product_new_so($id = '')
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

        return $this->db->get()->result_array();
    }
	
	public function get_product_tasks_so($id = '')
    {
        $this->db->select(' * ');
        $this->db->from('tblproductstasksso');
        $this->db->where('tblproductstasksso.product_id', $id);
        return $this->db->get()->result_array();
    }
	
	public function getTasksDetailsSo($itemid)
    {
        $this->db->select('*');
        $this->db->from('tblproductstasksso');
        $this->db->join('tbl_tasks t1', 't1.id = tblproductstasksso.task_id', 'left');
        if (is_numeric($itemid)) {
            $this->db->where('tblproductstasksso.product_id', $itemid);
            return $this->db->get()->result_array();
        }
    }

	//bilal code end


}
