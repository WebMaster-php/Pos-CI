<?php
// sajid
// exit('here');
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns = array(
    'tblhidden_items.hide_item_id as hide_item_id',
    'tblhidden_items.userid as userid',
    'tblhidden_items.company as company',
    'tblhidden_items.phonenumber as phone',
    '(SELECT short_name FROM tblcountries WHERE country_id = tblhidden_items.country) as country',
    '(SELECT names FROM tblportfolio_names WHERE name_id = tblhidden_items.portfolio_id) as portfolio',
    // '(SELECT email FROM tblcontacts WHERE userid = tblhidden_items.userid)as contact_primary_email',
    // '(SELECT phonenumber FROM tblcontacts WHERE userid = tblhidden_items.userid)as primary_contact',
    // 'tblhidden_items.hidden_created_date as date',
    );

$sIndexColumn = "userid";
$sTable = 'tblhidden_items';
$CI =& get_instance();
$where = array(); 
$result = data_tables_init($aColumns,$sIndexColumn,$sTable,array(),$where,array());
$output = $result['output'];
$rResult = $result['rResult'];
// echo "<pre>"; print_r($rResult); exit; 
foreach ( $rResult as $aRow )
{
    
    $row = array();
    
    $row[] = $aRow['userid'];
 
    $row[] = '<a href="#" data-toggle="modal" data-target="#hidden_items" class = "hid" value = ' . $aRow['hide_item_id'] .'>' . $aRow['company'] . '</a>';
    // $row[] = '<a href="#" class = "hid">' . $aRow['company'] . '</a>';
    
    // $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';

    $row[] = $aRow['portfolio'];
    
    // $row[] = $aRow['country'];
    $primary = getPrimary($aRow['userid']);   
    // echo "<pre>"; print_r($primary); exit;
    if($primary){
    // Primary contact name
        $row[] =  $primary->firstname . ' ' . $primary->lastname ;
    }else{
            $row[] = '';
        }

    if($primary){
    // Primary contact email
        $row[] =  $primary->email;
    }else{
        $row[] = '';
    }    
    // $row[] = $aRow['primary_contact'];
    
    // $row[] = $aRow['contact_primary_email'];

    // $row[] = $aRow['date'];
    // $row[] = date('m-d-Y  h:i A', strtotime($aRow['date']));
     

    $options = icon_btn('hidden_items/restore/'.$aRow['hide_item_id'],'fa fa-window-restore','btn-default');
    
    $row[]  = $options .= icon_btn('hidden_items/delete/'.$aRow['hide_item_id'].'/'.$aRow['userid'],'remove','btn-danger _delete');

    // echo "<pre>";print_r($aRow);exit();
    $output['aaData'][] = $row;
}
