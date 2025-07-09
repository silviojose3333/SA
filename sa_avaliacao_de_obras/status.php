<?php
session_start();
require_once "conexao.php";
require_once "funcao.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$acao = $_GET['acao'] ?? '';
$tabela = $_GET['tabela'] ?? '';
$serie = $_GET['serie'] ?? '';

if ($id && in_array($acao, ['ativar', 'desativar'])) {
    $novoStatus = ($acao === 'ativar') ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE $tabela SET ativo = :ativo WHERE id_$tabela = :id");
    $stmt->execute([
        ':ativo' => $novoStatus,
        ':id' => $id
    ]);
}

header("Location: detales_obra.php?nome=$serie");
exit;
?>