<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Tickets_model extends CRM_Model
{
    private $piping = false;

    public function __construct()
    {
        parent::__construct();
    }

    private function _maybe_fix_pipe_encoding_chars($text)
    {
        $text = str_replace("ð", "ğ", $text);
        $text = str_replace("þ", "ş", $text);
        $text = str_replace("ý", "ı", $text);
        $text = str_replace("Ý", "İ", $text);
        $text = str_replace("Ð", "Ğ", $text);
        $text = str_replace("Þ", "Ş", $text);

        return $text;
    }

    public function insert_piped_ticket($data)
    {
        $data = do_action('piped_ticket_data',$data);

        $this->piping = true;
        $attachments  = $data['attachments'];
        $subject      = $this->_maybe_fix_pipe_encoding_chars($data['subject']);
        // Prevent insert ticket to database if mail delivery error happen
        // This will stop createing a thousand tickets
        $system_blocked_subjects = array(
            'Mail delivery failed',
            'failure notice',
            'Returned mail: see transcript for details',
            'Undelivered Mail Returned to Sender',
            );

        $subject_blocked = false;

        foreach ($system_blocked_subjects as $sb) {
            if (strpos('x'.$subject, $sb) !== false) {
                $subject_blocked = true;
                break;
            }
        }

        if ($subject_blocked == true) {
            return;
        }

        $message      = $this->_maybe_fix_pipe_encoding_chars($data['body']);
        $name         = $this->_maybe_fix_pipe_encoding_chars($data['fromname']);

        $email        = $data['email'];
        $to           = $data['to'];
        $subject      = $subject;
        $message      = $message;
        $mailstatus   = false;
    
    $this->db->select('*');
    $this->db->where('portfolio_id', $this->session->userdata('portfolio_id'));
    $spam_filters = $this->db->get()->result_array();
         
    foreach ($spam_filters as $filter) {
            $type  = $filter['type'];
            $value = $filter['value'];
            if ($type == "sender") {
                if (strtolower($value) == strtolower($email)) {
                    $mailstatus = "Blocked Sender";
                }
            }
            if ($type == "subject") {
                if (strpos("x" . strtolower($subject), strtolower($value))) {
                    $mailstatus = "Blocked Subject";
                }
            }
            if ($type == "phrase") {
                if (strpos("x" . strtolower($message), strtolower($value))) {
                    $mailstatus = "Blocked Phrase";
                }
            }
        }
        // No spam found
        if (!$mailstatus) {
            $pos = strpos($subject, "[Ticket ID: ");
            if ($pos === false) {
            } else {
                $tid = substr($subject, $pos + 12);
                $tid = substr($tid, 0, strpos($tid, "]"));
                $this->db->where('ticketid', $tid);
                $data = $this->db->get('tbltickets')->row();
                $tid  = $data->ticketid;
            }
            $to            = trim($to);
            $toemails      = explode(",", $to);
            $department_id = false;
            $userid        = false;
            foreach ($toemails as $toemail) {
                if (!$department_id) {
                    $this->db->where('email', $toemail);
                    $data = $this->db->get('tbldepartments')->row();
                    if ($data) {
                        $department_id = $data->departmentid;
                        $to            = $data->email;
                    }
                }
            }
            if (!$department_id) {
                $mailstatus = "Department Not Found";
            } else {
                if ($to == $email) {
                    $mailstatus = "Blocked Potential Email Loop";
                } else {
                    $message = trim($message);
                    $this->db->where('active', 1);
                    $this->db->where('email', $email);
                    $result = $this->db->get('tblstaff')->row();
                    if ($result) {
                        if ($tid) {
                            $data            = array();
                            $data['message'] = $message;
                            $data['status']  = 1;
                            if ($userid == false) {
                                $data['name']  = $name;
                                $data['email'] = $email;
                            }
              
                            $reply_id = $this->add_reply($data, $tid, $result->staffid, $attachments);
                
                
                 
              
              if ($reply_id) {
                  
                                $mailstatus = "Ticket Reply Imported Successfully";
                            }
                        } else {
                            $mailstatus = "Ticket ID Not Found";
                        }
                    } else {
                        $this->db->where('email', $email);
                        $result = $this->db->get('tblcontacts')->row();
                        if ($result) {
                            $userid    = $result->userid;
                            $contactid = $result->id;
                        }
                        if ($userid == false && get_option('email_piping_only_registered') == '1') {
                            $mailstatus = "Unregistered Email Address";
                        } else {
                            $filterdate = date("YmdHis", mktime(date("H"), date("i") - 15, date("s"), date("m"), date("d"), date("Y")));
                            $query      = 'SELECT count(*) as total FROM tbltickets WHERE date > "' . $filterdate . '" AND (email="' . $this->db->escape($email) . '"';
                            if ($userid) {
                                $query .= " OR userid=" . (int) $userid;
                            }
                            $query .= ")";
                            $result = $this->db->query($query)->row();
                            if (10 < $result->total) {
                                $mailstatus = "Exceeded Limit of 10 Tickets within 15 Minutes";
                            } else {
                                if (isset($tid)) {
                                    $data            = array();
                                    $data['message'] = $message;
                                    $data['status']  = 1;
                                    if ($userid == false) {
                                        $data['name']  = $name;
                                        $data['email'] = $email;
                                    } else {
                                        $data['userid']    = $userid;
                                        $data['contactid'] = $contactid;

                                        $this->db->where('userid', $userid);
                                        $this->db->where('ticketid', $tid);
                                        $t = $this->db->get('tbltickets')->row();
                                        if (!$t) {
                                            $abuse = true;
                                        }
                                    }
                                    if (!isset($abuse)) {
                                        $reply_id = $this->add_reply($data, $tid, null, $attachments);
                                        if ($reply_id) {
                                            // Dont change this line
                                            $mailstatus = "Ticket Reply Imported Successfully";
                                        }
                                    } else {
                                        $mailstatus = 'Ticket ID Not Found For User';
                                    }
                                } else {
                                    if (get_option('email_piping_only_registered') == 1 && !$userid) {
                                        $mailstatus = "Blocked Ticket Opening from Unregistered User";
                                    } else {
                                        if (get_option('email_piping_only_replies') == '1') {
                                            $mailstatus = "Only Replies Allowed by Email";
                                        } else {
                                            $data               = array();
                                            $data['department'] = $department_id;
                                            $data['subject']    = $subject;
                                            $data['message']    = $message;
                                            $data['contactid']  = $contactid;
                                            $data['priority']   = get_option('email_piping_default_priority');
                                            if ($userid == false) {
                                                $data['name']  = $name;
                                                $data['email'] = $email;
                                            } else {
                                                $data['userid'] = $userid;
                                            }
                                            $tid        = $this->add($data, null, $attachments);
                                            // Dont change this line
                      $mailstatus = "Ticket Imported Successfully";
                    }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($mailstatus == "") {
            $mailstatus = "Ticket Import Failed";
        }
        $this->db->insert('tblticketpipelog', array(
            'date'      => date('Y-m-d H:i:s'),
            'email_to'    => $to,
            'name'      => $name,
            'email'     => $email,
            'subject'     => $subject,
            'message'     => $message,
            'status'    => $mailstatus,
      'portfolio_id'  => $this->session->userdata('portfolio_id')
        ));

        return $mailstatus;
    }

    private function process_pipe_attachments($attachments, $ticket_id, $reply_id = '')
    {
        if (!empty($attachments)) {
            $ticket_attachments = array();
            $allowed_extensions = explode(',', get_option('ticket_attachments_file_extensions'));

            $path = FCPATH .'uploads/ticket_attachments' . '/' . $ticket_id . '/';

            foreach ($attachments as $attachment) {
                $filename      = $attachment["filename"];
                $filenameparts = explode(".", $filename);
                $extension     = end($filenameparts);
                $extension     = strtolower($extension);
                if (in_array('.' . $extension, $allowed_extensions)) {
                    $filename = implode(array_slice($filenameparts, 0, 0 - 1));
                    $filename = trim(preg_replace("/[^a-zA-Z0-9-_ ]/", "", $filename));
                    if (!$filename) {
                        $filename = "attachment";
                    }
                    if (!file_exists($path)) {
                        mkdir($path);
                        $fp = fopen($path . 'index.html', 'w');
                        fclose($fp);
                    }
                    $filename = unique_filename($path, $filename . "." . $extension);
                    $fp       = fopen($path . $filename, "w");
                    fwrite($fp, $attachment["data"]);
                    fclose($fp);
                    array_push($ticket_attachments, array(
                        'file_name' => $filename,
                        'filetype' => get_mime_by_extension($filename)
                    ));
                }
            }
            $this->insert_ticket_attachments_to_database($ticket_attachments, $ticket_id, $reply_id);
        }
    }

    public function get($id = '', $where = array())
    {
        $this->db->select('*,tbltickets.userid,tbltickets.name as from_name,tbltickets.email as ticket_email, tbldepartments.name as department_name, tblpriorities.name as priority_name, statuscolor, tbltickets.admin, tblservices.name as service_name, service, tblticketstatus.name as status_name,tbltickets.ticketid,subject,tblcontacts.firstname as user_firstname,.tblcontacts.lastname as user_lastname,tblstaff.firstname as staff_firstname, tblstaff.lastname as staff_lastname,lastreply,message,tbltickets.status,subject,department,priority,tblcontacts.email,adminread,clientread,date,tbltickets.ip');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        $this->db->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.id = tbltickets.contactid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltickets.admin', 'left');
        $this->db->join('tblpriorities', 'tblpriorities.priorityid = tbltickets.priority', 'left');
    $this->db->where($where);
    if (is_numeric($id)) {
            $this->db->where('tbltickets.ticketid', $id);

            return $this->db->get('tbltickets')->row();
        }
        // $this->db->order_by('lastreply', 'asc');
        $this->db->order_by('tbltickets.ticketid', 'DESC');
    
    $re = $this->db->get('tbltickets')->result_array();
    return $re; 
    }

    public function get_ticket_client_side($id = '', $where = array())
    {
        $this->db->select('*,tbltickets.userid,tbltickets.name as from_name,tbltickets.email as ticket_email, tbldepartments.name as department_name, tblpriorities.name as priority_name, statuscolor, tbltickets.admin, tblservices.name as service_name, service, tblticketstatus.name as status_name,tbltickets.ticketid,subject,tblcontacts.firstname as user_firstname,.tblcontacts.lastname as user_lastname,tblstaff.firstname as staff_firstname, tblstaff.lastname as staff_lastname,lastreply,message,tbltickets.status,subject,department,priority,tblcontacts.email,adminread,clientread,date,tbltickets.ip');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        $this->db->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.id = tbltickets.contactid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltickets.admin', 'left');
        $this->db->join('tblpriorities', 'tblpriorities.priorityid = tbltickets.priority', 'left');
    $this->db->where($where);
    if (is_numeric($id)) {
            $this->db->where('tbltickets.ticketid', $id);

            return $this->db->get('tbltickets')->row();
        }
        $this->db->order_by('tbltickets.ticketid', 'DESC');
    
    $re = $this->db->get('tbltickets')->result_array();
    // echo $this->db->last_query(); exit('here');
    return $re; 
    }

  public function getclients($id = '', $where = array())
    { 
        $this->db->select('*,tbltickets.userid,tbltickets.name as from_name,tbltickets.email as ticket_email, tbldepartments.name as department_name, tblpriorities.name as priority_name, statuscolor, tbltickets.admin, tblservices.name as service_name, service, tblticketstatus.name as status_name,tbltickets.ticketid,subject,tblcontacts.firstname as user_firstname,.tblcontacts.lastname as user_lastname,tblstaff.firstname as staff_firstname, tblstaff.lastname as staff_lastname,lastreply,message,tbltickets.status,subject,department,priority,tblcontacts.email,adminread,clientread,date,tbltickets.ip');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        $this->db->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.id = tbltickets.contactid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltickets.admin', 'left');
        $this->db->join('tblpriorities', 'tblpriorities.priorityid = tbltickets.priority', 'left');
    $this->db->where($where);
    if (is_numeric($id)) {
            $this->db->where('tbltickets.ticketid', $id);

            return $this->db->get('tbltickets')->row();
        }
        $this->db->order_by('lastreply', 'asc');
    
    $re = $this->db->get('tbltickets')->result_array();
    return $re; 
    }
  
    /**
     * Get ticket by id and all data
     * @param  mixed  $id     ticket id
     * @param  mixed $userid Optional - Tickets from USER ID
     * @return object
     */
    public function get_ticket_by_id($id, $userid = '')
    {
        $this->db->select('*,tbltickets.source as source, tbltickets.userid,tbltickets.name as from_name,tbltickets.email as ticket_email, tblclients.phonenumber as merchantsPhonenumber, tbldepartments.name as department_name, tblpriorities.name as priority_name, statuscolor, tbltickets.admin, tblservices.name as service_name, service, tblticketstatus.name as status_name,tbltickets.ticketid,subject,tblcontacts.firstname as user_firstname,.tblcontacts.lastname as user_lastname,tblstaff.firstname as staff_firstname, tblstaff.lastname as staff_lastname,lastreply,message,tbltickets.status,subject,department,priority,tblcontacts.email,adminread,clientread,date,tbltickets.ip');
        $this->db->from('tbltickets');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        $this->db->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltickets.admin', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.id = tbltickets.contactid', 'left');
        $this->db->join('tblpriorities', 'tblpriorities.priorityid = tbltickets.priority', 'left');
        $this->db->where('tbltickets.ticketid', $id);
        if (is_numeric($userid)) {
            $this->db->where('tbltickets.userid', $userid);
        }
    $ticket = $this->db->get()->row();  
    if ($ticket) {
            if ($ticket->admin == null || $ticket->admin == 0) {
                if ($ticket->contactid != 0) {
                    $ticket->submitter = $ticket->user_firstname . ' ' . $ticket->user_lastname;
                } else {
                    $ticket->submitter = $ticket->from_name;
                }
            } else {
                if ($ticket->contactid != 0) {
                    $ticket->submitter = $ticket->user_firstname . ' ' . $ticket->user_lastname;
                } else {
                    $ticket->submitter = $ticket->from_name;
                }
                $ticket->opened_by = $ticket->staff_firstname . ' ' . $ticket->staff_lastname;
            }

            $ticket->attachments = $this->get_ticket_attachments($id);
        }


        return $ticket;
    }
    public function get_tickets_by_id($id )
    { 
        $this->db->select('*,tbltickets.userid,tbltickets.name as from_name,tbltickets.email as ticket_email, tbldepartments.name as department_name, tblpriorities.name as priority_name, statuscolor, tbltickets.admin, tblservices.name as service_name, service, tblticketstatus.name as status_name,tbltickets.ticketid,subject,tblcontacts.firstname as user_firstname,.tblcontacts.lastname as user_lastname,tblstaff.firstname as staff_firstname, tblstaff.lastname as staff_lastname,lastreply,message,tbltickets.status,subject,department,priority,tblcontacts.email,adminread,clientread,date,tbltickets.ip');
        $this->db->from('tbltickets');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        $this->db->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltickets.admin', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.id = tbltickets.contactid', 'left');
        $this->db->join('tblpriorities', 'tblpriorities.priorityid = tbltickets.priority', 'left');
        $this->db->where('tbltickets.ticketid', $id);
         
    if (is_numeric($userid)) {
            $this->db->where('tbltickets.userid', $userid);
        }
    
    $ticket = $this->db->get()->row();
    if ($ticket) {
            if ($ticket->admin == null || $ticket->admin == 0) {
                if ($ticket->contactid != 0) {
                    $ticket->submitter = $ticket->user_firstname . ' ' . $ticket->user_lastname;
                } else {
                    $ticket->submitter = $ticket->from_name;
                }
            } else {
                if ($ticket->contactid != 0) {
                    $ticket->submitter = $ticket->user_firstname . ' ' . $ticket->user_lastname;
                } else {
                    $ticket->submitter = $ticket->from_name;
                }
                $ticket->opened_by = $ticket->staff_firstname . ' ' . $ticket->staff_lastname;
            }

            $ticket->attachments = $this->get_ticket_attachments($id);
        }


        return $ticket;
    }

  
    /**
     * Insert ticket attachments to database
     * @param  array  $attachments array of attachment
     * @param  mixed  $ticketid
     * @param  boolean $replyid If is from reply
     */
    public function insert_ticket_attachments_to_database($attachments, $ticketid, $replyid = false)
    {
        foreach ($attachments as $attachment) {
            $attachment['ticketid']  = $ticketid;
            $attachment['dateadded'] = date('Y-m-d H:i:s');
            if ($replyid !== false && is_int($replyid)) {
                $attachment['replyid'] = $replyid;
            }
            $this->db->insert('tblticketattachments', $attachment);
        }
    }

    /**
     * Get ticket attachments from database
     * @param  mixed $id      ticket id
     * @param  mixed $replyid Optional - reply id if is from from reply
     * @return array
     */
    public function get_ticket_attachments($id, $replyid = '')
    {
        $this->db->where('ticketid', $id);
        if (is_numeric($replyid)) {
            $this->db->where('replyid', $replyid);
        } else {
            $this->db->where('replyid', null);
        }
        $this->db->where('ticketid', $id);

        return $this->db->get('tblticketattachments')->result_array();
    }

    /**
     * Add new reply to ticket
     * @param mixed $data  reply $_POST data
     * @param mixed $id    ticket id
     * @param boolean $admin staff id if is staff making reply
     */
    public function add_reply($data, $id, $admin = null, $pipe_attachments = false)
    { 
		//echo "<pre>"; print_r($data); exit('here');
       if(isset($data['description'])){
        unset($data['description']);
       }
        if (isset($data['assign_to_current_user'])) {
            $assigned = get_staff_user_id();
            unset($data['assign_to_current_user']);
        }
        $unsetters = array(
            'note_description',
            'department',
            'priority',
            'subject',
            'assigned',
            'project_id',
            'service',
            'status_top',
            'attachments',
            'DataTables_Table_0_length',
            'DataTables_Table_1_length',
            'custom_fields', 
      'knowledgeBase'
        );
        foreach ($unsetters as $unset) {
            if (isset($data[$unset])) {
                unset($data[$unset]);
            }
        }
        if ($admin !== null) {
            $data['admin'] = $admin;
            $status        = $data['status'];
        } else {
            $status = 1;
        }
        
        if (isset($data['status'])) {
            unset($data['status']);
        }
        $email_contact_ids = array();
        if(isset($data['email_contacts_ids']))
        {
            $email_contact_ids[] = $data['email_contacts_ids'];
            unset($data['email_contacts_ids']);
        }
        $cc = '';
        if (isset($data['cc'])) {
            $cc = $data['cc'];
            unset($data['cc']);
        }

        $data['ticketid'] = $id;
        $data['date']     = date('Y-m-d H:i:s');
        $data['ip']       = $this->input->ip_address();
        $data['message']  = trim($data['message']);

        if ($this->piping == true) {
            $data['message'] = preg_replace('/\v+/u', '<br>', $data['message']);
        }

        // adminn can have html
        if ($admin == null) {
            $data['message'] = _strip_tags($data['message']);
            $data['message'] = nl2br_save_html($data['message']);
        }
        if (!isset($data['userid'])) {
            $data['userid'] = 0;
        }
        if (isset($data['status_show_or_not'])) {
            $status_show_or_not = $data['status_show_or_not'];
            unset($data['status_show_or_not']);
        }
        if (is_client_logged_in()) {
            $data['contactid'] = get_contact_user_id();
        }
     
      
      $_data = do_action('before_ticket_reply_add', array(
            'data' => $data,
            'id' => $id,
            'admin' => $admin
        ));
   // print_r($data); exit('kokok');
    $insert_id =  $this->db->insert('tblticketreplies', $data); 
        // echo $this->db->last_query(); 
        // print_r($insert_id); 
        // exit('whyyuy');      
        
    if ($insert_id) {
            if (isset($assigned)) {
                $this->db->where('ticketid', $id);
                $this->db->update('tbltickets', array(
                    'assigned' => $assigned
                ));
            }
            if ($pipe_attachments != false) {
                $this->process_pipe_attachments($pipe_attachments, $id, $insert_id);
            } else {
                $attachments = handle_ticket_attachments($id);
                if ($attachments) {
                    $this->tickets_model->insert_ticket_attachments_to_database($attachments, $id, $insert_id);
                }
            }

            $_attachments = $this->get_ticket_attachments($id, $insert_id);

            logActivity('New Ticket Reply [ReplyID: ' . $insert_id . ']');

            $this->db->select('status');
            $this->db->where('ticketid', $id);
            $old_ticket_status = $this->db->get('tbltickets')->row()->status;

                 if($data['email_reply'] !=''){
                    $historydata=array(
                          'ticket_id'=>$id,
                          'ticket_status'=>$this->input->post('status'),
                          'ticket_description'=>$data['message'],
                          'status_show_or_not'=>'0',
                          'email_sended' => 1,
                          'user_id'=>$this->session->userdata('staff_user_id'),
						  'created_at'=>date('Y-m-d h:i:s')	
                        );
                      }
                    else{
                       $historydata=array(
                          'ticket_id'=>$id,
                          'ticket_status'=>$this->input->post('status'),
                          'ticket_description'=>$data['message'],
                          'status_show_or_not'=>'0',
                          'email_sended' => 0,
                          'user_id'=>$this->session->userdata('staff_user_id'),
						   'created_at'=>date('Y-m-d h:i:s')
                        ); 
                    }  
                $this->db->select("status");
                $this->db->from('tbltickets');
                $this->db->where('ticketid', $id);
                $before_status = $this->db->get()->row()->status;
                if(isset($before_status) && $before_status != $status ){
                    $this->db->insert('tblticket_status_history', $historydata);
                }
                // echo $status ; echo "thu"; print_r($before_status);
                // exit('here');
             
        
        ///ab////


            $this->db->where('ticketid', $id);
            $this->db->update('tbltickets', array(
                'lastreply' => date('Y-m-d H:i:s'),
                'status' => $status,
                'adminread' => 0,
                'clientread' => 0
            ));
            

      
      ///my sms code start here
      $this->sms_reply($insert_id); 
      ///my sms code end
    
            if ($old_ticket_status != $status) {
                do_action('after_ticket_status_changed', array(
                    'id' => $id,
                    'status' => $status
                ));
            }
      //here
      
      //$cids = $this->rad_con_ch($id);
      if(!empty($data['email_reply'])){
        $cids = $data['email_reply'];
        $explod_cids = explode(',', $cids);
      //print_r($explod_cids); echo "hel"; 
      //echo "<pre>"; print_r($data); echo "hu"; echo $id;  exit('i am here');
    foreach ($explod_cids as $cid) {
        
        $rad_data = $this->rad_check($cid, 'email_alert_support');
          if($rad_data[0]['value']==1||empty($rad_data))
          {   
            ///
            $this->load->model('emails_model');
            $ticket = $this->get_ticket_by_id($id);
          //echo $ticket->userid; echo "jii"; echo $cid; echo "hel"; echo "<pre>"; print_r($data); echo "hu"; echo $id;  exit('i am here');
            $userid = $ticket->userid;
            if ($ticket->userid != 0 && $ticket->contactid != 0) {
                $email = $this->clients_model->get_contact($cid)->email; 
                //echo $email; 
                //echo "id"; 
                //echo $ticket->userid; echo "jii"; echo $cid; echo "hel"; echo "<pre>"; print_r($data); echo "hu"; echo $id;  exit('i am here');
              //$email = $this->clients_model->get_contact($ticket->contactid)->email;
            } else {
                //echo "this"; echo $ticket->userid; echo "jii"; echo $cid; echo "hel"; echo "<pre>"; print_r($data); echo "hu"; echo $id;  exit('i am here');
              $email = $ticket->ticket_email;
            }
            if ($admin == null) {
              $this->load->model('departments_model');
              $this->load->model('staff_model');
              $staff = $this->staff_model->get('', 1);
              foreach ($staff as $member) {
                if (get_option('access_tickets_to_none_staff_members') == 0 && !is_staff_member($member['staffid'])) {
                  continue;
                }


                $staff_departments = $this->departments_model->get_staff_departments($member['staffid'], true);
                if (in_array($ticket->department, $staff_departments)) {
                  foreach ($_attachments as $at) {
                    $this->emails_model->add_attachment(array(
                      'attachment' => get_upload_path_by_type('ticket') . $id . '/' . $at['file_name'],
                      'filename' => $at['file_name'],
                      'type' => $at['filetype'],
                      'read' => true
                    ));
                  }

                  $merge_fields = array();
                  $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($ticket->userid, $ticket->contactid));
                  $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('ticket-reply-to-admin', $id));
                  $this->emails_model->send_email_template('ticket-reply-to-admin', $member['email'], $merge_fields, $id);
                }
              }
            } else {
              $merge_fields = array();
              $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($ticket->userid, $ticket->contactid));
              $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('ticket-reply', $id));

              foreach ($_attachments as $at) {
                $this->emails_model->add_attachment(array(
                  'attachment' => get_upload_path_by_type('ticket') . $id . '/' . $at['file_name'],
                  'filename' => $at['file_name'],
                  'type' => $at['filetype'],
                  'read' => true
                ));
              }
              $this->emails_model->send_email_template('ticket-reply', $email, $merge_fields, $id, $cc);
              }
          }
        }
        }
		        //form here
        $data_reply =  $this->get($data['ticketid']);   
        if($data_reply->assigned){
            $replay_assignee = explode(',', $data_reply->assigned); 
            // echo "<pre>";  print_r($replay_assignee);  exit('jiji'); 
            foreach ($replay_assignee as $rep_assig){
                    $merge_fields = array();
                    $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($data['userid'], $data['contactid']));
                    $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('ticket-reply-to-admin',$data['ticketid'] ));
                    $this->db->where('staffid', $rep_assig);
                    $assignedEmail = $this->db->get('tblstaff')->row()->email;
                    // echo "<pre>"; print_r($merge_fields) ; print_r($assignedEmail);  exit('jiji'); 
                    $this->load->model('emails_model');
                    $this->emails_model->send_email_template('ticket-reply-to-admin', $assignedEmail, $merge_fields,  $data['ticketid'], '', $flag);
                                
            }

        }

        if($data_reply->followers){
            $replay_followers = explode(',', $data_reply->followers); 
            // echo "<pre>";  print_r($replay_followers);  exit('jiji'); 
            foreach ($replay_followers as $rep_follo){
                    $followers_merge_fields = array();
                    $followers_merge_fields = array_merge($followers_merge_fields, get_client_contact_merge_fields($data['userid'], $data['contactid']));
                    $followers_merge_fields = array_merge($followers_merge_fields, get_ticket_merge_fields('ticket-reply-to-admin', $data['ticketid']));
                    $this->db->where('staffid', $rep_follo);
                    $followersEmail = $this->db->get('tblstaff')->row()->email;
                    $this->load->model('emails_model');
                    $this->emails_model->send_email_template('ticket-reply-to-admin',$followersEmail , $followers_merge_fields, $data['ticketid']);
            }

        }
            do_action('after_ticket_reply_added', array(
                'data' => $data,
                'id' => $id,
                'admin' => $admin,
                'replyid' => $insert_id
            ));

            return $insert_id;
        }

        return false;
    }

    /**
     *  Delete ticket reply
     * @param   mixed $ticket_id    ticket id
     * @param   mixed $reply_id     reply id
     * @return  boolean
     */
    public function delete_ticket_reply($ticket_id, $reply_id)
    {
        $this->db->where('id', $reply_id);
        $this->db->delete('tblticketreplies');
        if ($this->db->affected_rows() > 0) {
            // Get the reply attachments by passing the reply_id to get_ticket_attachments method
            $attachments = $this->get_ticket_attachments($ticket_id, $reply_id);
            if (count($attachments) > 0) {
                foreach ($attachments as $attachment) {
                    if (unlink(get_upload_path_by_type('ticket') . $ticket_id . '/' . $attachment['file_name'])) {
                        $this->db->where('id', $attachment['id']);
                        $this->db->delete('tblticketattachments');
                    }
                }
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('ticket') . $ticket_id);
                if (count($other_attachments) == 0) {
                    delete_dir(get_upload_path_by_type('ticket') . $ticket_id);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * This functions is used when staff open client ticket
     * @param  mixed $userid client id
     * @param  mixed $id     ticketid
     * @return array
     */
    public function get_user_other_tickets($userid, $id)
    {
        $this->db->select('tbldepartments.name as department_name, tblservices.name as service_name,tblticketstatus.name as status_name,tblstaff.firstname as staff_firstname, tblclients.lastname as staff_lastname,ticketid,subject,firstname,lastname,lastreply');
        $this->db->from('tbltickets');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        $this->db->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltickets.admin', 'left');
        $this->db->where('tbltickets.userid', $userid);
        $this->db->where('tbltickets.ticketid !=', $id);
        $tickets = $this->db->get()->result_array();
        $i       = 0;
        foreach ($tickets as $ticket) {
            $tickets[$i]['submitter'] = $ticket['firstname'] . ' ' . $ticket['lastname'];
            unset($ticket['firstname']);
            unset($ticket['lastname']);
            $i++;
        }

        return $tickets;
    }

    /**
     * Get all ticket replies
     * @param  mixed  $id     ticketid
     * @param  mixed $userid specific client id
     * @return array
     */
    public function get_ticket_replies($id)
    {
        $ticket_replies_order = get_option('ticket_replies_order');
        // backward compatibility for the action hook
        $ticket_replies_order = do_action('ticket_replies_order', $ticket_replies_order);

        $this->db->select('tblticketreplies.id,tblticketreplies.ip,tblticketreplies.name as from_name,tblticketreplies.email_reply as reply_email, tblticketreplies.admin, tblticketreplies.userid,tblstaff.firstname as staff_firstname,.tblstaff.lastname as staff_lastname,tblcontacts.firstname as user_firstname,.tblcontacts.lastname as user_lastname,message,date,contactid');
        $this->db->from('tblticketreplies');
        $this->db->join('tblclients', 'tblclients.userid = tblticketreplies.userid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblticketreplies.admin', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.id = tblticketreplies.contactid', 'left');
        $this->db->where(array('ticketid'=> $id, 'pin_to_top' => 0));
        $this->db->order_by('date', $ticket_replies_order);
        $replies = $this->db->get()->result_array();
        $i       = 0;
        foreach ($replies as $reply) {
            if ($reply['admin'] !== null || $reply['admin'] != 0) {
                // staff reply
                $replies[$i]['submitter'] = $reply['staff_firstname'] . ' ' . $reply['staff_lastname'];
            } else {
                if ($reply['contactid'] != 0) {
                    $replies[$i]['submitter'] = $reply['user_firstname'] . ' ' . $reply['user_lastname'];
                } else {
                    $replies[$i]['submitter'] = $reply['from_name'];
                }
            }
            unset($replies[$i]['staff_firstname']);
            unset($replies[$i]['staff_lastname']);
            unset($replies[$i]['user_firstname']);
            unset($replies[$i]['user_lastname']);
            $replies[$i]['attachments'] = $this->get_ticket_attachments($id, $reply['id']);
            $i++;
        }

        return $replies;
    }
    public function get_ticket_body($id = ''){
        $this->db->select('*');
        $this->db->from('tbltickets');
        $this->db->where('ticketid', $id);
        // $this->db->limit(1);
        $result = $this->db->get()->result_array();
        return $result[0]; 
    }

    /**
     * Add new ticket to database
     * @param mixed $data  ticket $_POST data
     * @param mixed $admin If admin adding the ticket passed staff id
     */
    public function add($data, $admin = null, $pipe_attachments = false)
    { 

        if ($admin !== null) {
            $data['admin'] = $admin;
            unset($data['ticket_client_search']);
        }
        if (isset($data['assigned']) && $data['assigned'] == '') {
            $data['assigned'] = 0;
        }

        if (isset($data['project_id']) && $data['project_id'] == '') {
            $data['project_id'] = 0;
        }
    //bilal code start
    if (isset($data['saleopp_id']) && $data['saleopp_id'] == '') {
            $data['saleopp_id'] = 0;
        }
    if (isset($data['saleopp_id']) && $data['saleopp_id'] != '') {
            $data['saleopp_id'] = $data['saleopp_id'];
        }
    //bilal code end
    
    
        if ($admin == null) {
            if (isset($data['email'])) {
                $data['userid']    = 0;
                $data['contactid'] = 0;
            } else {
                // Opened from customer portal otherwise is passed from pipe or admin area
        if (!isset($data['userid']) && !isset($data['contactid'])) {
          $data['userid']    = get_client_user_id();
                    $data['contactid'] = get_contact_user_id();
                }
        if (!isset($data['contactid'])) { 
                    $data['contactid'] = get_contact_user_id();
                }
            }
            //sajid
            $data['status'] = $data['status'];
            // $data['status'] = 1;
        }


        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        // CC is only from admin area
        //$cc = '';
        //if (isset($data['cc'])) {
        //    $cc = $data['cc'];
        //    unset($data['cc']);
        //}
		
		//if ($data['cc'] != '') {
        //    $data['cc'] = implode(',',$data['cc']);
        //}
		//echo "<pre>"; print_r($data); exit('kokoko'); 

        $data['date']      = date('Y-m-d H:i:s');
        $data['ticketkey'] = md5(uniqid(time(), true));
        $data['status']    = $data['status'];
        $data['message']   = trim($data['message']);
        $data['subject']   = trim($data['subject']);
        if ($this->piping == true) {
            $data['message'] = preg_replace('/\v+/u', '<br>', $data['message']);
        }
        // Admin can have html
        if ($admin == null) {
            $data['message'] = _strip_tags($data['message']);
            $data['subject'] = _strip_tags($data['subject']);
            $data['message'] = nl2br_save_html($data['message']);
        }
        if (!isset($data['userid'])) {
            $data['userid'] = 0;
        }
        
        if(isset($data['notify_ticket_body'])){
            $flag = $data['notify_ticket_body'];
        }
        else{
            $flag = '';                
        }
        
        if (isset($data['priority']) && $data['priority'] == '' || !isset($data['priority'])) {
            $data['priority'] = 0;
        }


        $tags = '';
        if (isset($data['tags'])) {
            $tags  = $data['tags'];
            unset($data['tags']);
        }

        $data['ip'] = $this->input->ip_address();
        $_data      = do_action('before_ticket_created', array(
            'data' => $data,
            'admin' => $admin
        ));
        $data       = $_data['data'];
        // new code by zeeshan start
        if(isset($data['followers'])){
            $data['followers'] = implode(",", $data['followers']);           
        }
        

        $data['assigned'] = implode(",", $data['assigned']);
        // echo "<pre>"; print_r($data); exit('here');  
        // new code by zeeshan end
        $this->db->insert('tbltickets', $data); 
		$ticketid = $this->db->insert_id();
        if($ticketid) {
			$cc = explode(',',$data['cc']);
            ////ab////
            $historydata=array(
            'ticket_id'=>$ticketid,
            'ticket_status'=>$data['status'],
            'ticket_description'=>$data['message'],
            'created_at'=>date('Y-m-d')
            );

          // $this->db->insert('tblticket_status_history', $historydata);
          $this->sms_add($ticketid); 
                handle_tags_save($tags, $ticketid, 'ticket');
                if (isset($custom_fields)) {
                    handle_custom_fields_post($ticketid, $custom_fields);
                }
          $cid = $this->rad_con_ch($ticketid);
          $rad_data = $this->rad_check($cid, 'email_alert_support');
          if($rad_data[0]['value']==1||empty($rad_data)){
            
            $this->load->model('emails_model');

            if (isset($data['assigned']) && $data['assigned'] != 0) {
                 
                $assigned = explode(',', $data['assigned']);
                
                foreach ($assigned as $assignedval) {
                        // echo "<pre>"; print_r($assignedval); exit('jjiji'); 
                        if ($assignedval != get_staff_user_id()) {
                            $notified = add_notification(array(
                                'description' => 'not_ticket_assigned_to_you',
                                'touserid' => $assignedval,
                                'fromcompany' => 1,
                                'fromuserid' => null,
                                'link' => 'tickets/ticket/' . $ticketid,
                                'additional_data' => serialize(array(
                                    $data['subject']
                                ))
                            ));

                            $merge_fields = array();
                            $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($data['userid'], $data['contactid']));
                            $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('ticket-assigned-to-admin', $ticketid,'', $flag));
                            // $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('new-ticket-opened-admin', $ticketid));  
                            
                            $this->db->where('staffid', $assignedval);
                            $assignedEmail = $this->db->get('tblstaff')->row()->email;
                            
                            if(!empty($flag))
                                {
                                    // echo $flag;  exit('sskos');
                                    $this->emails_model->send_email_template('ticket-assigned-to-admin', $assignedEmail, $merge_fields, $ticketid, $cc, $flag);
                                }
                            else{
                                // echo $flag;  exit('sss');
                                    //$this->load->helper('tickets_helper');
                                     $this->emails_model->send_email_template('ticket-assigned-to-admin', $assignedEmail, $merge_fields, $ticketid, $cc, 0);
                                 }
                            // $this->emails_model->send_email_template('ticket-assigned-to-admin', $assignedEmail, $merge_fields, $ticketid);
                            if ($notified) {
                                // pusher_trigger_notification(array($data['assigned']));
                                pusher_trigger_notification($assignedval);
                                // echo "<pre>"; print_r($assignedval); exit('jjiji'); 
                                // pusher_trigger_notification(array($data['assigned']));
                            }
                        }
                    }
                }
                if (isset($data['followers']) && $data['followers'] != 0) {
                    $followers = explode(',', $data['followers']);
                    foreach ($followers as $followersval) {
                        $followers_merge_fields = array();
                        $sa = get_client_contact_merge_fields($data['userid'], $data['contactid']);
                            // echo "<pre>"; print_r($sa);  exit('i am ');
                            $followers_merge_fields = array_merge($followers_merge_fields, get_client_contact_merge_fields($data['userid'], $data['contactid']));
                            //$merge_fields = array_merge($merge_fields, get_ticket_merge_fields('ticket-assigned-to-admin', $ticketid));
                            // echo "<pre>"; print_r($followers_merge_fields);  exit('i m ');
                            $followers_merge_fields = array_merge($followers_merge_fields, get_ticket_merge_fields('new-ticket-open-followers', $ticketid));  
                            // echo "<pre>"; print_r($followers_merge_fields);  exit('i m ');
                            $this->db->where('staffid', $followersval);
                            $followersEmail = $this->db->get('tblstaff')->row()->email;
                            
                                    $this->emails_model->send_email_template('new-ticket-open-followers', $followersEmail, $followers_merge_fields);
                              

                    }
                    // echo "<pre>"; print_r($data);  exit('herein');     
                }
                // echo "<pre>"; print_r($data);  exit('here'); 
      
            if ($pipe_attachments != false) {
                $this->process_pipe_attachments($pipe_attachments, $ticketid);
            } else {
                $attachments = handle_ticket_attachments($ticketid);
                if ($attachments) {
                    $this->insert_ticket_attachments_to_database($attachments, $ticketid);
                }
            }

            $_attachments = $this->get_ticket_attachments($ticketid);

            // echo "<pre>"; print_r($data); exit('kokoko');  
            if (isset($data['userid']) && $data['userid'] != false) {
                $email = $this->clients_model->get_contact($data['contactid'])->email;
            } else {
                $email = $data['email'];
            }

            $template = 'new-ticket-opened-admin';
            if ($admin == null) {
                $template = 'ticket-autoresponse';

                $this->load->model('departments_model');
                $this->load->model('staff_model');
                $staff = $this->staff_model->get('', 1);

                $notifiedUsers = array();
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($data['userid'], $data['contactid']));
                $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('new-ticket-created-staff', $ticketid));

                foreach ($staff as $member) {
                    if (get_option('access_tickets_to_none_staff_members') == 0 && !is_staff_member($member['staffid'])) {
                        continue;
                    }
                    $staff_departments = $this->departments_model->get_staff_departments($member['staffid'], true);
                    if (in_array($data['department'], $staff_departments)) {
                        foreach ($_attachments as $at) {
                            $this->emails_model->add_attachment(array(
                                'attachment' => get_upload_path_by_type('ticket') . $ticketid . '/' . $at['file_name'],
                                'filename' => $at['file_name'],
                                'type' => $at['filetype'],
                                'read' => true
                            ));
                        }
                        if(!empty($flag))
                            {
                                $this->emails_model->send_email_template('new-ticket-created-staff', $member['email'], $merge_fields, $ticketid);
                            }
                        if (get_option('receive_notification_on_new_ticket') == 1) {
                            $notified = add_notification(array(
                                    'description' => 'not_new_ticket_created',
                                    'touserid' => $member['staffid'],
                                    'fromcompany' => 1,
                                    'fromuserid' => null,
                                    'link' => 'tickets/ticket/' . $ticketid,
                                    'additional_data' => serialize(array(
                                        $data['subject']
                                    ))
                                ));
                            if ($notified) {
                                array_push($notifiedUsers, $member['staffid']);
                            }
                        }
                    }
                }
                pusher_trigger_notification($notifiedUsers);
            }

            if ($admin != null) {
                // Admin opened ticket from admin area add the attachments to the email
                foreach ($_attachments as $at) {
                    $this->emails_model->add_attachment(array(
                        'attachment' => get_upload_path_by_type('ticket') . $ticketid . '/' . $at['file_name'],
                        'filename' => $at['file_name'],
                        'type' => $at['filetype'],
                        'read' => true
                    ));
                }
            }

            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($data['userid'], $data['contactid']));
            $merge_fields = array_merge($merge_fields, get_ticket_merge_fields($template, $ticketid));
                if(!empty($flag))
                    {
                       $this->emails_model->send_email_template($template, $email, $merge_fields, $ticketid, $cc);
                    }
            do_action('after_ticket_added', $ticketid);
            logActivity('New Ticket Created [ID: ' . $ticketid . ']');
      }
            return $ticketid;
        }

        return false;
    }

    /**
     * Get latest 5 client tickets
     * @param  integer $limit  Optional limit tickets
     * @param  mixed $userid client id
     * @return array
     */
    public function get_client_latests_ticket($limit = 5, $userid = '')
    {
        $this->db->select('tbltickets.userid, ticketstatusid, statuscolor, tblticketstatus.name as status_name,tbltickets.ticketid, subject, date');
        $this->db->from('tbltickets');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        if (is_numeric($userid)) {
            $this->db->where('tbltickets.userid', $userid);
        } else {
            $this->db->where('tbltickets.userid', get_client_user_id());
        }
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Delete ticket from database and all connections
     * @param  mixed $ticketid ticketid
     * @return boolean
     */
    public function delete($ticketid)
    {
       
        $affectedRows = 0;
        do_action('before_ticket_deleted', $ticketid);
        // final delete ticket
        $this->db->where('ticketid', $ticketid);
        $this->db->delete('tbltickets');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            $this->db->where('ticketid', $ticketid);
            $attachments = $this->db->get('tblticketattachments')->result_array();
            if (count($attachments) > 0) {
                if (is_dir(get_upload_path_by_type('ticket') . $ticketid)) {
                    if (delete_dir(get_upload_path_by_type('ticket') . $ticketid)) {
                        foreach ($attachments as $attachment) {
                            $this->db->where('id', $attachment['id']);
                            $this->db->delete('tblticketattachments');
                            if ($this->db->affected_rows() > 0) {
                                $affectedRows++;
                            }
                        }
                    }
                }
            }
             ///ab
            $this->db->where('ticket_id', $ticketid);
            $this->db->delete('tblticket_status_history');
            ///ab
            $this->db->where('relid', $ticketid);
            $this->db->where('fieldto', 'tickets');
            $this->db->delete('tblcustomfieldsvalues');

            // Delete replies
            $this->db->where('ticketid', $ticketid);
            $this->db->delete('tblticketreplies');

            $this->db->where('rel_id', $ticketid);
            $this->db->where('rel_type', 'ticket');
            $this->db->delete('tblnotes');

            $this->db->where('rel_id', $ticketid);
            $this->db->where('rel_type', 'ticket');
            $this->db->delete('tbltags_in');

            // Get related tasks
            $this->db->where('rel_type', 'ticket');
            $this->db->where('rel_id', $ticketid);
            $tasks = $this->db->get('tblstafftasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }
        }
        if ($affectedRows > 0) {
            logActivity('Ticket Deleted [ID: ' . $ticketid . ']');

            return true;
        }

        return false;
    }

    /**
     * Update ticket data / admin use
     * @param  mixed $data ticket $_POST data
     * @return boolean
     */
    public function update_single_ticket_settings($data)
    {
        $affectedRows = 0;
        $data         = do_action('before_ticket_settings_updated', $data);

        $ticketBeforeUpdate = $this->get_ticket_by_id($data['ticketid']);

        if (isset($data['custom_fields']) && count($data['custom_fields']) > 0) {
            if (handle_custom_fields_post($data['ticketid'], $data['custom_fields'])) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        if (handle_tags_save($tags, $data['ticketid'], 'ticket')) {
            $affectedRows++;
        }

        if (isset($data['priority']) && $data['priority'] == '' || !isset($data['priority'])) {
            $data['priority'] = 0;
        }

        if ($data['assigned'] == '') {
            $data['assigned'] = 0;
        }

        if (isset($data['project_id']) && $data['project_id'] == '') {
            $data['project_id'] = 0;
        }
        if(isset($data['followers'])){
            $data['followers'] = implode(",", $data['followers']);
        }
		else{
            $data['followers'] = '';   
        }
        //print_r($followers);exit('11111');
        $data['assigned'] = implode(",", $data['assigned']);
        $data['contactid'] = implode(",", $data['contactid']);

        //if(isset($data['cc'])){
        //    unset($data['cc']);
        //}
		if(!isset($data['cc']) || $data['cc'] == ''){
            $data['cc'] = '';
        }
        //echo "<pre>"; print_r($followers); exit('waqas');

        //$follower_data = $this->update_followers_data($data['ticketid'], $followers);
        //echo "<pre>";print_r($data); exit('hiiiii');

        $this->db->where('ticketid', $data['ticketid']);
        $this->db->update('tbltickets', $data);
        if ($this->db->affected_rows() > 0) {
                $historydata=array(
                'ticket_status'=>$data['status'],
                'ticket_description'=>$data['message']
                );
            $this->db->where('ticket_id',$data['ticketid']);
            $this->db->update('tblticket_status_history', $historydata);
            
            do_action('ticket_settings_updated',
            array(
                'ticket_id'=>$data['ticketid'],
                'original_ticket'=>$ticketBeforeUpdate,
                'data'=>$data)
            );
            $affectedRows++;
        }

        $sendAssignedEmail = false;

        $current_assigned = $ticketBeforeUpdate->assigned;
        if ($current_assigned != 0) {
            if ($current_assigned != $data['assigned']) {
                if ($data['assigned'] != 0 && $data['assigned'] != get_staff_user_id()) {
                    $sendAssignedEmail = true;
                    $notified = add_notification(array(
                        'description' => 'not_ticket_reassigned_to_you',
                        'touserid' => $data['assigned'],
                        'fromcompany' => 1,
                        'fromuserid' => null,
                        'link' => 'tickets/ticket/' . $data['ticketid'],
                        'additional_data' => serialize(array(
                            $data['subject']
                        ))
                    ));
                    if ($notified) {
                        pusher_trigger_notification(array($data['assigned']));
                    }
                }
            }
        } else {
            if ($data['assigned'] != 0 && $data['assigned'] != get_staff_user_id()) {
                $sendAssignedEmail = true;
                $notified = add_notification(array(
                    'description' => 'not_ticket_assigned_to_you',
                    'touserid' => $data['assigned'],
                    'fromcompany' => 1,
                    'fromuserid' => null,
                    'link' => 'tickets/ticket/' . $data['ticketid'],
                    'additional_data' => serialize(array(
                        $data['subject']
                    ))
                ));

                if ($notified) {
                    pusher_trigger_notification(array($data['assigned']));
                }
            }
        }
        if ($sendAssignedEmail === true) {
            $this->load->model('emails_model');
            $merge_fields = array();

            $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($data['userid'], $data['contactid']));
            $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('ticket-assigned-to-admin', $data['ticketid']));

            $this->db->where('staffid', $data['assigned']);
            $assignedEmail = $this->db->get('tblstaff')->row()->email;
            $this->emails_model->send_email_template('ticket-assigned-to-admin', $assignedEmail, $merge_fields, $data['ticketid']);
        }
        if ($affectedRows > 0) {
            logActivity('Ticket Updated [ID: ' . $data['ticketid'] . ']');

            return true;
        }

        return false;
    }

    /**
     * C<ha></ha>nge ticket status
     * @param  mixed $id     ticketid
     * @param  mixed $status status id
     * @return array
     */
    public function change_ticket_status($id, $status)
    {
        $this->db->where('ticketid', $id);
        $this->db->update('tbltickets', array(
            'status' => $status
        ));
        $alert   = 'warning';
        $message = _l('ticket_status_changed_fail');
        if ($this->db->affected_rows() > 0) {
            $alert   = 'success';
            $message = _l('ticket_status_changed_successfully');
            do_action('after_ticket_status_changed', array(
                'id' => $id,
                'status' => $status
            ));
        }

        return array(
            'alert' => $alert,
            'message' => $message
        );
    }

    // Priorities

    /**
     * Get ticket priority by id
     * @param  mixed $id priority id
     * @return mixed     if id passed return object else array
     */
    public function get_priority($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('priorityid', $id);
      
      if($this->session->userdata('portfolio_id'))
      {
        //$this->db->where('portfolio_id', $this->session->userdata('portfolio_id')); 
      }
      
            return $this->db->get('tblpriorities')->row();
        }
  
    if($this->session->userdata('portfolio_id'))
    {
      //$this->db->where('portfolio_id', $this->session->userdata('portfolio_id')); 
    }
    
       return $this->db->get('tblpriorities')->result_array();
    }

    /**
     * Add new ticket priority
     * @param array $data ticket priority data
     */
    public function add_priority($data)
    {
        if($this->session->userdata('portfolio_id'))
      {
        $data['portfolio_id'] = $this->session->userdata('portfolio_id'); 
      }
    $this->db->insert('tblpriorities', $data);
        $insert_id = $this->db->insert_id();
        
    if ($insert_id) {
            logActivity('New Ticket Priority Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }

        return $insert_id;
    }

    /**
     * Update ticket priority
     * @param  array $data ticket priority $_POST data
     * @param  mixed $id   ticket priority id
     * @return boolean
     */
    public function update_priority($data, $id)
    {
        $this->db->where('priorityid', $id);
        $this->db->update('tblpriorities', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Priority Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete ticket priorit
     * @param  mixed $id ticket priority id
     * @return mixed
     */
    public function delete_priority($id)
    {
        $current = $this->get($id);
        // Check if the priority id is used in tbltickets table
        if (is_reference_in_table('priority', 'tbltickets', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('priorityid', $id);
        $this->db->delete('tblpriorities');
        if ($this->db->affected_rows() > 0) {
            if (get_option('email_piping_default_priority') == $id) {
                update_option('email_piping_default_priority', '');
            }
            logActivity('Ticket Priority Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    // Predefined replies

    /**
     * Get predefined reply  by id
     * @param  mixed $id predefined reply id
     * @return mixed if id passed return object else array
     */
    public function get_predefined_reply($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
      if($this->session->userdata('portfolio_id'))
      {
        $this->db->where('portfolio_id', $this->session->userdata('portfolio_id')); 
      }
            return $this->db->get('tblpredefinedreplies')->row();
        }
    
    if($this->session->userdata('portfolio_id'))
    {
      $this->db->where('portfolio_id', $this->session->userdata('portfolio_id')); 
    }

        return $this->db->get('tblpredefinedreplies')->result_array();
    }

    /**
     * Add new predefined reply
     * @param array $data predefined reply $_POST data
     */
    public function add_predefined_reply($data)
    {
    if($this->session->userdata('portfolio_id'))
    {
      $data['portfolio_id'] = $this->session->userdata('portfolio_id'); 
    }   
    $this->db->insert('tblpredefinedreplies', $data);
        $insertid = $this->db->insert_id();
        logActivity('New Predefined Reply Added [ID: ' . $insertid . ', ' . $data['name'] . ']');

        return $insertid;
    }

    /**
     * Update predefined reply
     * @param  array $data predefined $_POST data
     * @param  mixed $id   predefined reply id
     * @return boolean
     */
    public function update_predefined_reply($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblpredefinedreplies', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Predefined Reply Updated [ID: ' . $id . ', ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete predefined reply
     * @param  mixed $id predefined reply id
     * @return boolean
     */
    public function delete_predefined_reply($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblpredefinedreplies');
        if ($this->db->affected_rows() > 0) {
            logActivity('Predefined Reply Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    // Ticket statuses

    /**
     * Get ticket status by id
     * @param  mixed $id status id
     * @return mixed     if id passed return object else array
     */
    public function get_ticket_status($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('ticketstatusid', $id);
      
      //if($this->session->userdata('portfolio_id'))
    //  {
    //    $this->db->where( 'portfolio_id' , $this->session->userdata('portfolio_id')); 
    //  } 
            return $this->db->get('tblticketstatus')->row();
        }
  //  if($this->session->userdata('portfolio_id'))
  //  {
  //    $this->db->where( 'portfolio_id' , $this->session->userdata('portfolio_id')); 
  //  } 
        $this->db->order_by('statusorder', 'asc');
    return $this->db->get('tblticketstatus')->result_array();
    }

    /**
     * Add new ticket status
     * @param array ticket status $_POST data
     * @return mixed
     */
    public function add_ticket_status($data)
    {
    if($this->session->userdata('portfolio_id'))
    {
      $data['portfolio_id'] = $this->session->userdata('portfolio_id'); 
    } 
    $this->db->insert('tblticketstatus', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Ticket Status Added [ID: ' . $insert_id . ', ' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update ticket status
     * @param  array $data ticket status $_POST data
     * @param  mixed $id   ticket status id
     * @return boolean
     */
    public function update_ticket_status($data, $id)
    {
        $this->db->where('ticketstatusid', $id);
        $this->db->update('tblticketstatus', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Status Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete ticket status
     * @param  mixed $id ticket status id
     * @return mixed
     */
    public function delete_ticket_status($id)
    {
        $current = $this->get_ticket_status($id);
        // Default statuses cant be deleted
        if ($current->isdefault == 1) {
            return array(
                'default' => true
            );
            // Not default check if if used in table
        } elseif (is_reference_in_table('status', 'tbltickets', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('ticketstatusid', $id);
        $this->db->delete('tblticketstatus');
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Status Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    // Ticket services
    public function get_service($id = '')
    {
    if (is_numeric($id)) {
            $this->db->where('serviceid', $id);
      if($this->session->userdata('portfolio_id'))
      {
        $this->db->where('portfolio_id' , $this->session->userdata('portfolio_id'));  
      } 
            return $this->db->get('tblservices')->row();
        }
    if($this->session->userdata('portfolio_id'))
    {
      $this->db->where('portfolio_id' , $this->session->userdata('portfolio_id'));  
    }
        $this->db->order_by('serviceid', 'asc');

        return $this->db->get('tblservices')->result_array();
    }

    public function add_service($data)
    {
        if($this->session->userdata('portfolio_id'))
    {
      $data['portfolio_id'] = $this->session->userdata('portfolio_id'); 
    }
    $this->db->insert('tblservices', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Ticket Service Added [ID: ' . $insert_id . '.' . $data['name'] . ']');
        }

        return $insert_id;
    }

    public function update_service($data, $id)
    {
        $this->db->where('serviceid', $id);
        $this->db->update('tblservices', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Service Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function delete_service($id)
    {
        if (is_reference_in_table('service', 'tbltickets', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('serviceid', $id);
        $this->db->delete('tblservices');
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Service Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @return array
     * Used in home dashboard page
     * Displays weekly ticket openings statistics (chart)
     */
    public function get_weekly_tickets_opening_statistics()
    {
        $departments_ids = array();
        if (!is_admin()) {
            if (get_option('staff_access_only_assigned_departments') == 1) {
                $this->load->model('departments_model');
                $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                $departments_ids = array();
                if (count($staff_deparments_ids) == 0) {
                    $departments = $this->departments_model->get();
                    foreach ($departments as $department) {
                        array_push($departments_ids, $department['departmentid']);
                    }
                } else {
                    $departments_ids = $staff_deparments_ids;
                }
            }
        }

        $chart   = array(
            'labels' => get_weekdays(),
            'datasets' => array(
                array(
                    'label' => _l('home_weekend_ticket_opening_statistics'),
                    'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
                    'borderColor' => '#c53da9',
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => array(
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0
                    )
                )
            )
        );

        $monday = new DateTime(date('Y-m-d', strtotime('monday this week')));
        $sunday = new DateTime(date('Y-m-d', strtotime('sunday this week')));

        $thisWeekDays = get_weekdays_between_dates($monday, $sunday);

        $byDepartments = count($departments_ids) > 0;
        if (isset($thisWeekDays[1])) {
            $i = 0;
            foreach ($thisWeekDays[1] as $weekDate) {
                $this->db->like('DATE(date)', $weekDate, 'after');
                if ($byDepartments) {
                    $this->db->where('department IN (SELECT departmentid FROM tblstaffdepartments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="'.get_staff_user_id().'")');
                }
        $this->db->where('tbltickets.portfolio_id', $this->session->userdata('portfolio_id')); 
                $chart['datasets'][0]['data'][$i] = $this->db->count_all_results('tbltickets');

                $i++;
            }
        }
        return $chart;
    }

    public function add_spam_filter($data)
    {
        if($this->session->userdata('portfolio_id'))
    {
      $data['portfolio_id'] = $this->session->userdata('portfolio_id'); 
    }
    
    $this->db->insert('tblticketsspamcontrol', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return true;
        }

        return false;
    }

    public function edit_spam_filter($data)
    {
        $this->db->where('id', $data['id']);
        unset($data['id']);
        $this->db->update('tblticketsspamcontrol', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function delete_spam_filter($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblticketsspamcontrol');
        if ($this->db->affected_rows() > 0) {
            logActivity('Tickets Spam Filter Deleted');

            return true;
        }

        return false;
    }

    public function get_tickets_assignes_disctinct()
    {
        $this->db->select('assigned');
        $this->db->from('tbltickets');
        $this->db->where('assigned !=' , 0);
        $this->db->where('portfolio_id' , $this->session->userdata('portfolio_id'));
        return $this->db->get()->result_array();
        // echo "<pre>"; print_r($s); print_r($s);  exit('kokok');   
        // echo "SELECT DISTINCT(assigned) as assigned FROM tbltickets WHERE assigned != 0 WHERE portfolio_id = " . $this->session->userdata('portfolio_id'); exit;
        // -- return $this->db->query("SELECT DISTINCT(assigned) as assigned FROM tbltickets WHERE assigned != 0")->result_array();
    }
  public function get_status($status)
  {
    $this->db->select('*');
    $this->db->from('tblticketstatus');
    $this->db->where('ticketstatusid', $status);
    return $this->db->get()->result_array();
    
  }
  
  public function get_ticket($id)
  {
    $this->db->select('status');
    $this->db->from('tbltickets');
    $this->db->where('ticketid', $id);
    return $this->db->get()->result_array();
    
  }
  public function sms_reply($id)
  {   
     
    $this->db->select('*');
    $this->db->from('tblticketreplies');
    $this->db->where('id', $id);
    $rest = $this->db->get()->result_array();   
    $ttid = $rest[0]['ticketid'];
    
    $this->db->select('*');
    $this->db->from('tbltickets');
    $this->db->where('ticketid', $ttid);
    $res = $this->db->get()->result_array();    
    

    $status = $res[0]['status'];
    
    $this->db->select('*');
    $this->db->from('tblticketstatus');
    $this->db->where('ticketstatusid', $status);
    $sta = $this->db->get()->result_array();
    // echo $this->db->last_query();
    // echo $status;
    // echo "<pre>";
    // print_r($sta); 
    // print_r($res); 
    // exit;  
    $tid = $res[0]['contactid'];
    
    $this->db->select('*');
    $this->db->from('tblrad');
    $this->db->where('userid', $tid);
    $this->db->where('name', 'sms_alert_support');
    $this->db->where('value', 1);
    $check = $this->db->get()->result_array();    
    if($check)
    {
      
      $this->db->select('*');
      $this->db->from('tblcontacts');
      $this->db->where('id', $tid);
      $res = $this->db->get()->result_array();    
      
      
      if(substr($res[0]['phonenumber'], 0, 1) == '+')
        {
          $number = $res[0]['phonenumber'];
        }
      else
        {
          $number = '+'.$res[0]['phonenumber'];
        }
      
      $msg  = 'Hello ' . $res[0]['firstname'] . ' ' . $res[0]['lastname'] . ', ' . 'your ticket has changed status to "' . $sta[0]['name'] . '"  you can view it here: ' .base_url('clients/tickets');
       
      //$msg  = 'Hello ' . $res[0]['firstname'] . ' ' . $res[0]['lastname'] . ' ' . 'reply of ticket No: '. $ttid .' has given. '.'<a href="' . base_url('clients/tickets/'.$ttid) . ' ">Please see reply</a>' ;
      
      $se =twilio_trigger_send_sms($number, $msg);
      if($se){
        return true; 
      }
      else{return false; }      
    }
    else{ return ; }
  }
  public function sms_add($id)
  { 
    $this->db->select('contactid');
    $this->db->from('tbltickets');
    $this->db->where('ticketid', $id);
    $res = $this->db->get()->result_array();    
    $tid = $res[0]['contactid'];
    
    $this->db->select('*');
    $this->db->from('tblrad');
    $this->db->where('userid', $tid);
    $this->db->where('name', 'sms_alert_support');
    $this->db->where('value', 1);
    $check = $this->db->get()->result_array();    
    
    if($check)
    {
      
      $this->db->select('*');
      $this->db->from('tblcontacts');
      $this->db->where('id', $tid);
      $res = $this->db->get()->result_array();
      
      if($res[0]['phonenumber'])
      {  
        if(substr($res[0]['phonenumber'], 0, 1) == '+')
          {
            $number = $res[0]['phonenumber'];
          }
        else
          {
            $number = '+'.$res[0]['phonenumber'];
          } 
        
        $msg  = 'Hello ' . $res[0]['firstname'] . ' ' . $res[0]['lastname'] . ', ' . 'a new support ticket has been open, you can view it here: '.base_url('clients/tickets');  
        $se = twilio_trigger_send_sms($number, $msg);
        if($se){
          return true; 
        }
      } 
      else{return false; }
    }
    else { return ; }
  }
  public function rad_check($id , $name)
  {
    $this->db->select('*');
    $this->db->from('tblrad');
    $this->db->where('userid', $id);
    $this->db->where('name', $name);
    $check = $this->db->get()->result_array();
    
    return $check; 
  }
  public function rad_con_ch($id)
  {
    $this->db->select('contactid');
    $this->db->from('tbltickets');
    $this->db->where('ticketid', $id);
    $res = $this->db->get()->result_array();    
    $tid = $res[0]['contactid'];
    return $tid;
  }
  // sajid
  //sajid code start
  public function get_contacts($res_id ='', $where = array('active' => 1))
  { 
      $this->db->select('*');
    $this->db->from('tblcontacts');
    $this->db->join('tblcontacts_rel_clients as cs1', 'cs1.contact_id = tblcontacts.id', 'LEFT');
    // $this->db->where($where);
    if ($res_id != '') {
      $this->db->where('cs1.company_id', $res_id);
    }
    // $date=date('m-d-Y h:i A', strtotime($data->datecreated));
    $this->db->order_by('cs1.is_primary', 'DESC');
    $data = $this->db->get()->result_array();
    return $data;
    
  }
  public function get_inventory($res_id = '')
  { 
    $this->db->select('ia.inventory_id, ia.account, ia.serial_number, ia.type_of_hardware, ia.date_in, ia.status, ia.origin, ia.equipment_owner, ia.manufacturer, ia.exp_date, ia.image, ia.description,    ia.user_id, ia.datecreated, cs1.value as statusvalue, cs2.value as originvalue, cs3.value as hardwarevalue , cs4.value as ownervalue, ts.firstname as firstname, ts.lastname as lastname, cl.company');
    $this->db->from('tblinventoryadd AS ia');// I use aliasing make joins easier
    $this->db->join('tblcustom AS cs1', 'cs1.id = ia.status', 'LEFT');
    $this->db->join('tblcustom AS cs2', 'cs2.id = ia.origin', 'LEFT');
    $this->db->join('tblcustom AS cs3', 'cs3.id = ia.type_of_hardware', 'LEFT');
    $this->db->join('tblcustom AS cs4', 'cs4.id = ia.equipment_owner', 'LEFT');
    $this->db->join('tblstaff AS ts', 'ts.staffid = ia.user_id', 'INNER');
    $this->db->join('tblclients AS cl', 'cl.userid = ia.account', 'INNER');
    $this->db->where('ia.account', $res_id);
    $data = $this->db->get()->result_array();
    return $data;
  }
//sajid code end
    public function get_ticket_status_history($id)
    {
        $this->db->select('*'); 

        $this->db->where('ticket_id',$id);
        // $this->db->where('status_show_or_not','1');
        return $this->db->get('tblticket_status_history')->result_array();
    }  
  public function get_sources($id = '')
    {
        $this->db->select('*'); 
        return $this->db->get('tblsources')->result_array();
    }  
   public function  get_userid_reply($id = ''){

    $this->db->select('tbltickets.userid as userid');
    $this->db->from('tbltickets');
    $this->db->join('tblticketreplies','tblticketreplies.ticketid = tbltickets.ticketid','left');
    $this->db->where('tbltickets.ticketid',$id);
    $this->db->limit(1);
    $res = $this->db->get()->result_array();
    return  $res[0]['userid'];


   }
   // abrar
    public function get_contact_for_comapny_ticket()
    {
    // $this->db->where('id', $id);

    return $this->db->get('tblcontacts')->result();
    }
    // abrar
    public function get_followers_data($id = '')
    {
        $this->db->select('*');
        $this->db->from('tbl_ticket_followers');
        $this->db->join('tbltickets','tbl_ticket_followers.ticket_id = tbltickets.ticketid','left');
        $this->db->where('tbl_ticket_followers.ticket_id',$id);
        $res = $this->db->get()->result_array();
        return $res;
    }
    public function update_followers_data($id = '', $followers = '')
    {
        $follower_data = $this->get_followers_data($id);
        $del = $this->db->delete('tbl_ticket_followers',array('ticket_id'=>$id));
        $no = 0; 
        foreach ($followers as $fval)
        {   
            $follow = array(
            'follower_id' => $fval,
            'ticket_id' => $id ,
            'created_at'=> date('Y-m-d h:i:s')
            );
            $res = $this->db->insert('tbl_ticket_followers', $follow);
            $no++;
        }
        if($no >0 || $del){
                return true; 
        }
        else{
            return false;
        }
    }
	public function get_companies_for_imap($email){
        $this->db->select("*");
        $this->db->from('tblcontacts');
        $this->db->join('tblcontacts_rel_clients as tcl','tcl.contact_id = tblcontacts.id');
        $this->db->where('tblcontacts.email', $email);
        return $this->db->get()->result_array();
    }
	
}
