<?php
session_start();
require_once "conexao.php";
require_once "funcao.php";

if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //  PROCESSAR ATUALIZAÇÕES
    $novo_nome = trim($_POST['nome']);
    $novo_email = trim($_POST['email']);

    if (!empty($novo_nome) && !empty($novo_email)) {
        $sql = "UPDATE usuario SET nome_usuario = :nome, email = :email WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $novo_nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $novo_email, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);

        try {
            $stmt->execute();
            header('Location: principal.php');
            exit();
        } catch (PDOException $e) {
            $mensagem = "Erro ao atualizar: " . $e->getMessage();
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos.";
    }
}

// CARREGAR DADOS DO USUÁRIO PARA EXIBIR NO FORMULÁRIO
$sql = "SELECT nome_usuario, email FROM usuario WHERE id_usuario = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "<script>alert('Usuário não encontrado!'); window.location.href='principal.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="alterar_clientee.css">

</head>
<body>

<h2>Editar Usuário</h2>

<?php if ($mensagem): ?>
    <p class="msg"><?= htmlspecialchars($mensagem) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label>Nome:</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome_usuario']) ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

    <button type="submit">Atualizar</button>
</form>
<a class="voltar" href="principal.php">Voltar</a>
</body>
</html>

  
  
  
