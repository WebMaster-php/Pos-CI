<?php
// print_r($userid); exit('chi'); 
defined('BASEPATH') or exit('No direct script access allowed');

// $has_permission_delete = has_permission('leads','','delete');
// $custom_fields = get_table_custom_fields('leads');

$aColumns     = array(
    '1',
    ' tblcustomerleads.id as id',
    ' tblcustomerleads.name as name',
    'company',
	'followers',
    ' tblcustomerleads.email as email',
    ' tblcustomerleads.phonenumber as phonenumber',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id =  tblcustomerleads.id and rel_type="lead" ORDER by tag_order ASC) as tags',
    'CONCAT(firstname, \' \', lastname) as assigned_name',
    ' tblleadsstatus.name as status_name',
    ' tblleadssources.name as source_name',
    'lastcontact',
    'dateadded'
    );

$sIndexColumn = "id";
$sTable       = ' tblcustomerleads';

$join = array(
    'LEFT JOIN tblstaff ON tblstaff.staffid =  tblcustomerleads.assigned',
    'LEFT JOIN  tblleadsstatus ON  tblleadsstatus.id =  tblcustomerleads.status',
    'LEFT JOIN  tblleadssources ON  tblleadssources.id =  tblcustomerleads.source',
);

// foreach ($custom_fields as $key => $field) {
//     $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
//     array_push($customFieldsColumns,$selectAs);
//     array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
//     array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $key . ' ON  tblcustomerleads.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
// }
$CI =& get_instance();
$where = array();
$filter = false;

// if ($this->ci->input->post('custom_view')) {
//     $filter = $this->ci->input->post('custom_view');
//     if ($filter == 'lost') {
//         array_push($where, 'AND lost = 1');
//     } elseif ($filter == 'junk') {
//         array_push($where, 'AND junk = 1');
//     } elseif ($filter == 'not_assigned') {
//         array_push($where, 'AND assigned = 0');
//     } elseif ($filter == 'contacted_today') {
//         array_push($where, 'AND lastcontact LIKE "'.date('Y-m-d').'%"');
//     } elseif ($filter == 'created_today') {
//         array_push($where, 'AND dateadded LIKE "'.date('Y-m-d').'%"');
//     } elseif ($filter == 'public') {
//         array_push($where, 'AND is_public = 1');
//     }
// }

// if (!$filter || ($filter && $filter != 'lost' && $filter != 'junk')) {
//     array_push($where, 'AND lost = 0 AND junk = 0');
// }

// if (has_permission('leads','','view') && $this->ci->input->post('assigned')) {
//     array_push($where, 'AND assigned =' . $this->ci->input->post('assigned'));
// }

// if ($this->ci->input->post('status')
//     && count($this->ci->input->post('status')) > 0
//     && ($filter != 'lost' && $filter != 'junk')) {
//     array_push($where, 'AND status IN ('.implode(',',$this->ci->input->post('status')).')');
// }

// if ($this->ci->input->post('source')) {
//     array_push($where, 'AND source =' . $this->ci->input->post('source'));
// }

// if (!has_permission('leads','','view')) {
//     array_push($where, 'AND (assigned =' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR is_public = 1)');
// }

// $aColumns = do_action('leads_table_sql_columns', $aColumns);

// // Fix for big queries. Some hosting have max_join_limit
// if (count($custom_fields) > 4) {
//     @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
// }
if ($userid!='') {
    array_push($where, ' AND  tblcustomerleads.client_id = ' .$userid);
}
if (!null == $CI->session->userdata('portfolio_id')) {
    array_push($where, ' AND  tblcustomerleads.portfolio_id = ' . $CI->session->userdata('portfolio_id') );
}


$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    // 'junk',
    // 'lost',
    // 'color',
    'assigned',
    ' tblcustomerleads.addedfrom as addedfrom',
    'zip'
));
// echo'<pre>';
// print_r($result);
// die;
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    $row[] = '<div class="checkbox"><input type="checkbox" value="'.$aRow['id'].'"><label></label></div>';

    $row[] =  '<a href="'.admin_url('leads/index/'.$aRow['id']).'" onclick="init_lead('.$aRow['id'].');return false;">'. $aRow['id'] . '</a>';

    $row[] = '<a href="'.admin_url('leads/index/'.$aRow['id']).'" onclick="init_lead('.$aRow['id'].');return false;">'. $aRow['name'] . '</a>';

    //$row[] = $aRow['company'];

    $row[] = ($aRow['email'] != '' ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');

    $row[] = ($aRow['phonenumber'] != '' ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : '');

    $row[] .= render_tags($aRow['tags']);

    $assignedOutput = '';
    if ($aRow['assigned'] != 0) {
		$assignedUsers=explode(',',$aRow['assigned']);
        
		foreach($assignedUsers as $aUser){
		$full_name=get_staff_full_name($aUser);
        $assignedOutput .= '<a data-toggle="tooltip" data-title="'.$full_name.'" href="'.admin_url('profile/'.$aUser).'">'.staff_profile_image($aUser, array(
            'staff-profile-image-small'
            )) . '</a>&nbsp;';
			//"'.$full_name.'"
		}
        // For exporting
        $assignedOutput .= '<span class="hide">'.$full_name.'</span>';
    }
	$row[] = $assignedOutput;
	$followersOutput = '';
    if ($aRow['followers'] != 0) {
		$followersUsers=explode(',',$aRow['followers']);
       
		foreach($followersUsers as $aFollower){
		$full_name=get_staff_full_name($aFollower);
        $followersOutput .= '<a data-toggle="tooltip" data-title="'.$full_name.'" href="'.admin_url('profile/'.$aFollower).'">'.staff_profile_image($aFollower, array(
            'staff-profile-image-small'
            )) . '</a>&nbsp;';
			//"'.$full_name.'"admin/leads/followers
		}
        // For exporting
        $followersOutput .= '<span class="hide">'.$full_name.'</span>';
    }
	$row[] = $followersOutput;
     if ($aRow['status_name'] == null) {
         if ($aRow['lost'] == 1) {
             $statusOutput = '<span class="label label-danger inline-block">' . _l('lead_lost') . '</span>';
         } elseif ($aRow['junk'] == 1) {
             $statusOutput = '<span class="label label-warning inline-block">' . _l('lead_junk') . '</span>';
         }
     } else {
         $statusOutput = '<span class="inline-block label'.(!$this->ci->input->post('status') ? ' pointer lead-status' : '').' label-' . (empty($aRow['color']) ? 'default': '') . '" style="color:' . $aRow['color'] . ';border:1px solid ' . $aRow['color'] . '">' . $aRow['status_name'] . '</span>';
     }

    $row[] = $statusOutput;

    $row[] = $aRow['source_name'];

    $row[] = ($aRow['lastcontact'] == '0000-00-00 00:00:00' || !is_date($aRow['lastcontact']) ? '' : '<span data-toggle="tooltip" data-title="'._dt($aRow['lastcontact']).'" class="text-has-action">'.time_ago($aRow['lastcontact']).'</span>');

    $row[] = '<span data-toggle="tooltip" data-title="'._dt($aRow['dateadded']).'" class="text-has-action">'.time_ago($aRow['dateadded']).'</span>';

     // Custom fields add values
    foreach($customFieldsColumns as $customFieldColumn){
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $hook_data = do_action('leads_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook_data['output'];

    $options = '';

    if ($aRow['addedfrom'] == get_staff_user_id() || $has_permission_delete) {

        $options .= icon_btn('clients/customer_lead_delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    }


    $row[] = $options;
    $row['DT_RowId'] = 'lead_'.$aRow['id'];

    if ($aRow['assigned'] == get_staff_user_id()) {
        $row['DT_RowClass'] = 'alert-info';
    }
// echo "<pre>";  print_r($row); exit('salik'); 
    $output['aaData'][] = $row;
}
