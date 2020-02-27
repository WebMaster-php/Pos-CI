
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns = array(
    'tblportfolio_names.name_id as id',
    'tblportfolio_names.names as name',
    '(SELECT portfolio_type FROM tblportfolio_info WHERE tblportfolio_info.portfolio_name_id=tblportfolio_names.name_id) as type',
    '(SELECT description FROM tblportfolio_info WHERE tblportfolio_info.portfolio_name_id=tblportfolio_names.name_id) as description',
    '(SELECT createdat FROM tblportfolio_info WHERE tblportfolio_info.portfolio_name_id=tblportfolio_names.name_id) as created',
    '(SELECT updatedat FROM tblportfolio_info WHERE tblportfolio_info.portfolio_name_id=tblportfolio_names.name_id) as updated',
    );

$sIndexColumn = "name_id";
$sTable = 'tblportfolio_names';
$CI =& get_instance();
$where = array(); 
$result = data_tables_init($aColumns,$sIndexColumn,$sTable,array(),$where,array());
$output = $result['output'];
$rResult = $result['rResult'];

$i = 1;
foreach ( $rResult as $aRow )
{   
    $row = array();

    $row[] = $i;
 
    $row[] = '<a href="' . admin_url('portfolio/portfolio/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';
    
    $row[] = $aRow['type'];
    
    $row[] = $aRow['description'];

    $row[] = date('m-d-Y  h:i A', strtotime($aRow['created']));
    
    $row[] = date('m-d-Y  h:i A', strtotime($aRow['updated']));
     
    $options = icon_btn('portfolio/portfolio/' . $aRow['id'], 'pencil-square-o');
    if($aRow['type'] !='main'){
        $options .= icon_btn('portfolio/delete_portfolio/' . $aRow['id'], 'remove', 'btn-danger _delete');
    }
    $row[]   = $options;

    $output['aaData'][] = $row;

    $i++;
}
