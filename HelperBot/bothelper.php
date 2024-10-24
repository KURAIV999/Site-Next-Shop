<?php
// Incluir a conexão com o banco de dados
include '../back/conexao.php'; // Certifique-se de que o caminho está correto

// Verifica se a conexão foi estabelecida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Supondo que você esteja lidando com um método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário ou do chatbot
    $email = $_POST['email'] ?? ''; // Captura o email
    $senha = $_POST['senha'] ?? ''; // Captura a senha

    // Verifica se os campos não estão vazios
    if (!empty($email) && !empty($senha)) {
        // Consulta para verificar se o usuário existe
        $stmt = $conn->prepare("SELECT usuario_id, nome FROM tblregistrar WHERE email = ? AND senha = ?");

        // Verifica se a preparação da consulta falhou
        if (!$stmt) {
            echo "Erro na preparação da consulta: " . $conn->error;
            exit; // Encerra a execução do script
        }

        // Vincula os parâmetros
        $stmt->bind_param("ss", $email, $senha);

        // Executa a consulta
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            // Verifica se o usuário foi encontrado
            if ($result->num_rows > 0) {
                $usuario = $result->fetch_assoc();
                $usuario_id = $usuario['usuario_id'];
                $nomeUsuario = $usuario['nome']; // Captura o nome do usuário
                echo "Bem-vindo, " . $nomeUsuario . "!"; // Mensagem de sucesso com o nome do usuário
            } else {
                echo "Usuário não encontrado.";
            }
        } else {
            echo "Erro na execução da consulta: " . $stmt->error;
        }

        // Fecha a declaração
        $stmt->close();
    } else {
        echo "Por favor, preencha todos os campos.";
    }
}

// Fecha a conexão
$conn->close();
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="botstyle.css">
    <title>Assistente Virtual Avançado</title>
</head>
<body>
    <div id="chatbot-container">
        <div id="chatbot-header">Assistente Virtual Avançado</div>
        <div id="chatbot-messages">
            <div class="message bot">Olá! Como posso ajudar você hoje?</div>
            <div class="quick-options">
                <button onclick="sendQuickMessage('meu último pedido')">Meu Último Pedido</button>
                <button onclick="sendQuickMessage('minha última compra')">Minha Última Compra</button>
                <button onclick="sendQuickMessage('informações da conta')">Informações da Conta</button>
            </div>
        </div>
        <div id="chatbot-input-container">
            <input type="text" id="chatbot-input" placeholder="Escreva sua pergunta aqui..." onkeypress="checkEnter(event)">
            <button id="send-btn" onclick="sendMessage()">Enviar</button>
        </div>

        <script>
            let usuarioLogado = false; // Variável para verificar se o usuário está logado
            let step = 0; // Para controlar o passo do processo de login
            let email = ''; // Variável para armazenar o email

            function sendMessage() {
                const message = document.getElementById('chatbot-input').value.trim();

                if (!message) return; // Ignora se a mensagem estiver vazia

                addMessageToChat('user', message);
                document.getElementById('chatbot-input').value = '';

                processMessage(message);
            }

            function checkEnter(event) {
                if (event.key === 'Enter') {
                    sendMessage(); // Chama a função sendMessage se a tecla pressionada for Enter
                }
            }

            function sendQuickMessage(option) {
                addMessageToChat('user', option);
                processMessage(option); // Processa a mensagem como se fosse uma entrada do usuário
            }

            function processMessage(message) {
                console.log('Mensagem recebida:', message); // Adiciona log para depuração

                if (step === 0) {
                    const lowerMessage = message.toLowerCase(); // Para melhorar a comparação

                    if (lowerMessage.includes("meu último pedido") || lowerMessage.includes("minha última compra") || lowerMessage.includes("informações da conta")) {
                        if (!usuarioLogado) {
                            addMessageToChat('bot', "Você não está logado. Por favor, forneça seu e-mail.");
                            step = 1; // Passo para solicitar o email
                        } else {
                            fetchUserData(); // Chama a função para buscar os dados do usuário logado
                        }
                    } else {
                        addMessageToChat('bot', "Desculpe, não entendi a sua solicitação. Tente novamente.");
                    }
                } else if (step === 1) {
                    email = message; // Armazena o email
                    addMessageToChat('bot', `Seu e-mail é ${email} Agora, por favor, forneça sua senha.`);
                    step = 2; // Passo para solicitar a senha
                } else if (step === 2) {
                    const senha = message; // Captura a senha
                    loginUser(email, senha); // Tenta logar o usuário
                    step = 0; // Reseta o passo após tentar o login
                }
            }

            function loginUser(email, senha) {
                fetch('verificar_login.php', { // Substitua pelo caminho correto do seu script PHP
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(email)}&senha=${encodeURIComponent(senha)}`,
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na resposta do servidor');
                    }
                    return response.text();
                })
                .then(data => {
                    addMessageToChat('bot', data);
                    if (data.includes("Usuário encontrado")) { // Verifique a resposta que você retorna ao logar
                        usuarioLogado = true; // Atualiza o status do login
                        addMessageToChat('bot', 'Login realizado com sucesso!'); // Mensagem de sucesso
                        fetchUserData(); // Chama a função para buscar dados do usuário
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    addMessageToChat('bot', 'Desculpe, ocorreu um erro ao fazer login. Detalhes: ' + error.message);
                });
            }

            function fetchUserData() {
                // Aqui você pode implementar a lógica para buscar os dados do usuário logado
                addMessageToChat('bot', 'Aqui estão as suas informações.'); // Exemplo de resposta
            }

            function addMessageToChat(sender, message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${sender}`;
                messageDiv.innerText = message;
                document.getElementById('chatbot-messages').appendChild(messageDiv);
                // Rola para a última mensagem
                const chatMessages = document.getElementById('chatbot-messages');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        </script>
    </div>
</body>
</html>
