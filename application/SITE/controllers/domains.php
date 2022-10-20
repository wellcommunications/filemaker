<?php 

if (! defined('BASEPATH')) exit('No direct script access');

class domains extends MY_Controller {

	var $subnav;

	function __construct(){
		parent::__construct();
		
		$this->load->model('domains_mdl');
		
		//	SET SUBNAV
		$this->subnav = array();
		array_push($this->subnav, array('label'=>'Overzicht',	'url'=>'overview'));
		if($this->session->userdata('crrUser')->allow_add==1)	array_push($this->subnav, array('label'=>'Nieuw', 	'url'=>'add'));
		array_push($this->subnav, array('label'=>'Zoeken', 	'url'=>'search'));
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	public function index() {
		redirect('domains/' . $this->subnav[0]['url']);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Show list of domainnames
	 *
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function overview(){
		//	LOAD EXTRA RESOURCES
		$this->load->model('addons_mdl');
		
		$dummy	= new Domainname();
		$dummy->month = 7;
		$dummy->get_remaining_months();
		
		//	GET DATA
		$ctt_data = array();
		$addon_prices	= $this->addons_mdl->get_pricelist('domains');
		$ctt_data['domainnames']	= $this->domains_mdl->get_list();
		$ctt_data['feedback']		= $this->session->flashdata('feedback');
		
		foreach($ctt_data['domainnames'] as $domain)	$domain->addon_prices = $addon_prices;
		
		//	LOAD VIEW
		$data['page_title']	= "Domeinnamen";
		$data['sub_title'] 	= "Overzicht";
		$data['subnav']		= $this->subnav;
		$data['content'] 	= $this->load->view('c_domains_overview', $ctt_data, TRUE);
		$this->load->view('master', $data);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Show details
	 *
	 * @param int $id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function details($id){
		if(!is_numeric($id)) 	redirect('domains/overview');
		
		//	LOAD EXTRA RESOURCES
		$this->load->model('logins_mdl');
		$this->load->model('addons_mdl');
		
		//	GET DATA
		$ctt_data 	= array();
		$ctt_data['hosting']	= NULL;
		$ctt_data['details']	= $this->domains_mdl->get_details($id);
		
		if(!empty($ctt_data['details'])){
			$ctt_data['details']->addons 		= $this->addons_mdl->get_connected_addons('domainname', $id);
			$ctt_data['details']->addon_prices 	= $this->addons_mdl->get_pricelist('domains');
			$ctt_data['details']->logins 		= $this->logins_mdl->get_connected_logins('domainname', $id);
			
			$hosting_id		= $this->domains_mdl->check_hosting($id);
			if($hosting_id){
				$this->load->model('hostings_mdl');
				$ctt_data['hosting'] = $this->hostings_mdl->get_details($hosting_id);
			}
		}
		
		
		//	LOAD VIEW
		$data['page_title']	= "Domeinnamen";
		$data['sub_title'] 	= "Details " . $ctt_data['details']->domain_full;
		$data['subnav']		= $this->subnav;
		$data['content'] 	= $this->load->view('c_domains_details', $ctt_data, TRUE);
		$this->load->view('master', $data);
	}
	
	
	//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Edit details of a domain name
	 *
	 * @param int $id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function edit($id){
		if(!is_numeric($id)) 	redirect('domains/overview');
		
		//	CHECK AUTHORISATION
		if(!$this->session->userdata('crrUser')->allow_edit==1)	redirect('domains/details/' . $id);
		
		//	LOAD EXTRA RESOURCES
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->model('addons_mdl');
		$this->load->model('logins_mdl');
		
		
		//	GET DATA
		$ctt_data = array();
		$ctt_data['hosting']	= NULL;
		$ctt_data['details']	= $this->domains_mdl->get_details($id);
		
		if(!empty($ctt_data['details'])){
			$ctt_data['details']->addons = $this->addons_mdl->get_connected_addons('domainname', $id);
			$ctt_data['details']->logins = $this->logins_mdl->get_connected_logins('domainname', $id);
			
			$ctt_data['resellers']		= $this->domains_mdl->get_resellers();
			$ctt_data['addons']			= $this->addons_mdl->get_addons('domains');
			$ctt_data['login_types']	= $this->logins_mdl->get_login_types();
			$ctt_data['price_type'] 	= array('Prijs', 'Intern', 'Pakket/Webdirect');
			
			$hosting_id		= $this->domains_mdl->check_hosting($id);
			if($hosting_id){
				$this->load->model('hostings_mdl');
				$ctt_data['hosting'] = $this->hostings_mdl->get_details($hosting_id);
			}
		}
		
		
		$ctt_data['feedback'] = $this->session->flashdata('feedback');
		
		// = PARSE POSTBACK =
		switch($this->input->post('postback')){
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
				$ctt_data['feedback'] = $this->_save_domain($id);
				break;
		}
		
		
		// ==============
		// = PARSE DATA =
		// ==============
		if($this->input->post('postback')){
			
		}
		
		//	LOAD VIEW
		$data['page_title']	= "Domeinnamen";
		$data['sub_title'] 	= "Details " . $ctt_data['details']->domain_full;
		$data['subnav']		= $this->subnav;
		$data['content'] 	= $this->load->view('c_domains_form', $ctt_data, TRUE);
		$this->load->view('master', $data);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Add new domainname
	 *
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function add(){
		//	CHECK AUTHORISATION
		if(!$this->session->userdata('crrUser')->allow_add==1)	redirect('domains/overview');
		
		//	LOAD EXTRA RESOURCES
		$this->load->library('form_validation');
		$this->load->helper('form');
		
		//	INIT DATA
		$ctt_data = array();
		$ctt_data['details']	= new Domainname();
		$ctt_data['details']->month = date('m');
		$ctt_data['details']->registration_date = time();
		
		//	GET DATA
		$ctt_data['resellers']	= $this->domains_mdl->get_resellers();
		$ctt_data['extensions']	= $this->domains_mdl->get_extensions();
		$ctt_data['extensions'][0]	= "--- Kies een extensie ---";
		$ctt_data['price_type'] = array('Prijs', 'Intern', 'Pakket/Webdirect');
		
		
		// ==============
		// = PARSE DATA =
		// ==============
		if($this->input->post('postback')){
			$this->form_validation->set_rules('domain', 'Domeinnaam', 'trim|required');
			$this->form_validation->set_rules('extension_id', 'TLD (extensie)', 'trim|is_natural_no_zero');
			
			$this->form_validation->set_rules('reseller_id', 'Reseller', 'trim');
			$this->form_validation->set_rules('contact_firstname', 'Voornaam', 'trim|required');
			$this->form_validation->set_rules('contact_lastname', 'Familienaam', 'trim|required');
			$this->form_validation->set_rules('contact_company', 'Firma', 'trim|required');
			$this->form_validation->set_rules('contact_email', 'E-mail', 'trim');
			$this->form_validation->set_rules('price', 'Prijs', 'trim|numeric');
			$this->form_validation->set_rules('intern', 'Intern', 'trim');
			$this->form_validation->set_rules('pakket', 'Pakket', 'trim');
			$this->form_validation->set_rules('month', 'Intern', 'trim');
			$this->form_validation->set_rules('registation_date', 'Geregisteerd op', 'trim');
			$this->form_validation->set_rules('deletion_date', 'Verwijderen op', 'trim');
			$this->form_validation->set_rules('remarks', 'opmerkingen', 'trim');
			
			if(trim($this->input->post('contact_email'))!=''){
				$this->form_validation->set_rules('contact_email', 'E-mail', 'trim|valid_email');
			}
			
			if($this->form_validation->run()){
				$db_data = array();
			
				$db_data['domain'] 				= $this->input->post('domain');
				$db_data['extension_id'] 		= $this->input->post('extension_id');
				
				$is_registered = $this->domains_mdl->is_registered($db_data['domain'], $db_data['extension_id']);
				if($is_registered===FALSE){
					$db_data['reseller_id'] 		= ($this->input->post('reseller_id')>0) ? (int)$this->input->post('reseller_id') : NULL;
					$db_data['contact_firstname'] 	= ($this->input->post('contact_firstname')!='') ? $this->input->post('contact_firstname') : NULL;
					$db_data['contact_lastname'] 	= ($this->input->post('contact_lastname')!='') ? $this->input->post('contact_lastname') : NULL;
					$db_data['contact_company'] 	= ($this->input->post('contact_company')!='') ? $this->input->post('contact_company') : NULL;
					$db_data['contact_email'] 		= ($this->input->post('contact_email')!='') ? $this->input->post('contact_email') : NULL;
					$db_data['month'] 				= (int)$this->input->post('month');
					$db_data['registration_date'] 	= ($this->input->post('registration_date')!='') ? $this->input->post('registration_date') : NULL;
					$db_data['deletion_date'] 		= ($this->input->post('deletion_date')!='') ? $this->input->post('deletion_date') : NULL;
					$db_data['remarks'] 			= ($this->input->post('remarks')!='') ? $this->input->post('remarks') : NULL;
					$db_data['price'] 				= ($this->input->post('price')!='') ? (double)$this->input->post('price') : NULL;
					$db_data['intern'] 				= (int)$this->input->post('intern');
					$db_data['webdirect'] 			= (int)$this->input->post('pakket');
					
					
					$domain_id = $this->domains_mdl->insert_domain($db_data);
					
					//	UPDATE DATABASE
					if($domain_id!=FALSE){
						$full = $db_data['domain'] . $this->domains_mdl->get_extension_by_id($db_data['extension_id']);
						$this->domains_mdl->reverse_connect_hosting($domain_id, $full);
						redirect('domains/details/'.$domain_id);
					}else{		//	Unkown error
						$ctt_data['feedback']->type 	= "error";
						$ctt_data['feedback']->title 	= "Oeps!";
						$ctt_data['feedback']->message 	= "Er is iets fout gegaan, probeer je nog eens? <br />Als je blijft problemen hebben, maak een screenshot en stuur die met de nodige uitleg naar Wouter ;-)";
					}
				}else{		//	Not unique
					$ctt_data['feedback']->type 	= "error";
					$ctt_data['feedback']->title 	= "Niet uniek";
					$ctt_data['feedback']->message 	= "De domeinnaam '" .$db_data['domain'] . $this->domains_mdl->get_extension_by_id($db_data['extension_id']) . "' is al geregistreerd in FileMaker.<br />";
					$ctt_data['feedback']->message .= "Klik <a href='" . site_url('domains/details/' . $is_registered) . "'>hier</a> om de details daarvan te bekijken en te bewerken.";
				}
			}else{		//	Validatio failed
				$ctt_data['feedback']->type 	= "error";
				$ctt_data['feedback']->title 	= "Fout bij form validatie";
				$ctt_data['feedback']->message 	= "Gelieve alle velden correct in te vullen";
			}
			
		}
		
		
		
		//	LOAD VIEW
		$data['page_title']	= "Domeinnamen";
		$data['sub_title'] 	= "Toevoegen";
		$data['subnav']		= $this->subnav;
		$data['content'] 	= $this->load->view('c_domains_form', $ctt_data, TRUE);
		$this->load->view('master', $data);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Search domains
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
			$ctt_data['search'] = $db_array;
			$ctt_data['domains'] = $this->domains_mdl->search_domain($db_array);
			
			if(!empty($ctt_data['domains'])){
				$addon_prices = $this->addons_mdl->get_pricelist('domains');
				foreach($ctt_data['domains'] as $dom){
					$dom->addon_prices = $addon_prices;
				}
			}
		}
		
		
		//	LOAD VIEW
		$data['page_title']	= "Domeinnamen";
		$data['sub_title'] 	= "Zoeken";
		$data['subnav']		= $this->subnav;
		$data['content'] 	= $this->load->view('c_domains_search', $ctt_data, TRUE);
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
		if(!is_numeric($id))	redirect('domains/overview');
		if(!$this->session->userdata('crrUser')->allow_remove=1)	redirect('domains/details/' . $id);
		
		//	remove domainname
		$this->domains_mdl->remove($id);
		$this->domains_mdl->remove_hosting_connection($id);
		
		//	remove addons and domains
		$this->load->model('addons_mdl');
		$this->addons_mdl->remove_all('domainname', $id);
		
		//	remove linked logins
		$this->load->model('logins_mdl');
		$logins = $this->logins_mdl->get_connected_logins('domainname', $id);
		if(!empty($logins)){
			$login_list = "";
			foreach($logins as $cred){
				$login_list .= $cred->id.',';
			}
			$login_list = trim($login_list, ',');
			$this->logins_mdl->remove($login_list);
			$this->logins_mdl->remove_connection('domainname', $id);
		}
		
		$feedback->type 	= "success";
		$feedback->title 	= "Domein verwijderd";
		$feedback->message 	= "De domeinnaam en de gekoppelde gegevens zijn verwijderd.";
		$this->session->set_flashdata('feedback', $feedback);
		redirect('domains/overview');
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Remove the connection between addon and hosting
	 *
	 * @param int $id 
	 * @param int $domain_id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function remove_addon($id, $domain_id){
		if(!is_numeric($id)) redirect('domains/edit/' . $domain_id);
		if(!$this->session->userdata('crrUser')->allow_edit==1)	redirect('domains/details/' . $id);
		
		//	LOAD EXTRA RESOURCES
		$this->load->model('addons_mdl');
		
		//	DELETE DATA
		$res = $this->addons_mdl->remove('domainname', $id);
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
		redirect('domains/edit/' . $domain_id);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	
	/**
	 * Remove a login
	 *
	 * @param int $login_id 
	 * @param int $domain_id 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function remove_login($login_id, $domain_id){
		if(!is_numeric($login_id) || !is_numeric($domain_id)) redirect('domains/edit/' . $domain_id);
		if(!$this->session->userdata('crrUser')->allow_edit==1)	redirect('domains/details/' . $domain_id);
		
		$this->load->model('logins_mdl');
		
		if($this->logins_mdl->remove_connection('domainname', $domain_id, $login_id) && $this->logins_mdl->remove($login_id)){
			$feedback->type 	= "success";
			$feedback->title 	= "Login verwijderd";
			$feedback->message 	= "De login is verwijderd uit de database.";
		}else{
			$feedback->type 	= "error";
			$feedback->title 	= "Fout bij verwijderen";
			$feedback->message 	= "De login is niet correct verwijderd uit de database.";
		}
		
		$this->session->set_flashdata('feedback', $feedback);
		redirect('domains/edit/' . $domain_id);
	}
	
	
	
// =====================
// = PRIVATE FUNCTIONS =
// =====================
	
	
	/**
	 * Update the domainname data
	 *
	 * @param int $id 
	 * @return object
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	private function _save_domain($id){
		$this->form_validation->set_rules('contact_firstname', 'Voornaam', 'trim|required');
		$this->form_validation->set_rules('contact_lastname', 'Familienaam', 'trim|required');
		$this->form_validation->set_rules('contact_company', 'Firma', 'trim|required');
		$this->form_validation->set_rules('contact_email', 'E-mail', 'trim');
		$this->form_validation->set_rules('price', 'Prijs', 'trim|numeric');
		$this->form_validation->set_rules('intern', 'Intern', 'trim');
		$this->form_validation->set_rules('pakket', 'Pakket', 'trim');
		$this->form_validation->set_rules('month', 'Intern', 'trim');
		$this->form_validation->set_rules('registation_date', 'Geregisteerd op', 'trim');
		$this->form_validation->set_rules('deletion_date', 'Verwijderen op', 'trim');
		$this->form_validation->set_rules('remarks', 'opmerkingen', 'trim');
		
		if(trim($this->input->post('contact_email'))!=''){
			$this->form_validation->set_rules('contact_email', 'E-mail', 'trim|valid_email');
		}
		
		if($this->form_validation->run()){
			$db_data = array();
			
			$db_data['reseller_id'] 		= ($this->input->post('reseller_id')>0) ? (int)$this->input->post('reseller_id') : NULL;
			$db_data['contact_firstname'] 	= ($this->input->post('contact_firstname')!='') ? $this->input->post('contact_firstname') : NULL;
			$db_data['contact_lastname'] 	= ($this->input->post('contact_lastname')!='') ? $this->input->post('contact_lastname') : NULL;
			$db_data['contact_company'] 	= ($this->input->post('contact_company')!='') ? $this->input->post('contact_company') : NULL;
			$db_data['contact_email'] 		= ($this->input->post('contact_email')!='') ? $this->input->post('contact_email') : NULL;
			$db_data['month'] 				= (int)$this->input->post('month');
			$db_data['registration_date'] 	= ($this->input->post('registration_date')!='') ? $this->input->post('registration_date') : NULL;
			$db_data['deletion_date'] 		= ($this->input->post('deletion_date')!='') ? $this->input->post('deletion_date') : NULL;
			$db_data['remarks'] 			= ($this->input->post('remarks')!='') ? $this->input->post('remarks') : NULL;
			$db_data['intern'] 				= (int)$this->input->post('intern');
			$db_data['webdirect'] 			= (int)$this->input->post('pakket');
			$db_data['price'] 				= (double)$this->input->post('price');
			
			if($this->input->post('reseller_id')>0){
				$db_data['reseller_id'] = $this->input->post('reseller_id');
			}else{
				$db_data['reseller_id'] = NULL;
			}
			
			//	UPDATE DATABASE
			if($this->domains_mdl->update($id, $db_data)){
				$feedback->type 	= "success";
				$feedback->title 	= "Domeinnaam aangepast";
				$feedback->message 	= "De nieuwe gegevens zijn bewaard in de database.";
				$this->session->set_flashdata('feedback', $feedback);
				redirect(current_url());
			}else{
				$feedback->type 	= "error";
				$feedback->title 	= "Oeps!";
				$feedback->message 	= "Er is iets fout gegaan, probeer je nog eens? <br />Als je blijft problemen hebben, maak een screenshot en stuur die met de nodige uitleg naar Wouter ;-)";
				return $feedback;
			}
		}else{
			$feedback->type 	= "error";
			$feedback->title 	= "Fout bij form validatie";
			$feedback->message 	= "Gelieve alle velden correct in te vullen";
			return $feedback;
		}
	}
	
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Add an extra addon to the domainname
	 *
	 * @param int $id 
	 * @return object
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	private function _add_addon($id){
		$feedback = NULL;
		if($this->addons_mdl->add('domainname', $id, (int)$this->input->post('add_addon'))){
			$feedback->type 	= "success";
			$feedback->title 	= "Add-on toegevoegd";
			$feedback->message 	= "De extra add-on is toegevoegd aan de domeinnaam";
			$this->session->set_flashdata('feedback', $feedback);
			redirect(current_url());
		}else{
			$feedback->type 	= "error";
			$feedback->title 	= "Fout bij toevoegen";
			$feedback->message 	= "Er is iets fout gegaan bij het toevoegen aan de domeinnaam.";
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
				$added = $this->logins_mdl->add_connection('domainname', $id, $login_id);
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
		$field->db_field 	= "full";
		$field->type		= "text";
		$field->strict		= FALSE;
		$arr_fields['full'] = $field;
		
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
		$field->label 		= "Reseller";
		$field->db_field 	= "reseller_id";
		$field->type		= "select";
		$field->numeric		= TRUE;
		$field->values		= $this->domains_mdl->get_resellers(FALSE);
		$arr_fields['reseller_id'] = $field;
		
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
		$field->label 		= "Intern";
		$field->db_field 	= "intern";
		$field->type		= "boolean";
		$field->numeric		= TRUE;
		$arr_fields['intern'] = $field;
		
		$field = new StdClass();
		$field->label 		= "Pakket/webdirect";
		$field->db_field 	= "webdirect";
		$field->type		= "boolean";
		$field->numeric		= TRUE;
		$arr_fields['webdirect'] = $field;
		
		$field = new StdClass();
		$field->label 		= "Registratie datum";
		$field->db_field 	= "registration_date";
		$field->type		= "date";
		$field->operators	= array('=', '<=', '>=');
		$field->multiple	= TRUE;
		$arr_fields['registration_date'] = $field;
		
		$field = new StdClass();
		$field->label 		= "Datum verwijderen";
		$field->db_field 	= "deletion_date";
		$field->type		= "date";
		$field->operators	= array('=', '<=', '>=');
		$field->multiple	= TRUE;
		$arr_fields['deletion_date'] = $field;
		
		$field = new StdClass();
		$field->label 		= "Addon";
		$field->db_field 	= "addons";
		$field->type		= "select";
		$field->operator	= "FIND_IN_SET";
		$field->values		= $this->addons_mdl->get_addons('domains');
		$field->multiple	= TRUE;
		$arr_fields['addons'] = $field;
		
		return $arr_fields;
	}
}