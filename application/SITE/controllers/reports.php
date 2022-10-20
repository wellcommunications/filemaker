<?php 

if (! defined('BASEPATH')) exit('No direct script access');

class reports extends MY_Controller {

	var $subnav;

	function __construct(){
		parent::__construct();
		
		if(!$this->session->userdata('crrUser')->allow_reports){
			redirect('');
		}
		
		$this->load->model('domains_mdl');
		$this->load->model('hostings_mdl');
		$this->load->model('addons_mdl');
		
		//	SET SUBNAV
		$this->subnav = array(
									array('label'=>'Nu verlengen',		'url'=>'month'),
									array('label'=>'Nu verwijderen', 	'url'=>'delete')
							);

	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	public function index(){
		redirect('reports/' . $this->subnav[0]['url']);
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	
	/**
	 * Get all domainnames/hostings width renewal this month
	 *
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function month(){
		$this->_search_database('month', '=', (int) date('m'));
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	public function delete(){
		$deletion_date = date('Y-m-d', strtotime(' + ' . DELETION_DATE_DAYS_AHEAD . ' days'));
		$this->_search_database('deletion_date', '<=', $deletion_date);
	}
	
	
	
	private function _search_database($field, $operator, $value){
		//	INIT VALUES
		$ctt_data = array();
		$ctt_data['hostings']	= array();
		$ctt_data['domains']	= array();
		
		//	GET DATA
		$selectors = array();
		$where = new StdClass();
		$where->field 	 = $field;
		$where->operator = $operator;
		$where->value 	 = $value;
		array_push($selectors, $where);
		

		if($field=='deletion_date'){
			$ctt_data['hostings']	= $this->hostings_mdl->search_hosting($selectors, 'deletion_date ASC');
			$ctt_data['domains']	= $this->domains_mdl->search_domain($selectors, 'deletion_date ASC');
		}else{
			$ctt_data['hostings']	= $this->hostings_mdl->search_hosting($selectors);
			$ctt_data['domains']	= $this->domains_mdl->search_domain($selectors);
		}
		
		if(!empty($ctt_data['domains'])){
			$addon_prices	= $this->addons_mdl->get_pricelist('domains');
			foreach($ctt_data['domains'] as $domain)	$domain->addon_prices = $addon_prices;
		}
		
		
		//	LOAD VIEW
		$data['page_title']	= "Rapporten";
		$data['sub_title'] 	= "";
		$data['subnav']		= $this->subnav;
		$data['content'] 	= $this->load->view('c_reports_list', $ctt_data, TRUE);
		$this->load->view('master', $data);
	}
}