<?php

require (dirname(__DIR__)."/connection/Connection.php");

class Wall{
	
	private $idMuro;
	private $idUsuario;
	private $privacidad;

	#Datos Muro [id_muro auto_increment,id_usuario,privacidad set('propietario','todos','registrados')]
	public function __construct($idMuro,$idUsuario,$privacidad){	
		$this -> idMuro = $idMuro;
		$this -> idUsuario = $idUsuario;
		$this -> privacidad = $privacidad;		
	}
	
	public function getMessages(){
		$myConnection = new Connection();
		$mySession = new Session();
			
			$result1 = $myConnection -> query("SELECT * FROM MENSAJE WHERE id_muro='$this->idMuro';");
			
		if($row1 = $result1 -> fetch_object()){//Devuelve la fila actual de un conjunto de resultados como un objeto
			
			$mySession -> initSession();
			$mySession -> setSession('idMessage',$fila->id_mensaje);
			$mySession -> setSession('idUser',$fila->id_usuario);
			$mySession -> setSession('idWall',$fila->id_muro);
			$mySession -> setSession('idContent',$fila->contenido);
			$mySession -> setSession('idDate',$fila->fecha);
			
				$result2 = $myConnection -> query("SELECT * FROM USUARIO WHERE id_usuario='$fila->id_usuario';");
				if($row2 = $result2 -> fetch_object()){
					$typeUser = $row2 -> rol;
					$idUser = $row2 -> id_usuario;
					switch($typeUser){			
						case 'Administrador':
							header('location: Administrador/index.php?idUser='.$idUser);//Redirecciono al index.php dentro de la carpeta Administrador
						break;
						case 'Comun':
							header('location: Comun/index.php?idUser='.$idUser);//Redirecciono al index.php dentro de la carpeta Comun
						break;				
					}
				}
		}
		else{
			//header ('location: ../index.php?error=1');
		}
			
		$myConnection -> close();
	}

}

?>