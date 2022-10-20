<?php 

if (! defined('BASEPATH')) exit('No direct script access');

/**
 * Domainname Value Object
 *
 * @package default
 * @author Wouter Samyn <wouter@well.be>
 * @copyright Copyright (c) 2012, Well Communications
 * @version	1.0 
 */
class Domainname{

// ==============
// = PROPERTIES =
// ==============
	public $id					= NULL;
	 
	public $domain				= NULL;
	public $domain_full			= NULL;
	public $extension_id		= NULL;
	public $extension_desc		= NULL;
	public $extension			= NULL;
	 
	public $contact				= NULL;
	public $reselller			= NULL;
	public $price				= NULL;
	public $intern				= FALSE;
	public $pakket				= FALSE;
	public $month				= NULL;
	public $month_full			= NULL;
	 
	private $registration_date	= NULL;
	private $deletion_date		= NULL;
	public $remarks				= NULL;
	
	public $forward				= FALSE;
	 
	public $logins				= array();
	public $addons				= array();
	public $addon_prices		= array();
	 
	public $arr_months			= array('error', 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december');
	


// ===================
// = MAGIC FUNCTIONS =
// ===================
	//php 5 constructor
	function __construct($params=NULL) {
		if(!empty($params)){
			$this->init($params);
		}
	}
	
	
	/**
	 * Getter for private properties
	 *
	 * @param string $item 
	 * @return mixed
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	function __get($item){
		switch($item){
			case 'registration_date':
				return (!empty($this->registration_date)) ? date('Y-m-d', $this->registration_date) : NULL;
				break;
			case 'deletion_date':
				return (!empty($this->deletion_date)) ? date('Y-m-d', $this->deletion_date) : NULL;
				break;
			default:
				return $this->{$item};
				break;
		}
	}
	
	
	/**
	 * Setter for private properties
	 *
	 * @param string $item 
	 * @param mixed $value 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	function __set($item, $value){
		switch($item){
			case 'registration_date':
				$this->registration_date = $value;
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
	 * Initialse a new Domainname
	 *
	 * @param mixed $params 
	 * @return void
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function init($params=NULL) {
		
		if(!empty($params->id))					$this->id = (int)$params->id;
		if(!empty($params->domain))				$this->domain = $params->domain;
		if(!empty($params->extension))			$this->extension = $params->extension;
		if(!empty($params->extension_id))		$this->extension_id = (int)$params->extension_id;
		if(!empty($params->description))		$this->extension_desc = $params->description;
		if(!empty($params->full))				$this->domain_full = $params->full;
		
		// $contact = NULL;
		// if(!empty($params->contact_firstname))	$contact->firstname = $params->contact_firstname;
		// if(!empty($params->contact_lastname))	$contact->lastname = $params->contact_lastname;
		// if(!empty($params->contact_company))	$contact->company = $params->contact_company;
		// if(!empty($params->contact_email))		$contact->email = $params->contact_email;
		// $this->contact = $contact;
		
		if(!empty($params->price))				$this->price = (double)$params->price;
		if(isset($params->intern))				$this->intern = (boolean) $params->intern;
		if(isset($params->webdirect))			$this->pakket = (boolean) $params->webdirect;
		
		if(!empty($params->month))				$this->month = (int)$params->month;
		if(!empty($params->month))				$this->month_full = $this->arr_months[$params->month];
		if(!empty($params->registration_date))	$this->registration_date = strtotime($params->registration_date);
		if(!empty($params->deletion_date))		$this->deletion_date = strtotime($params->deletion_date);
		
		// $this->reseller = NULL;
		// if(!empty($params->reseller_id))		$this->reseller->id = (int)$params->reseller_id;
		// if(!empty($params->reseller_company))	$this->reseller->company = $params->reseller_company;
		// if(!empty($params->reseller_firstname))	$this->reseller->firstname = $params->reseller_firstname;
		// if(!empty($params->reseller_lastname))	$this->reseller->lastname = $params->reseller_lastname;
		// if(!empty($params->reseller_email))		$this->reseller->email = $params->reseller_email;
		
		
		if(!empty($params->remarks))			$this->remarks = $params->remarks;
		
		if(!empty($params->forward))			$this->forward = TRUE;
		if(!empty($params->addons)){
			if(is_array($params->addons)){
				//	addons allready formatted as array
				$this->addons = $params->addons;
			}elseif(strpos($params->addons, ',') !== false){
				//	addons formatted as CSV
				foreach(explode(',', $params->addons) as $addon_id){
					$tmp = NULL;
					$tmp->addon_id = $addon_id;
					$this->addons[] = $tmp;
				}
			}else{
				//	single value
				$tmp = NULL;
				// $tmp->addon_id = (int) $params->addons;
				$this->addons[] = $tmp;
			}
			
			foreach($this->addons as $addon){
				if($addon->addon_id==5){
					$this->forward = TRUE;
				}
			}
		}
		
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
	 * Parse the reseller info to string
	 *
	 * @return string
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function reseller_toString(){
		if(!empty($this->reseller)){
			return "<strong>Reseller:</strong> " . $this->reseller->company;
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
		
		
		if($this->intern==TRUE)		return 'intern';
		if($this->pakket==TRUE)		return 'webdirect';
		
		if(!empty($this->price)){
			$price += $this->price;
		}
		
		if(!empty($this->addons)){					//	 if addons are attached, add to price
			foreach($this->addons as $addon){
				$price += $this->addon_prices[$addon->addon_id];
			}
		}elseif($this->forward===TRUE){				//	if no addons are connected, check for forward anyway
			$price += $this->addon_prices[5];
		}
				
		if($format){
			return '€ ' . number_format($price, 2, ',', '.');
		}else{
			return $price;
		}
	}
	
}

?>