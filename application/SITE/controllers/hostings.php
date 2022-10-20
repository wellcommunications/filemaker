<?php 

if (! defined('BASEPATH')) exit('No direct script access');

class hostings extends MY_Controller {

	var $subnav;

	function __construct(){
		parent::__construct();
		
		$this->load->model('hostings_mdl');
		$this->load->model('logins_mdl');
		
		//	SET SUBNAV
		$this->subnav = array();
		array_push($this->subnav, array('label'=>'Overzicht',	'url'=>'overview'));
		if($this->session->userdata('crrUser')->allow_add==1)	array_push($this->subnav, array('label'=>'Nieuw', 	'url'=>'add'));
		array_push($this->subnav, array('label'=>'Zoeken', 	'url'=>'search'));
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	public function index() {
		redirect('hostings/' . $this->subnav[0]['url']);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Show list of hostings
	 *
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function overview(){
		$ctt_data = array();
		$ctt_data['hostings']	= $this->hostings_mdl->get_list();
		$ctt_data['months']		= array('error', 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december');
		
		
		$data['page_title']	= "Hostings";
		$data['sub_title'] 	= "Overzicht";
		$data['subnav']		= $this->subnav;
		$data['content'] 	= $this->load->view('c_hostings_overview', $ctt_data, TRUE);
		$this->load->view('master', $data);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Show details of the hosting
	 *
	 * @param int $id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function details($id){
		if(!is_numeric($id)) 	redirect('hostings/overview');
		
		//	LOAD EXTRA RESOURCES
		$this->load->model('addons_mdl');
		
		//	GET DATA
		$ctt_data = array();
		$ctt_data['details'] = $this->hostings_mdl->get_details($id);
		$ctt_data['details']->domains	= $this->hostings_mdl->get_domains($id);
		$ctt_data['details']->addons	= $this->addons_mdl->get_connected_addons('hosting', $id);
		$ctt_data['details']->logins	= $this->logins_mdl->get_connected_logins('hosting', $id);

		//	LOAD VIEW
		$data['page_title']	= "Hostings";
		$data['sub_title'] 	= "Details " . $ctt_data['details']->domain_full;
		$data['subnav']		= $this->subnav;
		$data['content'] 	= $this->load->view('c_hostings_details', $ctt_data, TRUE);
		$this->load->view('master', $data);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Edit details of a hosting
	 *
	 * @param int $id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function edit($id){
		if(!is_numeric($id)) 	redirect('hostings/overview');
		
		//	CHECK AUTHORISATION
		if(!$this->session->userdata('crrUser')->allow_edit==1)	redirect('hostings/overview');
		
		//	LOAD EXTRA RESOURCES
		$this->load->model('domains_mdl');
		$this->load->model('addons_mdl');
		$this->load->library('form_validation');
		$this->load->helper('form');
		
		//	INIT VALUES
		$ctt_data = array();
		$ctt_data['feedback'] = $this->session->flashdata('feedback');
		
		// = PARSE POSTBACK =
		switch($this->input->post('postback')){
			case 'domain':
				$ctt_data['feedback'] = $this->_add_domain($id);
				break;
			case 'addon':
				$ctt_data['feedback'] = $this->_add_addon($id);
				break;
			case 'login':
				$ctt_data['feedback'] = $this->_add_login($id);
				break;
			case 'update_login':
				$ctt_data['feedback'] = $this->_update_login($id);
				break;
			case 'full':
				$ctt_data['feedback'] = $this->_save_hosting($id);
				break;
		}
		
		
		//	GET DATA
		$ctt_data['details'] = $this->hostings_mdl->get_details($id);
		$ctt_data['details']->domains	= $this->hostings_mdl->get_domains($id);
		$ctt_data['details']->addons	= $this->addons_mdl->get_connected_addons('hosting', $id);
		$ctt_data['details']->logins	= $this->logins_mdl->get_connected_logins('hosting', $id);
		$ctt_data['login_types']		= $this->logins_mdl->get_login_types();
		$ctt_data['hosting_sizes'] 		= $this->hostings_mdl->get_hosting_sizes();
		$ctt_data['addons'] 			= $this->addons_mdl->get_addons('hostings');
		$ctt_data['extensions']			= $this->domains_mdl->get_extensions_short();
		$ctt_data['extensions'][0]		= "---";
		
		
		//	LOAD VIEW
		$data['page_title']	= "Hostings";
		$data['sub_title'] 	= "Details " . $ctt_data['details']->domain_full;
		$data['subnav']		= $this->subnav;
		$data['content'] 	= $this->load->view('c_hostings_form', $ctt_data, TRUE);
		$this->load->view('master', $data);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Remove the connection between domainname and hosting
	 *
	 * @param int $id 
	 * @param int $hosting_id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function remove_domain($id, $hosting_id){
		if(!is_numeric($id)) redirect('hostings/edit/' . $hosting_id);
		if(!$this->session->userdata('crrUser')->allow_edit==1)	redirect('hostings/details/' . $hosting_id);
		
		//	DELETE DATA
		$res = $this->hostings_mdl->remove_domain($id);
		if(!$res){
			$feedback->type 	= "error";
			$feedback->title 	= "Fout bij verwijderen";
			$feedback->message 	= "Verwijderen van de domeinnaam was niet succesvol.";
		}else{	
			$feedback->type 	= "success";
			$feedback->title 	= "Domeinnaam verwijderd";
			$feedback->message 	= "De koppeling tussen de domeinnaam en de hosting is verwijderd.";
		}
		
		$this->session->set_flashdata('feedback', $feedback);
		redirect('hostings/edit/' . $hosting_id);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Remove the connection between addon and hosting
	 *
	 * @param int $id 
	 * @param int $hosting_id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function remove_addon($id, $hosting_id){
		if(!is_numeric($id)) redirect('hostings/edit/' . $hosting_id);
		if(!$this->session->userdata('crrUser')->allow_edit==1)	redirect('hostings/details/' . $hosting_id);
		
		//	LOAD EXTRA RESOURCES
		$this->load->model('addons_mdl');
		
		//	DELETE DATA
		$res = $this->addons_mdl->remove('hosting', $id);
		if(!$res){
			$feedback->type 	= "error";
			$feedback->title 	= "Fout bij verwijderen";
			$feedback->message 	= "Verwijderen van de add-on was niet succesvol.";
		}else{	
			$feedback->type 	= "success";
			$feedback->title 	= "Add-on verwijderd";
			$feedback->message 	= "De add-on is verwijderd van op het hostingpakket.";
		}
		
		$this->session->set_flashdata('feedback', $feedback);
		redirect('hostings/edit/' . $hosting_id);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	
	/**
	 * Remove a login
	 *
	 * @param int $login_id 
	 * @param int $hosting_id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function remove_login($login_id, $hosting_id){
		if(!is_numeric($login_id) || !is_numeric($hosting_id)) redirect('hostings/edit/' . $hosting_id);
		if(!$this->session->userdata('crrUser')->allow_edit==1)	redirect('hostings/details/' . $hosting_id);
		
		if($this->logins_mdl->remove_connection('hosting', $hosting_id, $login_id) && $this->logins_mdl->remove($login_id)){
			$feedback->type 	= "success";
			$feedback->title 	= "Login verwijderd";
			$feedback->message 	= "De login is verwijderd uit de database.";
		}else{
			$feedback->type 	= "error";
			$feedback->title 	= "Fout bij verwijderen";
			$feedback->message 	= "De login is niet correct verwijderd uit de database.";
		}
		
		$this->session->set_flashdata('feedback', $feedback);
		redirect('hostings/edit/' . $hosting_id);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	
	/**
	 * Add a new hosting to the database
	 *
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function add(){
		if(!$this->session->userdata('crrUser')->allow_add=1)	redirect('hostings/overview');
		
		//	LOAD EXTRA RESOURCES
		$this->load->model('domains_mdl');
		$this->load->model('addons_mdl');
		$this->load->library('form_validation');
		$this->load->helper('form');
		
		//	INIT DATA
		$ctt_data = array();
		$ctt_data['details']	= new Hosting();
		$ctt_data['details']->month = date('m');
		$ctt_data['details']->creation_date = time();
		$ctt_data['details']->pakket->id = 0;
		$ctt_data['login_types']		= $this->logins_mdl->get_login_types();
		$ctt_data['hosting_sizes'] 		= $this->hostings_mdl->get_hosting_sizes();
		$ctt_data['addons'] 			= $this->addons_mdl->get_addons();
		$ctt_data['extensions']			= $this->domains_mdl->get_extensions_short();
		$ctt_data['extensions'][0]		= "--- Kies een extensie ---";
		
		
		//	PARSE POSTBACK
		if($this->input->post('postback')){
			//	SET FORM VALIDATION
			$this->form_validation->set_rules('domainname', 'Domeinnaam', 'trim|required');
			$this->form_validation->set_rules('extension_id', 'TLD (extensie)', 'trim|is_natural_no_zero');
			
			$this->form_validation->set_rules('contact_firstname', 'Voornaam', 'trim|required');
			$this->form_validation->set_rules('contact_lastname', 'Familienaam', 'trim|required');
			$this->form_validation->set_rules('contact_company', 'Firma', 'trim|required');
			$this->form_validation->set_rules('contact_email', 'E-mail', 'trim');
			
			$this->form_validation->set_rules('month', 'Verlenging', 'trim|is_natural_no_zero|required');
			$this->form_validation->set_rules('creation_date', 'Aangemaakt op', 'trim');
			$this->form_validation->set_rules('deletion_date', 'Verwijderen op', 'trim');
			$this->form_validation->set_rules('hosting_size', 'Pakket', 'trim|is_natural');
			$this->form_validation->set_rules('remarks', 'Opmerkingen', 'trim');
			
			if(trim($this->input->post('contact_email'))!=''){
				$this->form_validation->set_rules('contact_email', 'E-mail', 'trim|valid_email');
			}
			
			if($this->form_validation->run()){
				$db_data = array();
				$db_data['contact_firstname'] 	= ($this->input->post('contact_firstname')!='') ? $this->input->post('contact_firstname') : NULL;
				$db_data['contact_lastname'] 	= ($this->input->post('contact_lastname')!='') ? $this->input->post('contact_lastname') : NULL;
				$db_data['contact_company'] 	= ($this->input->post('contact_company')!='') ? $this->input->post('contact_company') : NULL;
				$db_data['contact_email'] 		= ($this->input->post('contact_email')!='') ? $this->input->post('contact_email') : NULL;
				$db_data['month'] 				= (int)$this->input->post('month');
				$db_data['creation_date'] 		= ($this->input->post('creation_date')!='') ? $this->input->post('creation_date') : NULL;
				$db_data['deletion_date'] 		= ($this->input->post('deletion_date')!='') ? $this->input->post('deletion_date') : NULL;
				$db_data['remarks'] 			= ($this->input->post('remarks')!='') ? $this->input->post('remarks') : NULL;
				$db_data['size_id']				= (int)$this->input->post('hosting_size');
				
				$hosting_id = $this->hostings_mdl->insert_hosting($db_data);
				$redirect = 'hostings/details/'.$hosting_id;
				$this->_add_domain($hosting_id, $redirect);
			}else{
				$ctt_data['feedback']->type 	= "error";
				$ctt_data['feedback']->title 	= "Fout bij form validatie";
				$ctt_data['feedback']->message 	= "Gelieve alle velden correct in te vullen";
			}
		}
		
		
		//	LOAD VIEW
		$data['page_title']	= "Hostings";
		$data['sub_title'] 	= "Toevoegen";
		$data['subnav']		= $this->subnav;
		$data['content'] 	= $this->load->view('c_hostings_form', $ctt_data, TRUE);
		$this->load->view('master', $data);
		
	}
	
	
	
	
//	-------------------------------------------------------------------------------------
	
	
	
	/**
	 * Remove hosting and connected objects
	 *
	 * @param int $id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function delete($id){
		if(!is_numeric($id))	redirect('hostings/overview');
		if(!$this->session->userdata('crrUser')->allow_remove=1)	redirect('hostings/overview');
		
		//	remove hosting
		$this->hostings_mdl->delete_hosting($id);
		
		//	remove addons and domains
		$this->hostings_mdl->remove_connected_records($id);
		
		//	remove linked logins
		$logins = $this->logins_mdl->get_connected_logins('hosting', $id);
		if(!empty($logins)){
			$login_list = "";
			foreach($logins as $cred){
				$login_list .= $cred->id.',';
			}
			$login_list = trim($login_list, ',');
			$this->logins_mdl->remove($login_list);
			$this->logins_mdl->remove_connection('hosting', $id);
		}
		
		$feedback->type 	= "success";
		$feedback->title 	= "Hosting verwijderd";
		$feedback->message 	= "Het hostingpakket en de gekoppelde gegevens zijn verwijderd.";
		$this->session->set_flashdata('feedback', $feedback);
		redirect('hostings/overview');
	}
	
	
	
//	-------------------------------------------------------------------------------------
	
	
	
	/**
	 * Search through hostings
	 *
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function search(){
		//	LOAD EXTRA RESOURCES
		$this->load->helper('form');
		$this->load->model('addons_mdl');
				
		//	GET DATA
		$ctt_data = array();
		$ctt_data['fields']	= $this->_get_searchFields();
		
		
		// =======================
		// = PARSE SEARCH VALUES =
		// =======================
		if($this->input->post('arr_fields')){
			$db_array = array();
			$field_array = explode('|', trim($this->input->post('arr_fields'), '|'));
			
			foreach($field_array as $field){
				$orig_field = $field;
				$field = trim($field, '1');
				$where = new StdClass();
				$where->field = $field;
				$where->label = $ctt_data['fields'][$field]->label;
				$where->value = $this->input->post($orig_field);
				
				//	Parse to int if needed
				if(isset($ctt_data['fields'][$field]->numeric)){
					$where->value = (int)$where->value;
				}
				
				//	set operator
				if($this->input->post('operator_' . $orig_field)){
					$where->operator = $this->input->post('operator_' . $orig_field);
				}elseif((isset($ctt_data['fields'][$field]->strict) && $ctt_data['fields'][$field]->strict==FALSE)){
					$where->operator = ' LIKE ';
					$where->value	 = "%" . $where->value . "%";
				}elseif(isset($ctt_data['fields'][$field]->operator)){
					$where->operator = $ctt_data['fields'][$field]->operator;
				}else{
					$where->operator = "=";
				}
				
				array_push($db_array, $where);
			}
			
			//	GET DATA
			$ctt_data['search'] 	= $db_array;
			$ctt_data['hostings'] 	= $this->hostings_mdl->search_hosting($db_array);
		}
		
		
		//	LOAD VIEW
		$data['page_title']	= "Hostings";
		$data['sub_title'] 	= "Zoeken";
		$data['subnav']		= $this->subnav;
		$data['content'] 	= $this->load->view('c_hostings_search', $ctt_data, TRUE);
		$this->load->view('master', $data);
	}
	
	
	
// ===================================================================================
// = ================================= PRIVATE METHODS ============================= =
// ===================================================================================
	
	
	
	/**
	 * Add connection between domain and hosting
	 *
	 * @param int $id
	 * @return object
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	private function _add_domain($id, $redirect=''){
		
		$this->form_validation->set_rules('domainname', 'Domeinnaam', "callback_not_default|trim|required");
		$this->form_validation->set_rules('extension_id', 'Extensie', 'trim|is_natural_no_zero');
				
		if($this->form_validation->run()){
			$where = array();

			$criteria = NULL;
			$criteria->field = 'domain';
			$criteria->operator = '=';
			$criteria->value = $this->input->post('domainname');
			array_push($where, $criteria);

			$criteria = NULL;
			$criteria->field = 'extension_id';
			$criteria->operator = '=';
			$criteria->value = $this->input->post('extension_id');
			array_push($where, $criteria);

			$this->load->model('domains_mdl');
			$domain = $this->domains_mdl->search_domain($where);
			$domain = $domain[0];

			$insert = array();
			$insert['hosting_id']	= $id;

			if(!empty($domain)){
				$insert['domain_name'] = $domain->domain_full;
				$insert['domain_id'] = $domain->id;
			}else{
				$ext = $this->domains_mdl->get_extension_byId($this->input->post('extension_id'));
				$insert['domain_name'] = $this->input->post('domainname') . $ext->extension;
			}

			if($this->hostings_mdl->insert_domain_connection($insert)){

				$this->session->set_flashdata('feedback', $feedback);
				if(empty($redirect)){
					$feedback->type 	= "success";
					$feedback->title 	= "Domeinnaam toegevoegd";
					$feedback->message 	= "De extra domainnaam '{$insert['domain_name']}' is toegevoegd aan het hostingpakket";
					redirect(current_url());
				}else{
					$feedback->type 	= "success";
					$feedback->title 	= "Toegevoegd";
					$feedback->message 	= "Met succes toegevoegd aan de database.";
					redirect($redirect);
				}
			}else{
				$feedback->type 	= "error";
				$feedback->title 	= "Fout bij toevoegen";
				$feedback->message 	= "Er is iets fout gegaan bij het toevoegen van de domeinnaam '{$insert['domain_name']}'.";
			}
		}else{
			$feedback->type 	= "error";
			$feedback->title 	= "Fout bij toevoegen";
			$feedback->message 	= "Gelieve zowel een domeinnaam als extensie op te geven.";
		}
		
		
		$this->session->set_flashdata('feedback', $feedback);
	}
	
	
	public function not_default($str){
		$str = str_replace('Domeinnaam', '', $str);
		return $str;
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Add an extra addon to hosting
	 *
	 * @param int $id 
	 * @return object
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	private function _add_addon($id){
		$feedback = NULL;
		if($this->addons_mdl->add('hosting', $id, (int)$this->input->post('add_addon'))){
			$feedback->type 	= "success";
			$feedback->title 	= "Add-on toegevoegd";
			$feedback->message 	= "De extra add-on is toegevoegd aan het hostingpakket";
			$this->session->set_flashdata('feedback', $feedback);
			redirect(current_url());
		}else{
			$feedback->type 	= "error";
			$feedback->title 	= "Fout bij toevoegen";
			$feedback->message 	= "Er is iets fout gegaan bij het toevoegen van de add-on.";
			$this->session->set_flashdata('feedback', $feedback);
		}
		
		
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Add a login to the database and connect it to login
	 *
	 * @param int $id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	private function _add_login($id){
		$this->form_validation->set_rules('login_user', 'Gebruikersnaam', "trim|required");
		$this->form_validation->set_rules('login_pass', 'Paswoord', 'trim|required');
		$this->form_validation->set_rules('login_type', 'Type', 'trim|is_natural_no_zero|required');
		$this->form_validation->set_rules('login_host', 'Host', 'trim');
		$this->form_validation->set_rules('login_remarks', 'Remarks', 'trim');
				
		if($this->form_validation->run()){
			$insert = array();
			$insert['type_id']	= (int) $this->input->post('login_type');
			$insert['host']		= $this->input->post('login_host');
			$insert['user']		= $this->input->post('login_user');
			$insert['pass']		= $this->input->post('login_pass');
			$insert['remarks']	= $this->input->post('login_remarks');
			
			$added = FALSE;
			$login_id = $this->logins_mdl->add($insert);
			
			if(!empty($login_id)){
				$added = $this->logins_mdl->add_connection('hosting', $id, $login_id);
			}
			
			if($added){
				$feedback->type 	= "success";
				$feedback->title 	= "Login toegevoegd";
				$feedback->message 	= "De login '{$insert['user']}' is toegevoegd aan de database";
				$this->session->set_flashdata('feedback', $feedback);
				redirect(current_url());
			}else{
				$feedback->type 	= "error";
				$feedback->title 	= "Fout bij toevoegen van login";
				$feedback->message 	= "Er is een ongekende fout opgetreden bij het toevoegen aan de database.";
				$this->session->set_flashdata('feedback', $feedback);
				return $feedback;
			}
		}else{
			$feedback->type 	= "error";
			$feedback->title 	= "Fout bij toevoegen van login";
			$feedback->message 	= "Gelieve minstens een type, gebruikersnaam en paswoord op te geven.";
			$this->session->set_flashdata('feedback', $feedback);
			return $feedback;
		}
	}
	
	
	//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Update a login
	 *
	 * @param int $id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	private function _update_login($id){
		$login_id = $this->input->post('update_login_id');
		
		$this->form_validation->set_rules('update_login_user', 'Gebruikersnaam', "trim|required");
		$this->form_validation->set_rules('update_login_pass', 'Paswoord', 'trim|required');
		$this->form_validation->set_rules('update_login_type', 'Type', 'trim|is_natural_no_zero|required');
		$this->form_validation->set_rules('update_login_host', 'Host', 'trim');
		$this->form_validation->set_rules('update_login_remarks', 'Remarks', 'trim');
		
		if($this->form_validation->run()){
			$update = array();
			$update['type_id']	= (int) $this->input->post('update_login_type');
			$update['host']		= $this->input->post('update_login_host');
			$update['user']		= $this->input->post('update_login_user');
			$update['pass']		= $this->input->post('update_login_pass');
			$update['remarks']	= $this->input->post('update_login_remarks');
			
			$updated = $this->logins_mdl->update($login_id, $update);
			
			if($updated){
				$feedback->type 	= "success";
				$feedback->title 	= "Login aangepast";
				$feedback->message 	= "De logingegevens van '{$update['user']}' zijn aangepast in de database.";
				$this->session->set_flashdata('feedback', $feedback);
				redirect(current_url());
			}else{
				$feedback->type 	= "error";
				$feedback->title 	= "Fout bij aanpassen van login";
				$feedback->message 	= "Er is een ongekende fout opgetreden bij het toevoegen aan de database.";
				$this->session->set_flashdata('feedback', $feedback);
				redirect(current_url());
			}
		}else{
			$feedback->type 	= "error";
			$feedback->title 	= "Login niet aangepast";
			$feedback->message 	= "De login werd niet aangepast. Gelieve minstens een type, gebruikersnaam en paswoord op te geven..";
			$this->session->set_flashdata('feedback', $feedback);
			redirect(current_url());
		}
	}
	
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Update basic info of hosting pakket
	 *
	 * @param int $id 
	 * @return object
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	private function _save_hosting($id){
		//	SET FORM VALIDATION
		$this->form_validation->set_rules('contact_firstname', 'Voornaam', 'trim|required');
		$this->form_validation->set_rules('contact_lastname', 'Familienaam', 'trim|required');
		$this->form_validation->set_rules('contact_company', 'Firma', 'trim|required');
		$this->form_validation->set_rules('contact_email', 'E-mail', 'trim');
		$this->form_validation->set_rules('month', 'Verlenging', 'trim|is_natural_no_zero|required');
		$this->form_validation->set_rules('creation_date', 'Aangemaakt op', 'trim');
		$this->form_validation->set_rules('deletion_date', 'Verwijderen op', 'trim');
		$this->form_validation->set_rules('hosting_size', 'Pakket', 'trim|is_natural');
		$this->form_validation->set_rules('remarks', 'Opmerkingen', 'trim');
		
		if(trim($this->input->post('contact_email'))!=''){
			$this->form_validation->set_rules('contact_email', 'E-mail', 'trim|valid_email');
		}
		
		if($this->form_validation->run()){
			$db_data = array();
			$db_data['contact_firstname'] 	= ($this->input->post('contact_firstname')!='') ? $this->input->post('contact_firstname') : NULL;
			$db_data['contact_lastname'] 	= ($this->input->post('contact_lastname')!='') ? $this->input->post('contact_lastname') : NULL;
			$db_data['contact_company'] 	= ($this->input->post('contact_company')!='') ? $this->input->post('contact_company') : NULL;
			$db_data['contact_email'] 		= ($this->input->post('contact_email')!='') ? $this->input->post('contact_email') : NULL;
			$db_data['month'] 				= (int)$this->input->post('month');
			$db_data['creation_date'] 		= ($this->input->post('creation_date')!='') ? $this->input->post('creation_date') : NULL;
			$db_data['deletion_date'] 		= ($this->input->post('deletion_date')!='') ? $this->input->post('deletion_date') : NULL;
			$db_data['remarks'] 			= ($this->input->post('remarks')!='') ? $this->input->post('remarks') : NULL;
			$db_data['size_id']				= (int)$this->input->post('hosting_size');
			
			if($this->hostings_mdl->update_hosting($id, $db_data)){
				$feedback->type 	= "success";
				$feedback->title 	= "Registratiegegevens aangepast";
				$feedback->message 	= "De nieuwe waarden zijn bewaard in de database.";
				$this->session->set_flashdata('feedback', $feedback);
				redirect(current_url());
			}else{
				$feedback->type 	= "error";
				$feedback->title 	= "Fout bij opslaan";
				$feedback->message 	= "Er is een ongekende fout opgetreden bij het wegschrijven naar de database.";
				return $feedback;
			}
		}else{
			$feedback->type 	= "error";
			$feedback->title 	= "Fout bij validatie";
			$feedback->message 	= "Gelieve alle velden correct in te vullen.";
			return $feedback;
		}
		
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Get list of all possible search fields
	 *
	 * @return array
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	private function _get_searchFields(){
		$arr_fields = array();
		
		$field = new StdClass();
		$field->label 		= "Domeinnaam";
		$field->db_field 	= "domain_list";
		$field->type		= "text";
		$field->strict		= FALSE;
		$arr_fields['domain_list'] = $field;
		
		$field = new StdClass();
		$field->label 		= "Contactpersoon";
		$field->db_field 	= "contact_fullname";
		$field->type		= "text";
		$field->strict		= FALSE;
		$arr_fields['contact_fullname'] = $field;
		
		$field = new StdClass();
		$field->label 		= "Firma";
		$field->db_field 	= "contact_company";
		$field->type		= "text";
		$field->strict		= FALSE;
		$arr_fields['contact_company'] = $field;
		
		$field = new StdClass();
		$VO = new Domainname();
		$field->label 		= "Maand verlenging";
		$field->db_field 	= "month";
		$field->type		= "select";
		$field->numeric		= TRUE;
		$field->values		= $VO->arr_months;
		unset($field->values[0]);
		$arr_fields['month'] = $field;
		
		$field = new StdClass();
		$field->label 		= "Datum van aanmaken";
		$field->db_field 	= "creation_date";
		$field->type		= "date";
		$field->operators	= array('=', '<=', '>=');
		$field->multiple	= TRUE;
		$arr_fields['creation_date'] = $field;
		
		$field = new StdClass();
		$field->label 		= "Datum van verwijderen";
		$field->db_field 	= "deletion_date";
		$field->type		= "date";
		$field->operators	= array('=', '<=', '>=');
		$field->multiple	= TRUE;
		$arr_fields['deletion_date'] = $field;
		
		$field = new StdClass();
		$field->label 		= "Soort hosting";
		$field->db_field 	= "size_id";
		$field->type		= "select";
		$field->values		= $this->hostings_mdl->get_hosting_sizes();
		$arr_fields['size_id'] = $field;
		
		$field = new StdClass();
		$field->label 		= "Addon";
		$field->db_field 	= "addons";
		$field->type		= "select";
		$field->operator	= "FIND_IN_SET";
		$field->values		= $this->addons_mdl->get_addons('hostings');
		$field->multiple	= TRUE;
		$arr_fields['addons'] = $field;
		
		
		return $arr_fields;
	}
	
}