<?php  
session_start(); // Inicia a sessão

// Inicializa as variáveis
$usuarioLogado = false; // Assume que o usuário não está logado
$acessoEspecial = false; // Assume que o usuário não tem acesso especial

// Verifica se o usuário está logado
if (isset($_SESSION['usuario_id'])) {
    $usuarioLogado = true; // Usuário está logado
    $nomeUsuario = $_SESSION['snome']; // Recupera o nome do usuário logado
    
    // Verifica se o usuário tem acesso especial
    if (isset($_SESSION['acesso_especial']) && $_SESSION['acesso_especial']) {
        $acessoEspecial = true; // Usuário tem acesso especial
    }
} else {
    header("Location: login.php"); // Redireciona para a página de login se não estiver logado
    exit(); // Encerra a execução do script
}

// Conectar ao banco de dados
include('../back/conexao.php');

// Verifica se a conexão foi estabelecida corretamente
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Recupera os itens do carrinho do usuário, agrupando produtos iguais
$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT nome, preco, SUM(quantidade) AS quantidade, produto_id
        FROM tblcarrinho 
        WHERE usuario_id = ? 
        GROUP BY produto_id";

$stmt = $conn->prepare($sql);

// Verifica se a consulta foi preparada corretamente
if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$carrinho = [];
$total = 0;

// Verifica se há produtos no carrinho
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $carrinho[] = $row;
        $total += $row['preco'] * $row['quantidade']; // Calcula o total
    }
} else {
    $carrinho = []; // Se não houver produtos, o carrinho fica vazio
}

// Fecha a consulta e a conexão
$stmt->close();
$conn->close();

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - LojasNext</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Inclua seu CSS -->
</head>
<body>
    <header>
        <h1>Carrinho - LojasNext</h1>
    </header>

    <!-- Menu Lateral -->
    <div id="overlay" class="overlay"></div>
    <aside id="sidebar" class="sidebar">
        <button class="closebtn" onclick="closeSidebar()">×</button>
        <div class="menu-content">
            <?php if ($usuarioLogado): ?>
                <a style="color: #FFFFFF;">Bem-vindo <strong class="user-name" style="color: <?php echo $acessoEspecial ? '#228B22' : '#FFFFFF'; ?>;"><?php echo htmlspecialchars($nomeUsuario); ?></strong></a>
                <hr>
            <?php else: ?>
                <a href="login.php" style="color: #FFA500;">Faça seu Login</a>
                <hr>
            <?php endif; ?>
            <a href="index.php">Início</a>
            <hr>
            <a href="produtos.php">Produtos</a>
            <hr>
            <a href="carrinho.php">Carrinho</a>
            <hr>
            <a href="historico.php">Histórico</a>
            <hr>
            <?php if ($acessoEspecial): ?>
                <h2 class="menu-especial">Menu Especial</h2>
                <a href="cadastrar-produto.php" style="color: #228B22;">Cadastrar Produto</a>
                <hr>
                <a href="pesquisa-produto.php" style="color: #228B22;">Pesquisar Produto</a>
                <hr>
                <a href="pesquisa-produto.php" style="color: #228B22;">Editar Produto</a>
                <hr>
            <?php endif; ?>
        </div>
        <a href="../back/logout.php" style="color: #8B0000; margin-top: auto;">Sair</a>
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

    <div class="cart-item">
    <h2>Produtos no seu Carrinho</h2>
    <?php if (!empty($carrinho)): ?>
        <table>
            <tr>
                <th>Nome</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th>Total</th>
                <th>Ações</th> <!-- Adicione uma coluna para ações -->
            </tr>
            <?php foreach ($carrinho as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nome']); ?></td>
                    <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($item['quantidade']); ?></td>
                    <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                    <td>
                    <form action="remover_carrinho.php" method="POST">
                        <input type="hidden" name="produto_id" value="<?php echo $item['produto_id']; ?>"> <!-- ID do produto -->
                        <button type="submit" class="remove-button">Remover</button>
                    </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Total:</strong></td>
                <td><strong>R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></td>
            </tr>
        </table>
        <form action="pagamento.php" method="POST">
            <button type="submit" class="checkout-button">Finalizar Compra</button>
        </form>
    <?php else: ?>
        <p class="empty-cart-message" style="text-align: center;">Seu carrinho está vazio.</p>
    <?php endif; ?>
</div>


    <!-- Rodapé -->
    <footer class="footer">
        &copy; LojasNext 2024. Todos os direitos reservados.<br>
        Formas de pagamento: Cartão de Crédito, Boleto, PayPal
    </footer>

    <!-- Scripts -->
    <script src="../script/main.js" defer></script>
    <script src="../script/script.js"></script>
    <script src="../script/search.js"></script>
</body>
</html>
