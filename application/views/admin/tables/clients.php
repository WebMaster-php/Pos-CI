<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('customers', '', 'delete');

$custom_fields = get_table_custom_fields('customers');

$aColumns = array(
    '1',
    'tblclients.userid as userid',
    'company',
    '(SELECT names FROM tblportfolio_names WHERE name_id = tblclients.portfolio_id) as portfolio',
    '(SELECT names FROM tblportfolio_names WHERE name_id = tblclients.portfolio_id) as portfolio',

   // 'CONCAT(firstname, " ", lastname) as contact_fullname',
   // 'email',
    'tblclients.phonenumber as phonenumber',
    'tblclients.active',
    '(SELECT GROUP_CONCAT(name ORDER BY name ASC) FROM tblcustomersgroups LEFT JOIN tblcustomergroups_in ON tblcustomergroups_in.groupid = tblcustomersgroups.id WHERE customer_id = tblclients.userid) as groups'
    );

$sIndexColumn = "userid";
$sTable       = 'tblclients';
$CI =& get_instance();


$where   = array();
// Add blank where all filter can be stored
$filter  = array();

$join = array(
	// 'LEFT JOIN tblcustomerservices_in ON tblcustomerservices_in.customer_id = tblclients.userid',
	// 'LEFT JOIN tblcustomersservices ON tblcustomersservices.id = tblcustomerservices_in.serviceid' ,
);

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $key . ' ON tblclients.userid = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}
// Filter by custom groups
$groups  = $this->ci->clients_model->get_groups();
$groupIds = array();
foreach ($groups as $group) {
    if ($this->ci->input->post('customer_group_' . $group['id'])) {
        array_push($groupIds, $group['id']);
    }
}
if (count($groupIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT customer_id FROM tblcustomergroups_in WHERE groupid IN (' . implode(', ', $groupIds) . '))');
}

$this->ci->load->model('invoices_model');
// Filter by invoices
$invoiceStatusIds = array();
foreach ($this->ci->invoices_model->get_statuses() as $status) {
    if ($this->ci->input->post('invoices_' . $status)) {
        array_push($invoiceStatusIds, $status);
    }
}
if (count($invoiceStatusIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT clientid FROM tblinvoices WHERE status IN (' . implode(', ', $invoiceStatusIds) . '))');
}

// Filter by estimates
$estimateStatusIds = array();
$this->ci->load->model('estimates_model');
foreach ($this->ci->estimates_model->get_statuses() as $status) {
    if ($this->ci->input->post('estimates_' . $status)) {
        array_push($estimateStatusIds, $status);
    }
}
if (count($estimateStatusIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT clientid FROM tblestimates WHERE status IN (' . implode(', ', $estimateStatusIds) . '))');
}

// Filter by projects
$projectStatusIds = array();
$this->ci->load->model('projects_model');
foreach ($this->ci->projects_model->get_project_statuses() as $status) {
    if ($this->ci->input->post('projects_' . $status['id'])) {
        array_push($projectStatusIds, $status['id']);
    }
}
if (count($projectStatusIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT clientid FROM tblprojects WHERE status IN (' . implode(', ', $projectStatusIds) . '))');
}

// Filter by proposals
$proposalStatusIds = array();
$this->ci->load->model('proposals_model');
foreach ($this->ci->proposals_model->get_statuses() as $status) {
    if ($this->ci->input->post('proposals_' . $status)) {
        array_push($proposalStatusIds, $status);
    }
}
if (count($proposalStatusIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT rel_id FROM tblproposals WHERE status IN (' . implode(', ', $proposalStatusIds) . ') AND rel_type="customer")');
}

// Filter by having contracts by type
$this->ci->load->model('contracts_model');
$contractTypesIds = array();
$contract_types  = $this->ci->contracts_model->get_contract_types();

foreach ($contract_types as $type) {
    if ($this->ci->input->post('contract_type_' . $type['id'])) {
        array_push($contractTypesIds, $type['id']);
    }
}
if (count($contractTypesIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT client FROM tblcontracts WHERE contract_type IN (' . implode(', ', $contractTypesIds) . '))');
}

// Filter by proposals
$customAdminIds = array();
foreach ($this->ci->clients_model->get_customers_admin_unique_ids() as $cadmin) {
    if ($this->ci->input->post('responsible_admin_' . $cadmin['staff_id'])) {
        array_push($customAdminIds, $cadmin['staff_id']);
    }
}

if (count($customAdminIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id IN (' . implode(', ', $customAdminIds) . '))');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if (!has_permission('customers', '', 'view')) {
    array_push($where, 'AND tblclients.userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id=' . get_staff_user_id() . ')');
}

if($this->ci->input->post('exclude_inactive')){
    array_push($where,'AND tblclients.active=1');
}

if($this->ci->input->post('my_customers')){
    array_push($where,'AND tblclients.userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id='.get_staff_user_id().')');
}

$aColumns = do_action('customers_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}
//echo 
//if portfolio is selected
if(!null == $CI->session->userdata('portfolio_id')){
  $check = main_portfolio($CI->session->userdata('portfolio_id'));  
    if(empty($check))
    {
        // array_push($where,'AND tblclients.portfolio_id='.$CI->session->userdata('portfolio_id'));
        array_push($where,'AND tblclients.portfolio_id='. $CI->session->userdata('portfolio_id') );
    }
}

    // array_push($where, ' AND (SELECT tblcustomersservices.name FROM tblcustomersservices LEFT JOIN tblcustomerservices_in ON tblcustomerservices_in.serviceid = tblcustomersservices.id WHERE tblcustomerservices_in.customer_id = tblclients.userid AND tblcustomersservices.name != "none") ');

// array_push($where,'AND (SELECT tblcustomersservices.name FROM  tblcustomersservices where ='.$CI->session->userdata('portfolio_id'));	

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    //'tblcontacts.id as contact_id',
    'tblclients.zip as zip'
));

$output  = $result['output'];
$rResult = $result['rResult'];
// print_r($rResult);
// exit;
foreach ($rResult as $aRow) {

    if(!empty($check)){
        $services_none  = getServicesNone('none', $aRow['userid']);
       if(!empty($services_none))
        {
            continue ;
        }
    }
    
    $row = array();

    // Bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['userid'] . '"><label></label></div>';
    // User id
    $row[] = $aRow['userid'];

    // Company
    $company = $aRow['company'];

    if ($company == '') {
        $company = _l('no_company_view_profile');
    }

    // portfolio colom added // sajid
       

        //
    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $company . '</a>';

    $row[] = $aRow['portfolio'];
    // $row[] = $aRow['portfolio'];
    
    $services = getServices($aRow['userid']);
    
    // echo "<pre>"; print_r($services); exit; 
    
    $serve = '';
    $val = 1;
        foreach($services as $service){
            if($service['name'] != ''){
                if($val ==2|| $val== 4|| $val ==6 ||$val == 8){
                $serve .= $service['name'] .'</br>'. ',' ;
                }
                else{
                 $serve .= $service['name'] . ',' ;   
                }
            }
    $val++;    
        }
    $row[] = rtrim($serve,", "); 
	
    $primary = getPrimary($aRow['userid']);

	if($primary){
    // Primary contact
		$row[] = ($primary->contactsid ? '<a href="' . admin_url('clients/client/' . $aRow['userid'] . '?contactid=' . $primary->contactsid) . '" target="_blank">' . $primary->firstname .' '. $primary->lastname . '</a>' : '');
	}else{
		$row[] = '';
	}
	if($primary){
    // Primary contact email
		$row[] = ($primary->email ? '<a href="mailto:' . $primary->email . '">' . $primary->email . '</a>' : '');
	}else{
		$row[] = '';
	}
	if($primary){
    // Primary contact phone
		$row[] = ($primary->phonenumber ? '<a href="tel:' . $primary->phonenumber . '">' . $primary->phonenumber . '</a>' : '');
	}else{
			$row[] = '';
		}
    // Toggle active/inactive customer
    $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('customer_active_inactive_help') . '">
        <input type="checkbox" data-switch-url="' . admin_url().'clients/change_client_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['userid'] . '" data-id="' . $aRow['userid'] . '" ' . ($aRow['tblclients.active'] == 1 ? 'checked' : '') . '>
        <label class="onoffswitch-label" for="' . $aRow['userid'] . '"></label>
    </div>';

    // For exporting
    $toggleActive .= '<span class="hide">' . ($aRow['tblclients.active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

    $row[] = $toggleActive;

    // Customer groups parsing
    $groupsRow  = '';
    if ($aRow['groups']) {
        $groups = explode(',', $aRow['groups']);
        foreach ($groups as $group) {
            $groupsRow .= '<span class="label label-default mleft5 inline-block customer-group-list pointer">' . $group . '</span>';
        }
    }

    $row[] = $groupsRow;

    // Custom fields add values
    foreach($customFieldsColumns as $customFieldColumn){
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $hook = do_action('customers_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook['output'];

    // Table options
    $options = icon_btn('clients/client/' . $aRow['userid'], 'pencil-square-o');

    // Show button delete if permission for delete exists
    if ($hasPermissionDelete) {
        $options .= icon_btn('clients/delete/' . $aRow['userid'], 'remove', 'btn-danger _delete');
    }

    $row[] = $options;
    $output['aaData'][] = $row;
	// echo "kokjojiojkmklml"; 
	// print_r($this->session->all_userdata());
	// echo "<pre>"; 
	// echo $this->session->userdata('portfolio_id');
	// $CI =& get_instance();
	// print_r($row); 
	// print_r($CI->session->userdata('portfolio_id'));
	// exit; 
	
	
}
