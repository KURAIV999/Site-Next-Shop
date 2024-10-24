<?php
include('conexao.php'); // Inclui a conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['produto_id'])) {
    $produto_id = $_POST['produto_id'];

    // Consulta para deletar o produto pelo ID
    $sql = "DELETE FROM tblproduto WHERE produto_id = ?";
    $stmt = $conn->prepare($sql);
    
    // Verifica se a preparação da consulta foi bem-sucedida
    if ($stmt) {
        $stmt->bind_param("i", $produto_id);

        if ($stmt->execute()) {
            // Define a mensagem de sucesso
            $_SESSION['message'] = "Produto deletado com sucesso!";
        } else {
            // Define a mensagem de erro
            $_SESSION['error'] = "Erro ao deletar o produto: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Erro ao preparar a consulta: " . $conn->error;
    }

    $conn->close();

    // Redireciona de volta para a página de gerenciamento de produtos
    header("Location: ../pages/pesquisa-produto.php");
    exit(); // Encerra a execução do script
} else {
    echo "ID do produto não fornecido.";
}
?>
