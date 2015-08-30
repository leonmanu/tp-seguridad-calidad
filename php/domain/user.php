<?php
include_once (dirname(__DIR__)."/connection/Connection.php");

class Usuario extends Connection{
    private $surname;
    private $name;
    private $rol;
    private $mail;
    private $pass;
    private $userName;

    public function __construct(){

        parent::__construct();
    }

    public function SignUp($data){
        $errorMessage = array();

        //Guardo en variable cada dato.
        $name = $data["name"];
        $surname = $data["surname"];
        $userName = $data["userName"];
        $pass = $data["pass"];
        $repass = $data["repass"];
        $mail = $data["mail"];
        $rol = "COMUN";

        //Valido los datos
        if(preg_match("/^[a-zA-ZñÑáéíóÁÉÍÓÚ]*$/",$name))
        {
            $errorMessage["name"] = 0;
        }else
        {
            $errorMessage["name"] = "El nombre no es correcto";
        }

        if(preg_match("/^[a-zA-ZñÑáéíóÁÉÍÓÚ]*$/",$surname))
        {
            $errorMessage["name"] = 0;
        }else
        {
            $errorMessage["name"] = "El nombre no es correcto";
        }

        if(preg_match("/[\w]{6,}/",$pass))
        {
            $errorMessage["pass"] = 0;
        }else
        {
            $errorMessage["pass"] = "La contraseña no es correcta";
        }

        if($repass == $pass)
        {
            $errorMessage["repass"] = 0;
        }else
        {
            $errorMessage["repass"] = "Las contraseñas no coinciden";
        }

        if(preg_match("/^[a-zA-Z0-9_ñÑáéíóÁÉÍÓÚ]*[@]+[a-z]+([.]{1}[a-z]+)*$/",$mail))
        {
            $errorMessage["mail"] = 0;
        }else
        {
            $errorMessage["mail"] = "El mail ingresado no es válido";
        }

        $isError = false;
        $errorMessageView = "";
        foreach($errorMessage as $error){
            if( $error !== 0 )
            {
                $isError = true;
                $errorMessageView = $errorMessageView . $error."<br>";
            }
        }


        if($isError)
        {
            //Informo que hay errores y el mensaje de error
            echo $errorMessageView;
        }else
        {
            //Verifico que el mail y nombre de usuario no estén en uso
            $userQuery = "SELECT nombre_usuario FROM USUARIO WHERE nombre_usuario = '$userName' ";
            $result = $this->db->query($userQuery) or die("Error en el SELECT USUARIO: ".mysqli_error($this->db));
            if($result->num_rows === 0)
            {
                $userExist = false;
            }else
            {
                $userExist = true;
                $errorMessageView += "El usuario ingresado ya existe<br>";
            }
            $mailQuery = "SELECT mail FROM USUARIO where mail = '$mail' ";
            $result = $this->db->query($mailQuery) or die("Error en el SELECT mail: ".mysqli_error($this->db));

            if($result->num_rows === 0)
            {
                $mailExist = false;
            }else
            {
                $mailExist = true;
                $errorMessageView += "El mail ingresado ya existe<br>";
            }
            if($mailExist == true || $userExist == true)
            {
                //Informo.
                echo $errorMessageView;
            }else
            {
                //Procedo a insertar los datos a la bdd.
                $query = "INSERT INTO USUARIO(nombre, apellido, nombre_usuario, pass, rol, mail)
                VALUES ('$name', '$surname', '$userName', '$pass', '$rol', '$mail')";
                $this->db->query($query) or die('Error en el INSERT: ' . mysqli_error($this->db));
                echo "Usuario registrado";
            }
        }
    }

    public function Login($userName, $pass){
        $this -> userName = $userName;
        $this -> pass = $pass;

        $query = "SELECT nombre_usuario, rol FROM USUARIO where nombre_usuario = '$userName' and pass = '$pass'";
        $result = $this->db->query($query) or die( "Error en el SELECT: ".mysqli_error($this->db) );
        if($result->num_rows === 1)
        {
            $canLogin = true;
        };
        while ($obj = $result->fetch_object()) {
            $userName = $obj->nombre_usuario;
            if ($obj->rol === "ADMIN") {
                $isAdmin = true;
            } else {
                $isAdmin = false;
            }
        }

        //Inicio session.
        if($canLogin)
        {
            $data['valido'] = true;
            $data['message'] = "Todos los datos correctos.<br/>" ;
            session_start();
            $_SESSION['userName'] = $userName;
            if($isAdmin)
            {
                $_SESSION['rol'] = "ADMIN";
            }else
            {
                $_SESSION['rol'] = "COMUN";
            }
        }else
        {
            $data['valido'] = false;
        }
        echo json_encode($data);
    }

    public function Update($userName, $data){



    }
}

?>

