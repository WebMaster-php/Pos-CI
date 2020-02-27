<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Client_services_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new customer group
     * @param array $data $_POST data
     */
    public function add($data)
    {
        $this->db->insert('tblcustomersservices', $data);
        $insert_id = $this->db->insert_id(); 
        if ($insert_id) {
            logActivity('New Customer Service Created [ID:' . $insert_id . ', Name:' . $data['name'] . ']');
            return $insert_id;
        }
        return false;
     }

    /**
    * Get customer groups where customer belongs
    * @param  mixed $id customer id
    * @return array
    */
    // public function get_customer_groups($id)
    // {
    //     $this->db->where('customer_id', $id);

    //     return $this->db->get('tblcustomergroups_in')->result_array();
    // }

    /**
     * Get all customer groups
     * @param  string $id
     * @return mixed
     */
    public function get_services($id = '')
    {
		// $this->db->where('portfolio_id', $this->session->userdata('portfolio_id'));
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get('tblcustomersservices')->row();
        }
        $this->db->order_by('id', 'asc');

        return $this->db->get('tblcustomersservices')->result_array();
    }
    public function get_services_selected($id = '')
    {
        // $this->db->where('portfolio_id', $this->session->userdata('portfolio_id'));
        if (is_numeric($id)) {
            $this->db->where('customer_id', $id);

            return $this->db->get('tblcustomerservices_in')->result_array();
        }
        $this->db->order_by('id', 'asc');
        $this->db->get('tblcustomerservices_in')->result_array();
        // echo $this->db->last_query(); exit; 
        return $this->db->get('tblcustomerservices_in')->result_array();
    }

    /**
     * Edit customer group
     * @param  array $data $_POST data
     * @return boolean
     */
    public function edit($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('tblcustomersservices', array(
            'name' => $data['name'],
        ));
        if ($this->db->affected_rows() > 0) {
            logActivity('Customer Services Updated [ID:' . $data['id'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete customer group
     * @param  mixed $id group id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblcustomersservices');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('groupid', $id);
            $this->db->delete('tblcustomergroups_in');
            logActivity('Customer Services Deleted [ID:' . $id . ']');

            return true;
        }

        return false;
    }

    /**
    * Update/sync customer groups where belongs
    * @param  mixed $id        customer id
    * @param  mixed $groups_in
    * @return boolean
    */
    public function sync_customer_services($id, $services_in)
    {
        if ($services_in == false) {
            unset($services_in);
        }
        $affectedRows    = 0;
        $customer_services = $this->get_services_selected($id);
        if (sizeof($customer_services) > 0) {
            foreach ($customer_services as $customer_service) {
                if (isset($services_in)) {
                    if (!in_array($customer_service['serviceid'], $services_in)) {
                        $this->db->where('customer_id', $id);
                        $this->db->where('id', $customer_service['id']);
                        $this->db->delete('tblcustomerservices_in');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                } else {
                    $this->db->where('customer_id', $id);
                    $this->db->delete('tblcustomerservices_in');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if (isset($services_in)) {
                foreach ($services_in as $service) {
                    $this->db->where('customer_id', $id);
                    $this->db->where('serviceid', $service);
                    $_exists = $this->db->get('tblcustomerservices_in')->row();
                    if (!$_exists) {
                        if (empty($service)) {
                            continue;
                        }
                        $this->db->insert('tblcustomerservices_in', array(
                            'customer_id' => $id,
                            'serviceid' => $service,
                        ));
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            }
        } else {
            if (isset($services_in)) {
                foreach ($services_in as $service) {
                    if (empty($service)) {
                        continue;
                    }
                    $this->db->insert('tblcustomerservices_in', array(
                        'customer_id' => $id,
                        'serviceid' => $service,
                    ));
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }

        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }
}
