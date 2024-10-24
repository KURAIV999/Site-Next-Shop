<?php
session_start(); // Inicia a sessão para acessar as variáveis de sessão

include '../back/conexao.php'; // Inclui a conexão com o banco de dados

// Verifica se o usuário está logado e recupera as informações de sessão
$usuarioLogado = isset($_SESSION['usuario_id']);
$nomeUsuario = $usuarioLogado ? $_SESSION['snome'] : '';
$acessoEspecial = $usuarioLogado && isset($_SESSION['acesso_especial']) && $_SESSION['acesso_especial'] == 1;

// Função para buscar produtos
function buscarProdutos($conn, $query = '') {
    $query = trim($query); // Remove espaços desnecessários
    // Atualizando a consulta para usar imagem_principal
    $sql = "SELECT produto_id, nome, preco, imagem_principal, descricao FROM tblproduto";

    // Adiciona a condição de busca, se houver
    if (!empty($query)) {
        $sql .= " WHERE nome LIKE '%" . mysqli_real_escape_string($conn, $query) . "%'";
    }

    $sql .= " ORDER BY produto_id DESC"; // Ordena por ID do produto em ordem decrescente
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die('Erro na consulta: ' . mysqli_error($conn)); // Mostra o erro da consulta
    }

    return $result;
}

// Obter a query de busca, se existir
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Obtenha os produtos do banco de dados usando a função
$result = buscarProdutos($conn, $query);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Produtos</title>
</head>
<body>
    <header>
        <h1>Produtos</h1>
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

    <!-- Barra de navegação -->
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

    <!-- Galeria de produtos -->
    <div class="product-gallery">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($produto = mysqli_fetch_assoc($result)): ?>
                <div class="product">
                    <!-- Atualizando a exibição para usar imagem_principal -->
                    <img src="<?php echo htmlspecialchars($produto['imagem_principal']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                    <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                    <p class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p><br>
                    
                    <?php
                    // Verifica se a descrição tem mais de 25 caracteres
                    $descricao = htmlspecialchars($produto['descricao'] ?: 'Descrição não disponível.');
                    if (strlen($descricao) > 25) {
                        // Limita a 25 caracteres e adiciona "..."
                        $descricao = substr($descricao, 0, 25) . '...';
                    }
                    ?>
                    <p class="description"><?php echo $descricao; ?></p><br>
                    
                    <a href="ver-mais.php?produto_id=<?php echo $produto['produto_id']; ?>" class="buy-button">Ver Mais</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhum produto cadastrado no momento.</p>
        <?php endif; ?>
    </div>

    <footer class="footer">
        &copy; LojasNext 2024. Todos os direitos reservados.<br>
        Formas de pagamento: Cartão de Crédito, Boleto, PayPal
    </footer>

    <script src="../script/search.js"></script>
    <script src="../script/main.js" defer></script>
    <script src="../script/script.js"></script>
</body>
</html>

<?php
// Fecha a conexão
mysqli_close($conn);
?>
