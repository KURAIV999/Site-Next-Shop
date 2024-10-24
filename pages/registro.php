<?php
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redireciona se o usuário já estiver logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../pages/index.php');
    exit();
}

// Inclui a conexão com o banco de dados
include '../back/conexao.php';

// Verifica se o método de requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os valores de email e senha
    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;

    // Verifica se ambos os campos estão preenchidos
    if ($email && $senha) {
        // Prepara a consulta SQL para evitar SQL Injection
        $sql = "SELECT * FROM tblregistrar WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            // Verifica se a consulta retornou algum resultado
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verifica a senha
                if ($user['senha'] === $senha) { // Considere usar password_verify()
                    // Armazena as informações do usuário na sessão
                    $_SESSION['usuario_id'] = $user['usuario_id'];
                    $_SESSION['slogin'] = $user['email'];
                    $_SESSION['snome'] = $user['nome'];
                    $_SESSION['acesso_especial'] = $user['acesso_especial'];

                    // Define o cabeçalho para JSON
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'redirect' => 'index.php']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
    }

    exit();
}

// Função para buscar produtos
function buscarProdutos($conn, $query = '') {
    if ($query) {
        $sql = "SELECT nome, preco, imagem_principal FROM tblproduto WHERE nome LIKE ? ORDER BY produto_id DESC";
        $stmt = $conn->prepare($sql);
        $searchQuery = "%" . $query . "%";
        $stmt->bind_param("s", $searchQuery);
    } else {
        $sql = "SELECT nome, preco, imagem_principal FROM tblproduto ORDER BY produto_id DESC";
        $stmt = $conn->prepare($sql);
    }

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } else {
        die('Erro na consulta: ' . mysqli_error($conn));
    }
}

// Uso da função nas páginas
$query = isset($_GET['query']) ? $_GET['query'] : '';
$result = buscarProdutos($conn, $query);
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
                <a href="login.php" style="color: #FFA500;">Faça seu Login</a>
                <hr> <!-- Separador -->

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

        <button id="theme-toggle">
            <img id="theme-icon" src="https://www.svgrepo.com/show/309493/dark-theme.svg" alt="Theme Icon" style="width: 20px; height: 20px;">
        </button>

        <div>
            <span class="menu-toggle" onclick="toggleSidebar()">☰</span>
        </div>
    </nav>
    
    <div class="register-container">
        <h2>Registro</h2>
        <form action="../back/register.php" method="POST">
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" placeholder="Nome" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
            
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" placeholder="Senha" required>
            
            <button type="submit">Registrar</button>
        </form>
        <p class="error" id="register-error-message"></p>
        <a href="login.php">Já tem uma conta? Faça login</a>
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
