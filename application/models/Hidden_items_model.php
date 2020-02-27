<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Hidden_items_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Get hidden items
     */
    public function get($id = '', $where = array())
    {
        $this->db->select('tblhidden_items.*, tblportfolio_names.names, tblcountries.short_name, tblstaff.firstname,tblstaff.lastname');
        $this->db->from('tblhidden_items');        
        $this->db->join('tblportfolio_names' , 'tblportfolio_names.name_id = tblhidden_items.portfolio_id', 'LEFT');
        $this->db->join('tblcountries' , 'tblcountries.country_id = tblhidden_items.country', 'LEFT');
        $this->db->join('tblstaff' , 'tblstaff.staffid = tblhidden_items.deleted_by', 'LEFT');
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('hide_item_id', $id);
            $estimate = $this->db->get()->row();
            return $estimate;
        }
        $this->db->order_by('userid', 'desc');

        return $this->db->get()->result_array();
    }

/*restoration of company*/

    public function restore($id)
    {
        $this->db->select('*');
        $this->db->from('tblhidden_items');
        $this->db->where('hide_item_id', $id);
        $result = $this->db->get()->row();
        
        unset($result->hide_item_id);
        unset($result->deleted_by);
        unset($result->hidden_created_date);
        $insert = $this->db->insert('tblclients', $result); 
        if($insert){
            $this->db->where('hide_item_id', $id);
            $del = $this->db->delete('tblhidden_items');
            if($del){
                return true;    
            }
        } 
        else{
            return false;
            // exit('not');
        }
    }
    public function delete($del_id = '', $id)
    {
         $this->db->where('hide_item_id', $del_id);
            $del = $this->db->delete('tblhidden_items');
            if($del)
            {  
                // Delete all tickets start here

                $this->db->where('userid', $id);
                $tickets = $this->db->get('tbltickets')->result_array();
                $this->load->model('tickets_model');
                foreach ($tickets as $ticket) {
                    $this->tickets_model->delete($ticket['ticketid']);
                }

                $this->db->where('rel_id', $id);
                $this->db->where('rel_type', 'customer');
                $this->db->delete('tblnotes');

                // Delete all user contacts
                $this->db->where('userid', $id);
                $contacts = $this->db->get('tblcontacts')->result_array();

                foreach ($contacts as $contact) {
                    $this->delete_contact($contact['id'], $id);
                }

                // Get all client contracts
                $this->load->model('contracts_model');
                $this->db->where('client', $id);
                $contracts = $this->db->get('tblcontracts')->result_array();
                foreach ($contracts as $contract) {
                    $this->contracts_model->delete($contract['id']);
                }
                // Delete the custom field values
                $this->db->where('relid', $id);
                $this->db->where('fieldto', 'customers');
                $this->db->delete('tblcustomfieldsvalues');

                // Get customer related tasks
                $this->db->where('rel_type', 'customer');
                $this->db->where('rel_id', $id);
                $tasks = $this->db->get('tblstafftasks')->result_array();

                foreach ($tasks as $task) {
                    $this->tasks_model->delete_task($task['id']);
                }
                $this->db->where('rel_type', 'customer');
                $this->db->where('rel_id', $id);
                $this->db->delete('tblreminders');

                $this->db->where('customer_id', $id);
                $this->db->delete('tblcustomeradmins');

                $this->db->where('customer_id', $id);
                $this->db->delete('tblvault');

                $this->db->where('customer_id', $id);
                $this->db->delete('tblcustomergroups_in');

                // Delete all projects
                $this->load->model('projects_model');
                $this->db->where('clientid', $id);
                $projects = $this->db->get('tblprojects')->result_array();
                foreach ($projects as $project) {
                    $this->projects_model->delete($project['id']);
                }

                $this->load->model('proposals_model');
                $this->db->where('rel_id', $id);
                $this->db->where('rel_type', 'customer');
                $proposals = $this->db->get('tblproposals')->result_array();
                foreach ($proposals as $proposal) {
                    $this->proposals_model->delete($proposal['id']);
                }
                
                $this->db->where('rel_id', $id);
                $this->db->where('rel_type', 'customer');
                $attachments = $this->db->get('tblfiles')->result_array();
                
                foreach ($attachments as $attachment) {
                    $this->delete_attachment($attachment['id']);
                }

                $this->db->where('clientid', $id);
                $expenses = $this->db->get('tblexpenses')->result_array();

                $this->load->model('expenses_model');
                foreach ($expenses as $expense) {
                    $this->expenses_model->delete($expense['id']);
                }

                return true;    
            }
            else{
             return false;       
            }
    }
       public function delete_attachment($id)
    {
        $this->db->where('id', $id);
        $attachment = $this->db->get('tblfiles')->row();
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                $relPath = get_upload_path_by_type('customer') . $attachment->rel_id . '/';
                $fullPath =$relPath.$attachment->file_name;
                unlink($fullPath);
                $fname = pathinfo($fullPath, PATHINFO_FILENAME);
                $fext = pathinfo($fullPath, PATHINFO_EXTENSION);
                $thumbPath = $relPath.$fname.'_thumb.'.$fext;
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

            $this->db->where('id', $id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                $this->db->where('file_id', $id);
                $this->db->delete('tblcustomerfiles_shares');
                logActivity('Customer Attachment Deleted [CustomerID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('customer') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('customer') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    delete_dir(get_upload_path_by_type('customer') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }
     public function delete_contact($id, $customer_id )
    {
        // if (!has_permission('customers', '', 'delete')) {
        //     if (!is_customer_admin($customer_id)) {
        //         access_denied('customers');
        //     }
        // }
        // echo "<pre>"; print_r($customer_id); echo $id; exit; 
        $this->clients_model->delete_contact($id);
        $this->clients_model->delete_contacts_rel_clients($customer_id, $id);
        return true;
        // redirect(admin_url('clients/client/' . $customer_id . '?tab=contacts'));
    }
}
