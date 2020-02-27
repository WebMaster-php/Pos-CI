<?php
defined('BASEPATH') or exit('No direct script access allowed');

$aColumns     = array(
    'cs3.value as hardwarevalue',
    'serial_number',
    'cs4.value as ownervalue',
    'description',
    'cs1.value as statusvalue',
    'date_in',
);
$sIndexColumn = "inventory_id";
$sTable       = 'tblinventoryadd';
$join             = array(
    'INNER JOIN tblcustom AS cs1 ON cs1.id = tblinventoryadd.status',
	'INNER JOIN tblcustom AS cs3 ON cs3.id = tblinventoryadd.type_of_hardware',
	'INNER JOIN tblcustom AS cs4 ON cs4.id = tblinventoryadd.equipment_owner',
    );
$moreSelectFromDB = array(
	'inventory_id',
	'status',
	'type_of_hardware',
	'equipment_owner',
	);

$result           = data_tables_init($aColumns, $sIndexColumn, $sTable, array(),$join,$moreSelectFromDB);

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
            $_data = '<a href="'.base_url().'admin/inventory_items/index/'.$aRow['serial_number'].'"  target="__blank">'.$_data.'</a>';//data-toggle="modal" data-target="#sales_item_modal" data-id="'.$aRow['inventory_id'].'"
        }
        if ($aColumns[$i] == 'company') {
            $_data = '<a href="'.base_url().'admin/clients/client/'.$aRow['account'].'" target="__blank">'.$_data.'</a>';
        }
        
        $row[] = $_data;
    }
    $options = '';
     if (has_permission('items', '', 'edit')) {
         $options .= icon_btn('#' . $aRow['inventory_id'], 'pencil-square-o', 'btn-default', array(
             'data-toggle' => 'modal',
             'data-target' => '#sales_inventory_modal',
             'onclick'=>'edit_inventory(this.id)',
             'data-id' => $aRow['inventory_id'],
             'id' => $aRow['inventory_id'],
             ));
     }
     if (has_permission('items', '', 'delete')) {
         $options .= icon_btn('inventory/delete/' . $aRow['inventory_id'], 'remove', 'btn-danger _delete');
     }
    $row[] = $options;

    $output['aaData'][] = $row;
}
