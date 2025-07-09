<?php
session_start();
require_once 'conexao.php';
//  GARANTE QUE O USUÁRIO ESTEJA LOGADO


if($_SERVER ["REQUEST_METHOD"]=="POST"){
    if(!empty($_POST['senha']) && !empty($_POST['codigo'])){
    if($_POST['senha']!=$_POST['codigo']){
        $email=$_POST['email'];
        echo "<script>alert('algo deu errado'); window.location.href='verificar_email.php?email=$email';</script>";
        exit();
    }
}
    $id_usuario=$_POST['usuario'];
    if(empty($_POST['senha'])){
    
    $nova_senha=$_POST['nova_senha'];
    $confirmar_senha=$_POST['confirmar_senha'];
    if($nova_senha !==$confirmar_senha){
        echo"<script>alert('As senhas não coincidem!');</script>";
    }elseif(strlen($nova_senha)<8){
        echo"<script>alert('A senha deve ter pelo menos oito caracteres!');</script>";
    }elseif($nova_senha==="tem123"){
        echo"<script>alert('escolha uma senha diferente de temporaria!');</script>";
    }else{
        //  ATUALIZA A SENHA E REMOVE O STATUS DE TEMPORARIA
        $sql="UPDATE usuario SET senha=:senha WHERE id_usuario=:id";
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':senha',$confirmar_senha);
        $stmt->bindParam(':id',$id_usuario);
        if($stmt->execute()){
            session_destroy();
            //    FINALIZA A SESSÃO
           echo"<script>alert('Senha alterada com sucesso! Faça login novamente');window.location.href='login.php';</script>";
           exit();

        }else{
            echo"<script>alert('Erro ao alterar a senha');</script>";
        }
    }
}
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar a senha</title>
    <link rel="stylesheet" href="alterar_senha.css">
    <script src="script.js"></script>
</head>
<body>
    <h2>Alterar a senha</h2>
    <p>Digite sua nova senha abaixo</p>

    <form action="alterar_senha.php" method="POST">
    <label for="nova_senha">Nova senha</label>
    <input type="password" id="nova_senha" name="nova_senha" required>

    <label for="confirmar_senha">confirmar nova senha</label>
    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
    <input type="hidden" id="usuario" name="usuario" value="<?=htmlspecialchars($id_usuario)?>">
    

    <label>
    <input type="checkbox" onclick="mostrarSenha()">Mostrar senha
    </label>
    <button type="submit">Salvar nova senha</button>
        
    </form>
    <script>
        function mostrarSenha(){
            var senha1=document.getElementById("nova_senha");
            var senha2=document.getElementById("confirmar_senha");
            var tipo=senha1.type==="password"? "text":"password";
            senha1.type= tipo
            senha2.type= tipo
        }

    </script>
</body>
</html>