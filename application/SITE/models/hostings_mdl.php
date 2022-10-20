<?php 

if (! defined('BASEPATH')) exit('No direct script access');

class hostings_mdl extends CI_Model {



// =================================================================================
// = ===================================== GETTERS =============================== =
// =================================================================================


	/**
	 * Get list of all hostings
	 *
	 * @return array
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */	
	public function get_list() {
		$sql = "SELECT 		DH.hosting_id, DH.domain_name,
							GROUP_CONCAT( DISTINCT(DH.domain_name) ORDER BY DH.id ASC ) AS domain_list,
							HS.name as hosting_size, 
							H.id, H.contact_firstname, H.contact_lastname, H.contact_company, H.month, H.size_id, 
							HA.addon_id as mysql
				FROM		HOSTINGS H
				INNER JOIN 	DOMAINNAME_HOSTING DH ON H.id = DH.hosting_id
				LEFT JOIN	HOSTING_SIZES HS ON H.size_id = HS.id
				LEFT JOIN	HOSTING_ADDONS HA ON H.id = HA.hosting_id 
											  AND HA.addon_id = 1
				GROUP BY	DH.hosting_id
				ORDER BY	domain_list ASC";
		$res = $this->db->query($sql);
		
		if($res->num_rows()>0){
			$arr_hostings = array();
			foreach($res->result() as $host)	array_push($arr_hostings, new Hosting($host));
			return $arr_hostings;
		}else{
			return FALSE;
		}
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Get details of the hosting
	 *
	 * @param int $id 
	 * @return Hosting
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function get_details($id){
		$sql = "SELECT		H.*,
							HS.name as hosting_size, HS.price as hosting_price, HS.specs, 
							GROUP_CONCAT( DISTINCT(DH.domain_name) ORDER BY DH.id ASC ) AS domain_list 
				FROM		HOSTINGS H 
				LEFT JOIN	DOMAINNAME_HOSTING DH ON H.id = DH.hosting_id
				LEFT JOIN	HOSTING_SIZES HS ON H.size_id = HS.id
				WHERE		H.id = $id
				GROUP BY	H.id 
				LIMIT		1;";
		$res = $this->db->query($sql);
		
		if($res->num_rows()>0){
			return new Hosting($res->row());
		}else{
			return FALSE;
		}
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * get a list of domains connected to this hosting
	 *
	 * @param int $id 
	 * @return array
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function get_domains($id){
		$sql = "SELECT		DH.*,
							CONCAT(DN.domain, DE.extension) as domain_full
				FROM		DOMAINNAME_HOSTING DH
				LEFT JOIN	DOMAINNAMES DN ON DN.id = DH.domain_id
				LEFT JOIN	DOMAINNAME_EXTENSIONS DE ON DN.extension_id = DE.id
				WHERE		DH.hosting_id = $id
				ORDER BY	DH.id ASC, DH.domain_name ASC";
		$res = $this->db->query($sql);
		
		if($res->num_rows()>0){
			return $res->result();
		}else{
			return FALSE;
		}
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Search table for hostings
	 *
	 * @param array $where	
	 * @return array
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function search_hosting($where, $order_by='domain_list ASC'){
		$sql = "SELECT 		CONCAT(H.contact_firstname, ' ', H.contact_lastname) as contact_fullname, 
							DH.hosting_id, DH.domain_name,
							GROUP_CONCAT( DISTINCT(DH.domain_name) ORDER BY DH.id ASC ) AS domain_list, 
							GROUP_CONCAT( DISTINCT(HA.addon_id) ORDER BY HA.addon_id ASC ) AS addons, HA.id, 
							HS.name as hosting_size, 
							H.id, H.contact_firstname, H.contact_lastname, H.contact_company, H.month, H.size_id, H.deletion_date, H.creation_date
				FROM		HOSTINGS H
				LEFT JOIN 	DOMAINNAME_HOSTING DH ON H.id = DH.hosting_id
				LEFT JOIN	HOSTING_SIZES HS ON H.size_id = HS.id
				LEFT JOIN 	HOSTING_ADDONS HA ON HA.hosting_id = H.id
				GROUP BY	H.id
				HAVING		";
		
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
			$arr_hostings = array();
			foreach($res->result() as $host)	array_push($arr_hostings, new Hosting($host));
			return $arr_hostings;
		}else{
			return FALSE;
		}
	}
	

	
	
// =================================================================================
// = ===================================== SETTERS =============================== =
// =================================================================================

	
	/**
	 * Insert new hosting
	 *
	 * @param array $data 
	 * @return int
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function insert_hosting($data){
		$this->db->insert('HOSTINGS', $data);
		if($this->db->insert_id()){
			return (int)$this->db->insert_id();
		}else{
			return FALSE;
		}
	}
	
	
	
	/**
	 * Update the data of a hosting pakket
	 *
	 * @param int $id 
	 * @param array $data 
	 * @return boolean
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function update_hosting($id, $data){
		$this->db->where('id', $id);
		$this->db->update('HOSTINGS', $data);
		
		if($this->db->_error_message()){
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	


// =================================================================================
// = ===================================== DELETE =============================== =
// =================================================================================
	
	
	/**
	 * Remove entire hosting pakket
	 *
	 * @param int $id 
	 * @return boolean
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function delete_hosting($id){
		$this->db->where('id', $id);
		$this->db->delete('HOSTINGS');
		
		if($this->db->_error_message()){
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	
	/**
	 * Remove connected records from addons and domainnames
	 *
	 * @param int $id 
	 * @return boolean
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function remove_connected_records($id){
		$tables = array('HOSTING_ADDONS', 'DOMAINNAME_HOSTING');
		$this->db->where('hosting_id', $id);
		$this->db->delete($tables);
		
		if($this->db->_error_message()){
			return FALSE;
		}else{
			return TRUE;
		}
	}
	

// =================================================================================
// = ===================================== DOMAINS =============================== =
// =================================================================================


	
	/**
	 * Remove the connection between a domainname and a hosting
	 *
	 * @param int $id 
	 * @return bool
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function remove_domain($id){
		$this->db->where('id', $id);
		$this->db->delete('DOMAINNAME_HOSTING');
		
		if($this->db->_error_message()){
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Add a connection between domainname and hosting
	 *
	 * @param array $data 
	 * @return boolean
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function insert_domain_connection($data){
		$this->db->insert('DOMAINNAME_HOSTING', $data);
		
		if($this->db->_error_message()){
			return FALSE;
		}else{
			return TRUE;
		}
	}

	
// ===========================================================================
// = ============================== HOSTING SIZES ========================== =
// ===========================================================================
	
	
	/**
	 * Get array of possible hosting sizes
	 *
	 * @return array
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function get_hosting_sizes(){
		$this->db->select('id, name');
		$res = $this->db->get('HOSTING_SIZES');
		
		if($res->num_rows()>0){
			$arr_return = array();
			$arr_return[0] = "--- Geen hostingpakket ---";
			foreach($res->result() as $row) $arr_return[$row->id] = $row->name;
			return $arr_return;
		}else{
			return FALSE;
		}
	}
	
	
	
	
	
}