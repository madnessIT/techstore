<?php
/**
 * TechStore - AuthController
 * Archivo: controllers/AuthController.php
 */

class AuthController {
    
    private ClienteModel $clienteModel;
    
    public function __construct() {
        $this->clienteModel = new ClienteModel();
    }
    
    /**
     * Login de cliente
     */
    public function loginCliente(): void {
        if (clienteLogueado()) redirect('/mi-cuenta');
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verificarCsrf($_POST['_csrf'] ?? '')) {
                $error = 'Error de seguridad. Intente de nuevo.';
            } else {
                $email    = strtolower(trim($_POST['email'] ?? ''));
                $password = $_POST['password'] ?? '';
                
                if (empty($email) || empty($password)) {
                    $error = 'Complete todos los campos.';
                } else {
                    $cliente = $this->clienteModel->obtenerPorEmail($email);
                    
                    if ($cliente && $cliente['activo'] && 
                        $this->clienteModel->verificarPassword($password, $cliente['password'])) {
                        
                        // Login exitoso
                        session_regenerate_id(true);
                        $_SESSION['cliente_id']       = $cliente['id'];
                        $_SESSION['cliente_nombre']   = $cliente['nombre'];
                        $_SESSION['cliente_apellido'] = $cliente['apellido'];
                        $_SESSION['cliente_email']    = $cliente['email'];
                        
                        $this->clienteModel->actualizarUltimoAcceso($cliente['id']);
                        
                        // Migrar carrito de sesión
                        $sessionId = $_SESSION['cart_session_id'] ?? null;
                        if ($sessionId) {
                            $carritoModel = new CarritoModel();
                            $carritoModel->migrarSesionACliente($sessionId, $cliente['id']);
                        }
                        
                        $destino = $_SESSION['redirect_after_login'] ?? '/mi-cuenta';
                        unset($_SESSION['redirect_after_login']);
                        redirect($destino);
                    } else {
                        $error = 'Credenciales incorrectas o cuenta inactiva.';
                    }
                }
            }
        }
        
        require BASE_PATH . '/views/auth/login.php';
    }
    
    /**
     * Registro de cliente
     */
    public function registroCliente(): void {
        if (clienteLogueado()) redirect('/mi-cuenta');
        
        $errores = [];
        $datos   = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verificarCsrf($_POST['_csrf'] ?? '')) {
                $errores[] = 'Error de seguridad.';
            } else {
                $datos = [
                    'nombre'    => trim($_POST['nombre'] ?? ''),
                    'apellido'  => trim($_POST['apellido'] ?? ''),
                    'email'     => strtolower(trim($_POST['email'] ?? '')),
                    'password'  => $_POST['password'] ?? '',
                    'password2' => $_POST['password2'] ?? '',
                    'telefono'  => trim($_POST['telefono'] ?? ''),
                    'ciudad'    => trim($_POST['ciudad'] ?? ''),
                ];
                
                // Validaciones
                if (empty($datos['nombre']))   $errores[] = 'El nombre es requerido.';
                if (empty($datos['apellido'])) $errores[] = 'El apellido es requerido.';
                if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL))
                    $errores[] = 'Email inválido.';
                if (strlen($datos['password']) < 8)
                    $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
                if ($datos['password'] !== $datos['password2'])
                    $errores[] = 'Las contraseñas no coinciden.';
                if ($this->clienteModel->emailExiste($datos['email']))
                    $errores[] = 'Este email ya está registrado.';
                
                if (empty($errores)) {
                    $id = $this->clienteModel->registrar($datos);
                    if ($id) {
                        setFlash('success', '¡Cuenta creada exitosamente! Inicia sesión.');
                        redirect('/login');
                    } else {
                        $errores[] = 'Error al crear la cuenta. Intente de nuevo.';
                    }
                }
            }
        }
        
        require BASE_PATH . '/views/auth/registro.php';
    }
    
    /**
     * Mi Cuenta
     */
    public function miCuenta(): void {
        if (!clienteLogueado()) {
            $_SESSION['redirect_after_login'] = '/mi-cuenta';
            redirect('/login');
        }
        
        $cliente      = $this->clienteModel->obtenerPorId($_SESSION['cliente_id']);
        $pedidoModel  = new PedidoModel();
        $pedidos      = $pedidoModel->obtenerDeCliente($_SESSION['cliente_id']);
        
        require BASE_PATH . '/views/auth/mi-cuenta.php';
    }
    
    /**
     * Logout
     */
    public function logout(): void {
        session_destroy();
        redirect('/');
    }
}
