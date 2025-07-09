<?php
require_once "conexao.php";
session_start();
require_once 'conexao.php';
if(isset($_SESSION['id_usuario'])==1){
    $id_perfil=$_SESSION['perfil'];
    $sqlperfil="SELECT nome_perfil FROM perfil WHERE id_perfil=:id_perfil";
    $stmtperfil=$pdo->prepare($sqlperfil);
    $stmtperfil->bindParam(':id_perfil',$id_perfil);
    $stmtperfil->execute();
    $perfil=$stmtperfil->fetch(PDO::FETCH_ASSOC);
    $nome_perfil=$perfil['nome_perfil'];
  }else{
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
  }
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
$permissoes=[
    1=>["função"=>["adicionar_obra.php","relatorio.php",]],
    2=>["função"=>["relatorio.php"]],
    3=>["função"=>[]]
];
$opcoes_menu=$permissoes[$id_perfil];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de cliente</title>
    <link rel="stylesheet" href="adicionar_adm.css">
    <script src="script.js"></script>
</head>
<body>
<ul class="menu">
    <?php foreach($opcoes_menu as $categoria=>$arquivos):?>
        <li class="dropdown">
            <a href="#"><?=$categoria?></a>
            <ul class="dropdown-menu">
                <?php foreach($arquivos as $arquivo):?>
                    <li>
                        <a href="<?=$arquivo?>"><?=ucfirst(str_replace("_"," ",basename($arquivo,".php")))?></a>
                    </li>
                <?php endforeach;?>
                <li><a href="principal.php">Novidades</a></li>
            </ul>
        </li>
    <?php endforeach;?>
    <form action="pesquisa.php" method="POST">
        <input type="text" name="pesquisa" class="pesquisa" id="pesquisa" placeholder="Pesquisar">
        <button type="submit">Pesquisar</button>
    </form>
    <li class="dropdown">
        <a href="#"><?php echo $nome_perfil;?></a>
        <ul class="dropdown-menu2">
            <?php if($id_perfil!=3):?>
                <li><a href="alterar_cliente.php">Atualizar dados</a></li>
            <?php endif;?>
            <li><a href="login.php">Trocar de conta</a></li>
            <li><a href="logout.php">Sair da conta</a></li>
        </ul>
    </li>
</ul>

<h2 class="titulo_adm">Adicionar Administrador</h2>

<div class="adicionarAdm">
    <form action="adicionar_adm.php" method="POST">
        <label for="nome_usuario">Nome:</label>
        <input type="text" id="nome_usuario" name="nome_usuario" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <label for="confirmar_senha">Confirmar senha:</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required>

        <label>
            <input type="checkbox" onclick="mostrarSenha()"> Mostrar senha
        </label>

        <input type="hidden" name="idperfil" value="1">

        <button type="submit">Salvar nova senha</button>
    </form>
</div>

<script>
    function mostrarSenha(){
        var senha1 = document.getElementById("senha");
        var senha2 = document.getElementById("confirmar_senha");
        var tipo = senha1.type === "password" ? "text" : "password";
        senha1.type = tipo;
        senha2.type = tipo;
    }
</script>

</body>
</html>
