<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
		$this->load->model('portfolio_model');		
		$this->load->helper('cookie');
    }
    
	public function index()
	{	 		
		$id = $this->session->userdata('staff_user_id');
		$portfolio = $this->portfolio_model->get_portfolio_data($id); 				
		//echo "<pre>"; print_r($portfolio); exit('out');
		if(empty($portfolio))
		{
			$data['massage'] = "<h3>You have no Portfolio assigned. Please contact Administrator</h3>"; 
			$data['title'] = 'Portfolio Selection';
			$this->load->view('admin/portfolio/selection', $data);		
		}
		else
		{
			if(sizeof($portfolio)==1)
			{	

				
					$this->session->set_userdata('portfolio_id', $portfolio[0]->portfolio_name_id);
					check_portfolio_id();
					if($_COOKIE['autologin']  != '')
					{	
						$se = unserialize($_COOKIE['autologin']); 
						$portfolio_id = $portfolio[0]->id ; 
						$user_id = $se['user_id']; 
						$key = $se['key'];		
					
						set_cookie(array(
							'name' => 'autologin',
							'value' => serialize(array(
								'user_id' => $user_id,
								'key' => $key,
								'portfolio_id' => $portfolio_id
								)),
							'expire' => 60 * 60 * 24 * 31 * 2, // 2 months
						));
					}
					$only_portfolio = $this->dashboard();
			}
			else
			{
				$data['portfolio'] = $portfolio; 
				$data['title'] = 'Portfolio Selection';
				$this->load->view('admin/portfolio/selection', $data);		
			}
		}
	}
	
	/* This is admin dashboard view */
    public function dashboard($id = '')
    { 
		if($id)
		{
            // decoding encoded url below 
            $id = decode_uri($id);
			$this->session->set_userdata('portfolio_id', $id);
			check_portfolio_id();
			  
			if($_COOKIE['autologin'] == '')
				{	
					$se = unserialize($_COOKIE['autologin']); 
					$portfolio_id = $portfolio[0]->id ; 
					$user_id = $se['user_id']; 
					$key = $se['key'];		
				
					set_cookie(array(
						'name' => 'autologin',
						'value' => serialize(array(
							'user_id' => $user_id,
							'key' => $key,
							'portfolio_id' => $id,
							)),
						'expire' => 60 * 60 * 24 * 31 * 2, // 2 months
					));
				}
			
		} 
		//echo "here"; print_r( unserialize($_COOKIE['autologin']) ); exit; 
		close_setup_menu();
        $this->load->model('departments_model');
        
		$this->load->model('todo_model');
        $data['departments']               = $this->departments_model->get();

        $data['todos']                     = $this->todo_model->get_todo_items(0);
        // Only show last 5 finished todo items
        $this->todo_model->setTodosLimit(5);
        $data['todos_finished']            = $this->todo_model->get_todo_items(1);
        
		$data['upcoming_events_next_week'] = $this->dashboard_model->get_upcoming_events_next_week();
        //echo "<pre>"; print_r($data['upcoming_events_next_week']); exit; 
        $data['upcoming_events']           = $this->dashboard_model->get_upcoming_events();
        
		$data['title']                     = _l('dashboard_string');
        $this->load->model('currencies_model');
        
		$data['currencies']                           = $this->currencies_model->get();
        $data['base_currency']                        = $this->currencies_model->get_base_currency();
        
		$data['activity_log']                         = $this->misc_model->get_activity_log();
        // Tickets charts
        $tickets_awaiting_reply_by_status = $this->dashboard_model->tickets_awaiting_reply_by_status();
        $tickets_awaiting_reply_by_department = $this->dashboard_model->tickets_awaiting_reply_by_department();

        $data['tickets_reply_by_status']              = json_encode($tickets_awaiting_reply_by_status);
        $data['tickets_awaiting_reply_by_department'] = json_encode($tickets_awaiting_reply_by_department);

        $data['tickets_reply_by_status_no_json']              = $tickets_awaiting_reply_by_status;
        $data['tickets_awaiting_reply_by_department_no_json'] = $tickets_awaiting_reply_by_department;

        $data['projects_status_stats']                = json_encode($this->dashboard_model->projects_status_stats());
        $data['leads_status_stats']                   = json_encode($this->dashboard_model->leads_status_stats());
        $data['google_ids_calendars']                 = $this->misc_model->get_google_calendar_ids();
        $data['bodyclass']                            = 'home dashboard invoices_total_manual';
        $this->load->model('announcements_model');
        $data['staff_announcements'] = $this->announcements_model->get();
        $data['total_undismissed_announcements'] = $this->announcements_model->get_total_undismissed_announcements();

        $data['goals'] = array();
        if (is_staff_member()) {
            $this->load->model('goals_model');
            $data['goals'] = $this->goals_model->get_staff_goals(get_staff_user_id());
        }

        $this->load->model('projects_model');
        $data['projects_activity'] = $this->projects_model->get_activity('', do_action('projects_activity_dashboard_limit', 20));
        // To load js files
        $data['calendar_assets']   = true;
        $this->load->model('utilities_model');
        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $wps_currency = 'undefined';
        if (is_using_multiple_currencies()) {
            $wps_currency = $data['base_currency']->id;
        }
        $data['weekly_payment_stats'] = json_encode($this->dashboard_model->get_weekly_payments_statistics($wps_currency));
		
        $data['dashboard']             = true;

        $data['user_dashboard_visibility'] = $GLOBALS['current_user']->dashboard_widgets_visibility;

        if (!$data['user_dashboard_visibility']) {
            $data['user_dashboard_visibility'] = array();
        } else {
            $data['user_dashboard_visibility'] = unserialize($data['user_dashboard_visibility']);
        }
		
        $data['user_dashboard_visibility'] = json_encode($data['user_dashboard_visibility']);
 
        $data = do_action('before_dashboard_render', $data);
       	//echo "<pre>"; print_r($data); exit;  
		$this->load->view('admin/dashboard/dashboard', $data);
    }
	 
	

    /* Chart weekly payments statistics on home page / ajax */
    public function weekly_payments_statistics($currency)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->dashboard_model->get_weekly_payments_statistics($currency));
            die();
        }
    }
	
	
    public function get_email(){
    	$id = 2;
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
						echo "<pre>"; print_r($unreads); exit('all unreads');  
						foreach($unreads as $unread){
                            $from_arr = explode(' ', $unread['from']);
                             $customers = $this->tickets_model->get_companies_for_imap($from_arr[2]);
                            if($customers){
                                foreach ($customers as $cust) {
                                    $data['adminreplying'] = 0; 
                                    $data['userid'] = $cust['company_id'];
                                    $data['contactid'] = $cust['contact_id'];
                                    $data['department'] = 2; 
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
	public function create_tickets_reply_by_imap(){
        $id = 2;
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
						$folders = $imap->getFolders();
						echo "<pre>"; 
						//print_r($folders);  exit('io');
						$imap->selectFolder($folders[4]);
						$unreads = $imap->getMessages();
						print_r($unreads);  
						exit('io');
						//echo $unreads[0]['uid'];
						$msg_id = $imap->getMessageHeader(16);
						//$msg_id = $imap->getMessageHeader(13);
						
						echo "<pre>"; print_r($msg_id) ; //print_r($unreads);  
						exit('io');
						foreach($unreads as $unread){;
                            $from_arr = explode(' ', $unread['from']);
                             $customers = $this->tickets_model->get_companies_for_imap($from_arr[2]);
                            if($customers){
                                foreach ($customers as $cust) {
                                    $data['adminreplying'] = 0; 
                                    $data['userid'] = $cust['company_id'];
                                    $data['contactid'] = $cust['contact_id'];
                                    $data['department'] = 2; 
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
}
