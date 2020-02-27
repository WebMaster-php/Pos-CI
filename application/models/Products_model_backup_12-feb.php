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
        $this->db->select($rateCurrencyColumns.'tblproducts.id as itemid,rate,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            t2.taxrate as taxrate_2,t2.id as taxid_2,t2.name as taxname_2,
            description,long_description,group_id,tblitems_groups.name as group_name,unit');
        $this->db->from('tblproducts');
        $this->db->join('tbltaxes t1', 't1.id = tblproducts.tax', 'left');
        $this->db->join('tbltaxes t2', 't2.id = tblproducts.tax2', 'left');
        $this->db->join('tblitems_groups', 'tblitems_groups.id = tblproducts.group_id', 'left');
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
        $this->db->select($rateCurrencyColumns.'tblproducts.id as itemid,rate,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            t2.taxrate as taxrate_2,t2.id as taxid_2,t2.name as taxname_2,
            description,long_description,group_id,tblitems_groups.name as group_name,unit');
        $this->db->from('tblproducts');
        $this->db->join('tbltaxes t1', 't1.id = tblproducts.tax', 'left');
        $this->db->join('tbltaxes t2', 't2.id = tblproducts.tax2', 'left');
        $this->db->join('tblitems_groups', 'tblitems_groups.id = tblproducts.group_id', 'left');
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

	public function getTasks()
	{
		$this->db->select('id, task_name');
		$query = $this->db->get('tbl_tasks');
		return $query->result_array();
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


}
