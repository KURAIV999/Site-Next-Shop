<?php
// conexao.php

$servername = "localhost"; // geralmente localhost
$username = "root"; // padrão do XAMPP
$password = ""; // deixe vazio se não tiver senha
$dbname = "banco-dados"; // nome do seu banco de dados

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
