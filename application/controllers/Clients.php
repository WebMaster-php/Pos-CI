<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Clients extends Clients_controller

{
    public function __construct()
    {
        parent::__construct();
        $this->form_validation->set_error_delimiters('<p class="text-danger alert-validation">', '</p>');
        do_action('after_clients_area_init', $this);
    }

public function abcd(){

	
	//$emails = explode(',',$this->input->post('to'));
    //$html = $this->input->post('html_code');
	
$html = '<html>
<head>
<title>HTML email</title>
</head>
<body>
<p>This email contains HTML Tags!</p>
<table>
<tr>
<th>pic</th>
</tr>
<tr>
<td><img src="https://image-charts.com/chart?chs=700x190&chd=t:60,40&cht=p3&chl=Hello%7CWorld&chan&chf=ps0-0,lg,45,ffeb3b,0.2,f44336,1|ps0-1,lg,45,8bc34a,0.2,009688,1"></td>
</tr>
</table>
</body>
</html>';
	
	//$html = $this->load->view('themes/flat/views/dompdf', true); 
     //<td><img src="https://image-charts.com/chart?chs=700x190&chd=t:60,40&cht=p3&chl=Hello%7CWorld&chan&chf=ps0-0,lg,45,ffeb3b,0.2,f44336,1|ps0-1,lg,45,8bc34a,0.2,009688,1"></td>
	
    require_once(APPPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php'); 
    $tcpdf = new TCPDF(); 
    //$tcpdf->addTTFfont('vendor/tecnickcom/tcpdf/fonts/Courier_Prime.ttf', 'TrueTypeUnicode','',20);
	$tcpdf->SetFont('courier', '', 14, '', true);
    $tcpdf->AddPage();
    $file_path = $_SERVER['DOCUMENT_ROOT'].'doms.pdf';
    $tcpdf->writeHTML($html);

	//Close and output PDF document
    $sa = $tcpdf->Output($file_path, 'F');
	echo $sa ; exit('daad'); 
	$this->load->model('Emails_model');
	$email = 'zeeshananweraziz@gmail.com';
	$subject = "dom";
   	$message = 'msg';
	
	$send_mail = $this->Emails_model->report_email($email, $subject, $message, true);
	echo  $send_mail; 
	exit('here'); 
        if($send_mail){
            unlink($file_path);
        }
}
	
	
    public function index()
    {

        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        //setting portfolio_id as client_portfolio_id in session
       // $get_portfolio_id= $_SESSION['contact_user_id']; 
       // $catch = contacts_by_portfolio($get_portfolio_id);
        //print_r($catch); 
       // $this->session->set_userdata('client_portfolio_id', $catch);
        // portfolio setting 
        
        $data['is_home'] = true;
        $this->load->model('reports_model');
        $data['payments_years'] = $this->reports_model->get_distinct_customer_invoices_years();

        //$data['title'] = get_company_name(get_client_user_id());
        $data['title'] = 'Dashboard';
        $this->data    = $data;
        $this->view    = 'home';
        $this->layout_new();
    }
	


    public function announcements()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        $data['title']         = _l('announcements');
        $data['announcements'] = $this->announcements_model->get();
        $this->data            = $data;
        $this->view            = 'announcements';
        $this->layout();
    }

    public function announcement($id)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        $data['announcement'] = $this->announcements_model->get($id);
        $data['title']        = $data['announcement']->name;
        $this->data           = $data;
        $this->view           = 'announcement';
        $this->layout();
    }

    public function calendar()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        $data['title'] = _l('calendar');
        $this->view            = 'calendar';
        $this->data            = $data;
        $this->layout();
    }

    public function get_calendar_data()
    {
        if (!is_client_logged_in()) {
            echo json_encode(array());
            die;
        }
        $this->load->model('utilities_model');
        $data = $this->utilities_model->get_calendar_data(
            $this->input->get('start'),
            $this->input->get('end'),
            get_user_id_by_contact_id(get_contact_user_id()),
            get_contact_user_id()
        );

        echo json_encode($data);
    }

    public function projects($status = '')
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if (!has_contact_permission('projects')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }


        $data['project_statuses'] = $this->projects_model->get_project_statuses();

        $where = 'clientid='.get_client_user_id();

        if (is_numeric($status)) {
            $where .= ' AND status='.$status;
        } else {
            $where .= ' AND status IN (';
            foreach ($data['project_statuses'] as $projectStatus) {
                if (isset($projectStatus['filter_default']) && $projectStatus['filter_default'] == true) {
                    $where .= $projectStatus['id'] . ',';
                }
            }
            $where = rtrim($where, ',');
            $where .= ')';
        }
        $data['projects']         = $this->projects_model->get('', $where);
        $data['title']            = _l('clients_my_projects');
        $this->data               = $data;
        $this->view               = 'projects';
        $this->layout();
    }

    public function project($id)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if (!has_contact_permission('projects')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $project = $this->projects_model->get($id, array(
            'clientid' => get_client_user_id(),
        ));

        $data['project'] = $project;
        $data['project']->settings->available_features = unserialize($data['project']->settings->available_features);

        $data['title'] = $data['project']->name;
        if ($this->input->post('action')) {
            $action = $this->input->post('action');

            switch ($action) {
                  case 'new_task':
                  case 'edit_task':

                    $data = $this->input->post();
                    $task_id = false;
                    if (isset($data['task_id'])) {
                        $task_id = $data['task_id'];
                        unset($data['task_id']);
                    }

                    $data['rel_type'] = 'project';
                    $data['rel_id'] = $project->id;
                    $data['description'] = nl2br($data['description']);

                    $assignees = isset($data['assignees']) ? $data['assignees'] : array();
                    if (isset($data['assignees'])) {
                        unset($data['assignees']);
                    }
                    unset($data['action']);

                    if (!$task_id) {
                        $task_id = $this->tasks_model->add($data, true);
                        if ($task_id) {
                            foreach ($assignees as $assignee) {
                                $this->tasks_model->add_task_assignees(array('taskid'=>$task_id, 'assignee'=>$assignee), false, true);
                            }
                            $uploadedFiles = handle_task_attachments_array($task_id);
                            if ($uploadedFiles && is_array($uploadedFiles)) {
                                foreach ($uploadedFiles as $file) {
                                    $file['contact_id'] = get_contact_user_id();
                                    $this->misc_model->add_attachment_to_database($task_id, 'task', array($file));
                                }
                            }
                            set_alert('success', _l('added_successfully', _l('task')));
                            redirect(site_url('clients/project/' . $project->id . '?group=project_tasks&taskid='.$task_id));
                        }
                    } else {
                        if ($project->settings->edit_tasks == 1
                            && total_rows('tblstafftasks', array('is_added_from_contact'=>1, 'addedfrom'=>get_contact_user_id())) > 0) {
                            $affectedRows = 0;
                            $updated = $this->tasks_model->update($data, $task_id, true);
                            if ($updated) {
                                $affectedRows++;
                            }

                            $currentAssignees = $this->tasks_model->get_task_assignees($task_id);
                            $currentAssigneesIds = array();
                            foreach ($currentAssignees as $assigned) {
                                array_push($currentAssigneesIds, $assigned['assigneeid']);
                            }

                            $totalAssignees = count($assignees);

                            /**
                             * In case when contact created the task and then was able to view team members
                             * Now in this case he still can view team members and can edit them
                             */
                            if ($totalAssignees == 0 && $project->settings->view_team_members == 1) {
                                $this->db->where('taskid', $task_id);
                                $this->db->delete('tblstafftaskassignees');
                            } elseif ($totalAssignees > 0 && $project->settings->view_team_members == 1) {
                                foreach ($currentAssignees as $assigned) {
                                    if (!in_array($assigned['assigneeid'], $assignees)) {
                                        if ($this->tasks_model->remove_assignee($assigned['id'], $task_id)) {
                                            $affectedRows++;
                                        }
                                    }
                                }
                                foreach ($assignees as $assignee) {
                                    if (!$this->tasks_model->is_task_assignee($assignee, $task_id)) {
                                        if ($this->tasks_model->add_task_assignees(array('taskid'=>$task_id, 'assignee'=>$assignee), false, true)) {
                                            $affectedRows++;
                                        }
                                    }
                                }
                            }
                            if ($affectedRows > 0) {
                                set_alert('success', _l('updated_successfully', _l('task')));
                            }
                            redirect(site_url('clients/project/' . $project->id . '?group=project_tasks&taskid='.$task_id));
                        }
                    }

                    redirect(site_url('clients/project/' . $project->id . '?group=project_tasks'));
                    break;
                case 'discussion_comments':
                    echo json_encode($this->projects_model->get_discussion_comments($this->input->post('discussion_id'), $this->input->post('discussion_type')));
                    die;
                case 'new_discussion_comment':
                    echo json_encode($this->projects_model->add_discussion_comment($this->input->post(), $this->input->post('discussion_id'), $this->input->post('discussion_type')));
                    die;
                    break;
                case 'update_discussion_comment':
                    echo json_encode($this->projects_model->update_discussion_comment($this->input->post(), $this->input->post('discussion_id')));
                    die;
                    break;
                case 'delete_discussion_comment':
                    echo json_encode($this->projects_model->delete_discussion_comment($this->input->post('id')));
                    die;
                    break;
                case 'new_discussion':
                    $discussion_data = $this->input->post();
                    unset($discussion_data['action']);
                    $success = $this->projects_model->add_discussion($discussion_data);
                    if ($success) {
                        set_alert('success', _l('added_successfully', _l('project_discussion')));
                    }
                    redirect(site_url('clients/project/' . $id . '?group=project_discussions'));
                    break;
                case 'upload_file':
                    handle_project_file_uploads($id);
                    die;
                    break;
                case 'project_file_dropbox':
                        $data = array();
                        $data['project_id'] = $id;
                        $data['files'] = $this->input->post('files');
                        $data['external'] = $this->input->post('external');
                        $data['visible_to_customer'] = 1;
                        $data['contact_id'] = get_contact_user_id();
                        $this->projects_model->add_external_file($data);
                die;
                break;
                case 'get_file':
                    $file_data['discussion_user_profile_image_url'] = contact_profile_image_url(get_contact_user_id());
                    $file_data['current_user_is_admin']             = false;
                    $file_data['file']                              = $this->projects_model->get_file($this->input->post('id'), $this->input->post('project_id'));

                    if (!$file_data['file']) {
                        header("HTTP/1.0 404 Not Found");
                        die;
                    }
                    echo get_template_part('projects/file', $file_data, true);
                    die;
                    break;
                case 'update_file_data':
                    $file_data = $this->input->post();
                    unset($file_data['action']);
                    $this->projects_model->update_file_data($file_data);
                    break;
                case 'upload_task_file':
                    $taskid = $this->input->post('task_id');
                    $files   = handle_task_attachments_array($taskid, 'file');
                    if ($files) {
                        $i = 0;
                        $len = count($files);
                        foreach ($files as $file) {
                            $file['contact_id'] = get_contact_user_id();
                            $file['staffid'] = 0;
                            $this->tasks_model->add_attachment_to_database($taskid, array($file), false, ($i == $len - 1 ? true : false));
                            $i++;
                        }
                    }
                    die;
                    break;
                case 'add_task_external_file':
                    $taskid                = $this->input->post('task_id');
                    $file                  = $this->input->post('files');
                    $file[0]['contact_id'] = get_contact_user_id();
                    $file[0]['staffid']    = 0;
                    $this->tasks_model->add_attachment_to_database($this->input->post('task_id'), $file, $this->input->post('external'));
                    die;
                    break;
                case 'new_task_comment':
                    $comment_data = $this->input->post();
                    $comment_data['content'] = nl2br($comment_data['content']);
                    $comment_id      = $this->tasks_model->add_task_comment($comment_data);
                    $url = site_url('clients/project/' . $id . '?group=project_tasks&taskid=' . $comment_data['taskid']);

                    if ($comment_id) {
                        set_alert('success', _l('task_comment_added'));
                        $url .= '#comment_'.$comment_id;
                    }

                    redirect($url);
                    break;
                default:
                    redirect(site_url('clients/project/' . $id));
                    break;
            }
        }
        if (!$this->input->get('group')) {
            $group = 'project_overview';
        } else {
            $group = $this->input->get('group');
        }
        if ($group != 'edit_task') {
            if ($group == 'project_overview') {
                $data['project_status'] =  get_project_status_by_id($data['project']->status);
                $percent          = $this->projects_model->calc_progress($id);
                @$data['percent'] = $percent / 100;
                $this->load->helper('date');
                $data['project_total_days']        = round((human_to_unix($data['project']->deadline . ' 00:00') - human_to_unix($data['project']->start_date . ' 00:00')) / 3600 / 24);
                $data['project_days_left']         = $data['project_total_days'];
                $data['project_time_left_percent'] = 100;
                if ($data['project']->deadline) {
                    if (human_to_unix($data['project']->start_date . ' 00:00') < time() && human_to_unix($data['project']->deadline . ' 00:00') > time()) {
                        $data['project_days_left']         = round((human_to_unix($data['project']->deadline . ' 00:00') - time()) / 3600 / 24);
                        $data['project_time_left_percent'] = $data['project_days_left'] / $data['project_total_days'] * 100;
                    }
                    if (human_to_unix($data['project']->deadline . ' 00:00') < time()) {
                        $data['project_days_left']         = 0;
                        $data['project_time_left_percent'] = 0;
                    }
                }
                $total_tasks = total_rows('tblstafftasks', array(
            'rel_id' => $id,
            'rel_type' => 'project',
            'visible_to_client' => 1,
        ));

                $data['tasks_not_completed'] = total_rows('tblstafftasks', array(
            'status !=' => 5,
            'rel_id' => $id,
            'rel_type' => 'project',
            'visible_to_client' => 1,
        ));

                $data['tasks_completed'] = total_rows('tblstafftasks', array(
            'status' => 5,
            'rel_id' => $id,
            'rel_type' => 'project',
            'visible_to_client' => 1,
        ));

                $data['total_tasks']                  = $total_tasks;
                $data['tasks_not_completed_progress'] = ($total_tasks > 0 ? number_format(($data['tasks_completed'] * 100) / $total_tasks, 2) : 0);
            } elseif ($group == 'new_task') {
                if ($project->settings->create_tasks == 0) {
                    redirect(site_url('clients/project/'.$project->id));
                }
                $data['milestones']  = $this->projects_model->get_milestones($id);
            } elseif ($group == 'project_gantt') {
                $data['gantt_data']  = $this->projects_model->get_gantt_data($id);
            } elseif ($group == 'project_discussions') {
                if ($this->input->get('discussion_id')) {
                    $data['discussion_user_profile_image_url'] = contact_profile_image_url(get_contact_user_id());
                    $data['discussion']                        = $this->projects_model->get_discussion($this->input->get('discussion_id'), $id);
                    $data['current_user_is_admin']             = false;
                }
                $data['discussions'] = $this->projects_model->get_discussions($id);
            } elseif ($group == 'project_files') {
                $data['files']       = $this->projects_model->get_files($id);
            } elseif ($group == 'project_tasks') {
                $data['tasks_statuses'] = $this->tasks_model->get_statuses();
                $data['project_tasks'] = $this->projects_model->get_tasks($id);
            } elseif ($group == 'project_activity') {
                $data['activity']   = $this->projects_model->get_activity($id);
            } elseif ($group == 'project_milestones') {
                $data['milestones']  = $this->projects_model->get_milestones($id);
            } elseif ($group == 'project_invoices') {
                $data['invoices'] = array();
                if (has_contact_permission('invoices')) {
                    $data['invoices'] = $this->invoices_model->get('', array(
                            'clientid' => get_client_user_id(),
                            'project_id' => $id,
                        ));
                }
            } elseif ($group == 'project_tickets') {
                $data['tickets'] = array();
                if (has_contact_permission('support')) {
                    $where_tickets = array(
                        'tbltickets.userid' => get_client_user_id(),
                        'project_id' => $id,
                    );

                    if (!is_primary_contact() && get_option('only_show_contact_tickets') == 1) {
                        $where_tickets['tbltickets.contactid'] = get_contact_user_id();
                    }

                    $data['tickets'] = $this->tickets_model->get('', $where_tickets);
                }
            } elseif ($group == 'project_estimates') {
                $data['estimates'] = array();
                if (has_contact_permission('estimates')) {
                    $data['estimates'] = $this->estimates_model->get('', array(
                            'clientid' => get_client_user_id(),
                            'project_id' => $id,
                        ));
                }
            } elseif ($group == 'project_timesheets') {
                $data['timesheets'] = $this->projects_model->get_timesheets($id);
            }

            if ($this->input->get('taskid')) {
                $data['view_task'] = $this->tasks_model->get($this->input->get('taskid'), array(
                    'rel_id' => $project->id,
                    'rel_type' => 'project',
                ));

                $data['title'] = $data['view_task']->name;
            }
        } elseif ($group == 'edit_task') {
            $data['task'] = $this->tasks_model->get($this->input->get('taskid'), array(
                    'rel_id' => $project->id,
                    'rel_type' => 'project',
                    'addedfrom'=>get_contact_user_id(),
                    'is_added_from_contact'=>1,
                ));
        }

        $data['group'] = $group;
        $data['currency'] = $this->projects_model->get_currency($id);
        $data['members']     = $this->projects_model->get_project_members($id);

        $this->data            = $data;
        $this->view            = 'project';
        $this->layout();
    }


    public function delete_file($id, $type = '')
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if (get_option('allow_contact_to_delete_files') == 1) {
            if ($type == 'general') {
                $file = $this->misc_model->get_file($id);
                if ($file->contact_id == get_contact_user_id()) {
                    $this->clients_model->delete_attachment($id);
                    set_alert('success', _l('deleted', _l('file')));
                }
                redirect(site_url('clients/files'));
            } elseif ($type == 'project') {
                $this->load->model('projects_model');
                $file = $this->projects_model->get_file($id);
                if ($file->contact_id == get_contact_user_id()) {
                    $this->projects_model->remove_file($id);
                    set_alert('success', _l('deleted', _l('file')));
                }
                redirect(site_url('clients/project/' . $file->project_id . '?group=project_files'));
            } elseif ($type == 'task') {
                $file = $this->misc_model->get_file($id);
                if ($file->contact_id == get_contact_user_id()) {
                    $this->tasks_model->remove_task_attachment($id);
                    set_alert('success', _l('deleted', _l('file')));
                }
                redirect(site_url('clients/project/' . $this->input->get('project_id') . '?group=project_tasks&taskid=' . $file->rel_id));
            }
        }
        redirect(site_url());
    }

    public function remove_task_comment($id)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        echo json_encode(array(
            'success' => $this->tasks_model->remove_comment($id),
        ));
    }

    public function edit_comment()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['content'] = nl2br($data['content']);
            $success = $this->tasks_model->edit_comment($data);
            if ($success) {
                set_alert('success', _l('task_comment_updated'));
            }
            echo json_encode(array(
                'success' => $success,
            ));
        }
    }

     public function tickets($status = '')
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if (!has_contact_permission('support')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        //sajid code start
         $new = get_company_ids_by_contact(get_contact_user_id());
         if($new){
            $where = 'tbltickets.userid IN (' . $new . ')';   
         }
        $data['list_status']= $status;
        $data['bodyclass']  = 'tickets';
        if($new){
            $data['tickets']    = $this->tickets_model->get('', $where);    
        }
        else{
            $data['tickets'] ='';
        }
        //sajid code end
        $data['title']      = _l('clients_tickets_heading');        
        $this->data         = $data;                
        $this->view         = 'tickets'; 
        // echo $this->db->last_query();exit();
        $this->layout_new();
    }

    public function change_ticket_status()
    {
        if (is_client_logged_in() && has_contact_permission('support')) {
            $post_data = $this->input->post();
            $response  = $this->tickets_model->change_ticket_status($post_data['ticket_id'], $post_data['status_id']);
            set_alert('alert-' . $response['alert'], $response['message']);
        }
    }

    public function viewproposal($id, $hash)
    {
        check_proposal_restrictions($id, $hash);
        $proposal = $this->proposals_model->get($id);
        if ($proposal->rel_type == 'customer' && !is_client_logged_in()) {
            load_client_language($proposal->rel_id);
        }
        $identity_confirmation_enabled = get_option('proposal_accept_identity_confirmation');
        if ($this->input->post()) {
            $action = $this->input->post('action');
            switch ($action) {
                case 'proposal_pdf':

                    $proposal_number = format_proposal_number($id);
                    $companyname     = get_option('invoice_company_name');
                    if ($companyname != '') {
                        $proposal_number .= '-' . mb_strtoupper(slug_it($companyname), 'UTF-8');
                    }

                    try {
                        $pdf = proposal_pdf($proposal);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        die;
                    }

                    $pdf->Output($proposal_number . '.pdf', 'D');
                    break;
                case 'proposal_comment':
                    // comment is blank
                    if (!$this->input->post('content')) {
                        redirect($this->uri->uri_string());
                    }
                    $data               = $this->input->post();
                    $data['proposalid'] = $id;
                    $this->proposals_model->add_comment($data, true);
                    redirect($this->uri->uri_string());
                    break;
                case 'accept_proposal':
                    $success = $this->proposals_model->mark_action_status(3, $id, true);
                    if ($success) {
                        $this->db->where('id', $id);
                        $this->db->update('tblproposals', get_acceptance_info_array());
                        redirect($this->uri->uri_string(), 'refresh');
                    }
                    break;
                case 'decline_proposal':
                    $success = $this->proposals_model->mark_action_status(2, $id, true);
                    if ($success) {
                        redirect($this->uri->uri_string(), 'refresh');
                    }
                    break;
            }
        }

        $number_word_lang_rel_id = 'unknown';
        if ($proposal->rel_type == 'customer') {
            $number_word_lang_rel_id = $proposal->rel_id;
        }
        $this->load->library('numberword', array(
            'clientid' => $number_word_lang_rel_id,
        ));

        $this->use_footer     = false;
        $this->use_navigation = false;
        $this->use_submenu    = false;

        $data['title']        = $proposal->subject;
        $data['proposal']     = do_action('proposal_html_pdf_data', $proposal);
        $data['bodyclass']    = 'proposal proposal-view';

        $data['identity_confirmation_enabled'] = $identity_confirmation_enabled;
        if ($identity_confirmation_enabled == '1') {
            $data['bodyclass'] .= ' identity-confirmation';
        }

        $data['comments']     = $this->proposals_model->get_comments($id);
        add_views_tracking('proposal', $id);
        do_action('proposal_html_viewed', $id);
        $data['exclude_reset_css'] = true;
        $data = do_action('proposal_customers_area_view_data', $data);
        $this->data                = $data;
        $this->view                = 'viewproposal';
        $this->layout();
    }

    public function proposals()
    {
        //sajid
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if (!has_contact_permission('proposals')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $new = get_company_ids_by_contact(get_contact_user_id());
        // $client = $this->clients_model->get(get_client_user_id());
        if($new)
        {
           $where = 'tblproposals.rel_id IN (' . $new . ')';
        }
        // echo "<pre>";print_r($where);exit();
        // if (!is_null($client->leadid)) {
        //     $where .= ' OR rel_type="lead" AND rel_id=' . $client->leadid;
        // }
        
        $primary_contacts = get_primary_contacts();

        if($primary_contacts){
            $results = array();
            foreach($primary_contacts as $contacts){
                // $where  = 'rel_id =' . $contacts['company_id'] . ' AND rel_type ="customer"';
                // if (get_option('exclude_proposal_from_client_area_with_draft_status') == 1) {
                //  $where .= ' AND status != 6';
                // $where .= ' AND status != 6'. ' AND tblproposals.portfolio_id = ' . 1;
                // }
                $result = $this->proposals_model->get('', $where);
                // echo "<pre>";print_r($result);exit();
                if($result != FALSE){
                    $results = $result;
                }
            }
            $data['proposalsresult']  = $results;
        }else{
            if(!empty($where)){
            $data['proposalsresult'] = $this->proposals_model->get('', $where);
        }
        }
        $data['title']     = _l('proposals');
        $this->data        = $data;
        $this->view        = 'proposals';
        // echo "<pre>"; print_r($data); exit ; 
        $this->layout_new();
    }

    public function open_ticket()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if (!has_contact_permission('support')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        if ($this->input->post()) {
            $this->form_validation->set_rules('subject', _l('customer_ticket_subject'), 'required');
            $this->form_validation->set_rules('department', _l('clients_ticket_open_departments'), 'required');
            // $this->form_validation->set_rules('priority', _l('priority'), 'required');
            $custom_fields = get_custom_fields('tickets', array(
                'show_on_client_portal' => 1,
                'required' => 1,
            ));
            foreach ($custom_fields as $field) {
                $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
                if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                    $field_name .= '[]';
                }
                $this->form_validation->set_rules($field_name, $field['name'], 'required');
            }

            if ($this->form_validation->run() !== false) {
                $data = $this->input->post(); 
                $data['status'] = 1;
                // $data['assigned'] = $this->;
                
                
                $data['portfolio_id'] = get_client_portfolio($data['userid']);
                // echo "<pre>"; print_r($data['portfolio_id']); exit('iinnn');  
                $id = $this->tickets_model->add($data);
                // echo "<pre>"; print_r($data); exit('iinnn');  
                // echo $this->db->last_query(); 
                // echo $id; echo "<pre>"; print_r($_POST); exit('lplplpkoji'); 
                if ($id) {
                    set_alert('success', _l('new_ticket_added_successfully', $id));
                    redirect(site_url('clients/tickets'));
                    // redirect(site_url('clients/ticket/' . $id));
                }
            }
            
        }
        $data                   = array();
        $data['projects']       = $this->projects_model->get_projects_for_ticket(get_client_user_id());
        $data['title']          = _l('new_ticket');
        $user_id                = $this->session->userdata('contact_user_id');
        $companies              = $this->inventory_model->get_company($user_id);
        $data['companies']      = $companies;
        $this->data             = $data;
        $this->view             = 'open_ticket';
        $this->layout_new();
    }

    public function ticket($id)
    { 
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if (!has_contact_permission('support')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }       
        if (!$id) {
            redirect(site_url());
        }
        
        if ($this->input->post()) {
            $this->form_validation->set_rules('message', _l('ticket_reply'), 'required');
            if ($this->form_validation->run() !== false) {
                $replyid = $this->tickets_model->add_reply($this->input->post(), $id);
                if ($replyid) {
                    set_alert('success', _l('replied_to_ticket_successfully', $id));
                    redirect(site_url('clients/ticket/' . $id));
                }
            }
        }
        $data['ticket'] = $this->tickets_model->get_tickets_by_id($id);
//        echo "<pre>"; print_r($data['ticket']); exit; 
        // if ($data['ticket']->userid != get_client_user_id()) {
            // redirect(site_url());
        // }
    //  echo $id; exit; 
        $data['ticket_replies'] = $this->tickets_model->get_ticket_replies($id);
        $data['title']          = $data['ticket']->subject;
        $this->data             = $data;
        $this->view             = 'single_ticket';
        $this->layout_new();
    }
    public function tickets_history($ticketId)
    {
     
         $new = get_company_ids_by_contact(get_contact_user_id());
         if($new){
            $where = 'tbltickets.ticketid = '. $ticketId ;   
         }
        $data['list_status']= $status;
        $data['bodyclass']  = 'tickets';
        if($new){
            $tickets    = $this->tickets_model->get('', $where); 
            $data['tickets']    = $tickets[0];   
        }
        else{
            $data['tickets'] ='';
        }

         $this->load->model('tickets_model');
         $data['ticket_body'] =$this->tickets_model->get_ticket_body($ticketId); 
         $data['statuses']= $this->tickets_model->get_ticket_status();
         // $data['statuses']= $this->tickets_model->get_ticket_status();
         $data['tickets_history'] = $this->tickets_model->get_ticket_status_history($ticketId); 
         $this->data = $data; 
         $this->view = 'ticket_status_history'; 
         $this->layout_new_1();
    }

    public function contracts()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if (!has_contact_permission('contracts')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $this->load->model('contracts_model');
        $data['contracts'] = $this->contracts_model->get('', array(
            'client' => get_client_user_id(),
            'not_visible_to_client' => 0,
            'trash' => 0,
        ));

        $data['contracts_by_type_chart'] = json_encode($this->contracts_model->get_contracts_types_chart_data());
        $data['title']                   = _l('clients_contracts');
        $this->data                      = $data;
        $this->view                      = 'contracts';
        $this->layout();
    }

    public function contract_pdf($id)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if (!has_contact_permission('contracts')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $this->load->model('contracts_model');
        $contract = $this->contracts_model->get($id, array(
            'client' => get_client_user_id(),
            'not_visible_to_client' => 0,
            'trash' => 0,
        ));

        try {
            $pdf      = contract_pdf($contract);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $pdf->Output(slug_it($contract->subject) . '.pdf', 'D');
    }

    public function invoices($status = false)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if (!has_contact_permission('invoices')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $where = array(
            'clientid' => get_client_user_id(),
        );
        if (is_numeric($status)) {
            $where['status'] = $status;
        }

        if (isset($where['status'])) {
            if ($where['status'] == 6 && get_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                unset($where['status']);
                $where['status !='] = 6;
            }
        } else {
            if (get_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                $where['status !='] = 6;
            }
        }

        $data['invoices'] = $this->invoices_model->get('', $where);
        $data['title']    = _l('clients_my_invoices');
        $this->data       = $data;
        $this->view       = 'invoices';
        $this->layout();
    }

    public function viewinvoice($id = '', $hash = '')
    {
        check_invoice_restrictions($id, $hash);
        $invoice = $this->invoices_model->get($id);

        $invoice = do_action('before_client_view_invoice', $invoice);

        if (!is_client_logged_in()) {
            load_client_language($invoice->clientid);
        }
        // Handle Invoice PDF generator
        if ($this->input->post('invoicepdf')) {
            try {
                $pdf            = invoice_pdf($invoice);
            } catch (Exception $e) {
                echo $e->getMessage();
                die;
            }

            $invoice_number = format_invoice_number($invoice->id);
            $companyname    = get_option('invoice_company_name');
            if ($companyname != '') {
                $invoice_number .= '-' . mb_strtoupper(slug_it($companyname), 'UTF-8');
            }
            $pdf->Output(mb_strtoupper(slug_it($invoice_number), 'UTF-8') . '.pdf', 'D');
            die();
        }
        // Handle $_POST payment
        if ($this->input->post('make_payment')) {
            $this->load->model('payments_model');
            if (!$this->input->post('paymentmode')) {
                set_alert('warning', _l('invoice_html_payment_modes_not_selected'));
                redirect(site_url('viewinvoice/' . $id . '/' . $hash));
            } elseif ((!$this->input->post('amount') || $this->input->post('amount') == 0) && get_option('allow_payment_amount_to_be_modified') == 1) {
                set_alert('warning', _l('invoice_html_amount_blank'));
                redirect(site_url('viewinvoice/' . $id . '/' . $hash));
            }
            $this->payments_model->process_payment($this->input->post(), $id);
        }
        if ($this->input->post('paymentpdf')) {
            $id                    = $this->input->post('paymentpdf');
            $payment               = $this->payments_model->get($id);
            $payment->invoice_data = $this->invoices_model->get($payment->invoiceid);
            $paymentpdf            = payment_pdf($payment);
            $paymentpdf->Output(mb_strtoupper(slug_it(_l('payment') . '-' . $payment->paymentid), 'UTF-8') . '.pdf', 'D');
            die;
        }
        $this->load->library('numberword', array(
            'clientid' => $invoice->clientid,
        ));
        $this->load->model('payment_modes_model');
        $this->load->model('payments_model');
        $data['payments']      = $this->payments_model->get_invoice_payments($id);
        $data['payment_modes'] = $this->payment_modes_model->get();
        $data['title']         = format_invoice_number($invoice->id);
        $this->use_navigation  = false;
        $this->use_submenu     = false;
        $data['hash']          = $hash;
        $data['invoice']       = do_action('invoice_html_pdf_data', $invoice);
        $data['bodyclass']     = 'viewinvoice';
        $this->data            = $data;
        $this->view            = 'invoicehtml';
        add_views_tracking('invoice', $id);
        do_action('invoice_html_viewed', $id);
        $this->layout();
    }

    public function statement()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if (!has_contact_permission('invoices')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $data = array();
        // Default to this month
        $from = _d(date('Y-m-01'));
        $to = _d(date('Y-m-t'));

        if ($this->input->get('from') && $this->input->get('to')) {
            $from = $this->input->get('from');
            $to = $this->input->get('to');
        }

        $data['statement'] = $this->clients_model->get_statement(get_client_user_id(), to_sql_date($from), to_sql_date($to));

        $data['from'] = $from;
        $data['to'] = $to;

        $data['period_today'] = json_encode(
                     array(
                     _d(date('Y-m-d')),
                     _d(date('Y-m-d')),
                     )
        );
        $data['period_this_week'] = json_encode(
                     array(
                     _d(date('Y-m-d', strtotime('monday this week'))),
                     _d(date('Y-m-d', strtotime('sunday this week'))),
                     )
        );
        $data['period_this_month'] = json_encode(
                     array(
                     _d(date('Y-m-01')),
                     _d(date('Y-m-t')),
                     )
        );

        $data['period_last_month'] = json_encode(
                     array(
                     _d(date('Y-m-01', strtotime("-1 MONTH"))),
                     _d(date('Y-m-t', strtotime('-1 MONTH'))),
                     )
        );

        $data['period_this_year'] = json_encode(
                     array(
                     _d(date('Y-m-d', strtotime(date('Y-01-01')))),
                     _d(date('Y-m-d', strtotime(date('Y-12-'.date('d', strtotime('last day of this year')))))),
                     )
        );
        $data['period_last_year'] = json_encode(
                     array(
                     _d(date('Y-m-d', strtotime(date(date('Y', strtotime('last year')).'-01-01')))),
                     _d(date('Y-m-d', strtotime(date(date('Y', strtotime('last year')). '-12-'.date('d', strtotime('last day of last year')))))),
                     )
        );

        $data['period_selected'] = json_encode(array($from, $to));

        $data['custom_period'] = ($this->input->get('custom_period') ? true : false);

        $data['title']         = _l('customer_statement');
        $this->data            = $data;
        $this->view            = 'statement';
        $this->layout();
    }

    public function statement_pdf()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if (!has_contact_permission('invoices')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $data['statement'] = $this->clients_model->get_statement(get_client_user_id(), to_sql_date($from), to_sql_date($to));

        try {
            $pdf            = statement_pdf($data['statement']);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type           = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf_name = slug_it(_l('customer_statement') . '_' .get_option('companyname'));
        $pdf->Output($pdf_name . '.pdf', $type);
    }

    public function viewestimate($id, $hash)
    {
        check_estimate_restrictions($id, $hash);
        $estimate = $this->estimates_model->get($id);
        if (!is_client_logged_in()) {
            load_client_language($estimate->clientid);
        }

        $identity_confirmation_enabled = get_option('estimate_accept_identity_confirmation');

        if ($this->input->post('estimate_action')) {
            $action = $this->input->post('estimate_action');
            // Only decline and accept allowed
            if ($action == 4 || $action == 3) {
                $success = $this->estimates_model->mark_action_status($action, $id, true);

                $redURL = $this->uri->uri_string();
                $accepted = false;
                if (is_array($success) && $success['invoiced'] == true) {
                    $accepted = true;
                    $invoice = $this->invoices_model->get($success['invoiceid']);
                    set_alert('success', _l('clients_estimate_invoiced_successfully'));
                    $redURL = site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash);
                } elseif (is_array($success) && $success['invoiced'] == false || $success === true) {
                    if ($action == 4) {
                        $accepted = true;
                        set_alert('success', _l('clients_estimate_accepted_not_invoiced'));
                    } else {
                        set_alert('success', _l('clients_estimate_declined'));
                    }
                } else {
                    set_alert('warning', _l('clients_estimate_failed_action'));
                }
                if ($action == 4 && $accepted = true) {
                    $this->db->where('id', $id);
                    $this->db->update('tblestimates', get_acceptance_info_array());
                }
            }
            redirect($redURL);
        }
        // Handle Estimate PDF generator
        if ($this->input->post('estimatepdf')) {
            try {
                $pdf             = estimate_pdf($estimate);
            } catch (Exception $e) {
                echo $e->getMessage();
                die;
            }

            $estimate_number = format_estimate_number($estimate->id);
            $companyname     = get_option('invoice_company_name');
            if ($companyname != '') {
                $estimate_number .= '-' . mb_strtoupper(slug_it($companyname), 'UTF-8');
            }
            $pdf->Output(mb_strtoupper(slug_it($estimate_number), 'UTF-8') . '.pdf', 'D');
            die();
        }
        $this->load->library('numberword', array(
            'clientid' => $estimate->clientid,
        ));

        $data['title']        = format_estimate_number($estimate->id);
        $this->use_navigation = false;
        $this->use_submenu    = false;
        $data['hash']         = $hash;
        $data['can_be_accepted'] = false;
        $data['estimate']     = do_action('estimate_html_pdf_data', $estimate);
        $data['bodyclass']    = 'viewestimate';
        $data['identity_confirmation_enabled'] = $identity_confirmation_enabled;
        if ($identity_confirmation_enabled == '1') {
            $data['bodyclass'] .= ' identity-confirmation';
        }
        $this->data           = $data;
        $this->view           = 'estimatehtml';
        add_views_tracking('estimate', $id);
        do_action('estimate_html_viewed', $id);
        $this->layout();
    }

    public function estimates($status = '')
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if (!has_contact_permission('estimates')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $where = array(
            'clientid' => get_client_user_id(),
        );
        if (is_numeric($status)) {
            $where['status'] = $status;
        }
        if (isset($where['status'])) {
            if ($where['status'] == 1 && get_option('exclude_estimate_from_client_area_with_draft_status') == 1) {
                unset($where['status']);
                $where['status !='] = 1;
            }
        } else {
            if (get_option('exclude_estimate_from_client_area_with_draft_status') == 1) {
                $where['status !='] = 1;
            }
        }
        $data['estimates'] = $this->estimates_model->get('', $where);
        $data['title']     = _l('clients_my_estimates');
        $this->data        = $data;
        $this->view        = 'estimates';
        $this->layout();
    }

    public function survey($id, $hash)
    {
        if (!$hash || !$id) {
            die('No survey specified');
        }
        $this->load->model('surveys_model');
        $survey = $this->surveys_model->get($id);
        if (!$survey || ($survey->hash != $hash)) {
            show_404();
        }
        if ($survey->active == 0) {
            // Allow users with permission manage surveys to preview the survey even if is not active
            if (!has_permission('surveys', '', 'view')) {
                die('Survey not active');
            }
        }
        // Check if survey is only for logged in participants / staff / clients
        if ($survey->onlyforloggedin == 1) {
            if (!is_logged_in()) {
                die('This survey is only for logged in users');
            }
        }
        // Ip Restrict check
        if ($survey->iprestrict == 1) {
            $this->db->where('surveyid', $id);
            $this->db->where('ip', $this->input->ip_address());
            $total = $this->db->count_all_results('tblsurveyresultsets');
            if ($total > 0) {
                die('Already participated on this survey. Thanks');
            }
        }
        if ($this->input->post()) {
            $success = $this->surveys_model->add_survey_result($id, $this->input->post());
            if ($success) {
                $survey = $this->surveys_model->get($id);
                if ($survey->redirect_url !== '') {
                    redirect($survey->redirect_url);
                }
                set_alert('success', 'Thank you for participating in this survey. Your answers are very important to us.');
                $default_redirect = do_action('survey_default_redirect', site_url());
                redirect($default_redirect);
            }
        }
        $this->use_navigation = false;
        $this->use_submenu    = false;
        $data['survey']       = $survey;
        $data['title']        = $data['survey']->subject;
        $this->data           = $data;
        $this->view           = 'survey_view';
        $this->layout();
    }

    public function company()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if ($this->input->post()) {
            
        //  print_r($this->input->post()); exit ; 
            if (get_option('company_is_required') == 1) {
                $this->form_validation->set_rules('company', _l('clients_company'), 'required');
            }

            if (active_clients_theme() == 'perfex') {
                // Fix for custom fields checkboxes validation
                $this->form_validation->set_rules('company_form', '', 'required');
            }

            $custom_fields = get_custom_fields('customers', array(
                'show_on_client_portal' => 1,
                'required' => 1,
                'disalow_client_to_edit' => 0,
            ));
            //print_r($this->input->post()); exit ;     
            foreach ($custom_fields as $field) {
                $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
                if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                    $field_name .= '[]';
                }
                $this->form_validation->set_rules($field_name, $field['name'], 'required');
                
            }
            
            if ($this->form_validation->run() !== false) {
                $data    = $this->input->post();
                
                if (isset($data['company_form'])) {
                    unset($data['company_form']);
                }
                $success = $this->clients_model->update_company_details($data, get_client_user_id());
                if ($success == true) {
                    set_alert('success', _l('clients_profile_updated'));
                }
                redirect(site_url('clients/company'));
            }
            
        }
       
        $data['title'] = _l('client_company_info');
        $user_id = $this->session->userdata('contact_user_id');
        $data['company']        = $this->inventory_model->get_company($user_id); 
        $this->data    = $data;
        $this->view    = 'company_details';
        $this->layout_new();
    }

    public function profile()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if ($this->input->post('profile')) {
            $this->form_validation->set_rules('firstname', _l('client_firstname'), 'required');
            $this->form_validation->set_rules('lastname', _l('client_lastname'), 'required');
            $custom_fields = get_custom_fields('contacts', array(
                'show_on_client_portal' => 1,
                'required' => 1,
                'disalow_client_to_edit' => 0,
            ));
            foreach ($custom_fields as $field) {
                $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
                if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                    $field_name .= '[]';
                }
                $this->form_validation->set_rules($field_name, $field['name'], 'required');
            }
            if ($this->form_validation->run() !== false) {
                handle_contact_profile_image_upload();
                $data = $this->input->post();
                // Unset the form indicator so we wont send it to the model
                unset($data['profile']);

                $contact = $this->clients_model->get_contact(get_contact_user_id());

                if (has_contact_permission('invoices')) {
                    $data['invoice_emails'] = isset($data['invoice_emails']) ? 1 : 0;
                    $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 : 0;
                } else {
                    $data['invoice_emails'] = $contact->invoice_emails;
                    $data['credit_note_emails'] = $contact->credit_note_emails;
                }

                if (has_contact_permission('estimates')) {
                    $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 : 0;
                } else {
                    $data['estimate_emails'] = $contact->estimate_emails;
                }

                if (has_contact_permission('contracts')) {
                    $data['contract_emails'] = isset($data['contract_emails']) ? 1 : 0;
                } else {
                    $data['contract_emails'] = $contact->contract_emails;
                }

                if (has_contact_permission('projects')) {
                    $data['project_emails'] = isset($data['project_emails']) ? 1 : 0;
                    $data['task_emails'] = isset($data['task_emails']) ? 1 : 0;
                } else {
                    $data['project_emails'] = $contact->project_emails;
                    $data['task_emails'] = $contact->task_emails;
                }
                // For all cases
                if (isset($data['password'])) {
                    unset($data['password']);
                }
                $success = $this->clients_model->update_contact($data, get_contact_user_id(), true);

                if ($success == true) {
                    set_alert('success', _l('clients_profile_updated'));
                }
                redirect(site_url('clients/profile'));
            }
        } elseif ($this->input->post('change_password')) {
            $this->form_validation->set_rules('oldpassword', _l('clients_edit_profile_old_password'), 'required');
            $this->form_validation->set_rules('newpassword', _l('clients_edit_profile_new_password'), 'required');
            $this->form_validation->set_rules('newpasswordr', _l('clients_edit_profile_new_password_repeat'), 'required|matches[newpassword]');
            if ($this->form_validation->run() !== false) {
                $success = $this->clients_model->change_contact_password($this->input->post(null, false));
                if (is_array($success) && isset($success['old_password_not_match'])) {
                    set_alert('danger', _l('client_old_password_incorrect'));
                } elseif ($success == true) {
                    set_alert('success', _l('client_password_changed'));
                }
                redirect(site_url('clients/profile'));
            }
        }
        $data['title'] = _l('clients_profile_heading');
        $this->data    = $data;
        $this->view    = 'edit_profile';
        $this->layout_new();
    }

    public function remove_profile_image()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        do_action('before_remove_contact_profile_image');
        if (file_exists(get_upload_path_by_type('contact_profile_images') . get_contact_user_id())) {
            delete_dir(get_upload_path_by_type('contact_profile_images') . get_contact_user_id());
        }
        $this->db->where('id', get_contact_user_id());
        $this->db->update('tblcontacts', array(
            'profile_image' => null,
        ));
        if ($this->db->affected_rows() > 0) {
            redirect(site_url('clients/profile'));
        }
    }

    public function register()
    {
        if (get_option('allow_registration') != 1 || is_client_logged_in()) {
            redirect(site_url());
        }
        if (get_option('company_is_required') == 1) {
            $this->form_validation->set_rules('company', _l('client_company'), 'required');
        }
        $this->form_validation->set_rules('firstname', _l('client_firstname'), 'required');
        $this->form_validation->set_rules('lastname', _l('client_lastname'), 'required');
        $this->form_validation->set_rules('email', _l('client_email'), 'trim|required|is_unique[tblcontacts.email]|valid_email');
        $this->form_validation->set_rules('password', _l('clients_register_password'), 'required');
        $this->form_validation->set_rules('passwordr', _l('clients_register_password_repeat'), 'required|matches[password]');

        if (get_option('use_recaptcha_customers_area') == 1 && get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != '') {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }

        $custom_fields = get_custom_fields('customers', array(
            'show_on_client_portal' => 1,
            'required' => 1,
        ));

        $custom_fields_contacts = get_custom_fields('contacts', array(
            'show_on_client_portal' => 1,
            'required' => 1,
        ));

        foreach ($custom_fields as $field) {
            $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                $field_name .= '[]';
            }
            $this->form_validation->set_rules($field_name, $field['name'], 'required');
        }
        foreach ($custom_fields_contacts as $field) {
            $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                $field_name .= '[]';
            }
            $this->form_validation->set_rules($field_name, $field['name'], 'required');
        }
        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $data = $this->input->post();
                // Unset recaptchafield
                if (isset($data['g-recaptcha-response'])) {
                    unset($data['g-recaptcha-response']);
                }
                $clientid = $this->clients_model->add($data, true);
                if ($clientid) {
                    do_action('after_client_register', $clientid);
                    $this->load->model('authentication_model');
                    $logged_in = $this->authentication_model->login($this->input->post('email'), $this->input->post('password', false), false, false);

                    $redUrl = site_url();

                    if ($logged_in) {
                        do_action('after_client_register_logged_in', $clientid);
                        set_alert('success', _l('clients_successfully_registered'));
                    } else {
                        set_alert('warning', _l('clients_account_created_but_not_logged_in'));
                        $redUrl = site_url('clients/login');
                    }

                    $admins = $this->db->select('email')->
                    where('admin', 1)
                    ->get('tblstaff')->result_array();

                    $this->load->model('emails_model');
                    foreach ($admins as $admin) {
                        $merge_fields = get_client_contact_merge_fields($clientid, get_primary_contact_user_id($clientid));
                        $this->emails_model->send_email_template('new-client-registered-to-admin', $admin['email'], $merge_fields);
                    }
                    redirect($redUrl);
                }
            }
        }
        $data['title'] = _l('clients_register_heading');
        $data['bodyclass'] = 'register';
        $this->data    = $data;
        $this->view    = 'register';
        $this->layout();
    }

    public function forgot_password()
    {
        if (is_client_logged_in()) {
            redirect(site_url());
        }

        $this->form_validation->set_rules('email', _l('customer_forgot_password_email'), 'trim|required|valid_email|callback_contact_email_exists');

        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $this->load->model('Authentication_model');
                $success = $this->Authentication_model->forgot_password($this->input->post('email'));
                if (is_array($success) && isset($success['memberinactive'])) {
                    set_alert('danger', _l('inactive_account'));
                } elseif ($success == true) {
                    set_alert('success', _l('check_email_for_resetting_password'));
                } else {
                    set_alert('danger', _l('error_setting_new_password_key'));
                }
                redirect(site_url('clients/forgot_password'));
            }
        }
        $data['title'] = _l('customer_forgot_password');
        $this->data    = $data;
        $this->view    = 'forgot_password';
        $this->layout();
    }

    public function reset_password($staff, $userid, $new_pass_key)
    {
        $this->load->model('Authentication_model');
        if (!$this->Authentication_model->can_reset_password($staff, $userid, $new_pass_key)) {
            set_alert('danger', _l('password_reset_key_expired'));
            redirect(site_url('clients/login'));
        }

        $this->form_validation->set_rules('password', _l('customer_reset_password'), 'required');
        $this->form_validation->set_rules('passwordr', _l('customer_reset_password_repeat'), 'required|matches[password]');
        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                do_action('before_user_reset_password', array(
                    'staff' => $staff,
                    'userid' => $userid,
                ));
                $success = $this->Authentication_model->reset_password(0, $userid, $new_pass_key, $this->input->post('passwordr', false));
                if (is_array($success) && $success['expired'] == true) {
                    set_alert('danger', _l('password_reset_key_expired'));
                } elseif ($success == true) {
                    do_action('after_user_reset_password', array(
                        'staff' => $staff,
                        'userid' => $userid,
                    ));
                    set_alert('success', _l('password_reset_message'));
                } else {
                    set_alert('danger', _l('password_reset_message_fail'));
                }
                redirect(site_url('clients/login'));
            }
        }
        $data['title'] = _l('admin_auth_reset_password_heading');
        $this->data = $data;
        $this->view = 'reset_password';
        $this->layout();
    }

    public function dismiss_announcement($id)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        $this->misc_model->dismiss_announcement($id, false);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function knowledge_base($slug = '')
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if ((get_option('use_knowledge_base') == 1 && !is_client_logged_in() && get_option('knowledge_base_without_registration') == 1) || (get_option('use_knowledge_base') == 1 && is_client_logged_in()) || is_staff_logged_in()) {
            if (is_staff_logged_in() && get_option('use_knowledge_base') == 0) {
                set_alert('warning', 'Knowledge base is disabled, navigate to Setup->Settings->Customers and set Use Knowledge Base to YES.');
            }

            $data     = array();
            $where_kb = array();
            if ($this->input->get('groupid')) {
                $where_kb = 'articlegroup =' . $this->input->get('groupid')  ;
            } elseif ($this->input->get('kb_q')) {
                $where_kb = '(subject LIKE "%' . $this->input->get('kb_q') . '%" OR description LIKE "%' . $this->input->get('kb_q') . '%")';
            }
            
            $data['groups']                = get_all_knowledge_base_articles_grouped(true, $where_kb);
            $data['knowledge_base_search'] = true;
            if ($slug == '' || $this->input->get('groupid')) {
                $data['title'] = _l('clients_knowledge_base');
                $this->view    = 'knowledgebase';
            } else {
                $data['article'] = $this->knowledge_base_model->get(false, $slug);
                if ($data['article']) {
                    $data['related_articles'] = $this->knowledge_base_model->get_related_articles($data['article']->articleid);
                    add_views_tracking('kb_article', $data['article']->articleid);
                    if ($data['article']->active_article == 0) {
                        redirect(site_url('knowledge_base'));
                    }
                    $data['title'] = $data['article']->subject;

                    $this->view = 'knowledge_base_article';
                } else {
                    show_404();
                }
            }
            $this->data = $data;
            //echo "<pre>"; print_r($data); exit; 
            $this->layout_new();
            //$this->layout();
        } else {
            show_404();
        }
    }

    public function add_kb_answer()
    {   
        // This is for did you find this answer useful
        if (($this->input->post() && $this->input->is_ajax_request())) {
            echo json_encode($this->knowledge_base_model->add_article_answer($this->input->post()));
            die();
        }
    }

    public function login()
    {
        // exit('kokoko'); 
        if (is_client_logged_in()) {            redirect(site_url());
        }
        $this->form_validation->set_rules('password', _l('clients_login_password'), 'required');
        $this->form_validation->set_rules('email', _l('clients_login_email'), 'trim|required|valid_email');
        if (get_option('use_recaptcha_customers_area') == 1 && get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != '') {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }
        if ($this->form_validation->run() !== false) {
            $this->load->model('Authentication_model');
            $success = $this->Authentication_model->login($this->input->post('email'), $this->input->post('password', false), $this->input->post('remember'), false);
            if (is_array($success) && isset($success['memberinactive'])) {
                set_alert('danger', _l('inactive_account'));
                redirect(site_url('clients/login'));
            } elseif ($success == false) {
                set_alert('danger', _l('client_invalid_username_or_password'));
                redirect(site_url('clients/login'));
            }

            do_action('after_contact_login');
            // redirect(site_url('knowledge-base'));
            redirect(site_url('clients/tickets'));
        }
        if (get_option('allow_registration') == 1) {
            $data['title'] = _l('clients_login_heading_register');
        } else {
            $data['title'] = _l('clients_login_heading_no_register');
        }
        $data['bodyclass'] = 'customers_login';

        $this->data        = $data;
        $this->view        = 'login';
        $this->layout();
    }

    public function logout()
    {
        $this->load->model('authentication_model');
        $this->authentication_model->logout(false);
        do_action('after_client_logout');
        redirect(site_url('clients/login'));
    }

    public function contact_email_exists($email = '')
    {
        if ($email == '') {
            $email = $this->input->post('email');
        }
        $this->db->where('email', $email);
        $total_rows = $this->db->count_all_results('tblcontacts');
        if ($this->input->post() && $this->input->is_ajax_request()) {
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        } elseif ($this->input->post()) {
            if ($total_rows == 0) {
                $this->form_validation->set_message('contact_email_exists', _l('auth_reset_pass_email_not_found'));

                return false;
            }

            return true;
        }
    }

    public function change_language($lang = '')
    {
        if (!is_client_logged_in() || !is_primary_contact()) {
            redirect(site_url());
        }
        $lang = do_action('before_customer_change_language', $lang);
        $this->db->where('userid', get_client_user_id());
        $this->db->update('tblclients', array('default_language'=>$lang));
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(site_url());
        }
    }

    /**
     * Client home chart
     * @return mixed
     */
    public function client_home_chart()
    {
        if (is_client_logged_in()) {
            $statuses        = array(
                1,
                2,
                4,
                3,
            );
            $months          = array();
            $months_original = array();
            for ($m = 1; $m <= 12; $m++) {
                array_push($months, _l(date('F', mktime(0, 0, 0, $m, 1))));
                array_push($months_original, date('F', mktime(0, 0, 0, $m, 1)));
            }
            $chart = array(
                'labels' => $months,
                'datasets' => array(),
            );
            foreach ($statuses as $status) {
                $this->db->select('total as amount, date');
                $this->db->from('tblinvoices');
                $this->db->where('clientid', get_client_user_id());
                $this->db->where('status', $status);
                $by_currency = $this->input->post('report_currency');
                if ($by_currency) {
                    $this->db->where('currency', $by_currency);
                }
                if ($this->input->post('year')) {
                    $this->db->where('YEAR(tblinvoices.date)', $this->input->post('year'));
                }
                $payments      = $this->db->get()->result_array();
                $data          = array();
                $data['temp']  = $months_original;
                $data['total'] = array();
                $i             = 0;
                foreach ($months_original as $month) {
                    $data['temp'][$i] = array();
                    foreach ($payments as $payment) {
                        $_month = date('F', strtotime($payment['date']));
                        if ($_month == $month) {
                            $data['temp'][$i][] = $payment['amount'];
                        }
                    }
                    $data['total'][] = array_sum($data['temp'][$i]);
                    $i++;
                }

                if ($status == 1) {
                    $borderColor = '#fc142b';
                } elseif ($status == 2) {
                    $borderColor = '#84c529';
                } elseif ($status == 4 || $status == 3) {
                    $borderColor = '#ff6f00';
                }

                $backgroundColor = 'rgba('.implode(',', hex2rgb($borderColor)).',0.3)';

                array_push($chart['datasets'], array(
                    'label' => format_invoice_status($status, '', false, true),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => $data['total'],
                ));
            }
            echo json_encode($chart);
        }
    }
    public function profile_new()
    {       
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        $notes = array();
        $inventory = array();
        $check = 'external';
       $user_id = $this->session->userdata('contact_user_id');
        
        $companies  = $this->inventory_model->get_company($user_id);
        //echo "<pre>";
        //print_r($companies);
        //exit;
        $data['company'] = $companies;
        foreach($companies as $company){
            $notes[$company['userid']] = $this->inventory_model->get_notes($company['userid'], $check);
            $inventory[$company['userid']] = $this->inventory_model->get_inventory($company['userid']);
        }
        $data['notes'] = $notes;
        //echo "<pre>";
    //  print_r($data['notes']);
        //exit; 
        
    $data['inventory'] = $inventory;        
        $data['announcement']   = $this->inventory_model->get_announcement($user_id);       
        
        $data['title'] = 'profile_new';
        $this->data    = $data;
        $this->view    = 'profile_new';
        $this->layout_new();
    }
    public function company_details()
    {   
        
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if($this->input->post('update'))
            {
                 $this->form_validation->set_rules('company', 'Company Name', 'required');
                 $this->form_validation->set_rules('phonenumber', 'Phone Number', 'required');
                 if($this->form_validation->run() !== false)
                 {
                     $data = $this->input->post();
                     $query = $this->inventory_model->company_details($data);
                    if($query)
                    {
                        set_alert('success', 'Company Details Updated');
                    }
                    set_alert('danger', 'Company Details not Updated');
                 }
            }
        $user_id = $this->session->userdata('contact_user_id');
        //$data['company']      = $this->inventory_model->get_company($user_id); 
        $data['title'] = 'company_details';
        $this->data    = $data;
        $this->view    = 'company_details';
        $this->layout_new();    
    }
    public function announcement_new()
    {
         if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        $user_id = $this->session->userdata('contact_user_id');
        $data['announcement']   = $this->inventory_model->get_announcement($user_id);       
        $data['title'] = 'announcement_new';
        $this->data    = $data;
        $this->view    = 'announcement_new'; 
        $this->layout_new();    
    }
    
    public function notificationRefresh()
    {
        echo json_encode(array(getAnounceStatusCount_()));
    }
    
    public function knowledgebase()
    {
        $user_id = $this->session->userdata('contact_user_id');
    //  $data['announcement']   = $this->inventory_model->get_announcement($user_id);       
        $data['title'] = 'knowledgebase';
        $this->data    = $data;
        $this->view    = 'knowledgebase';
        $this->layout_new();    
    }
    public function edit_profile()
    {
        
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        if ($this->input->post('profile')) {
            
            $this->form_validation->set_rules('firstname', _l('client_firstname'), 'required');
            $this->form_validation->set_rules('lastname', _l('client_lastname'), 'required');
            // $custom_fields = get_custom_fields('contacts', array(
                // 'show_on_client_portal' => 1,
                // 'required' => 1,
                // 'disalow_client_to_edit' => 0,
            // ));
            // foreach ($custom_fields as $field) {
                // $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
                // if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                    // $field_name .= '[]';
                // }
                // $this->form_validation->set_rules($field_name, $field['name'], 'required');
            // }
            
            if ($this->form_validation->run() !== false) {
            
            
               // handle_contact_profile_image_upload();
                // $data = $this->input->post();
                //Unset the form indicator so we wont send it to the model
                // unset($data['profile']);

                 $contact = $this->clients_model->get_contact(get_contact_user_id());

                // if (has_contact_permission('invoices')) {
                    // $data['invoice_emails'] = isset($data['invoice_emails']) ? 1 : 0;
                    // $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 : 0;
                // } else {
                    // $data['invoice_emails'] = $contact->invoice_emails;
                    // $data['credit_note_emails'] = $contact->credit_note_emails;
                // }

                // if (has_contact_permission('estimates')) {
                    // $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 : 0;
                // } else {
                    // $data['estimate_emails'] = $contact->estimate_emails;
                // }

                // if (has_contact_permission('contracts')) {
                    // $data['contract_emails'] = isset($data['contract_emails']) ? 1 : 0;
                // } else {
                    // $data['contract_emails'] = $contact->contract_emails;
                // }

                // if (has_contact_permission('projects')) {
                    // $data['project_emails'] = isset($data['project_emails']) ? 1 : 0;
                    // $data['task_emails'] = isset($data['task_emails']) ? 1 : 0;
                // } else {
                    // $data['project_emails'] = $contact->project_emails;
                    // $data['task_emails'] = $contact->task_emails;
                // }
                // For all cases
                $data = $this->input->post();
                if (isset($data['profile'])) {
                    unset($data['profile']);
                }
                if (isset($data['position'])) {
                    unset($data['position']);
                }
                if (isset($data['profile_image'])) {
                    unset($data['profile_image']);
                }
                if (isset($data['password'])) {
                    unset($data['password']);
                }
                //print_r($data); exit;                 
                $success = $this->clients_model->update_contact_new($data, get_contact_user_id());
                if ($success == true) {
                    set_alert('success', 'Profile Updated');                
                }
                redirect(site_url('clients/edit_profile'));
            }
        } elseif ($this->input->post('change_password')) {

            $this->form_validation->set_rules('oldpassword', _l('clients_edit_profile_old_password'), 'required');
            $this->form_validation->set_rules('newpassword', _l('clients_edit_profile_new_password'), 'required');
            $this->form_validation->set_rules('newpasswordr', _l('clients_edit_profile_new_password_repeat'), 'required|matches[newpassword]');
            if ($this->form_validation->run() !== false) {
            //print_r($this->input->post());  
               $success = $this->clients_model->change_contact_password($this->input->post(null, false));
                if (is_array($success) && isset($success['old_password_not_match'])) {
                   // echo "hare"; exit; 
                    set_alert('danger', 'Old Password is incorrect');
                } elseif ($success == true) {
                    //echo "right "; exit ; 
                    set_alert('success', 'Password Updated');
                }
                redirect(site_url('clients/edit_profile'));
            }
        }
    
        $user_id = $this->session->userdata('contact_user_id');
        $data['company']        = $this->inventory_model->get_company($user_id); 
        $data['title'] = 'edit_profile';
        $this->data    = $data;
        $this->view    = 'edit_profile';
        $this->layout_new();    
    }
    
    public function files_new()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        $files_where = 'visible_to_customer = 1 AND id IN (SELECT file_id FROM tblcustomerfiles_shares WHERE contact_id ='.get_contact_user_id() . ')';
        $files_where = do_action('customers_area_files_where', $files_where);
        $files = $this->clients_model->get_customer_files($this->session->userdata['contact_user_id'],$files_where);
        $user_id = $this->session->userdata('contact_user_id');
        $companies  = $this->inventory_model->get_company($user_id);
        $data['companies'] = $companies;
        $data['files'] = $files;
        $data['title'] = 'Files';
        $this->data    = $data;
        $this->view    = 'files_new';
        $this->layout_new();    
    }
    
    public function upload_files_new()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
          if (!empty($_FILES)) {
            // echo "here"; exit; 
        // $tempFile = $_FILES['file']['tmp_name'];
        // $fileName = $_FILES['file']['name'];
        // $targetPath = getcwd() . '/uploads/';
        // $targetFile = $targetPath . $fileName ;
        // move_uploaded_file($tempFile, $targetFile);
        //if you want to save in db,where here
        // with out model just for example
        // $this->load->database(); // load database
        // $this->db->insert('file_table',array('file_name' => $fileName));
            //echo "here1" ; exit; 
            
            // $file                        = $this->input->post('files');
            // $file[0]['staffid']          = 0;
            // $file[0]['contact_id']       = get_contact_user_id();
            // $file['visible_to_customer'] = 1;
            
        //  print_r($file); echo "no";  exit ;  
            
            $this->misc_model->add_attachment_to_database(get_client_user_id(), 'customer', $file, $this->input->post('external'));
        } 
        //echo "here"; exit; 
        else {
            handle_client_attachments_upload(get_client_user_id(), true);
        }
    }
    
    public function files()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        $files_where = 'visible_to_customer = 1 AND id IN (SELECT file_id FROM tblcustomerfiles_shares WHERE contact_id =' . get_contact_user_id() . ')';

        $files_where = do_action('customers_area_files_where', $files_where);

        $files = $this->clients_model->get_customer_files(get_client_user_id(), $files_where);

        $data['files'] = $files;
        $data['title'] = _l('customer_attachments');
        $this->data    = $data;
        $this->view    = 'files';
        $this->layout();
    }
    
    public function upload_files()
    {   
        if (!is_client_logged_in()) {
        redirect(site_url('clients/login'));
        }   
        if ($this->input->post('external')) {
        $file = $this->input->post('files');
        $file[0]['staffid'] = 0;
        $file[0]['contact_id'] = get_contact_user_id();
        $file['visible_to_customer'] = 1;
        $companyId = $this->input->post('companyvalueid');
        $this->misc_model->add_attachment_to_database($companyId, 'customer', $file, $this->input->post('external'));
        // $this->misc_model->add_attachment_to_database(get_client_user_id(), 'customer', $file, $this->input->post('external'));
        } else {
        $companyId = $this->input->post('companyvalueid');
        if($companyId){
        handle_client_attachments_upload($companyId, true);
        echo json_encode(array('succes'=>'uploaded'));
        }else{
        echo json_encode(array('error'=>'Please select company first!'));
        }
        // handle_client_attachments_upload(get_client_user_id(), true);
        }
    }

    
    public function read_announcement()
    {
        $user_id = $this->session->userdata('contact_user_id');
        $announcement_id = $this->input->post('id');
        $this->db->select('*');
        $this->db->from('announcement_status');
        $this->db->where('announcement_id', $announcement_id);
        $this->db->where('user_id', $user_id);
        $fields = $this->db->get()->result_array();
        if($fields){
            echo json_encode("noneed");
        }
        else 
        {
            $data =  array(
                'announcement_id' => $announcement_id ,
                'user_id' => $user_id,
                'status_announcement' => 1
            );
            $this->db->insert('announcement_status', $data);            
            if($this->db->insert_id())
            {
                echo json_encode("save");
            }
            else
            {
                echo json_encode('error');
            }
        }
        die;
    } 

    
    
    public function open_announcement()
    {
        $announcement_id = $this->input->post('id');
        $this->db->select('*');
        $this->db->from('tblannouncements');
        $this->db->where('announcementid', $announcement_id);       
        $fields = $this->db->get()->result_array();
        if($fields){
            echo json_encode($fields);
        }
        die;
    } 
    
    public function clear_announcement()
    {
        $userid = $this->input->post('id');
        $announcement = $this->inventory_model->get_announcement();
        if($announcement){
            foreach($announcement as $announce => $val){
                //echo $val['announcement_id'];
                if($val['announcementid'] != $val['announcement_id'] && $val['userid'] != $userid){
                    $data =  array(
                        'announcement_id' => $val['announcementid'] ,
                        'user_id' => $userid,
                        'status_announcement' => 1
                    );
                    $this->db->insert('announcement_status', $data);
                    $result = $this->db->insert_id();
                }
            }
            if($result)
            {
                echo json_encode(array('save'));
            }
            else
            {
                echo json_encode(array('noresponse'));
            }
        }
        die;
    } 
    
    public function recaptcha($str = '')
    {
        return do_recaptcha_validation($str);
    }
    public function getmorefile()
    {
        if (!is_client_logged_in()) {
        redirect(site_url('clients/login'));
        }
        $filesid = $this->input->post('filesId');
        $files_where = 'visible_to_customer = 1 AND id IN (SELECT file_id FROM tblcustomerfiles_shares WHERE contact_id =' . get_contact_user_id() . ')';

        $files_where = do_action('customers_area_files_where', $files_where);

        $files = $this->clients_model->get_customer_more_files(get_contact_user_id(), $files_where, $filesid);
        $html ='';
        $fileId = '';
        if($files){
        foreach($files as $file){

        $html .= '<div class="col-lg-2 col-xl-2">
        <div class="file-man-box">';
        if(get_option("allow_contact_to_delete_files") == 1){ 
        if($file["contact_id"] == get_contact_user_id()){
        $html .= '<a href="'.site_url('clients/delete_file/'.$file['id'].'/general').'" class="file-close"><i class="mdi mdi-close-circle"></i></a>';
        } }

        $html .= '<div class="file-img-box">';
        $url = site_url().'download/file/client/';
        $path = get_upload_path_by_type("customer").$file["rel_id"]."/".$file["file_name"];
        $is_image = false;
        if(!isset($file["external"])) {
        $attachment_url = $url . $file['attachment_key'];
        $is_image = is_image($path);
        $img_url = site_url("download/preview_image?path=".protected_file_url_by_path($path,true).'&type='.$file["filetype"]);
        } else if(isset($file["external"]) && !empty($file["external"])){
        if(!empty($file["thumbnail_link"])){
        $is_image = true;
        $img_url = optimize_dropbox_thumbnail($file["thumbnail_link"]);
        }
        $attachment_url = $file["external_link"];
        }
        $ext = explode(".",$file["file_name"]);
        $ext = $ext[1];
        if($is_image){
        } if($is_image){
        $html .= '<img src="'.$img_url.'" style="width: 100%; height: 100%;">';
        } else {
        //$html .= '<img src="'. base_url().'"assets/images/file_icons/"'.$ext.'" alt="icon" style="width: 80%; height: 80%;">';
        
        $html .= '<img src="'. base_url()."assets/images/file_icons/".$ext.'.svg" alt="icon" style="width: 80%; height: 80%;">';
        
        }
        $html .= '</div>';

        $html .= '<a href="'.$attachment_url.'" class="file-download"><i class="mdi mdi-download"></i> </a>
        <div class="file-man-title">
        <h5 class="mb-0 text-overflow">'.$file["file_name"].'</h5>
        <p class="mb-0"><small>'. _dt($file["dateadded"]). '</small></p>
        </div>
        </div>
        </div>';
        $fileId = $file['id'];
        }
        $result = array(
        'status' => 'success',
        'data' => $html,
        'id' => $fileId
        );
        echo json_encode($result);
        die;
        }
        else
        {
        $result = array(
        'status' => 'error',
        'message' => 'No more ressult found',
        );
        echo json_encode($result);
        die;
        }
    }
    
    public function gettickets()
    {//sajid
        $html ='';
        $new = get_company_ids_by_contact(get_contact_user_id());
         // $where = 'tbltickets.userid IN (' . $new . ')';
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if (!has_contact_permission('support')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        if (!is_primary_contact() && get_option('only_show_contact_tickets') == 1) {
            $where['tbltickets.contactid'] = get_contact_user_id();
            // $where['tbltickets.userid'] = get_company_ids_by_contact(get_contact_user_id());
        }
        if ($this->input->post('id') == 2)
        {
            $result = array();
            // $where = 'tbltickets.userid IN (' . $new . ') AND status = ' . $this->input->post('id');
            //changed as per client's requirement.
            
            $where = 'tbltickets.userid IN (' . $new . ') AND status IN ( 1,2,3 )';
            
            $data['list_status'] = $this->input->post('id');
            $data['bodyclass'] = 'tickets';
            $result[] = $this->tickets_model->get('', $where);
            //sajid code start
            $where = 'tbltickets.userid IN (' . $new . ') AND status = ' . 3;  
            //sajid code end
            $data['list_status'] = $this->input->post('id');
            $data['bodyclass'] = 'tickets';
            $result[] = $this->tickets_model->get('', $where);
            $tickets = $result; 
            if(empty($tickets[0])&& empty($tickets[1])){
                
                echo $html .= ' <tr class="">
                            <td colspan="6">
                                <b>No Record Found</b>
                            </td>
                        </tr>';
                exit;   
            }else{
                foreach($tickets as $tick){
                 foreach($tick as $ticket){
                    if($ticket['clientread'] == 0){
                        $text = 'text-danger';
                    }else{
                        $text = '';
                    }
                    $html .= ' <tr class="'.$text.'">
                            <td data-order="'.$ticket["ticketid"].'">
                                    <span  style="color: #333333 !important; cursor:pointer; ">
                                       <a onclick="show_ticket_status('.$ticket["ticketid"].')" ><b> # '.$ticket["ticketid"].'</b></a> 
                                    </span>    
                            </td>
                            <td><span style="color: #333333 !important; cursor:pointer; " >
                                <a onclick="show_ticket_status('.$ticket["ticketid"].')" ><b>'.$ticket["subject"].'</b></a>
                                </span>
                            </td>
                             <td>
                               <span class="badge badge-success" style= "background-color:'.$ticket['statuscolor'].'">'.$ticket["status_name"].'</span>
                              </td>
                              <td>';
                            $html .=    date("m-d-Y", strtotime($ticket["date"]));
                    $html .= '</td>
                              <td>';
                                    if ($ticket["lastreply"] == NULL) {
                                    $html .=  _l("client_no_reply");
                                   } else {
                                    $html .=  _dt($ticket["lastreply"]);
                                   }
                                  
                    $html .= '</td>
                            </tr>';
                    }
                }
                echo $html; 
                exit ;
            }
        }
        else
        { 
            // $where = 'tbltickets.userid IN (' . $new . ') AND status = ' . $this->input->post('id');  
            //changed as per client's requirement. 
            $where = 'tbltickets.userid IN (' . $new . ') AND status IN ( 4,5 )';
            $data['list_status'] = $this->input->post('id');
            $data['bodyclass'] = 'tickets';
            $tickets   = $this->tickets_model->get('', $where);
                    if($tickets){
                     foreach($tickets as $ticket){
                        if($ticket['clientread'] == 0){
                            $text = 'text-danger';
                        }else{
                            $text = '';
                        }
                        $html .= ' <tr class="'.$text.'">
                                <td data-order="'.$ticket["ticketid"].'">
                                        <span style="color: #333333 !important; cursor: pointer; ">
                                            <a onclick="show_ticket_status('.$ticket["ticketid"].')" ><b> # '.$ticket["ticketid"].'</b></a>
                                        </span>     
                                </td>
                                 <td><span  style="color: #333333 !important; cursor:pointer; " >
                                        <a onclick="show_ticket_status('.$ticket["ticketid"].')" ><b>'.$ticket["subject"].'</b></a>
                                    </span>
                                 </td>
                                 <td>
                                   <span class="badge badge-success" style= "background-color:'.$ticket['statuscolor'].'">'.$ticket["status_name"].'</span>
                                  </td>
                                  <td>';
                                $html .=    date("m-d-Y", strtotime($ticket["date"]));
                        $html .= '</td>
                                  <td>';
                                        if ($ticket["lastreply"] == NULL) {
                                        $html .=  _l("client_no_reply");
                                       } else {
                                        $html .=  _dt($ticket["lastreply"]);
                                       }
                        $html .= '</td>
                                 </tr>';
                    }
                    echo $html; 
                    exit ;
                }
                else{
                    echo $html .= ' <tr class="">
                                    <td colspan="6">
                                        <b>No Record Found</b>
                                    </td>
                                </tr>';
                    exit;
                }
                
            }
        
        }
        public function rad()
        {
            if (!is_client_logged_in()) {
                redirect(site_url('clients/login'));
            }
            if($this->input->post())
                {
                    
                    $user_id = $this->session->userdata('contact_user_id');
                    $data = array(
                        'name'      => $this->input->post('name'),
                        'value'     => $this->input->post('value')
                    );
                
                    $save       = $this->clients_model->rad_save($user_id , $data);
                        
                }
                
            $user_id                = $this->session->userdata('contact_user_id');
            $check_ticket           = $this->tickets_model->rad_check($user_id, 'email_alert_support');
            if(empty($check_ticket))
                {
                    $data['default_support'] = 1;
                }
                
            $check_proposal             = $this->tickets_model->rad_check($user_id, 'email_alert_proposal');
            
            if(empty($check_proposal))
                {
                    $data['default_proposal'] = 1;
                }
                
            $data['alerts'] = $this->clients_model->rad_get($user_id);
            $data['title']  = "RAD";
            $this->data     = $data;
            $this->view     = 'rad';
            $this->layout_new();
        }
    public function reports($id = '')
    {
        
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }
        
        $user_id = $this->session->userdata('contact_user_id');
        $data['company_for_versieats']        = $this->inventory_model->get_company($user_id);
       // echo "<pre>"; print_r($data['company_for_versieats']); exit('no'); 
       if(empty($data['company_for_versieats'])){
            $data['having_no_company'] = 'no'; 
       }
       else{
            $related_merchant = ''; $related_merchant_versiPos = ''; 
            foreach($data['company_for_versieats'] as $com){ 
                if($com['versieats_merchant_id'] != ''){
                    $related_merchant .= $com['versieats_merchant_id'] . ',' ; 
                }///versiPos
                if($com['versiPOS_username'] != '' && $com['versiPOS_password'] != '' && $com['versiPOS_merchant_id'] != '' && $com['versiPOS_store_id'] != ''){
                    $related_merchant_versiPos .= $com['userid'] . ',' ; 
                }
                ///versiPos end
            }
            if($related_merchant ){
                 $data['flagToShowVerseatsTab'] = true;
                 $related_merchant = rtrim($related_merchant, ',' );
                 $related_merchant = trim($related_merchant); 
                 $related_merchant_array = explode(',', $related_merchant);
                 $data['all_related_merchant'] = $this->clients_model->merchant($related_merchant_array);
                }
            if($_POST['run']){
                $formdata = array();
                parse_str($_POST['formdata'], $formdata);
                $reports_data = $this->clients_model->reports($formdata);                
                $time_zone = $this->clients_model->get_time_zone($formdata['restaurant_name']);
                
                $data['reportingon'] = date('m/d/Y', strtotime(date('Y-m-d H:i:s'))) ; 
                if($time_zone){
                     date_default_timezone_set($qtime_zone[0]['option_value']);
                     $data['reportingon'] = date('m/d/Y', strtotime(date('Y-m-d H:i:s'))) ;               
                    }
                    else{
                         date_default_timezone_set('Pacific/Midway');
                         $data['reportingon'] = date('m/d/Y', strtotime(date('Y-m-d H:i:s'))) ; 
                    }

                $merchant = $this->clients_model->merchant($formdata['restaurant_name']);
                 if($reports_data){

                        $data['reports_data'] = $reports_data; 
                        $data['related_merchant_array'] = $related_merchant_array; 
                        $data['runtime'] = date('Y-m-d H:i:s');
                        $data['r_of_day'] = ' ALL DAY REPORT ';

                        
                        if($formdata['day'] == 2) {
                            $data['r_of_day'] = 'YESTERYDAY REPORT';
                            $data['reportingon']  = date('m/d/Y', strtotime("-1 day", strtotime(date('m/d/Y'))));
                        }
                        if($formdata['day'] == 7) {
                            $data['r_of_day'] = 'SEVEN DAYS REPORT';
                            $data['reportingon']  = date('m/d/Y', strtotime("-7 day", strtotime(date('m/d/Y')))) . '  To  '. date('m/d/Y', strtotime(date('Y-m-d H:i:s')));
                         }
                        if($formdata['day'] == 30) { 
                            $data['r_of_day'] = 'THIRTY DAYS REPORT';
                            $data['reportingon']  = date('m/d/Y', strtotime("-30 day", strtotime(date('m/d/Y')))). ' To  '. date('m/d/Y', strtotime(date('Y-m-d H:i:s')));
                        }
                        if(isset($formdata['datefrom']) && isset($formdata['dateto'])){
                            $data['r_of_day'] = 'CUSTOM REPORT';
                            $data['reportingon']  = date('m/d/Y', strtotime($formdata['datefrom'])) . '  To  '. date('m/d/Y', strtotime($formdata['dateto'])) ; 
                            // echo "<pre>"; print_r($data['reportingon'] ); exit('lplplp'); 
                         }

                        $data['merchant'] = $merchant; 
                        $data['report_id'] = $_POST['day']; 
                        
                    }
                //$this->daily_report_mail() ; 
	//			echo "<pre>";  print_r($data); exit('2485'); 
                $datah['all'] = $this->load->view('themes/flat/views/reportresponce', $data, true); 
                echo json_encode($datah['all']); 
                exit; 
            }
            else{     
                if($data['flagToShowVerseatsTab'] == true){             
                    $related_merchant = trim($related_merchant); 
                    $related_merchant_array = explode(',', $related_merchant);
                    $reports_data = $this->clients_model->reports($related_merchant_array[0]);
                    $data['reportingon'] = date('m/d/Y', strtotime(date('Y-m-d H:i:s'))) ; 
                    // $data['runtime'] = date('Y-m-d H:i:s');
                    
                    $time_zone = $this->clients_model->get_time_zone($related_merchant_array[0]); 
                    if($time_zone){
                         
                         date_default_timezone_set($qtime_zone[0]['option_value']);
                         $data['reportingon'] = date('m/d/Y', strtotime(date('Y-m-d H:i:s'))) ;
                         // $data['runtime'] = date('Y-m-d H:i:s');  

                        }
                        else{
                             date_default_timezone_set('Pacific/Midway');
                             $data['reportingon'] = date('m/d/Y', strtotime(date('Y-m-d H:i:s'))) ;
                             // $data['runtime'] = date('Y-m-d H:i:s');  
                        }

                    $merchant = $this->clients_model->merchant($related_merchant_array[0]);
                    // print_r($related_merchant_array[0]); exit('inner'); 
                    if($reports_data){
                        $data['reports_data'] = $reports_data; 
                        $data['related_merchant_array'] = $related_merchant_array; 
                        $data['runtime'] = date('Y-m-d H:i:s'); 
                        $data['reportingon'] = date('m/d/Y', strtotime(date('Y-m-d H:i:s'))) ; 
                        $data['r_of_day'] = ' ALL DAY REPORT ';
                        $data['merchant'] = $merchant; 
                        $data['report_id'] = 1;
                        $data['report_api_id'] = 1; 
                        // // for versiPos tab start
                        // // echo $related_merchant_versiPos; exit('lpp'); 
                        //     $versidata              = $this->get_versiPos_data($related_merchant_versiPos);
                        //     $data['versiPos']       = $versidata['ReportTxt'];
                        //     $data['list']           = $versidata['list'];
                        //     // echo "<pre>"; print_r($data['list']); 
                        //     // exit('kp');
                        //     $data['versiPos_merchants'] = $this->clients_model->get_versiPos_merchants($this->session->userdata('contact_user_id'));
                        // // echo "<pre>"; print_r($data['versiPos_merchants']); exit('kokokoko'); 
                        // // for versiPos tab end 
                    }
                }
                // for versiPos tab start
                        // echo $related_merchant_versiPos; exit('lpp'); 
                            $versidata              = $this->get_versiPos_data($related_merchant_versiPos);
                            
                            // echo "<pre>"; print_r($versidata);  exit('kp');
                            
                            $data['list']           = $versidata['list'];
                            $data['Report_details'] = $versidata['Report_details'];
                            $data['versiPos']       = $versidata['ReportTxt'];
			    $data['error']       	= $versidata['error'];
                            
                            // echo "<pre>"; print_r($data['list']);  exit('kp');
                            $data['versiPos_merchants'] = $this->clients_model->get_versiPos_merchants($this->session->userdata('contact_user_id'), $data['list']);
                            $related_merchant_array = explode(',', $related_merchant_versiPos);
                            $data['versiPos_default_filter'] = $this->clients_model->versiPos_reports_default_filter($this->session->userdata('contact_user_id'), '', $related_merchant_array[0]);
                        // echo "<pre>"; print_r($data['versiPos_merchants']); exit('kokokoko'); 
                        // for versiPos tab end 
            }
            // $data['all_related_merchant'] = $this->clients_model->merchant($related_merchant_array);
        }
        


        $data['title'] = 'Reports';
        $this->data           = $data;
        // echo "<pre>"; print_r($data); exit('out');
        $this->view           = 'reports';
        $this->layout_new();
    }
public function get_versiPos_data($val = ''){
        
        if($_POST){
			$formdata	= $_POST;  
			//parse_str($_POST['formdata'], $formdata); 
            $reports_versipos = $this->clients_model->get_versiPOS_api_data($formdata);
            // echo "<pre>"; print_r($reports_versipos); exit('l');  
            if($reports_versipos != ''){
                $data['versiPos']       = $reports_versipos['ReportTxt'];  
                $data['Report_details']  = $reports_versipos['Report_details'];      
            }
            else{
                 $data['versiPos'] = '';   
            }
			// echo "<pre>"; print_r($data) ; exit('ll'); 
            $dataall['all_versiPos'] = $this->load->view('themes/flat/views/api_report_reponce', $data, true); 
            echo json_encode($dataall['all_versiPos']); 
            exit;
        }
        else{
        
            $related_merchant_POS = trim($val); 
            $related_merchant_array = explode(',', $related_merchant_POS);
            // echo "<pre>"; print_r($related_merchant_array); exit('kl');  
            $reps_val = $this->clients_model->get_versiPOS_api_data($related_merchant_array[0]);
            // echo "<pre>"; print_r($reps_val) ; exit('ll'); 
            return  $reps_val;
        }


}

 public function versiPos_reports_default_filter(){
    if($_POST)
        {
            $client_id = $_POST['selected'];  
            if(isset($_POST['checkbox'])){
                $filters = json_encode($_POST['checkbox']);
                   
            }
            else{
                $filters = '';
            }
            
            $data = array(
                'filters'       => $filters,
                'contact_id'    => $this->session->userdata('contact_user_id'),
                'created_at'    => date('Y-m-d h:i:s'), 
                'client_id'     => $client_id,
            );
           // print_r($data); exit('w'); 
            $insert_filters = $this->clients_model->versiPos_reports_default_filter($data, 'insert', $client_id);
             if($insert_filters){
                   return  $insert_filters; //echo "<pre>";  echo $insert_filters;exit('insert'); 
             }  
        }
    }

//email code
public function daily_report_mail(){ 
    //$this->load->model('emails_model');
    //$es = $this->emails_model->report_email('zeeshananweraziz@gmail.com','zeeshananwar@leadconcept.com', 'i am here ! check all' );
    //echo $es; exit('ji');
    $clients_for_email = $this->clients_model->get_all_clients_for_mail();
          if($clients_for_email){
                $c_emails = array(); 
                foreach ($clients_for_email as $c_email) {
                     if($c_email['versieats_daily_reports'] !='' && $c_email['versieats_merchant_id'] !=''){
                        $mails     = explode(',', $c_email['versieats_daily_reports']);
                        $clientid = $c_email['userid']; 
                        $merchants  = explode(',', $c_email['versieats_merchant_id']);
                    }
                    else{
                        continue;
                    }
                    if(!empty($merchants) && !empty($mails)){

                        $reports = array();
                        foreach ($merchants as $mer){                           //geting daily reports data of each merchant.
                           
                                $closingtime = $this->clients_model->get_closing_hours($mer);
                                $currenttime = date('H:i ', time());   
                                //timezone start
                                 $time_zone = $this->clients_model->get_time_zone($mer); 
                                    if($time_zone){
                                        date_default_timezone_set($time_zone);
                                        $currenttime = date('H:i ', time()); 
                                    }
                                    else{
                                         date_default_timezone_set('Pacific/Midway');
                                         $currenttime = date('H:i ', time());    
                                    }
                                //timezone end
                            
                                if(strtotime($currenttime) >= strtotime($closingtime)){
                                     
                                    $repo = $this->clients_model->reports($mer);
                                    $merchant = $this->clients_model->merchant($mer);
                                    if(!empty($repo)){
                                            $data['reports_data'] = $repo; 
                                            $data['related_merchant_array'] = $related_merchant_array; 
                                            $data['runtime'] = date('Y-m-d H:i:s'); 
                                            $data['reportingon'] = date('m/d/Y', strtotime(date('Y-m-d H:i:s'))) ; 
                                            $data['r_of_day'] = ' ALL DAY REPORT ';
                                            $data['merchant'] = $merchant; 
                                            $data['report_id'] = 1; 
                                        $html_email = $this->load->view('themes/flat/views/report_email_templete', $data, true);
                                        foreach ($mails as $mail) {
                                            
                                            $report_sender = $this->clients_model->report_sended($mer, $mail, $clientid, date('Y-m-d'));
                                            
                                            if($report_sender){
                                                     continue;                                       
                                            }
                                            else{
                                                 $email = $mail ;
                                                    //$email = 'zeeshananwar@leadconcept.com' ;
                                                    $subject = "Lagacy Report";
                                                    $message = $html_email;
                                                    $this->load->model('emails_model');
                                                    $send_mail = $this->emails_model->report_email($email, $subject, $message);    
                                                    //$send_mail = $this->emails_model->send_simple_email($email, $subject, $message);
                                                    if($send_mail){
                                                        $this->clients_model->add_reports_flag($mer , $mail, $clientid,  date('Y-m-d'));
                                                    }  
                                            }

                                                    
                                        }
                                    }
                            }
                          } // exit('out'); 
                      }
                } 
            }
           //exit('outt');
    }
    
    public function create_tickets_by_imap(){
        $val = 0 ;
        $this->load->model('departments_model');
        $this->load->model('tickets_model');
        $departments_data=$this->departments_model->get();
        
        if($departments_data){
            require_once(APPPATH . 'third_party/php-imap/Imap.php');
            foreach ($departments_data as $dept_data) {
                if($dept_data['host'] && $dept_data['imap_username'] && $dept_data['password'] ){
                    $password = $this->encryption->decrypt($dept_data['password']);
                    $imap       = new Imap( $dept_data['host'], $dept_data['imap_username'], $password, $dept_data['encryption']);
                    if ($imap->isConnected() === true) {
                            $unreads = $imap->getUnreadMessages(true);
                        foreach($unreads as $unread){
                            $from_arr = explode(' ', $unread['from']);
                             $customers = $this->tickets_model->get_companies_for_imap($from_arr[2]);
                            if($customers){
                                foreach ($customers as $cust) {
                                    $data['adminreplying'] = 0; 
                                    $data['userid'] = $cust['company_id'];
                                    $data['contactid'] = $cust['contact_id'];
                                    $data['department'] = $dept_data['departmentid']; 
                                    $data['priority'] = 2;
                                    $data['service'] = 3;
                                    $data['subject'] = $unread['subject'];    // not set
                                    $data['message'] = $unread['body'];            
                                    $data['status'] = 1;
                                    $data['source'] = 3;
                                    $data['portfolio_id'] = get_client_portfolio($cust['company_id']);
                                    $data['notify_ticket_body'] = 1;  
                                    $res = $this->tickets_model->add($data); 
                                    if($res){
                                        $val++;
                                    }
                                }
                                $val = $imap->setUnseenMessage($unread['uid']);
                            }  
                        }
                        return $val;
                    }
                    else{
                        //exit('out');
                        return false;
                    }
                }
            }
        }
    }


public function report_email_send()
    {
    
    $emails = explode(',',$this->input->post('to'));
    $html = $this->input->post('html_code');
       
    require_once(APPPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php'); 
    $tcpdf = new TCPDF(); 
    //$tcpdf->addTTFfont('vendor/tecnickcom/tcpdf/fonts/Courier_Prime.ttf', 'TrueTypeUnicode','',20);
	$tcpdf->SetFont('courier', '', 14, '', true);
    $tcpdf->AddPage();
	
    $file_path = $_SERVER['DOCUMENT_ROOT'].'versi Report.pdf';
    $tcpdf->writeHTML($html);

//Close and output PDF document
    $tcpdf->Output($file_path, 'F'); 
    
    
        foreach($emails as $email) 
        { 
        $subject = "Versi Reports";
        $message = 'Versipos Reports';
        $this->load->model('Emails_model');
            
        $send_mail = $this->Emails_model->report_email($email, $subject, $message, true);
        } 
        if($send_mail){
            unlink($file_path);
        }
        
    }    
    
   public function get_versipos_listing()
   {
        if($_POST){
            $config = $this->clients_model->get_versiPOS_api_configuration($_POST['id']);
            $defult = $this->clients_model->versiPos_reports_default_filter('','', $_POST['id']);
        
            $fields = array(
            "ApiKey"=> "d17aaf42f76047339de1c749e9cf3bbc",
            "MerchantID"=> $config['versiPOS_merchant_id'],
            "StoreID"   => $config['versiPOS_store_id'],
            "Username"  => $config['versiPOS_username'],
            "Password"  => $config['versiPOS_password'],
            "Format"=> "text"
            );
            $ch = curl_init('https://reports.versipos.net:1502/dataconnect/GET_REPORT_LISTING');
            curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,

            CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            ),
            CURLOPT_POSTFIELDS => json_encode($fields)
            ));
            $list_response = curl_exec($ch);
            $list = json_decode($list_response);
            $data['repo_list'] = $list->Reports;
            $data['defult'] = $defult;
            echo json_encode($data);
            exit();
        }
   }
    public function report_pdf()
    {
      $this->load->helper('download');

	  $html = $this->input->post('html_code');
   
	  require_once(APPPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php'); 
	  $tcpdf = new TCPDF(); 
	  $tcpdf->SetFont('courier', '', 14, '', true);
	  $tcpdf->AddPage();
      $newtime=time();
      $filename=$newtime.'versiPos_Report.pdf';
      // echo base_url();
	  $file_path=$_SERVER['DOCUMENT_ROOT'].$filename;
      $file_path_n='../../'.$filename;

	  
	  $tcpdf->writeHTML($html);

	  //Close and output PDF document
	  $tcpdf->Output($file_path,'F');

      echo json_encode(array("file_path" => $file_path_n,"file_name" => $filename));
   }

    public function delete_report_pdf(){

        if(isset($_POST['f_name'])){
            $newurl=$_SERVER['DOCUMENT_ROOT'].'/'.$_POST['f_name'];
            $response=unlink($newurl); 
            echo $response;
        }
    }	
   public function versieats_settings(){
      if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }   
        if($_POST){
            parse_str($_POST['form_data'],$post_data); 
            if(!isset($post_data['merchants_turn'])){
                return false; 
            }
            $arr = array(); 
            if($post_data['merchants_turn'] == 1) {
                    $now = time();
                    $datefrom   = date('Y-m-d h:i:s'); 
                    $now = time();
                    $days = $now + (24 * 60 * 60);
                    $datefrom = date('Y-m-d H:i:s', $now);
                    $dateto = date('Y-m-d H:i:s', $days);
                    array_push($arr, $post_data['merchants_turn']);
                    array_push($arr, $datefrom);
                    array_push($arr, $dateto);
                }

            if($post_data['merchants_turn'] == 2) {
                $now = time();
                $datefrom   = date('Y-m-d h:i:s'); 
                $now = time();
                $days = $now + (1 * 60 * 60);
                $datefrom = date('Y-m-d H:i:s', $now);
                $dateto = date('Y-m-d H:i:s', $days);
                array_push($arr, $post_data['merchants_turn']);
                array_push($arr, $datefrom);
                array_push($arr, $dateto);
            }

            if( isset($post_data['hours']) ){
                $now = time();
                $datefrom   = date('Y-m-d h:i:s'); 
                $now = time();
                $days = $now + ($post_data['hours'] * 60 * 60);
                $datefrom = date('Y-m-d h:i:s', $now);
                $dateto = date('Y-m-d h:i:s', $days);
                array_push($arr, $post_data['merchants_turn']);
                array_push($arr, $datefrom);
                array_push($arr, $dateto);
                unset($post_data['hours']); 
            }
            if( isset($post_data['datefrom']) &&  !isset($post_data['dateto']) ){
                $datefrom  = $post_data['datefrom'] . ' 00:00:00'; 
                $dateto  = $post_data['datefrom'] . ' 23:59:59'; 
                array_push($arr, $post_data['merchants_turn']);
                array_push($arr, $datefrom);
                array_push($arr, $dateto);
                unset($post_data['datefrom']);

            }
            if( isset($post_data['datefrom']) &&  isset($post_data['dateto']) ){
                  $datefrom  = $post_data['datefrom'] . ' 00:00:00'; 
                  $dateto  = $post_data['dateto'] . ' 23:59:59';
                  array_push($arr, $post_data['merchants_turn']);
                  array_push($arr, $datefrom);
                  array_push($arr, $dateto);
                  unset($post_data['datefrom']);
                  unset($post_data['dateto']); 
            }

            if(!empty($arr)){
                $post_data['merchants_turn'] = json_encode($arr, true);                 
            }

            if($post_data['merchants_turn'] == '0'){ $post_data['merchants_turn'] = 'no';}

            if(!isset($post_data['turn_off_pickup']) ){ $post_data['turn_off_pickup'] = 0;}
            if(!isset($post_data['turn_off_delivery']) ){ $post_data['turn_off_delivery'] = 0;}

            
            $post_data['temp_pickup_interval']      = $post_data['temp_pickup_interval'] + $post_data['dp_interval'] ; 
            $post_data['temp_delivery_interval']    = $post_data['temp_delivery_interval']+ $post_data['dd_interval'];


            if($post_data['temp_pickup_next_time'] == ''){ $post_data['temp_pickup_next_time'] = 0;}
            if($post_data['temp_delivery_next_time'] =='') { $post_data['temp_delivery_next_time'] = 0 ; }
            
            if(!empty($post_data['merchant_holiday'])) {  
                $json = json_encode($post_data['merchant_holiday'] );
                $post_data['merchant_holiday'] = addslashes($json); 
                }
             
             $insert = $this->clients_model->versieats_settings_save($post_data);
                if($insert){
                    return true; 
                }
                else{
                    return false;
                }
            }
        
        else{
            $user_id = $this->session->userdata('contact_user_id');
            $data['company_for_versieats']        = $this->inventory_model->get_company($user_id);
               if(empty($data['company_for_versieats'])){
                    $data['having_no_company'] = 'no'; 
               }
           else{
                $related_merchant = array();  
                foreach($data['company_for_versieats'] as $com){ 
                        if($com['versieats_merchant_id'] != ''){
                            $ids = explode(',', $com['versieats_merchant_id']);
                            foreach ($ids as $id) {
                                $merchant_info = $this->clients_model->versieats_merchants_info($id, 'merchant_id, restaurant_slug, restaurant_name');
                                if($merchant_info){
                                    array_push($related_merchant, $merchant_info);        
                                }
                            }
                        }
                    }
                    if(count($related_merchant)  == 1){
                        $all_data = $this->clients_model->get_restaurant_info($related_merchant[0]['merchant_id']); 
                        // echo "<pre>"; print_r($all_data); exit('hhjjhhjjh'); 
                        $data['all'] = $all_data[0]; 
                        $data['option'] = $all_data[1];
                        $data['merchant_holiday'] = stripslashes($all_data[2]['option_value']);

                }
            }
        }      
    $data['all_merchant'] =   $related_merchant;   
    // echo "<pre>"; print_r($related_merchant); exit('min');  
    $data['title'] = 'Versieats';
    $this->data           = $data;
    $this->view           = 'versieats_settings';
    $this->layout_new();
   }
  public function get_restaurant_data(){

        $id = $this->input->post('val');
        if($id)
        {
            $all_data = $this->clients_model->get_restaurant_info($id);
            // echo "<pre>"; print_r($all_data); exit('hhjjhhjjh');  
            $data['all'] = $all_data[0]; 
            $data['option'] = $all_data[1];
            $data['merchant_holiday'] = stripslashes($all_data[2]['option_value']);

             
            // echo "<pre>";  print_r($data); exit('kokokok'); 

             // $html = '<div>hellllllllllllllllllllllllllllllladip osafdsi</div>';  
            
            $res = $this->load->view('themes/flat/views/versieats_sub_settings',$data, true);


            echo json_encode($res);
            // echo "<pre>"; print_r(json_encode($res)); exit('welllll'); 
            exit();
        } 
    }

}
