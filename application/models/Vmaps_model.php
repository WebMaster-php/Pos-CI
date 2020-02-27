<?php 
defined('BASEPATH') OR exit('Direct Access Denied');

/**
 * A model made by Matee to handle db request coming from vmap controller
 */
class Vmaps_model extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
	}

	function get_leads_and_clients_address()
	{
		return $this->db->query(
			"SELECT name AS 'Name', address AS 'Address', 'xyz' AS 'Lat', 'xyz' AS 'Lan', 'lead' AS 'Type', phonenumber AS 'phone' 
			 FROM tblleads WHERE tblleads.portfolio_id = " . $this->session->userdata('portfolio_id') . "
			 UNION ALL SELECT company AS 'Name', CONCAT(address,' ', city, ' OR ', zip) AS 'Address', latitude AS 'Lat', longitude AS 'Lan', 'client' AS 'Type', phonenumber AS 'phone' 
			 FROM tblclients WHERE tblclients.portfolio_id = " . $this->session->userdata('portfolio_id') . " ORDER BY Name ASC"
				)->result_array();		
	}
}


 ?>