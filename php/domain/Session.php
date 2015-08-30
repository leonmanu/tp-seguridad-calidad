<?php
	class Session{
		public function __construct(){}
		
		public function initSession(){
			@session_start();
		}
		
		public function setSession($varnombre, $valor){
			$_SESSION[$varnombre] = $valor;
		}
		
		public function destroySession(){
			session_unset();
			session_destroy();
		}
	
	}
?>