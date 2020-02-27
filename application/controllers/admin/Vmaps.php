<?php
// echo "index"; die;
error_reporting(1);
defined('BASEPATH') or exit('No direct script access allowed');
class Vmaps extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Vmaps_model', 'vm');
    }

    public function index()
    {

        //close_setup_menu();
        //$data['statuses'] = $this->sales_model->get_project_statuses();
        $data['lcs'] = $this->vm->get_leads_and_clients_address();
        $data['title']    = _l('VMAP | Track Clients and Leads');
        $this->load->view('admin/vmaps/vmaps_view', $data);
    }
}
