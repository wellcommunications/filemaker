<?php 

if (! defined('BASEPATH')) exit('No direct script access');

class logins_mdl extends CI_Model {


// ====================================================================
// = ============================= GETTERS ========================== =
// ====================================================================
		
		
		/**
		 * Get a list of the types of logins
		 *
		 * @return array
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_login_types(){
			$this->db->order_by('priority', 'DESC');
			$res = $this->db->get('LOGIN_TYPES');
			
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
		 * Get a list of all connected logins
		 *
		 * @param string $type domainname, hosting
		 * @param int $id 
		 * @return array
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function get_connected_logins($type, $id){
			$sql = "SELECT 		L.*, LT.description as login_desc, LT.name as login_type 
					FROM 		LOGINS L
					INNER JOIN	{$type}_LOGINS koppel ON koppel.login_id = L.id
					INNER JOIN	LOGIN_TYPES LT ON LT.id = L.type_id
					WHERE		koppel.{$type}_id = $id
					ORDER BY	LT.priority DESC;";
			$res = $this->db->query($sql);
			
			if($res->num_rows()>0){
				return $res->result();
			}else{
				return FALSE;
			}
		}
		
		
		
// ====================================================================
// = ============================= SETTERS ========================== =
// ====================================================================
		
		
		/**
		 * Add a new login to database
		 *
		 * @param array $data 
		 * @return int
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function add($data){
			$this->db->insert('LOGINS', $data);
			
			if($this->db->insert_id()){
				return (int)$this->db->insert_id();
			}else{
				return FALSE;
			}
		}
		
		
	//	---------------------------------------------------------------------------------
		
		
		/**
		 * Add a new connection between login and product
		 *
		 * @param string $type domainname, hosting
		 * @param int $id 
		 * @param int $login_id 
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function add_connection($type, $id, $login_id){
			$db_data = array();
			$db_data['login_id']	= $login_id;
			$db_data[$type . '_id']	= $id;
			
			$this->db->insert(strtoupper($type) . '_LOGINS', $db_data);
			
			if($this->db->_error_message()){
				return FALSE;
			}else{
				return TRUE;
			}
		}
		
		
	//	---------------------------------------------------------------------------------
		
		
		/**
		 * Add a new connection between login and product
		 *
		 * @param string $type domainname, hosting
		 * @param int $id 
		 * @param int $login_id 
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function update($id, $data){
			$this->db->where('id', $id);
			$this->db->update('LOGINS', $data);
			
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
		 * Remove a login by ID or list of ID's
		 *
		 * @param mixed $login_id
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function remove($login_id){
			$sql = "DELETE 	FROM LOGINS
					WHERE	id IN($login_id)";
			$this->db->query($sql);
			
			if($this->db->_error_message()){
				return FALSE;
			}else{
				return TRUE;
			}
		}
		
		
	//	---------------------------------------------------------------------------------
		
		
		/**
		 * Remove a  login connection the id of the product
		 *
		 * @param string $type domainname, hosting
		 * @param int $id
		 * @param int $login_id
		 * @return boolean
		 * @author Wouter Samyn 
		 * @version	1.0 
		 */
		public function remove_connection($type, $id, $login_id){
			$this->db->where($type . '_id', $id);
			$this->db->where('login_id', $login_id);
			$this->db->delete( strtoupper($type) . '_LOGINS');
			
			if($this->db->_error_message()){
				return FALSE;
			}else{
				return TRUE;
			}
		}
		
		
}