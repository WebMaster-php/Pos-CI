<?php
$CI =& get_instance();
	
	$queryClientId = "SELECT * FROM tblleads WHERE id = $userid";
	$client = $CI->db->query($queryClientId)->row();
	
	$clientId = $client->client_id;
	$userId = $userid;
	$leadType = $client->type;
	
	
	$contactsQuery = "SELECT tblleadcontacts.id, userid ,is_primary_, first_name, last_name, lead_email, tblleadcontacts.title, lead_phonenumber, active, last_login, cs.company_id, t.type, t.client_id FROM tblleadcontacts LEFT JOIN tblleads as t on tblleadcontacts.userid = t.id LEFT JOIN tbl_lead_contacts_rel_clients AS cs ON cs.contact_id = tblleadcontacts.id WHERE cs.company_id = $userId  UNION SELECT tblcontacts.id, tblcontacts.userid, tblcontacts.is_primary_, tblcontacts.firstname, tblcontacts.lastname, tblcontacts.email, tblcontacts.title, tblcontacts.phonenumber, tblcontacts.active, tblcontacts.last_login, cs1.company_id, t1.type, t1.client_id FROM tblcontacts LEFT JOIN tblleads as t1 on tblcontacts.userid = t1.client_id LEFT JOIN tblcontacts_rel_clients AS cs1 ON cs1.contact_id = tblcontacts.id WHERE cs1.company_id = $clientId and t1.type = 0";
	//$contactsQuery = "SELECT t.type, t.client_id, cl.*, cc.*, c1.company_id, c2.company_id FROM tblleads as t LEFT JOIN tblleadcontacts as cl on cl.userid = t.id LEFT JOIN tblcontacts as cc on cc.userid = t.client_id LEFT JOIN tbl_lead_contacts_rel_clients as c1 on c1.contact_id = cl.id LEFT JOIN tblcontacts_rel_clients as c2 on c2.contact_id = cc.id where c1.company_id = $userId OR c2.company_id = $clientId"
	$contactResults = $CI->db->query($contactsQuery)->result_array();
	$total = count($contactResults);

// echo "<pre>"; print_r($userid);exit('i m here'); 
defined('BASEPATH') or exit('No direct script access allowed');
$total_client_contacts = total_rows('tblleadcontacts', array('userid'=>$client_id));
// echo "<pre>"; print_r($total_client_contacts);exit('i m here'); 
$aColumns = array(
    'first_name',
    'last_name',
    'lead_email',
    'title',
    'lead_phonenumber',
    'active',
    'last_login',
	'cs1.company_id',	
);

$sIndexColumn = "id";
$sTable = 'tblleadcontacts';
$join = array(
	'LEFT JOIN tbl_lead_contacts_rel_clients AS cs1 ON cs1.contact_id = tblleadcontacts.id',

);

$custom_fields = get_table_custom_fields('contacts');

// foreach ($custom_fields as $key => $field) {
//     $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
//     array_push($customFieldsColumns, $selectAs);
//     array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
//     array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_'.$key . ' ON tblleadcontacts.id = ctable_'.$key . '.relid AND ctable_'.$key . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$key . '.fieldid='.$field['id']);
// }
$where = array();
//$where = array('WHERE );
// $where = array('AND cs1.company_id ='.$client_id);

// Fix for big queries. Some hosting have max_join_limit
if ($userid !='') {
    array_push($where,'WHERE company_id = '. $userid);
    // @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}
// print_r($where); exit('hell'); 
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblleadcontacts.id as id', 'userid', 'is_primary'));

$output = $result['output'];
$rResult = $result['rResult'];

//foreach ($rResult as $aRow) {
	foreach ($contactResults as $aRow) {
    $row = array();

    $row[] = '<img src="'.contact_profile_image_url($aRow['id']).'" class="client-profile-image-small mright5"><a href="#" onclick="lead_contact('.$aRow['userid'].','.$aRow['id'].');return false;">'.$aRow['first_name'].'</a>';

    $row[] = $aRow['last_name'];

    $row[] = '<a href="mailto:'.$aRow['lead_email'].'">'.$aRow['lead_email'].'</a>';

    $row[] = $aRow['title'];

    $row[] = '<a href="tel:'.$aRow['lead_phonenumber'].'">'.$aRow['lead_phonenumber'].'</a>';

    $outputActive = '<div class="onoffswitch">
                <input type="checkbox" data-switch-url="'.admin_url().'leads/change_contact_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_'.$aRow['id'].'" data-id="'.$aRow['id'].'"' . ($aRow['active'] == 1 ? ' checked': '') . '>
                <label class="onoffswitch-label" for="c_'.$aRow['id'].'"></label>
            </div>';
            // For exporting
            $outputActive .= '<span class="hide">' . ($aRow['active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
    $row[] = $outputActive;

    $row[] = (!empty($aRow['last_login']) ? '<span class="text-has-action" data-toggle="tooltip" data-title="'._dt($aRow['last_login']).'">' . time_ago($aRow['last_login']) . '</span>' : '');

     // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $options = '';
    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('onclick'=>'lead_contact('.$aRow['company_id'].','.$aRow['id'].', '.$aRow['type'].');return false;'));
    if (has_permission('customers', '', 'delete') || is_customer_admin($aRow['userid'])) {
        if ($aRow['is_primary'] == 0 || ($aRow['is_primary'] == 1 && $total_client_contacts == 1)) {
                       $options .= icon_btn('leads/delete_contacts_rel_clients/'.$aRow['company_id'].'/'.$aRow['id'].'/'.$aRow['type'], 'remove', 'btn-danger _delete');

        }
    }

    $row[] = $options;
    $output['aaData'][] = $row;
	
}
