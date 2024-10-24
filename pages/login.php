<?php
// Inicia a sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../pages/index.php'); // Redireciona o usuário logado
    exit(); // Encerra a execução após o redirecionamento
}

// Inclui a conexão com o banco de dados
include '../back/conexao.php';

// Inicializa uma variável para armazenar mensagens de erro
$errorMessage = '';

// Verifica se o método de requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os valores de email e senha com filtragem
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

    // Verifica se ambos os campos estão preenchidos
    if (!empty($email) && !empty($senha)) {
        // Prepara a consulta SQL para evitar SQL Injection
        $sql = "SELECT * FROM tblregistrar WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email); // 's' indica que o parâmetro é uma string
            $stmt->execute();
            $result = $stmt->get_result();

            // Verifica se a consulta retornou algum resultado
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verifica a senha diretamente (sem hash)
                if ($senha === $user['senha']) { // Comparação direta
                    // Armazena as informações do usuário na sessão
                    $_SESSION['usuario_id'] = $user['usuario_id'];
                    $_SESSION['slogin'] = $user['email'];
                    $_SESSION['snome'] = $user['nome'];
                    $_SESSION['acesso_especial'] = $user['acesso_especial'];

                    // Redireciona conforme o nível de acesso
                    header('Location: ../pages/index.php');
                    exit();
                } else {
                    // Senha incorreta
                    $errorMessage = 'Email ou senha incorretos.';
                }
            } else {
                // Caso não tenha encontrado o usuário com as credenciais fornecidas
                $errorMessage = 'Email ou senha incorretos.';
            }
        } else {
            // Caso haja um erro ao preparar a consulta
            $errorMessage = 'Erro ao preparar a consulta: ' . $conn->error;
        }
    } else {
        // Caso um ou ambos os campos estejam vazios
        $errorMessage = 'Preencha todos os campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>LojasNext</h1>
    </header>

    <!-- Overlay -->
    <div id="overlay" class="overlay" onclick="closeSidebar()"></div>

    <!-- Menu Lateral -->
    <aside id="sidebar" class="sidebar">
        <button class="closebtn" onclick="closeSidebar()">×</button>

        <div class="menu-content">
            <a href="index.php">Início</a>
            <hr> <!-- Separador -->

            <a href="produtos.php">Produtos</a>
            <hr> <!-- Separador -->

            <a href="carrinho.php">Carrinho</a>
            <hr> <!-- Separador -->

            <a href="historico.php">Histórico</a>
            <hr> <!-- Separador -->
        </div>
    </aside>

    <nav>
        <a href="index.php">Início</a>
        <a href="produtos.php">Produtos</a>
        <a href="carrinho.php">Carrinho</a>
        <a href="historico.php">Histórico</a>
        <a href="login.php">Login</a>
        
        <!-- Barra de Pesquisa -->
        <div class="search-container">
            <form action="produtos.php" method="GET">
                <input type="text" name="query" id="search-input" placeholder="Buscar produtos..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                <button type="submit">
                    <!-- Utilizando a imagem como ícone do botão -->
                    <img src="https://www.townofbethlehem.org/ImageRepository/Document?documentID=3114" alt="Buscar" style="width: 20px; height: 20px;">
                </button>
            </form>
        </div>
        
        <div>
            <button id="theme-toggle">
                <img id="theme-icon" src="https://www.svgrepo.com/show/309493/dark-theme.svg" alt="Theme Icon" style="width: 20px; height: 20px;">
            </button>
        </div>
        <div>
            <span class="menu-toggle" onclick="toggleSidebar()">☰</span>
        </div>
    </nav>

    <div class="login-container" style="text-align: center;">
        <h2>Login</h2>
        <form method="POST">
            <div>
                <label for="login-email">Email:</label>
                <input type="email" id="login-email" name="email" placeholder="Email" required>
            </div>
            <div>
                <label for="login-password">Senha:</label>
                <input type="password" id="login-password" name="senha" placeholder="Senha" required>
            </div>
            <button type="submit">Entrar</button>
            <!-- Mensagem de erro exibida na interface -->
            <?php if (!empty($errorMessage)): ?>
                <p class="error" style="color: red;"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
            <div>
                <a href="registro.php">Ainda não tem uma conta? Registre-se</a>
            </div>
        </form>
    </div>

    <footer class="footer">
        &copy; LojasNext 2024. Todos os direitos reservados.<br>
        Formas de pagamento: Cartão de Crédito, Boleto, PayPal
    </footer>
    <script src="../script/main.js" defer></script>
    <script src="../script/script.js"></script>
    <script src="../script/search.js"></script>
</body>
</html>
