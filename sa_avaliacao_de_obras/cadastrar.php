
<?php
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    

    
    $senha=$_POST['senha'];
    $confirmar_senha=$_POST['confirmar_senha'];
    if($senha !==$confirmar_senha){
        echo"<script>alert('As senhas não coincidem!');</script>";
    }elseif(strlen($senha)<8){
        echo"<script>alert('A senha deve ter pelo menos oito caracteres!');</script>";
    }elseif($senha==="tem123"){
        echo"<script>alert('escolha uma senha diferente de temporaria!');</script>";
    }else{

    $sql="INSERT INTO usuario(nome_usuario,  email , senha, idperfil ) VALUES (:nome_usuario,:email ,:senha,:idperfil )";
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(":nome_usuario",$_POST["nome_usuario"]);
    $stmt->bindParam(":email",$_POST["email"]);
    $stmt->bindParam(":senha",$_POST["senha"]);
    $stmt->bindParam(":idperfil",$_POST["idperfil"]);
    try{
        $stmt->execute();
        header("Location:login.php");

    }catch(PDOException $e){
        error_log("Erro ao inserir cliente:".$e->getMessage());
        echo"Erro ao cadastrar cliente.";
    }
}
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de cliente</title>
    <link rel="stylesheet" href="cadastrar.css">
    <script src="script.js"></script>
    <!--ARQUIVO OPCIONAL PARA ESTILIZAR-->
</head>
<body>
    <h2>Cadastro de cliente</h2>
    <form class="cadastrarUsuario" action="cadastrar.php" method="POST">
        <label for="nome_usuario">Nome:</label>
        <input type="text" id="nome_usuario" name="nome_usuario" required>


        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>

        <label for="nova_senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <label for="confirmar_senha">Confirmar senha:</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required>

        <label>
        <input type="checkbox" onclick="mostrarSenha()">Mostrar senha
        </label>
        <input type="hidden" name="idperfil" value="2">
        <button class="submitAddUsuario" type="submit">Salvar nova senha</button>
        <button type="submit" class="submitAddUsuario">Enviar codigo de verificação para o e-mail</button>

        
    </form>
    <script>
        function mostrarSenha(){
            var senha1=document.getElementById("senha");
            var senha2=document.getElementById("confirmar_senha");
            var tipo=senha1.type==="password"? "text":"password";
            senha1.type= tipo
            senha2.type= tipo
        }

    </script>


</body>
</html>