<?php 

if (! defined('BASEPATH')) exit('No direct script access');

/**
 * Hosting Value Object
 *
 * @package default
 * @author Wouter Samyn <wouter@well.be>
 * @copyright Copyright (c) 2012, Well Communications
 * @version	1.0 
 */
class Hosting{

// ==============
// = PROPERTIES =
// ==============
	public $id					= NULL;
	 
	public $domain				= NULL;
	public $domain_full			= NULL;
	public $extension_id		= NULL;
	public $extension			= NULL;
	public $pakket				= NULL;
	 
	public $contact				= NULL;
	public $month				= NULL;
	public $month_full			= NULL;
	
	private $creation_date		= NULL;
	private $deletion_date		= NULL;
	public $remarks				= NULL;
	
	public $hasMySQL			= FALSE;
	 
	public $logins				= array();
	public $addons				= array();
	public $domains				= array();
	 
	public $arr_months			= array('error', 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december');
	


// ===================
// = MAGIC FUNCTIONS =
// ===================
	//php 5 constructor
	function __construct($params=NULL) {
		$this->pakket = new StdClass();
		$this->pakket->size = NULL;
		$this->pakket->price = NULL;
		$this->pakket->specs = NULL;
		
		if(!empty($params)){
			$this->init($params);
		}
	}
	
	
	function __get($item){
		switch($item){
			case 'creation_date':
				return (!empty($this->creation_date)) ? date('Y-m-d', $this->creation_date) : NULL;
				break;
			case 'deletion_date':
				return (!empty($this->deletion_date)) ? date('Y-m-d', $this->deletion_date) : NULL;
				break;
			default:
				return $this->{$item};
				break;
		}
	}
	
	
	function __set($item, $value){
		switch($item){
			case 'creation_date':
				$this->creation_date = $value;
				break;
			case 'deletion_date':
				$this->deletion_date = $value;
				break;
			default:
				return $this->{$item} = $value;
				break;
		}
	}
	
// ==================
// = PUBLIC METHODS =
// ==================
	
	
	/**
	 * Initialse a new Hosting
	 *
	 * @param mixed $params 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function init($params=NULL) {
		if(!empty($params->id))					$this->id = (int)$params->id;
		
		if(!empty($params->domain_list)){
			$list = explode(',', $params->domain_list);
			$this->domain_full = array_shift($list);
		}
		
		if(!empty($params->month))				$this->month = $params->month;
		if(!empty($params->month))				$this->month_full = $this->arr_months[$params->month];
		if(!empty($params->hosting_size))		$this->pakket->size = $params->hosting_size;
		if(!empty($params->hosting_price))		$this->pakket->price = $params->hosting_price;
		if(!empty($params->size_id))			$this->pakket->id = $params->size_id;
		if(!empty($params->specs))				$this->pakket->specs = $params->specs;
		if(!empty($params->creation_date))		$this->creation_date = strtotime($params->creation_date);
		if(!empty($params->deletion_date))		$this->deletion_date = strtotime($params->deletion_date);

		if(!empty($params->mysql))				$this->hasMySQL = TRUE;
		if(!empty($params->addons) && in_array(1, explode(',', $params->addons)))		$this->hasMySQL = TRUE;
		
		$contact = NULL;
		if(!empty($params->contact_firstname))	$contact->firstname = $params->contact_firstname;
		if(!empty($params->contact_lastname))	$contact->lastname = $params->contact_lastname;
		if(!empty($params->contact_company))	$contact->company = $params->contact_company;
		if(!empty($params->contact_email))		$contact->email = $params->contact_email;
		$this->contact = $contact;

		
		
		if(!empty($params->remarks))			$this->remarks = $params->remarks;
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Parse the contact data to a string
	 *
	 * @param boolean $oneLine 
	 * @return string
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function contact_toString($oneLine=TRUE){
		if(!empty($this->contact)){
			$contact = "";
			if(!empty($this->contact->firstname))		$contact .= $this->contact->firstname . " ";
			if(!empty($this->contact->lastname))		$contact .= $this->contact->lastname . " ";
			
			
			//	Glue Company
			if(!empty($this->contact->company)){
				$contact .= ($oneLine) ? ' (' : '<br />';
				$contact .= $this->contact->company;
				$contact .= ($oneLine) ? ')' : '<br />';
			}
			
			//	Glue email
			if(!empty($this->contact->email) && !$oneLine){
				$contact .= safe_mailto($this->contact->email);
			}

			return $contact;
		}else{
			return FALSE;
		}
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Get remaining months
	 *
	 * @return int
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function get_remaining_months(){
		if(empty($this->month)) return FALSE;
		
		$diff = $this->month - date('m');
		if($diff<0)		$diff+= 12;

		return $diff;
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Calculate the total price including add-ons
	 *
	 * @return float
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function get_price($format=TRUE){
		$price = 0;
		
		if(!empty($this->pakket->price)) $price+= $this->pakket->price;
		
		if(!empty($this->addons)){
			foreach($this->addons as $addon){
				$price += $addon->price;
			}
		}
		
		if($format){
			return number_format($price, 2, ',', '.');
		}else{
			return $price;
		}
	}
	
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Count number of days until scheduled deletion
	 *
	 * @return int
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function deletion_countdown(){
		$datetime_now 	= new DateTime(date('Y-m-d'));
		$datetime_this	= new DateTime(date('Y-m-d', $this->deletion_date));
		$interval = date_diff($datetime_now, $datetime_this);
		
		return $interval->format('%r%a');
	}
	
}

?>