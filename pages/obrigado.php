<?php
session_start(); // Inicia a sessão para acessar as variáveis de sessão

// Inclui a conexão com o banco de dados
include '../back/conexao.php';

// Verifica se o usuário está logado
$usuarioLogado = false;
$nomeUsuario = '';
$emailUsuario = '';
$acessoEspecial = false;

// Caso o usuário esteja logado, recupera o nome da sessão e o nível de acesso
if (isset($_SESSION['usuario_id'])) {
    $usuarioLogado = true;

    // Prepara a consulta para pegar o nome e o e-mail do usuário na tblregistrar
    $usuarioId = $_SESSION['usuario_id'];
    $query = "SELECT nome, email FROM tblregistrar WHERE usuario_id = ?"; // Ajuste aqui
    $stmt = $conn->prepare($query);

    // Verifica se a preparação da consulta foi bem-sucedida
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $usuarioId); // 'i' indica que o parâmetro é um inteiro
    $stmt->execute();
    $result = $stmt->get_result();

    // Se o usuário for encontrado, armazena o nome e o e-mail
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $nomeUsuario = $usuario['nome'];
        $emailUsuario = $usuario['email'];
    }

    $acessoEspecial = isset($_SESSION['acesso_especial']) && $_SESSION['acesso_especial'] == 1;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/dados.css">
    <title>Agradecimento - LojasNext</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        h1 {
            font-size: 2.5em;
            color: #228B22; /* Verde para um toque positivo */
        }
        p {
            font-size: 1.2em;
            margin: 20px 0;
        }
        .button {
            background-color: #228B22; /* Verde */
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #1e7b1e; /* Tom mais escuro ao passar o mouse */
        }
        footer {
            margin-top: 40px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>

    <h1>Obrigado pela sua compra <strong style="color: black;"><?php echo htmlspecialchars($nomeUsuario); ?></strong>!</h1>
    <p>Seu pedido foi recebido com sucesso e está sendo processado.</p>
    <p>Um e-mail de confirmação foi enviado para <strong><?php echo htmlspecialchars($emailUsuario); ?></strong>.</p>
    <p>Se precisar de ajuda, não hesite em <a href="https://api.whatsapp.com/send?phone=5513997701290&text=Assist%C3%AAncia%20Next%20Shop" style="color: #228B22;">entrar em contato conosco</a>.</p>

    <a href="index.php" class="button">Voltar para a Página Inicial</a>

    <footer>
        &copy; LojasNext 2024. Todos os direitos reservados.<br>
        Formas de pagamento: Cartão de Crédito, Boleto, PayPal.<br>
        Contato: <a href="mailto:LojasNextoficial@gmail.com.br" style="color: #228B22;">LojasNextoficial@gmail.com.br</a>
    </footer>
    
</body>
</html>
