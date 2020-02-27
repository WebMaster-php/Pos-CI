<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$aColumns     = array(
    'name',
    'description'
    );
$sIndexColumn = "id";
$sTable       = 'tblexpensescategories';
$CI = & get_instance(); 
$where = array(); 
if($CI->session->userdata('portfolio_id'))
{
	array_push($where, 'AND tblexpensescategories.portfolio_id = ' . $CI->session->userdata('portfolio_id'));	
}
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $where, array(
    'id'
    ));
$output       = $result['output'];
$rResult      = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="#" onclick="edit_category(this,' . $aRow['id'] . '); return false;" data-name="' . $aRow['name'] . '" data-description="' . clear_textarea_breaks($aRow['description']) . '">' . $_data . '</a>';
        }
        $row[] = $_data;
    }
    $options            = icon_btn('#', 'pencil-square-o', 'btn-default', array(
        'onclick' => 'edit_category(this,' . $aRow['id'] . '); return false;',
        'data-name' => $aRow['name'],
        'data-description' => clear_textarea_breaks($aRow['description'])
        ));
    $row[]              = $options .= icon_btn('expenses/delete_category/' . $aRow['id'], 'remove', 'btn-danger _delete');
    $output['aaData'][] = $row;
}
