<?php 

if (! defined('BASEPATH')) exit('No direct script access');

class login extends MY_Controller {
	
	
	/**
	 * Authenticate user and store to session
	 *
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function index() {
		$this->load->model('user_mdl');
		$this->load->library('form_validation');
		$this->load->helper('form');
		
		$ctt_data = array();
		$ctt_data['users'] = $this->user_mdl->get_users();
		
		if($this->input->post('submit')){
			//	allways clear current user on new attempt to login
			$this->session->unset_userdata('crrUser');
			
			//	Validate input
			$this->form_validation->set_rules('user', 'gebruikersnaam', 'required|is_natural_no_zero');
			$this->form_validation->set_rules('pass', 'wachtwoord', 'required');
			if($this->form_validation->run()){
				$crrUser = $this->user_mdl->check_pass($this->input->post('user'), $this->input->post('pass'));
				if(!empty($crrUser)){
					//	Store to session
					$this->session->set_userdata('crrUser', $crrUser);
					
					//	Redirect
					$redirect = $this->session->userdata('redirect_url');
					$this->session->unset_userdata('redirect_url');
					if(!empty($redirect)){
						redirect($redirect);
					}else{
						redirect('');
					}
				}else{
					$ctt_data['feedback']->type		= "error";
					$ctt_data['feedback']->message	= "Fout paswoord";
				}
			}else{
				$ctt_data['feedback']->type		= "error";
				$ctt_data['feedback']->message	= "Gelieve alle velden in te vullen";
			}
		}
		
		$this->load->view('login', $ctt_data);
	}
	
	
	
	/**
	 * Clear session and return to login
	 *
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function logout(){
		$this->session->unset_userdata('crrUser');
		redirect('login');
	}

}