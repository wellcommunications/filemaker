<?php 

if (! defined('BASEPATH')) exit('No direct script access');

class MY_Controller extends CI_Controller {

	//php 5 constructor
	function __construct() {
		parent::__construct();
		
		// = CHECK FOR LOGIN =
		if($this->uri->segment(1)!='login' && $this->uri->segment(1)!='logout'){
			$this->_check_login();
		}
	}
	
	
	/**
	 * Check if user is logged in
	 *
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	private function _check_login(){
		$user = $this->session->userdata('crrUser');
		if(!isset($user) || empty($user)){
			$this->session->set_userdata('redirect_url', $this->uri->uri_string());
			redirect('login');
		}
	}
	

}