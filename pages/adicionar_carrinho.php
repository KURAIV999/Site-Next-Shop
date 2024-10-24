<?php
session_start();
include '../back/conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../pages/login.php');
    exit(); // É importante usar exit após o redirecionamento
}

// Recupera os dados do produto e da quantidade
$produto_id = intval($_POST['produto_id']);
$nome = $_POST['nome'];
$preco = floatval($_POST['preco']);
$imagem_principal = $_POST['imagem']; // Aqui você deve assegurar que está passando a imagem principal corretamente
$quantidade = intval($_POST['quantidade']);
$usuario_id = $_SESSION['usuario_id']; // ID do usuário logado

// Verifica se o produto existe na tabela
$check_sql = "SELECT * FROM tblproduto WHERE produto_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $produto_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    die('Produto não encontrado.');
}

// Prepara a consulta SQL para inserir o produto no carrinho
$sql = "INSERT INTO tblcarrinho (usuario_id, produto_id, nome, preco, imagem_principal, quantidade) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die('Erro ao preparar a consulta: ' . $conn->error);
}

// Associa os parâmetros
$stmt->bind_param("iisdsi", $usuario_id, $produto_id, $nome, $preco, $imagem_principal, $quantidade);

// Executa a consulta
if ($stmt->execute()) {
    // Sucesso ao adicionar ao carrinho
    header('Location: carrinho.php'); // Redireciona para a página do carrinho
    exit;
} else {
    // Erro ao adicionar
    echo 'Erro ao adicionar ao carrinho: ' . $stmt->error;
}

// Fecha a consulta e a conexão
$stmt->close();
$conn->close();
?>
