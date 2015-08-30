<?php
//Datos para la conexión.
if(!defined('DB_HOST'))
{
    define('DB_HOST','localhost');
}
if(!defined('DB_USER'))
{
    define('DB_USER','root');
}
if(!defined('DB_PASS'))
{
    define('DB_PASS','');
}
if(!defined('DB_NAME'))
{
    define('DB_NAME','thewall');
}
if(!defined('DB_CHARSET'))
{
    define('DB_CHARSET','utf-8');
}
if(!defined('DB_RETURN')){
	define('DB_RETURN','array');
}
/*if(!defined('PATH'))
{
    $path = $_SERVER['DOCUMENT_ROOT'];
    $path .= "/facultad/web2/tp/pruebas";
    define('PATH',$path);
}*/

?>
