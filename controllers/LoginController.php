<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];

        // session_start();

        // if($_SESSION['login']){
        //     if ($_SESSION['admin'] === "1"){
        //         header('Location: /admin');
        //     }else {
        //         header('Location: /cita');
        //     }
        // }

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $usuario = new Usuario;
            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);

                if($usuario){
                    //Verificamos el password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)){
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        // $_SESSION['admin'] = $usuario->admin;
                        $_SESSION['login'] = true;

                        //Redireccionar

                        if ($usuario->admin === "1"){
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        }else {
                            header('Location: /cita');
                        }
                    };
                }else{
                    Usuario::setAlerta('error','El usuario o contraseña son erroneo');
                }
            }
        }

        $alertas= Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }
    public static function logout() {
        session_start();

        $_SESSION = [];

        header('Location: /');
    }
    
    public static function olvide(Router $router) {
        
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            
            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1"){
                    //generar token nuevo    
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //Alerta de exito
                    Usuario::setAlerta('exito', 'Te hemos enviado un correo a '
                    . $usuario->email . ". Revisa tú correo para cambiar la contraseña");
                    $alertas = Usuario::getAlertas();

                } else{
                    Usuario::setAlerta('error', 'El Usuario no existe o no esta confirmado');
                    $alertas = Usuario::getAlertas();
                }
            }
        }

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {
        $alertas = [];
        $error = false;
        $token = s($_GET['token']);

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no valido');
            $error = true;
        } 

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Leer el nuevo pssword y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)){
                $usuario->password = null;

                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = NULL;

                $resultado = $usuario->guardar();

                if($resultado){
                    header('Location: /');
                }
            }
        }

        
        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }
    public static function crear(Router $router) {
        $usuario = new Usuario;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            // Si alertas esta vacio

            if (empty($alertas)){
                $resultado = $usuario->existeUsuario();

                if ($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                } else {
                    // hashear el password
                    $usuario->hashPassword();
                    // Generar token
                    $usuario->crearToken();
                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    // crear el usuario
                    $resultado = $usuario->guardar();
                    if ($resultado){
                       header('Location: /mensaje');
                    }

                }
            }
        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router){
        
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if(empty($usuario) || $token === '' || $token === 'NULL'){
            Usuario::setAlerta('error', 'Token no valido');
        }else{
            $usuario->confirmado = "1";
            $usuario->token = NULL;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta activada correctamente');
        };

        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta',[
            'alertas' => $alertas
        ]);
    }
}