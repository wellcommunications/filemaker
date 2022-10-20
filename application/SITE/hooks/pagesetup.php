<?php

	class Pagesetup
	{
		function start()
		{	
				
			$CI =& get_instance();
			$exclude_arr = array();
			array_push($exclude_arr, 'lang', 'services', 'login', 'logout', 'image');
			
			if(!in_array($CI->uri->segment(1), $exclude_arr) && !in_array($CI->uri->segment(2), $exclude_arr)){
				$CI->session->set_userdata('current_page', $CI->uri->uri_string());
			}
			
			
			// =========================
			// = set common page parts that can be overriden =
			// =========================
			$CI->load->vars(array( //zodat we geen errors hebben
				"pagetitle" => "Page Title",
				"content" => "<p class='errorLabel'>Under construction.</p>"
			));
		}
		
		function finish()
		{
			
		}
	}

?>