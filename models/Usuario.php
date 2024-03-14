<?php 

namespace Model;

class Usuario extends ActiveRecord {
    //Base de datos

    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email' ,'password',
     'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? 0; 
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';       
    }
   
    public function validarNuevaCuenta() {

        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre es obligatorio';
        }
        if(!$this->apellido){
            self::$alertas['error'][] = 'El Apellido es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }
        if(!$this->telefono){
            self::$alertas['error'][] = 'El Telefono es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if($this->password && strlen($this->password) < 8){
            self::$alertas['error'][] = "El Password debe contener minimo 8 caracteres";
        }
       

        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El Password es obligatorio';
        }

        return self::$alertas;
        
    }

    public function validarEmail(){

        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }

        return self::$alertas;
    }

    public function validarPassword(){

        if(!$this->password){
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if($this->password && strlen($this->password) < 8){
            self::$alertas['error'][] = "El Password debe contener minimo 8 caracteres";
        }
    }

    public function existeUsuario(){
        //Revisar si el Usuario existe
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
    
        $resultado = self::$db->query($query);

        if ($resultado->num_rows){
            self::$alertas['error'][] = "Usuario ya existente";
        }

        return $resultado;
    }

    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
    public function crearToken(){
        $this->token = uniqid();
    }
    public function comprobarPasswordAndVerificado($password) {
      $resultado = password_verify($password, $this->password);

      if(!$this->confirmado) {
        self::$alertas['error'][] = "Tu cuenta no ha sido confirmada";
      }else if (!$resultado ){
        self::$alertas['error'][] = "El usuario o contraseña son erroneo";
      }else{
        return true;
      }
      
    }
    
}