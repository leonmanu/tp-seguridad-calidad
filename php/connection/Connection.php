<?php

require (dirname(__DIR__)."/connection/config.php");

class Connection extends mysqli{
	
	public function __construct(){
		//llamo al constructor de la superclase (mysqli), el cual es $mibd = new mysqli('host','usuarioBD','clave','nombreBD')
		parent::__construct(DB_HOST,DB_USER,DB_PASS,DB_NAME);
		
		$this -> query ("SET NAMES 'utf8';");//manejo de caracteres especiales
		
		//? = (si $this -> connect_errno == true entonces die) : = (de lo contrario se conecta sin problemas y asignamos el valor conectado a $x)
		$this -> connect_errno ? die ('Error con la conexion') : $x = 'Conectado';//TRUE = ERROR FALSE = CONECTADO
		
		//echo $x;
		unset($x);//Nos deshacemos de $x para liberar memoria
	}
	
}
	
?>