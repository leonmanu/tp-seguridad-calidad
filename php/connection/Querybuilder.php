<?php

class Querybuilder {
	
	private $conn;
	private $values;
	private $host = DB_HOST;
	private $user = DB_USER;
	private $pass = DB_PASS;
	private $name = DB_NAME;
	private $char = DB_CHARSET;
	private $variable = DB_RETURN;
	
	//Abre conexion a la base
	public function __construct($host = NULL,$user = NULL,$pass = NULL,$name = NULL,$char = NULL,$variable = NULL) {
		if($host != NULL) 
			$this->host = $host;
		if($user != NULL) 
			$this->user = $user;
		if($pass != NULL) 
			$this->pass = $pass;
		if($name != NULL) 
			$this->name = $name;
		if($char != NULL) 
			$this->char = $char;
		if($variable != NULL) 
			$this->variable = $variable;
		@$this->conn = mysql_connect($this->host,$this->user,$this->pass);
		if(!$this->conn)
			die();
		@mysql_select_db($this->name,$this->conn);
	}
	
	//Cerrar conexion a la base
	public function __destruct() {
		@$close = mysql_close($this->conn);
		if(DB_DEBUG)
			$this->debug("DESCONNECTED",$this->host,$close);
	}
	
	//Reemplaza strings en sql injection
	/*private function replace($value,$restore = false) {
		$injection = array("select","insert","delete","table","update","trucate","drop","applet","object","--");
		if($restore == false)
			foreach((array)$injection as $find)
				$value = str_ireplace($find." ","{{".$find."}}",$value);
		else
			foreach((array)$injection as $find)
				$value = str_ireplace("{{".$find."}}",$find." "	,$value);
		$value = $this->line_break($value);
		return $value;
	}

	//Reemplaza string para romper las lineas
	private function line_break($value) {
		$value = str_ireplace("\\n","\n",$value);
		$value = str_ireplace("\\r","\r",$value);
		$value = str_ireplace("\\","",$value);
		$value = str_ireplace("\\","",$value);
		return $value;
	}*/
	
	/**
	 * check the string against sql injection
	 *
	 * @param string $value
	 * @return string
	 */
	public function secure($value) {
		return mysql_real_escape_string($this->$value);// replace($value) para evitar la inyeccion
	}
	
	/**
	 * execute the query
	 * if is select query return the result lines
	 * if is insert query return the inserted id
	 *
	 * @param string $sql
	 * @return string|object
	 */
	public function query($sql) {
		if(strtolower($this->char) == "utf8")
			$sql = utf8_decode($sql);
		@$query = mysql_query($sql,$this->conn);
		if(DB_DEBUG)
			$this->debug("SQL",$sql,$query);
		if((strtolower(substr(trim($sql),0,6)) == "select") && ($query != false)) {
			$return = NULL;
			while($line = mysql_fetch_object($query)) {
				$new_line = ($this->variable == 'object') ? (object)NULL : (array)NULL;
				foreach((array)$line as $key => $value) {
					$value = $this->((strtolower($this->char) == "utf8") ? utf8_encode($value) : $value),true;//funcion replace despues del $this
					if($this->variable == 'object')
						$new_line->$key = $value;
					else
						$new_line["$key"] = $value;
				}
				$return[] = $new_line;
			}
		} elseif(strtolower(substr($sql,0,6)) == "insert")
			$return = mysql_insert_id();
		else
			$return = mysql_affected_rows();
		return $return;
	}
	
	//Obtener siguiente id
	public function nextid($table,$col) {
		$sql = "SELECT IFNULL(MAX(".$this->secure($col)."),0) + 1 AS maximum FROM `".$this->secure($table)."`";
		$lines = $this->query($sql);
		return ($lines != NULL) ? (($this->variable == 'object') ? (int)$lines[0]->maximum : (int)$lines[0]["maximum"]) : NULL;
	}
	
	public function insert($table,$values,$where = NULL,$is = NULL) {
		if($where != NULL) {
			$keys[] = "`".$this->secure($where)."`";
			$vals[] = "'".$this->secure($is)."'";
		}
		foreach((array)$values as $key => $value) {
			$keys[] = "`".$this->secure($key)."`";
			$vals[] = "'".$this->secure($value)."'";
		}
		$sql = "INSERT INTO `".$this->secure($table)."` (".implode(",",$keys).") VALUES (".implode(",",$vals).")";
		return $this->query($sql);
	}

	public function delete($table,$where,$is) {
		$where = $this->wheres($where,$is);
		$sql = "DELETE FROM `".$this->secure($table)."` WHERE ".$where;
		return $this->query($sql);
	}
	
	public function update($table,$values,$where) {
		foreach((array)$values as $key => $value)
			$updates[] = "`".$this->secure($key)."`='".$this->secure($value)."'";
		$sql = "UPDATE `".$this->secure($table)."` SET ".implode(",",$updates)." WHERE ".$where;
		return $this->query($sql);
	}

	public function simple_update($table,$values,$where,$is) {
		$where = $this->wheres($where,$is);
		foreach((array)$values as $key => $value)
			$updates[] = "`".$this->secure($key)."`='".$this->secure($value)."'";
		$sql = "UPDATE `".$this->secure($table)."` SET ".implode(",",$updates)." WHERE ".$where;
		return $this->query($sql);
	}
	
	public function select($table,$cols = "*",$where = NULL,$order = NULL,$ini = NULL,$end = NULL) {
		if(!is_array($cols) && ($cols != NULL) && ($cols != "*"))
			$cols = explode(",",trim($cols));
		foreach((array)$cols as $col)
			$rcols[] = "`".$this->secure($col)."`";
		if(!is_array($order) && ($order != NULL))
			$order = explode(",",trim($order));	
		foreach((array)$order as $ord)
			$rorder[] = "`".$this->secure($ord)."`";
		$sql = "SELECT ".((($cols != NULL) && ($cols != "*")) ? implode(", ",$rcols) : "*")." FROM `".$this->secure($table)."`";
		if($where != NULL)
			$sql.= " WHERE ".$this->$where; //replace($where)
		if($order != NULL)
			$sql.= " ORDER BY ".implode(", ",$rorder);
		if($end != NULL)
			$sql.= " LIMIT ".(int)$ini.",".(int)$end;
		return $this->query($sql);
	}

	public function simple_select($table,$cols = "*",$where = NULL,$is = NULL,$order = NULL,$ini = NULL,$end = NULL) {
		$where = $this->wheres($where,$is);
		return $this->select($table,$cols,$where,$order,$ini,$end);
	}

	public function sselect($table,$cols = "*",$where = NULL,$is = NULL,$order = NULL,$ini = NULL,$end = NULL) {
		return $this->simple_select($table,$cols,$where,$is,$order,$ini,$end);
	}
	
	public function search($table,$cols = "*",$search,$is,$order = NULL,$ini = NULL,$end = NULL) {		
		if(is_array($search))
			foreach((array)$search as $current)
				$where[] = "`".$this->secure($current)."` LIKE '".$this->secure($is)."'";
		else
			$where[] = "`".$this->secure($search)."` LIKE '".$this->secure($is)."'";
		$where = implode(" OR ",$where);
		return $this->select($table,$cols,$where,$order,$ini,$end);
	}
	
	private function wheres($where,$is,$concat = "AND") {
		if(($where != NULL) && ($is != NULL)) {
			if(is_array($where)) {
				for($i=0,$t=count($where);$i<$t;$i++)
					$array[] = "`".$this->secure($where[$i])."`='".$this->secure($is[$i])."'";
				$where = implode(" ".$concat." ",$array);
			} else	
				$where = "`".$this->secure($where)."`='".$this->secure($is)."'";
		}
		return $where;
	}

}