<?php
session_start();
include 'bancodedados.php'; // Certifique-se de que o caminho esteja correto

$email = $_POST['email'];
$senha = $_POST['senha'];

// Certifique-se de que as entradas estão seguras para evitar SQL Injection
$email = mysqli_real_escape_string($conexao, $email);
$senha = mysqli_real_escape_string($conexao, $senha);

// Buscando os dados na tabela tblregistrar
$busca = mysqli_query($conexao, "SELECT * FROM tblregistrar WHERE email='$email'");
$dados = mysqli_fetch_array($busca);

if ($dados) {
    $senha_ok = $dados['senha'];

    // Verifique se a senha está correta
    if ($senha != $senha_ok) {
        echo json_encode(["success" => false, "message" => "Senha incorreta!"]);
    } else {
        // Armazena as informações do usuário na sessão
        $_SESSION['snome'] = $dados['nome'];
        $_SESSION['slogin'] = $email; // Armazena o email na sessão
        echo json_encode(["success" => true, "message" => "Login bem-sucedido!"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Email não encontrado!"]);
}
?>
