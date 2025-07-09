<?php
session_start();
require_once 'conexao.php';
if($_SERVER ["REQUEST_METHOD"]=="POST"){
    $email=$_POST['email'];
    $senha=$_POST['senha'];
    $sql="SELECT * FROM usuario WHERE email=:email";
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':email',$email);
    $stmt->execute();
    $usuario=$stmt->fetch(PDO::FETCH_ASSOC);
    //if($usuario && password_verify($senha,$usuario['senha'])){

    if($usuario && $senha ==$usuario['senha']){
        //  LOGIN BEM SUCEDIDO DEFINE ATRAVÉS DE SESSÃO
        $_SESSION['usuario']=$usuario['nome'];
        $_SESSION['perfil']=$usuario['idperfil'];
        $_SESSION['id_usuario']=$usuario['id_usuario'];
        
        //  REDIRECIONA PARA A PÁGINA PRINCIPAL
        header("Location:principal.php");
        exit();
        }else{
        //  LOGIN INVÁLIDO 
        echo "<script>alert('E-mail ou senha incorretos');window.location.href='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Login</title>
    <link rel="stylesheet" href="login.css">
    <script src="script.js"></script>

</head>
<body>
    <!-- ...código anterior mantido... -->
<h2>Login</h2>
<form class="login" action="login.php" method="POST">
    <label for="email">E-mail</label>
    <input type="email" id="email" name="email" required>

    <label for="senha">Senha</label>
    <input type="password" id="senha" name="senha" required>

    <button class="submitLogin" type="submit">Entrar</button>
</form>

<div class="links-container">
    <p class="linkEsqueciSenha"><a href="enviar_email.php">Esqueceu sua senha?</a></p>
    <p class="linkCadastro"><a href="cadastrar.php">Cadastrar</a></p>
    <p class="linkAnonimo"><a href="principal.php">Entrar em modo Anônimo</a></p>
</div>

</body>
</html>