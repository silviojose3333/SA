<?php
require_once "conexao.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

//  ARQUIVO COM AS FUNÇÕES QUE GERAM SENHA E SIMULAM O ENVIO

if($_SERVER["REQUEST_METHOD"]=="POST" || $_SERVER["REQUEST_METHOD"]=="GET"){
    if($_SERVER["REQUEST_METHOD"]=="GET"){
        $email=$_GET['email'];
    }else{
    $email=$_POST['email'];
    }
    //  VERIFICA SE O EMAIL EXISTE NO BANCO
    $sql="SELECT * FROM usuario WHERE email=:email";
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':email',$email);
    $stmt->execute();
    $usuario=$stmt->fetch(PDO::FETCH_ASSOC);
    if($usuario){
        //  GERA UMA SENHA TEMPORÁRIA ALEATÓRIA
        $codigo=bin2hex(random_bytes(4));
        $mensagem = "Olá! Sua nova senha temporária é: $codigo\n";

        $registro = "Para: $email\n$mensagem\n----------------------\n";
    
        file_put_contents("emails_simulados.txt", $registro, FILE_APPEND);




    }else{
        echo"<script>alert('email não encontrado');window.location.href='enviar_email.php';</script>";
           exit();
    }
}else{
    echo"<script>alert('email não encontrado');window.location.href='enviar_email.php';</script>";
           exit();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar senha</title>
    <link rel="stylesheet" href="verificar_email.css">
 
</head>
<body>
    <h2>Recuperar senha</h2>
    <form action="alterar_senha.php" method="POST">
    <label for="cogigo">Coloque o código do email enviado para <?=htmlspecialchars($email)?></label>
    <input type="codigo" id="codigo" name="codigo" required>
    <input type="hidden" id="senha" name="senha"  value="<?=htmlspecialchars($codigo)?>">
    <input type="hidden" id="usuario" name="usuario"  value="<?=htmlspecialchars($usuario['id_usuario'])?>">
    <input type="hidden" id="email" name="email"  value="<?=htmlspecialchars($email)?>">
    <button type="submit">Enviar codigo</button>


    </form>
</body>
</html>