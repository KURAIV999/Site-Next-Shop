<?php
// Incluir a conexão com o banco de dados
include '../back/conexao.php'; // Certifique-se de que o caminho está correto

// Supondo que você esteja lidando com um método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $action = $_POST['action'] ?? ''; // Captura a ação se for uma requisição específica

    // Verifica se o usuário está tentando fazer login
    if (!empty($email) && !empty($senha)) {
        $stmt = $conn->prepare("SELECT usuario_id, nome FROM tblregistrar WHERE email = ? AND senha = ?");
        if (!$stmt) {
            die("Erro na preparação da consulta: " . $conn->error);
        }

        $stmt->bind_param("ss", $email, $senha);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $usuario = $result->fetch_assoc();
                $usuario_id = $usuario['usuario_id']; // Verifica se a chave correta está sendo usada
                $nome_usuario = $usuario['nome']; // Obtém o nome do usuário
                echo "Bem-vindo, $nome_usuario. ID: " . $usuario_id; // Mensagem personalizada
            } else {
                echo "Email ou senha incorretos.";
            }
        } else {
            echo "Erro na execução da consulta: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Por favor, preencha todos os campos.";
    }

    // Verifica se o usuário quer buscar informações
    if ($action === 'fetchUserData') {
        // Busca informações na tabela que você deseja
        $stmt = $conn->prepare("SELECT * FROM tbldados WHERE usuario_id = (SELECT usuario_id FROM tblregistrar WHERE email = ? LIMIT 1)");
        if (!$stmt) {
            die("Erro na preparação da consulta: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Aqui você pode formatar a resposta como desejar
                    echo "Dados do usuário: " . json_encode($row); // Exemplo de resposta
                }
            } else {
                echo "Nenhum dado encontrado para o usuário.";
            }
        } else {
            echo "Erro na execução da consulta: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fecha a conexão
$conn->close();
?>
