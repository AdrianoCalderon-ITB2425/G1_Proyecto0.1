<?php
session_start();
require_once 'db_config.php';

// 1. Recogida de datos segura
$user_form = isset($_POST['username']) ? trim($_POST['username']) : '';
$pass_form = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($user_form) || empty($pass_form)) {
    header("Location: login.php?error=1");
    exit();
}

// 2. Conexión a la DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { 
    die("Error de conexión"); 
}

// 3. Consulta preparada
$stmt = $conn->prepare("SELECT username, password FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $user_form);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // 4. Verificación del hash
    if (password_verify($pass_form, $row['password'])) {
        // ÉXITO: Creamos sesión y redirigimos
        $_SESSION['usuario'] = $row['username'];
        header("Location: index.php");
        exit();
    }
}

// 5. Si algo falla (usuario no existe o pass mal), volvemos al login
header("Location: login.php?error=1");
exit();
?>
