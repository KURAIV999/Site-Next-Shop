<?php
session_start();
include('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $info_especifica = $_POST['info_especifica'];
    $outro_dado = $_POST['outro_dado'];

    // Verifica se o usuário já tem informações na tabela
    $query_check = "SELECT id FROM tbldados_usuario WHERE usuario_id = ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param('i', $usuario_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Atualiza as informações existentes
        $query = "UPDATE tbldados_usuario SET info_especifica = ?, outro_dado = ? WHERE usuario_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssi', $info_especifica, $outro_dado, $usuario_id);
    } else {
        // Insere novas informações
        $query = "INSERT INTO tbldados_usuario (usuario_id, info_especifica, outro_dado) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iss', $usuario_id, $info_especifica, $outro_dado);
    }

    if ($stmt->execute()) {
        echo "Informações atualizadas com sucesso!";
    } else {
        echo "Erro ao atualizar informações: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Informações</title>
</head>
<body>
    <form method="POST" action="editar_informacoes.php">
        <label>Informação Específica:</label>
        <input type="text" name="info_especifica" required><br>

        <label>Outro Dado:</label>
        <input type="text" name="outro_dado" required><br>

        <button type="submit">Salvar</button>
    </form>
</body>
</html>
