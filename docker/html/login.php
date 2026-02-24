<?php
session_start();
// Si ya está logueado, lo mandamos directo al muro
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Extagram</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fafafa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border: 1px solid #dbdbdb; border-radius: 3px; width: 100%; max-width: 350px; text-align: center; }
        .login-box h1 { font-family: 'Georgia', serif; color: #262626; margin-bottom: 30px; }
        input { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #dbdbdb; border-radius: 3px; box-sizing: border-box; background: #fafafa; }
        button { width: 100%; padding: 10px; background-color: #0095f6; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        button:hover { background-color: #007bb5; }
        .error { color: red; font-size: 14px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="login-box">
    <h1>Extagram</h1>
    
    <?php
    if (isset($_GET['error']) && $_GET['error'] == 1) {
        echo '<div class="error">Usuario o contraseña incorrectos.</div>';
    }
    ?>

    <form action="auth.php" method="POST">
        <input type="text" name="username" placeholder="Usuario" required autocomplete="off">
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>
</div>

</body>
</html>