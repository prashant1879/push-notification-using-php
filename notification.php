<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	MODULE ID : 8
 */
class Notification extends CI_Controller {

	function __construct()
	{
		// Call the Controller constructor
        parent::__construct();
		
		//Check hospital session
		if(!$this->common_model->checkUserSession()){
			$direct_url		= $this->uri->uri_string();
			redirect(A_LOGINURL);
		}
		
		$this->utc_time    		= time();
	    }

	
	
	public function index()
	{
		
		if (count($_POST) > 0){
                    
			$appId			= $this->input->post('appType');
			$hspId			= $this->input->post('hspId');
			$notiText		= $this->input->post('notiText');
			$userList		= $this->input->post('userList');
			
			$userListStr = "";
			if(is_array($userList) && count($userList) > 0){
				foreach($userList as $key => $value){
					$userListStr .= "'".$value."',";
				}
				
			}
			$userListStr = trim($userListStr,',');
			
			$Search = "";
			if(is_numeric($appId) && $appId > 0){
				$Search .= " AND U_APP_REG_TYPE = '".$appId."'";
			}
			if(($appId == '1' || $appId == '2') && is_numeric($hspId) && $hspId > 0 ){
				$Search .= " AND U_HSP_ID = '".$hspId."'";
			}
			
			$userArr = array();
			if($userListStr != ""){
				$query	= "SELECT
							*
							FROM
								".TBL_USER_TOKEN_DEVICE." as udt
							JOIN
								".TBL_USER." as u ON UDT_U_ID = U_ID AND U_STATUS = 1
							WHERE
								UDT_U_ID IN (".$userListStr.") AND
								UDT_DEVICE_TOKEN != '' AND
								UDT_STATUS = '1'
							".$Search;
				$userArr      = $this->common_model->get_all_data_query($query);
			}else{
				$this->session->set_flashdata('failure', 'Please, Slelect some user');
				redirect(A_NOTIFICATION_URL);
			}
			
			if(is_array($userArr) && count($userArr) > 0){
                            
                            
				$userAllList = array();
				foreach($userArr as $key => $value){
					$u_app_id = $value['U_APP_REG_TYPE'];
					$u_dev_type = $value['UDT_DEVICE_TYPE'];
					if(!in_array($value['UDT_DEVICE_TOKEN'],$userAllList[$u_app_id][$u_dev_type])){
						$userAllList[$u_app_id][$u_dev_type][] = $value['UDT_DEVICE_TOKEN'];
					}
					
				}
				
				foreach($userAllList as $key_app => $value_app){
						
					$query	= "SELECT
									*
								FROM
									".TBL_APP_LIST."
								WHERE
									APP_ID = '".$key_app."' AND
									APP_STATUS = '1'
								";
					$appData      = $this->common_model->get_single_data_query($query);

file_put_contents('notification.txt', "\n\n --Notification deviceTokenData -\n " . json_encode($appData) . "\n\n -- End of deviceTokenData \n", FILE_APPEND | LOCK_EX);
					
					if(is_array($appData) && count($appData['APP_GCM_KEY']) > 0){
						foreach($value_app as $key_device_type => $userdeviceToken){
							if($key_device_type == '1'){
								$message					= array();
								$message['aps']['icon'] 	= "appicon";
								$message['aps']['alert'] 	= $notiText;
								$message['aps']['badge'] 	= "1";
								$message['aps']['category'] = "Notification";
								$message['aps']['sound'] 	= "default";
								$pem_file_path = ADMIN_PEM_REL_IMGPATH.$appData['APP_ID']."/".$appData['APP_PEM_FILE'];
file_put_contents('notification.txt', "\n\n - Notification pem_file_path-- \n " . json_encode($pem_file_path) . "\n\n --- End of message--- \n", FILE_APPEND | LOCK_EX);
								if($appData['APP_PEM_FILE'] != '' && file_exists($pem_file_path)){
file_put_contents('notification.txt', "\n\n - Notification message-- \n " . json_encode($message) . "\n\n --- End of message--- \n", FILE_APPEND | LOCK_EX);
									$this->common_model->send_notification_ios($userdeviceToken,$message,$pem_file_path,$appData['APP_IS_LIVE']); 
								}else{
file_put_contents('notification.txt', "\n\n - Notification pem_file_path-- \n " . json_encode($pem_file_path) . "\n\n --- not exists...End of message--- \n", FILE_APPEND | LOCK_EX);
}
								
							}else{
								$message				= array();
								
								$message['payload']['android']['title']       = $notiText;
								$message['payload']['android']['icon']        = 'appicon';
								$message['payload']['android']['vibrate']     = 'true';
								$message['payload']['android']['badge']       = '1';
								$message['payload']['android']['message']     = $notiText;
								$message['payload']['android']['alert']     = $notiText;
								
								if(isset($appData['APP_GCM_KEY']) && $appData['APP_GCM_KEY'] != ""){
									$this->common_model->send_notification_android($userdeviceToken, $message ,$appData['APP_GCM_KEY']);
								}
							}
						}
					}
				}
				$androidList = array();
				
				//if ($insertId > 0){
					$this->session->set_flashdata('success', 'Notification sent successfully.');
				//}else{
					//$this->session->set_flashdata('failure', 'Problem in sending notification');
				//}
			} else{
				$this->session->set_flashdata('success', 'Notification sent successfully.');
			}
			redirect(A_NOTIFICATION_URL);
		}
		
		$query	= "SELECT
						*
					FROM
						".TBL_HOSPITAL."
					WHERE
						`HSP_STATUS` = '1'
					";
		$hospitalArr      = $this->common_model->get_all_data_query($query);

		$query	= "SELECT
						*
					FROM
						".TBL_SECURITY."
					WHERE
						`SEC_STATUS` = '1'
					";
		$securityArr      = $this->common_model->get_all_data_query($query);
		
		$query	= "SELECT
						*
					FROM
						".TBL_USER."
					WHERE
						`U_STATUS` = '1'
					";
		$userArr      = $this->common_model->get_all_data_query($query);
		
		$query	= "SELECT
						*
					FROM
						".TBL_APP_LIST."
					WHERE
						`APP_STATUS` = '1'
					";
		$appArr      = $this->common_model->get_all_data_query($query);
		
		$userList = array();
		foreach($userArr as $key => $value){
			$id = $value['U_ID'];
			$userList[$id] = $value['U_NAME'];
		}
		
		$hospitalList = array(
				'0'	=> 'All'
			);
		foreach($hospitalArr as $key => $value){
			$id = $value['HSP_ID'];
			$hospitalList[$id] = $value['HSP_NAME'];
		}

		$securityList = array(
			'0'	=> 'All'
		);
		foreach($securityArr as $key => $value){
			$id = $value['SEC_ID'];
			$securityList[$id] = $value['SEC_NAME'];
		}
		
		$appList = array(
				'0'	=> 'All'
			);
		foreach($appArr as $key => $value){
			$id 			= $value['APP_ID'];
			$appList[$id] 	= $value['APP_NAME'];
		}
		
		$data = array(
				'hospitalList'	=> $hospitalList,
				'securityList'	=> $securityList,
				'userList'	=> $userList,
				'appList'	=> $appList,
			);
		
		//print_r($data);
		$this->load->view('layout/header');
		$this->load->view('layout/sidebar');
		$this->load->view('notification/notification_add',$data);
		$this->load->view('layout/footer');
	}
	
}
