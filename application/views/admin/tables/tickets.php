<?php
// echo "<pre>"; print_r($this->ci->input->post('ticket_follow')); exit('hellokooo');
defined('BASEPATH') or exit('No direct script access allowed');
// exit('here');

$aColumns = array(
    'tbltickets.ticketid',
    'tbltickets.date',
    get_sql_select_client_company(),
    'subject',
    'tblstaff.firstname',
    // '1',
    // '(SELECT GROUP_CONCAT(follower_id SEPARATOR ",") FROM tbl_ticket_followers JOIN tblstaff ON tblstaff.staffid = tbl_ticket_followers.follower_id WHERE tbl_ticket_followers.ticket_id = tbltickets.ticketid ) as followers',
    'followers',
    // get_sql_select_task_asignees_full_names().' as assignees',
    'tbldepartments.name as department_name',
    'tblservices.name as service_name',
    // 'followers',

    'status',
    'priority',
    'lastreply',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltickets.ticketid and rel_type="ticket" ORDER by tag_order ASC) as tags',
    );

$companyColumn = 2;
$tagsColumns = 10;
if ($this->ci->input->get('bulk_actions')) {
    array_unshift($aColumns, '1');
    $companyColumn++;
    $tagsColumns++;
}

$additionalSelect = array(
    'adminread',
    'tbltickets.userid',
    'statuscolor',
    'tbltickets.name as ticket_opened_by_name',
    'tbltickets.email',
    'tbltickets.userid',
    'tblstaff.lastname',
    'assigned',
    );

$join = array(
    'LEFT JOIN tblservices ON tblservices.serviceid = tbltickets.service',
    'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbltickets.department',
    'LEFT JOIN tblticketstatus ON tblticketstatus.ticketstatusid = tbltickets.status',
    'LEFT JOIN tblclients ON tblclients.userid = tbltickets.userid',
    'LEFT JOIN tblpriorities ON tblpriorities.priorityid = tbltickets.priority',
    'LEFT JOIN tblstaff ON tblstaff.staffid = tbltickets.assigned',
    );

$custom_fields = get_table_custom_fields('tickets');
foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $key . ' ON tbltickets.ticketid = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$where = array();
$filter = array();
$CI =& get_instance();
if (isset($userid) && $userid == ''){
     // array_push($filter, 'AND tbltickets.portfolio_id = ' . $CI->session->userdata('portfolio_id') );
    // if(!null == $CI->session->userdata('portfolio_id')){
    //   $check = main_portfolio($CI->session->userdata('portfolio_id'));  
    //     if(empty($check))
    //     {
    //         array_push($where,'AND tbltickets.portfolio_id='. $CI->session->userdata('portfolio_id') );
    //     }
    // }
    // array_push($where, 'AND tbltickets.portfolio_id = ' . $CI->session->userdata('portfolio_id'));
}
if (isset($userid) && $userid != '') {
    array_push($where, 'AND tbltickets.userid = ' . $userid);
    // array_push($where, 'AND tbltickets.portfolio_id = ' . $CI->session->userdata('portfolio_id'));
} elseif (isset($by_email)) {
    array_push($where, 'AND tbltickets.email = "'.$by_email.'"');
}
if (isset($where_not_ticket_id)) {
    //exit('2nd');
    array_push($where, 'AND tbltickets.ticketid != ' . $where_not_ticket_id);
}
if ($this->ci->input->post('project_id')) {
    //exit('3rd');
    array_push($where, 'AND project_id = ' . $this->ci->input->post('project_id'));
}

//bilal code start
if ($this->ci->input->post('saleopp_id')) {
    //exit('4th');
    array_push($where, 'AND saleopp_id = ' . $this->ci->input->post('saleopp_id'));
}
//bilal code end
// echo "<pre>"; print_r($this->ci->input->post()); exit('hellokooo');
$statuses = $this->ci->tickets_model->get_ticket_status();
// echo "<pre>"; print_r($statuses); exit('hello');  
$_statuses = array();
$i = 0;
foreach ($statuses as $__status) {
     // echo "<pre>"; print_r($statuses); exit('heljijiilo');  
     if ($this->ci->input->post('ticket_status_'.$__status['ticketstatusid'])) {
       // $i = $i +1; 
        // echo "<pre>"; print_r($__status); exit('hello');  
        // echo "<pre>"; print_r($this->ci->input->post()); exit('hello');

        array_push($_statuses, $__status['ticketstatusid']);
    }
}
// echo "<pre>"; echo $i; print_r($_statuses); exit('hellokooo');  
if (count($_statuses) > 0) { 
    array_push($filter, 'AND status IN (' . implode(', ', $_statuses) . ')');
}

if ($this->ci->input->post('my_tickets') && $this->ci->input->post('ticket_follow') =='' && $this->ci->input->post('ticket_unassigned') =='') {
    // array_push($where, 'AND assigned IN ( ' . get_staff_user_id(). ' )');
    array_push($where, 'AND assigned LIKE "%'. get_staff_user_id().'%"');
	//$conditions1 = ' assigned LIKE "%'. get_staff_user_id().'%" ';
}

if ($this->ci->input->post('ticket_follow') != '' && $this->ci->input->post('my_tickets') =='' && $this->ci->input->post('ticket_unassigned') =='' ) {
		array_push($where, 'AND followers LIKE "%'. get_staff_user_id().'%"');
}
// echo "<pre>"; echo $i; print_r($_POST); exit('hellokooo');  
if ($this->ci->input->post('ticket_unassigned') != '' && $this->ci->input->post('my_tickets') =='' && $this->ci->input->post('ticket_follow') ==''  ) {
    array_push($where, 'AND assigned IS NULL');
	//$conditions1 = ' assigned IS NULL ' ; 
}
if ($this->ci->input->post('ticket_unassigned') != '' && $this->ci->input->post('my_tickets') !='' && $this->ci->input->post('ticket_follow') !=''  ) {
    $conditions = 'AND ( assigned LIKE "%'. get_staff_user_id().'%" OR followers LIKE "%'. get_staff_user_id().'%" OR assigned IS NULL )'; 
    array_push($where, $conditions);
}

$assignees = $this->ci->tickets_model->get_tickets_assignes_disctinct();
$_assignees = array();
foreach ($assignees as $__assignee) {
    if ($this->ci->input->post('ticket_assignee_'.$__assignee['assigned'])) {
        array_push($_assignees, $__assignee['assigned']);
    }
}
if (count($_assignees) > 0) {
    array_push($filter, 'AND assigned IN (' . implode(', ', $_assignees)  . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND ('.prepare_dt_filter($filter) . ')');
}
// If userid is set, the the view is in client profile, should be shown all tickets
if (!is_admin()) {
    if (get_option('staff_access_only_assigned_departments') == 1) {
        $this->ci->load->model('departments_model');
        $staff_deparments_ids = $this->ci->departments_model->get_staff_departments(get_staff_user_id(), true);
        $departments_ids = array();
        if (count($staff_deparments_ids) == 0) {
            $departments = $this->ci->departments_model->get();
            foreach ($departments as $department) { 
                array_push($departments_ids, $department['departmentid']);
            }
        } else {
            $departments_ids = $staff_deparments_ids;
        }
        if (count($departments_ids) > 0) {
            array_push($where, 'AND department IN (SELECT departmentid FROM tblstaffdepartments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="'.get_staff_user_id().'")');
        }
    }
}

$sIndexColumn = 'ticketid';
$sTable       = 'tbltickets';

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

if(!null == $CI->session->userdata('portfolio_id')){
  $check = main_portfolio($CI->session->userdata('portfolio_id'));  
    if(empty($check))
    {
        // array_push($where,'AND tblclients.portfolio_id='.$CI->session->userdata('portfolio_id'));
        array_push($where,'AND tbltickets.portfolio_id='. $CI->session->userdata('portfolio_id') );
    }
}
 // echo "<pre>"; print_r($where); exit('llll');  
$show_picture = get_show_picture($CI->session->userdata('portfolio_id'));

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
// echo $this->ci->db->last_query(); exit('here'); 
// echo "<pre>"; print_r($result); exit('llll');  
$output  = $result['output'];
$rResult = $result['rResult'];


// $sa = get_small_picture($rResult[0]['followers']);
$check = main_portfolio($CI->session->userdata('portfolio_id'));  

foreach ($rResult as $aRow) {    
     // echo "<pre>"; print_r($aRow['followers']); exit('llll');  
     if(!empty($check)){
        $services_none  = getServicesNone('None', $aRow['userid']);
       if(!empty($services_none))
        {
            continue ;
        }
    }

    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        if ($aColumns[$i] == '1') {
            $_data = '<div class="checkbox"><input type="checkbox" value="'.$aRow['tbltickets.ticketid'].'"><label></label></div>';
        } elseif ($aColumns[$i] == 'lastreply') {
            if ($aRow[$aColumns[$i]] == null) {
                $_data = _l('ticket_no_reply_yet');
            } else {
                $_data = '<span class="text-has-action" data-toggle="tooltip" data-title="'._dt($aRow[$aColumns[$i]]).'">'.time_ago_specific($aRow[$aColumns[$i]]) . '</span>';
            }
        } elseif ($aColumns[$i] == 'subject' || $aColumns[$i] == 'tbltickets.ticketid') {
            $_data = '<a href="' . admin_url('tickets/ticket/' . $aRow['tbltickets.ticketid']) . '" class="valign">' . $_data . '</a>';
        } elseif($aColumns[$i] == 'tblstaff.firstname'){
            
            if($show_picture == 1){
                if($aRow['assigned']){
                    $pic = get_pic_icon($aRow['assigned']);    
                    $_data = $pic; 
                }
                else{
                    $_data = 'unassigned'; 
                }
            }
            else{
                    if($aRow['assigned']){
                        $val_assign = explode(',', $aRow['assigned']);
                        $str_assign = ''; $int = 0;
                        foreach ($val_assign as $val) {
                            $str_assign .= '<a href="' . admin_url('profile/' . $val) . '" class="valign">' .  get_staff_full_name($val) . '</a>'. ',  ';
                            
                        }
                        $trim_val = trim($str_assign , ' ');
                        $_data = trim($trim_val , ','); 
                        // $_data = $str_assign; 
                        // $_data = '<a href="' . admin_url('profile/' . $aRow['assigned']) . '" class="valign">' .  get_staff_full_name($aRow['assigned']) . '</a>';
                    }
                    else{
                        $_data = 'unassigned';
                    }
                 //Ticket is assigned
                }    
            }
            
            // elseif($aRow['followers']){
             elseif($aColumns[$i] == 'followers'){
                    if($aRow['followers'] != ''){
                        if($show_picture == 1){
                            $fpic = get_pic_icon($aRow['followers']); 
                            $_data = $fpic;    
                        }

                        else{
                                $val_follow = explode(',', $aRow['followers']);
                                $str_follow = ''; $int = 0;
                                foreach ($val_follow as $valf) { $str_follow .= '<a href="' . admin_url('profile/' . $val) . '" class="valign">' .  get_staff_full_name($valf) . '</a>'. ',  ';}
                                $trim_valf = trim($str_follow , ' ');
                                $_data = trim($trim_valf , ',');    
                        }
                         
                    }
                    else{
                        $_data = ' ';
                    }
                }
                 
          // $_data = 'a'; 
         elseif ($i == $companyColumn) {
            if ($aRow['userid'] != 0) {
                $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';
            } else {
                $_data = $aRow['ticket_opened_by_name'];
            }
        } elseif ($aColumns[$i] == 'department_name') {
            $_data = $aRow['department_name'];
        } elseif ($aColumns[$i] == 'service_name') {
            $_data = $aRow['service_name'];
        } elseif ($aColumns[$i] == 'status') {
            //$_data = '<span class="label inline-block" style="border:1px solid ' . $aRow["statuscolor"] . '; color:' . $aRow['statuscolor'] . '">' . ticket_status_translate($aRow['status']) . '</span>';
            $_data = '<span class="label inline-block" style="border:1px solid ' . $aRow["statuscolor"] . '; color:' . $aRow['statuscolor'] . '">' . ticket_status_translates($aRow['status']) . '</span>';
        } elseif ($aColumns[$i] == 'priority') {
            $_data = ticket_priority_translate($aRow['priority']);
        } elseif ($aColumns[$i] == 'tbltickets.date') {
            $_data = '<span data-toggle="tooltip" data-title="'._dt($_data).'" class="text-has-action">'.time_ago($_data).'</span>';
        } elseif ($i == $tagsColumns) {
            $_data = render_tags($_data);
        } else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }

        $row[] = $_data;

        if ($aRow['adminread'] == 0) {
            $row['DT_RowClass'] = 'text-danger bold';
        }
    }

    $options = icon_btn('tickets/ticket/' . $aRow['tbltickets.ticketid'].'/'. $aRow['userid'], 'pencil-square-o');
    $options .= icon_btn('tickets/delete/' . $aRow['tbltickets.ticketid'].'/'. $aRow['userid'], 'remove', 'btn-danger _delete');
    $row[]   = $options;
    $output['aaData'][] = $row;
}
