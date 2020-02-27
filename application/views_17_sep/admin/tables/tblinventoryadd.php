<?php
defined('BASEPATH') or exit('No direct script access allowed');

$aColumns     = array(
    'serial_number', 
	'cs3.value as hardwarevalue', 
	'company', 
	'description',
	'cs1.value as statusvalue',  
	'date_in',
	'cs2.value as originvalue',
	'cs4.value as ownervalue', 
);
$sIndexColumn = "inventory_id";
$sTable       = 'tblinventoryadd';
$join             = array(
    'LEFT JOIN tblcustom AS cs1 ON cs1.id = tblinventoryadd.status',
    'LEFT JOIN tblcustom AS cs2 ON cs2.id = tblinventoryadd.origin',
	'LEFT JOIN tblcustom AS cs3 ON cs3.id = tblinventoryadd.type_of_hardware',
	'LEFT JOIN tblcustom AS cs4 ON cs4.id = tblinventoryadd.equipment_owner',
	'INNER JOIN tblclients AS cl ON cl.userid = tblinventoryadd.account',
    );
$moreSelectFromDB = array(
	'inventory_id',
	'account',
	'status',
	'origin',
	'type_of_hardware',
	'equipment_owner',
	);

$result           = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, array(),$moreSelectFromDB); 

$output           = $result['output'];
$rResult          = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        if ($aColumns[$i] == 'serial_number') {
            $_data = '<a href="'.base_url().'admin/inventory_items/notes/'.$aRow['inventory_id'].'">'.$_data.'</a>';//data-toggle="modal" data-target="#sales_item_modal" data-id="'.$aRow['inventory_id'].'"
        } 
		if ($aColumns[$i] == 'company') {
            $_data = '<a href="'.base_url().'admin/clients/client/'.$aRow['account'].'">'.$_data.'</a>';
        }
		if ($aColumns[$i] == 'description') {
            $_data = character_limiter($aRow['description'], 50);
        }
		if ($aColumns[$i] == 'date_in') {
            $_data = date('m/d/Y', strtotime($aRow['date_in']));;
        }

        $row[] = $_data;
    }
    $options = '';
	
    if (getAdmin() || has_permission('inventory_items', '', 'edit') ) 
	{
        $options .= icon_btn('#' . $aRow['inventory_id'], 'pencil-square-o', 'btn-default', array(
            'data-toggle' => 'modal',
            'data-target' => '#sales_inventory_modal',
            'onclick'=>'edit_inventory(this.id)',
            'data-id' => $aRow['inventory_id'],
            'id' => $aRow['inventory_id']
        ));
	}
	if(getAdmin() || has_permission('inventory_items', '', 'delete') )
	{
        $options .= icon_btn('inventory/delete/' . $aRow['inventory_id'], 'remove', 'btn-danger _delete');
    }
    $row[] = $options;

    $output['aaData'][] = $row;
}
