<?php 

if (! defined('BASEPATH')) exit('No direct script access');

class domains_mdl extends CI_Model {


// ========================================================================
// = ============================= DOMAINNAMES ========================== =
// ========================================================================

		/**
		 * Get full list of domainnames
		 *
		 * @param string $order_by 
		 * @return array
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_list($order_by='full ASC') {
			$sql = "SELECT		CONCAT(DN.domain, DE.extension) as full, DN.id, DN.contact_firstname, DN.contact_lastname, DN.contact_company, DN.month, DN.price, DN.intern, DN.webdirect, 
								IF((DN.month - MONTH(NOW()))<0, (DN.month - MONTH(NOW()))+12, (DN.month - MONTH(NOW()))) as remaining, 
								DE.extension, 
								R.company as reseller_company, DN.reseller_id,
								GROUP_CONCAT( DISTINCT(DA.addon_id) ORDER BY DA.addon_id ASC ) AS addons
					FROM		DOMAINNAMES DN
					INNER JOIN	DOMAINNAME_EXTENSIONS DE ON DN.extension_id = DE.id
					LEFT JOIN	RESELLERS R ON DN.reseller_id = R.id 
					LEFT JOIN	DOMAINNAME_ADDONS DA ON DN.id = DA.domainname_id 
					GROUP BY	DN.id
					ORDER BY	{$order_by}";
			$res = $this->db->query($sql);
			
			if($res->num_rows()>0){
				$arr_domains = array();
				foreach($res->result() as $dom)	array_push($arr_domains, new Domainname($dom));
				return $arr_domains;
			}else{
				return FALSE;
			}
		}
	
	
	//	-------------------------------------------------------------------------------------
	
	
		/**
		 * Get details of domainname
		 *
		 * @param int $id 
		 * @return Domainname
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_details($id){
			$sql = "SELECT		CONCAT(DN.domain, DE.extension) as full, DN.*, 
								DE.extension, DE.description, 
								R.company as reseller_company, R.firstname as reseller_firstname, R.lastname as reseller_lastname, R.email as reseller_email, DN.reseller_id
					FROM		DOMAINNAMES DN
					INNER JOIN	DOMAINNAME_EXTENSIONS DE ON DN.extension_id = DE.id
					LEFT JOIN	RESELLERS R ON DN.reseller_id = R.id 
					WHERE		DN.id = {$id}";
			$res = $this->db->query($sql);
		
			if($res->num_rows()>0){
				return new Domainname($res->row());
			}else{
				return FALSE;
			}
		}
		
		
		
		/**
		 * Before adding, check if domainname is unique
		 *
		 * @param string $domain 
		 * @param int $extension 
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function is_registered($domain, $extension){
			$sql = "SELECT		id
					FROM		DOMAINNAMES
					WHERE		domain LIKE ? COLLATE utf8_bin
							AND	extension_id = ?;";
			$res = $this->db->query($sql, array($domain, $extension));
			
			if($res->num_rows()>0){
				return (int) $res->row()->id;
			}else{
				return FALSE;
			}
		}
	
	
	//	-------------------------------------------------------------------------------------
	
	
		/**
		 * Update domainnames in database
		 *
		 * @param int $id 
		 * @param array $db_data
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function update($id, $db_data){
			$this->db->where('id', $id);
			$this->db->update('DOMAINNAMES', $db_data);
		
			if($this->db->_error_message()){
				return FALSE;
			}else{
				return TRUE;
			}
		}
		
		
	//	-------------------------------------------------------------------------------------
		
		
		/**
		 * Add a new domain to the database
		 *
		 * @param array $db_data 
		 * @return int
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function insert_domain($db_data){
			$this->db->insert('DOMAINNAMES', $db_data);
			
			if($this->db->insert_id() && !$this->db->_error_message()){
				return $this->db->insert_id();
			}else{
				return FALSE;
			}
		}
		
		
	//	-------------------------------------------------------------------------------------
		
		
		/**
		 * Search table for domainnames
		 *
		 * @param array $where	
		 * @return array
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function search_domain($where, $order_by='full ASC'){
			$sql = "SELECT		CONCAT(DN.domain, DE.extension) as full, CONCAT(DN.contact_firstname, ' ', DN.contact_lastname) as contact_fullname, 
								DN.id, DN.extension_id, DN.domain, DN.contact_firstname, DN.contact_lastname, DN.contact_company, DN.month, DN.price, DN.intern, DN.webdirect, DN.registration_date, DN.deletion_date, 
								DE.extension, 
								GROUP_CONCAT( DISTINCT(DA.addon_id) ORDER BY DA.addon_id ASC ) AS addons,
								R.company as reseller_company, DN.reseller_id
					FROM		DOMAINNAMES DN
					INNER JOIN	DOMAINNAME_EXTENSIONS DE ON DN.extension_id = DE.id
					LEFT JOIN	RESELLERS R ON DN.reseller_id = R.id 
					LEFT JOIN 	DOMAINNAME_ADDONS DA ON DA.domainname_id = DN.id
					GROUP BY	DN.id 
					HAVING 		";
			
			foreach($where as $selector){
				if($selector->operator=="FIND_IN_SET"){
					$sql .= ' FIND_IN_SET(';
					$sql .= is_int($selector->value) ? $selector->value : "'" . $selector->value . "'";
					$sql .= ', ' . $selector->field . ') AND ';
				}else{
					$sql .= $selector->field . $selector->operator;
					$sql .= is_int($selector->value) ? $selector->value : "'" . $selector->value . "'";
					$sql .= ' AND ';
				}
			}
			
			$sql = trim(substr($sql, 0, strrpos($sql, ' AND ')+1));
			$sql .= " ORDER BY " . $order_by;
			$res = $this->db->query($sql);
			
			
			if($res->num_rows()>0){
				$arr_domains = array();
				foreach($res->result() as $dom)	array_push($arr_domains, new Domainname($dom));
				return $arr_domains;
			}else{
				return FALSE;
			}
		}
		
		
	//	-------------------------------------------------------------------------------------
		
		
		/**
		 * Remove a domainname from database
		 *
		 * @param string $id 
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function remove($id){
			$this->db->where('id', $id);
			$this->db->delete('DOMAINNAMES');
			
			if($this->db->_error_message()){
				return FALSE;
			}else{
				return TRUE;
			}
		}
		
		
		
	//	-------------------------------------------------------------------------------------
		
		
		/**
		 * Check if hosting is connected to domain
		 *
		 * @param int $id 
		 * @return object
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function check_hosting($id){
			$this->db->select('hosting_id');
			$this->db->where('domain_id', $id);
			$this->db->order_by('id', 'DESC');
			$this->db->limit(1);
			$res = $this->db->get('DOMAINNAME_HOSTING');
			
			if($res->num_rows()>0){
				return $res->row()->hosting_id;
			}else{
				return FALSE;
			}
		}
		
		
	//	---------------------------------------------------------------------------------
		
		
		/**
		 * Check if DOMAINNAME_HOSTING has records for this domainname
		 *
		 * @param int $id 
		 * @param string $domain 
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function reverse_connect_hosting($id, $domain){
			$data = array();
			$data['domain_id'] = $id;
			$this->db->where('domain_name', $domain);
			$this->db->update('DOMAINNAME_HOSTING', $data);
			
			if($this->db->_error_message()){
				return FALSE;
			}else{
				return TRUE;
			}
		}
		
		
	//	---------------------------------------------------------------------------------
		
		
		/**
		 * On deleting a domainname, remove the connection with the hosting
		 *
		 * @param int $id 
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function remove_hosting_connection($id){
			$data = array();
			$data['domain_id'] = NULL;
			$this->db->where('domain_id', $id);
			$this->db->update('DOMAINNAME_HOSTING', $data);
			
			
			if($this->db->_error_message()){
				return FALSE;
			}else{
				return TRUE;
			}
		}
		
		
// ======================================================================
// = ============================= RESELLERS ========================== =
// ======================================================================
	

		/**
		 * Get a list of reseller
		 *
		 * @param boolean $showNULL Show a default NULL value (Geen reseller);
		 * @return array
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_resellers($showNULL=TRUE){
			$this->db->select('id, CONCAT(company, " (", firstname, " ", lastname, ")") as full', FALSE);
			$this->db->order_by('company', 'asc');
			$res = $this->db->get('RESELLERS');
		
			if($res->num_rows()>0){
				$arr_return = array();
				if($showNULL){
					$arr_return[0] = "Geen reseller";
				}
				foreach($res->result() as $reseller)	$arr_return[$reseller->id] = $reseller->full;
				return $arr_return;
			}else{
				return FALSE;
			}
		}
		
		
		
// =======================================================================
// = ============================= EXTENSIONS ========================== =
// =======================================================================
		
		
		/**
		 * Get a list of all extensions
		 *
		 * @return array
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_extensions(){
			$sql = "SELECT		id, extension, CONCAT(extension, ' (' , description, ')') as full
					FROM		DOMAINNAME_EXTENSIONS
					ORDER BY	priority DESC, extension ASC";
			$res = $this->db->query($sql);
			
			if($res->num_rows()>0){
				$arr_extensions = array();
				foreach($res->result() as $ext){
					$arr_extensions[$ext->id] = $ext->full;
				}
				return $arr_extensions;
			}else{
				return FALSE;
			}
		}
		
		
	//	---------------------------------------------------------------------------------
		

		/**
		 * Get the extension by ID
		 *
		 * @param int $id 
		 * @return string
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_extension_by_id($id){
			$this->db->select('extension');
			$this->db->where('id', $id);
			$res = $this->db->get('DOMAINNAME_EXTENSIONS');
			
			if($res->num_rows()>0){
				return $res->row()->extension;
			}else{
				return FALSE;
			}
		}
		
		
		
	//	-------------------------------------------------------------------------------------
		
		
		/**
		 * Get a short list of extensions (without description)
		 *
		 * @return array
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_extensions_short(){
			$sql = "SELECT		id, extension
					FROM		DOMAINNAME_EXTENSIONS
					ORDER BY	priority DESC, extension ASC";
			$res = $this->db->query($sql);
			
			if($res->num_rows()>0){
				$arr_extensions = array();
				foreach($res->result() as $ext){
					$arr_extensions[$ext->id] = $ext->extension;
				}
				return $arr_extensions;
			}else{
				return FALSE;
			}
		}
		
		
		
	//	-------------------------------------------------------------------------------------
		
		
		/**
		 * Get a specific TLD
		 *
		 * @param int $id 
		 * @return object
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_extension_byId($id){
			$this->db->where('id', $id);
			$res = $this->db->get('DOMAINNAME_EXTENSIONS');
			
			if($res->num_rows()>0){
				return $res->row();
			}else{
				return FALSE;
			}
		}
		
		
}