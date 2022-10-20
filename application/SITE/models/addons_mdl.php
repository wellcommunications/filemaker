<?php 

if (! defined('BASEPATH')) exit('No direct script access');

class addons_mdl extends CI_Model {


// ====================================================================
// = ============================= GETTERS ========================== =
// ====================================================================
		
		
		/**
		 * Get a list of all possible addons, limited by type
		 *
		 * @param string $type 
		 * @return array
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_addons($type='all'){
			if($type=='domains') 	$this->db->where('allow_domain', 1);
			if($type=='hostings') 	$this->db->where('allow_hosting', 1);
			$this->db->order_by('priority', 'DESC');
			$res = $this->db->get('ADDONS');
			
			if($res->num_rows()>0){
				$arr_return = array();
				foreach($res->result() as $row) $arr_return[$row->id] = $row->name;
				return $arr_return;
			}else{
				return FALSE;
			}
		}
		
		
		
	//	---------------------------------------------------------------------------------
		
		
		/**
		 * Get a list of all connected addons
		 *
		 * @param string $type domainname, hosting
		 * @param int $id 
		 * @return array
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_connected_addons($type, $id){
			$sql = "SELECT 		A.*, A.id as addon_id, koppel.id as conn_id
					FROM 		ADDONS A
					INNER JOIN	{$type}_ADDONS koppel ON koppel.addon_id = A.id
					WHERE		koppel.{$type}_id = $id
					ORDER BY	A.priority DESC;";
			$res = $this->db->query($sql);

			if($res->num_rows()>0){
				return $res->result();
			}else{
				return FALSE;
			}
		}
		
		
	//	---------------------------------------------------------------------------------
		
		
		/**
		 * Get a list of the prices of the add-ons
		 *
		 * @param string $type 
		 * @return array
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_pricelist($type='all'){
			if($type=='domains') 	$this->db->where('allow_domain', 1);
			if($type=='hostings') 	$this->db->where('allow_hosting', 1);
			$this->db->select('id, price');
			$this->db->order_by('priority', 'DESC');
			$res = $this->db->get('ADDONS');
			
			if($res->num_rows()>0){
				$arr_return = array();
				foreach($res->result() as $row) $arr_return[$row->id] = (float)$row->price;
				return $arr_return;
			}else{
				return FALSE;
			}
		}
		
		
		
// ====================================================================
// = ============================= SETTERS ========================== =
// ====================================================================
		
		
		/**
		 * Add a new connection between addon and product
		 *
		 * @param string $type domainname, hosting
		 * @param int $id 
		 * @param in $addon_id 
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function add($type, $id, $addon_id){
			$db_data = array();
			$db_data['addon_id']	= $addon_id;
			$db_data[$type . '_id']	= $id;
			
			$this->db->insert(strtoupper($type) . '_ADDONS', $db_data);
			
			if($this->db->_error_message()){
				return FALSE;
			}else{
				return TRUE;
			}
		}
		
		
		
// ===================================================================
// = ============================= DELETE ========================== =
// ===================================================================
		
		
		/**
		 * Remove a single addon connection by it's unique id
		 *
		 * @param string $type domainname, hosting
		 * @param int $id 
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function remove($type, $id){
			$this->db->where('id', $id);
			$this->db->delete( strtoupper($type) . '_ADDONS');
			
			if($this->db->_error_message()){
				return FALSE;
			}else{
				return TRUE;
			}
		}
		
		
		
		/**
		 * Remove all addon connections for a hosting/domain
		 *
		 * @param string $type domainname, hosting
		 * @param int $id 
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function remove_all($type, $id){
			$this->db->where($type.'_id', $id);
			$this->db->delete( strtoupper($type) . '_ADDONS');
			
			if($this->db->_error_message()){
				return FALSE;
			}else{
				return TRUE;
			}
		}
		
}