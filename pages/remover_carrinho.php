<?php
session_start();
include '../back/conexao.php';

// Verifica se o ID do produto foi enviado via POST
if (!isset($_POST['produto_id'])) { // Verifica se o produto_id foi enviado corretamente
    echo 'Erro: ID do produto não fornecido.';
    exit;
}

$produtoId = intval($_POST['produto_id']); // Converte o produto_id para inteiro

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo 'Erro: Usuário não está logado.';
    exit;
}

$usuarioId = $_SESSION['usuario_id'];

// Prepara a consulta para remover o produto do carrinho
$sql = "DELETE FROM tblcarrinho WHERE produto_id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);

// Verifica se a preparação da consulta falhou
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}

// Liga os parâmetros na consulta (produto_id e usuario_id)
$stmt->bind_param('ii', $produtoId, $usuarioId);
$stmt->execute();

// Verifica se algum produto foi removido
if ($stmt->affected_rows > 0) {
    // Redireciona de volta para o carrinho com uma mensagem de sucesso
    header("Location: carrinho.php?msg=Produto removido com sucesso!");
    exit;
} else {
    // Redireciona de volta para o carrinho com uma mensagem de erro
    header("Location: carrinho.php?msg=Erro ao remover o produto do carrinho.");
    exit;
}

// Fecha a consulta e a conexão
$stmt->close();
$conn->close();
?>
