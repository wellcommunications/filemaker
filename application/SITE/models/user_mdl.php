<?php 

if (! defined('BASEPATH')) exit('No direct script access');

class user_mdl extends CI_Model {

	//php 5 constructor
	function __construct() {
		parent::__construct();
	}
	
	
	/**
	 * Get a list of usernames and ID
	 *
	 * @return array
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function get_users() {
		$this->db->select('id, username');
		$this->db->order_by('priority', 'DESC');
		$res = $this->db->get('USERS');
		
		if($res->num_rows()>0){
			$users = array();
			$users[0]	= "--- Selecteer een gebruiker ---";
			foreach($res->result() as $row){
				$users[$row->id] = $row->username;
			}
			return $users;
		}else{
			return FALSE;
		}
	}
	
	
//	-------------------------------------------------------------------------------------
	
	
	/**
	 * Check if password is correct
	 *
	 * @param int $user_id 
	 * @param string $pass 
	 * @return object
	 * @author Wouter Samyn 
	 * @version	1.0 
	 */
	public function check_pass($user_id, $pass){
		$this->db->select('U.username, R.allow_add, R.allow_remove, R.allow_edit, R.allow_users, R.allow_reports');
		$this->db->join('ROLES R', 'U.role = R.id');
		$this->db->where('U.id', $user_id);
		$this->db->where('password', md5($pass));
		$this->db->limit(1);
		$res = $this->db->get('USERS U');
		
		if($res->num_rows()>0){
			return $res->row();
		}else{
			return FALSE;
		}
	}

}