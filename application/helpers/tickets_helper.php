<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Render admin tickets table
 * @param string  $name        table name
 * @param boolean $bulk_action include checkboxes on the left side for bulk actions
 */
function AdminTicketsTableStructure($name = '', $bulk_action = false)
{

    $table = '<table class="table dt-table-loading '.($name == '' ? 'tickets-table' : $name).' table-tickets">';
    $table .= '<thead>';
    $table .= '<tr>';
    if ($bulk_action == true) {
        $table .= '<th>';
        $table .= '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="tickets"><label></label></div>';
        $table .= '</th>';
    }
    $table .= '<th>#</th>';
	$table .= '<th class="ticket_created_column">'._l('ticket_date_created').'</th>';
    $table .= '<th>'._l('ticket_dt_submitter').'</th>';
	$table .= '<th>'._l('ticket_dt_subject').'</th>';
	$table .= '<th>'._l('Assigned to').'</th>';
    $table .= '<th>Followers</th>';
    $table .= '<th>'._l('ticket_dt_department').'</th>';
    $services_th_attrs = '';
    if (get_option('services') == 0) {
        $services_th_attrs = ' class="not_visible"';
    }
    $table .= '<th'.$services_th_attrs.'>'._l('ticket_dt_service').'</th>';
    $table .= '<th>'._l('ticket_dt_status').'</th>';
    $table .= '<th>'._l('ticket_dt_priority').'</th>';
    $table .= '<th>'._l('ticket_dt_last_reply').'</th>';

   $custom_fields = get_table_custom_fields('tickets');

    foreach ($custom_fields as $field) {
        $table .= '<th>'.$field['name'].'</th>';
    }
	$table .= '<th>'._l('tags').'</th>';
    $table .= '<th>'._l('options').'</th>';
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody></tbody>';
    $table .= '</table>';
    // echo "<pre>"; print_r($table);  exit('jkooohuoi');
// echo "<pre>"; print_r($this->input->post());  exit('jkooooi');
    return $table;
}

/**
 * Function to translate ticket status
 * The app offers ability to translate ticket status no matter if they are stored in database
 * @param  mixed $id
 * @return string
 */
function ticket_status_translate($id)
{
    if ($id == '' || is_null($id)) {
        return '';
    }

    $line = _l('ticket_status_db_' . $id, '', false);

    if ($line == 'db_translate_not_found') {
        $CI =& get_instance();
        $CI->db->where('ticketstatusid', $id);
        $status = $CI->db->get('tblticketstatus')->row();

        return !$status ? '' : $status->name;
    }

    return $line;
}
function ticket_status_translates($id)
{
    if ($id == '' || is_null($id)) {
        return '';
    }
      
		$CI =& get_instance();
		//$portfolio_id = $CI->session->userdata('portfolio_id');
		//print_r($portfolio_id); 
		//exit; 
        $CI->db->where('ticketstatusid', $id);
		//$CI->db->where('portfolio_id', $portfolio_id);
        $status = $CI->db->get('tblticketstatus')->row();
		// echo "<pre>"; print_r($status); exit; 	
        return !$status ? '' : $status->name;
    return $line;
}

/**
 * Function to translate ticket priority
 * The apps offers ability to translate ticket priority no matter if they are stored in database
 * @param  mixed $id
 * @return string
 */
function ticket_priority_translate($id)
{
    if ($id == '' || is_null($id)) {
        return '';
    }

    $line = _l('ticket_priority_db_' . $id, '', false);

    if ($line == 'db_translate_not_found') {
        $CI =& get_instance();
        $CI->db->where('priorityid', $id);
        $priority = $CI->db->get('tblpriorities')->row();

        return !$priority ? '' : $priority->name;
    }

    return $line;
}

/**
 * When ticket will be opened automatically set to open
 * @param integer  $current Current status
 * @param integer  $id      ticketid
 * @param boolean $admin   Admin opened or client opened
 */
function set_ticket_open($current, $id, $admin = true)
{
    if ($current == 1) {
        return;
    }

    $field = ($admin == false ? 'clientread' : 'adminread');

    $CI =& get_instance();
    $CI->db->where('ticketid', $id);

    $CI->db->update('tbltickets', array(
        $field => 1,
    ));
}

/**
 * For html5 form accepted attributes
 * This function is used for the tickets form attachments
 * @return string
 */
function get_ticket_form_accepted_mimes()
{
    $ticket_allowed_extensions  = get_option('ticket_attachments_file_extensions');
    $_ticket_allowed_extensions = explode(',', $ticket_allowed_extensions);
    $all_form_ext               = $ticket_allowed_extensions;

    if (is_array($_ticket_allowed_extensions)) {
        foreach ($_ticket_allowed_extensions as $ext) {
            $all_form_ext .= ',' . get_mime_by_extension($ext);
        }
    }

    return $all_form_ext;
}
    function get_ticket_company_from_ticket_id($id ='')
    {
        $CI =& get_instance();
        $CI->db->select('userid');
        $CI->db->from('tbltickets');
        $CI->db->where('ticketid', $id);
        $res = $CI->db->get()->result_array();
        // echo "<pre>";print_r($res[0]);exit();
        return $res[0]['userid'];
    }
   function required_followers($portfolio_id){
    // echo $portfolio_id; 
        $CI =& get_instance();
        $CI->db->select('followers');
        $CI->db->from('tblportfolio_info');
        $CI->db->where('id', $portfolio_id);
        $res = $CI->db->get()->result_array();
        if($res){
            return $res[0]['followers'];
        }
   }  

   function email_to_assiginee_follower(){
        $CI =& get_instance();
        $CI->load->model('emails_model');
        
   }
   function get_default_filters($id = ''){
        $CI =& get_instance();
        $CI->db->select('ticket_permissions');
        $CI->db->from('tblstaff');
        $CI->db->where('staffid', $id);
        $res = $CI->db->get()->result_array();
        $result = unserialize($res[0]['ticket_permissions']); 
        return $result; 
   }
   function get_show_picture($id = ''){
       // echo $id; exit('koo'); 
        $CI =& get_instance();
        $CI->db->select('user_picture');
        $CI->db->from('tblportfolio_info');
        $CI->db->where('id', $id);
        $res = $CI->db->get()->result_array(); 
        // echo "<pre>"; print_r($res); exit('man');  
        if($res){
            return $res[0]['user_picture'];
        }
   }
   function get_pic_icon($id){
    if($id){
        // echo $id;
        $outputAssignees = '';
        $exportAssignees = '';
        $totalAssignees = '';

            $ides = explode(',', $id);
            foreach ($ides as  $ids) {
                 $CI = &get_instance();
                 $CI->db->select("*");
                 $CI->db->from('tblstaff');
                 $CI->db->where('staffid', $ids);
                 $result = $CI->db->get()->result_array();

                  $outputAssignees .= '<a href="' . admin_url('profile/' .trim($ids)) .'">' .
                    staff_profile_image($ids, array(
                      'staff-profile-image-small mright5',
                    ), 'small', array(
                      'data-toggle' => 'tooltip',
                      'data-title' => $result[0]['firstname'] ,
                    )) . '</a>';
                     $exportAssignees .= $ids . ', '; 
            }   
            return $outputAssignees; 
        }
    }

function get_email_template($assignedEmail, $merge_fields, $ticketid)
{
    $CI = &get_instance();
    $subject = 'New Ticket Opened';
    $html ='<span style="font-size: 12pt;">Hi '. $merge_fields["{contact_firstname}"] .' '.$merge_fields["{contact_lastname}"].'</span><br /> <br /><span style="font-size: 12pt;">A new support ticket has been opened.</span><br /> <br /><span style="font-size: 12pt;"><strong>Ticket Subject: </strong>'. $merge_fields["{ticket_subject}"].'</span><br /><span style="font-size: 12pt;"><strong>Department: </strong>'. $merge_fields["{ticket_department}"].'</span><br /><span style="font-size: 12pt;"><strong>Priority: </strong>'. $merge_fields["{ticket_priority}"].'</span><br /> <br /><span style="font-size: 12pt;">You can view your ticket using the following link: <a href="'.$merge_fields["{ticket_url}"].'">#.'.$merge_fields["{ticket_id}"].'<br /><br /></a>Kind Regards,</span><br /><span style="font-size: 12pt;">VersiPOS Team</span><span style="font-size: 12pt;"><br /><br /><br /></span></span><h4 class="no-margin">New Ticket Opened (Opened by Staff, Sent to Customer)</h4>';
    $send_mail = $CI->emails_model->report_email($assignedEmail, $subject, $html);
    if($send_mail){
        return true; 
    }
    else{
        return false; 
    }
}

function get_email_template_followers($Email, $merge_fields, $ticketid)
    {
        $CI = &get_instance();
        $subject = 'New Ticket Opened';
        $html ='<span style="font-size: 12pt;">Hi '. $merge_fields["{contact_firstname}"] .' '.$merge_fields["{contact_lastname}"].'</span><br /> <br /><span style="font-size: 12pt;">A new support ticket has been opened and You have been assigned as a follower.</span><br /> <br /><span style="font-size: 12pt;"><strong>Ticket Subject: </strong>'. $merge_fields["{ticket_subject}"].'</span><br /><span style="font-size: 12pt;"><strong>Department: </strong>'. $merge_fields["{ticket_department}"].'</span><br /><span style="font-size: 12pt;"><strong>Priority: </strong>'. $merge_fields["{ticket_priority}"].'</span><br /> <br /><span style="font-size: 12pt;">You can view your ticket using the following link: <a href="'.$merge_fields["{ticket_url}"].'">#.'.$merge_fields["{ticket_id}"].'<br /><br /></a>Kind Regards,</span><br /><span style="font-size: 12pt;">VersiPOS Team</span><span style="font-size: 12pt;"><br /><br /><br /></span></span><h4 class="no-margin">New Ticket Opened (Opened by Staff, Sent to Customer)</h4>';
         //echo "<pre>";print_r($html);exit('fff');
        $send_mail = $CI->emails_model->report_email($Email, $subject, $html);
        if($send_mail){
            return true; 
        }
        else{
            return false; 
        }

    }

function get_ticket_followers_full_name($followers)
{
    $CI =& get_instance();
    $strassign = ''; 
        if($followers){
            $followers = explode(',', $followers);
            foreach ($followers as $follow) {
                $CI->db->where('staffid', $follow);
                $staff = $CI->db->select('firstname,lastname')->from('tblstaff')->get()->row();
                if($staff){
                        $strassign .= ' '. $staff->firstname . ' ' . $staff->lastname . ',  '; 
                }  
             }              
         }   
    $strassigns = trim($strassign);
    $strassign = rtrim($strassigns, ',');
    return $strassign;
}
function get_email_reply_contact($id)
{
    $CI = &get_instance();
    $CI->db->select('firstname,lastname,title,email');
    $CI->db->from('tblcontacts');
    $CI->db->where('id',$id);
    $res = $CI->db->get()->result_array();
    return $res;
}

function get_contacts_firstname($id)
{
    $CI = &get_instance();
    $CI->db->select('firstname,email');
    $CI->db->from('tblcontacts');
    $CI->db->where('id', $id);
    $res = $CI->db->get()->result_array();
    //print_r($res);exit('saaaaaaaaaaaa');
    return $res[0];
    //echo $res[0]['email'];
}
function get_company_contact_tickets($id){
    $CI = &get_instance();
    $CI->load->model('Clients_model');
    return $CI->clients_model->get_compny_contacts($id);
   // echo "<pre>";  print_r($contacts); exit('k');  

}
