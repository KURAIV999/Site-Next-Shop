<?php  
session_start(); // Inicia a sessão para acessar as variáveis de sessão

// Verifica se o usuário está logado
$usuarioLogado = false;
$nomeUsuario = '';
$acessoEspecial = false; // Adicione esta variável para verificar o acesso especial

// Caso o usuário esteja logado, recupera o nome da sessão e o nível de acesso
if (isset($_SESSION['usuario_id'])) {
    $usuarioLogado = true;
    $nomeUsuario = $_SESSION['snome'] ?? ''; // Certifique-se de que 'snome' está sendo definido
    $acessoEspecial = isset($_SESSION['acesso_especial']) && $_SESSION['acesso_especial'] == 1; // Verifica se o usuário tem acesso especial
}

// Conexão com o banco de dados (certifique-se de que o arquivo conexao.php esteja correto)
include '../back/conexao.php'; // Verifique o caminho do seu arquivo de conexão

// Função para buscar histórico de compras
function buscarHistoricoCompras($conn, $usuarioId) {
    // Consulta para buscar o histórico de compras, incluindo o preço do produto
    $sql = "
        SELECT pedido_id, data, produto, preco 
        FROM tblhistorico 
        WHERE usuario_id = " . intval($usuarioId) . " 
        ORDER BY data DESC";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        // Exibe a mensagem de erro se a consulta falhar
        die("Erro na consulta: " . mysqli_error($conn));
    }
    return $result;
}

// Uso da função para buscar histórico
if ($usuarioLogado) {
    $historicoResult = buscarHistoricoCompras($conn, $_SESSION['usuario_id']);
} else {
     // Redirecionar para login.php se o usuário não estiver logado
     header("Location: login.php");
     exit; // Encerra a execução do script
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/historico.css">
    <title>Histórico de Compras</title>
</head>
<body>
    <header>
        <h1>LojasNext</h1>
    </header>

    <!-- Menu Lateral -->
    <div id="overlay" class="overlay"></div>
            <aside id="sidebar" class="sidebar">
                <button class="closebtn" onclick="closeSidebar()">×</button>
                <div class="menu-content">
                    <?php if ($usuarioLogado): ?>
                        <a style="color: #FFFFFF;">Bem-vindo <strong class="user-name" style="color: <?php echo $acessoEspecial ? '#228B22' : '#FFFFFF'; ?>;"><?php echo $nomeUsuario; ?></strong></a>
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

        <button id="theme-toggle">
            <img id="theme-icon" src="https://www.svgrepo.com/show/309493/dark-theme.svg" alt="Theme Icon" style="width: 20px; height: 20px;">
        </button>

        <div>
            <span class="menu-toggle" onclick="toggleSidebar()">☰</span>
        </div>
    </nav>

    <div class="content">
        <h2>Histórico de Compras</h2><br>

        <?php if (mysqli_num_rows($historicoResult) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($historicoResult)): ?>
                <div class="order">
                    <h3>Pedido #<?php echo $row['pedido_id']; ?></h3>
                    <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($row['data'])); ?></p>
                    <p><strong>Produto:</strong> <?php echo $row['produto']; ?></p>
                    <p><strong>Preço:</strong> R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></p> <!-- Exibe o preço do produto -->
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhum pedido encontrado.</p>
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
