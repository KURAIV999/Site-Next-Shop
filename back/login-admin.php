<?php
session_start();
include 'conexao.php'; // Inclua seu script de conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os valores do formulário de login, utilizando null coalesce para evitar erros de variáveis indefinidas
    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;

    // Verifica se os campos não estão vazios
    if (!empty($email) && !empty($senha)) {
        // Consulta para verificar as credenciais do usuário no banco de dados
        $sql = "SELECT * FROM tblregistrar WHERE email = ? AND senha = ?";
        $stmt = $conexao->prepare($sql);

        if ($stmt) {
            // Bind dos parâmetros email e senha
            $stmt->bind_param("ss", $email, $senha);
            $stmt->execute();
            $result = $stmt->get_result();

            // Verifica se o usuário foi encontrado no banco de dados
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Armazena as informações do usuário na sessão
                $_SESSION['usuario_id'] = $user['usuario_id']; // Certifique-se que 'usuario_id' é o campo correto
                $_SESSION['nome_usuario'] = $user['nome']; // Armazena o nome do usuário
                $_SESSION['acesso_especial'] = $user['acesso_especial']; // Define se o usuário tem acesso especial

                // Redireciona o usuário para a página principal (index)
                header("Location: ../pages/index.php");
                exit();
            } else {
                // Se o email ou a senha forem incorretos, redireciona com uma mensagem de erro
                header("Location: ../pages/login.php?error=" . htmlspecialchars('credenciais_incorretas'));
                exit();
            }
        } else {
            // Caso haja erro na preparação da consulta
            die('Erro na preparação da consulta: ' . $conexao->error);
        }
    } else {
        // Se os campos estiverem vazios, redireciona para a página de login com erro
        header("Location: ../pages/login.php?error=" . htmlspecialchars('campo_vazio'));
        exit();
    }
}
?>
