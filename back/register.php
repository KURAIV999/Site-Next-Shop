<?php
// register.php

// Ativa a exibição de erros (desativar em produção)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require 'conexao.php'; // Importa a conexão

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO tblregistrar (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Erro na preparação da consulta: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $password);
    $executar = mysqli_stmt_execute($stmt);

    if ($executar) {
        echo "<script>alert('LojasNext: CADASTRADO COM SUCESSO!'); window.location.href = '../pages/login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Erro ao registrar: " . mysqli_stmt_error($stmt) . "');</script>";
    }

    mysqli_stmt_close($stmt);
}
?>
