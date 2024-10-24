<?php
session_start(); // Inicia a sessão para acessar as variáveis de sessão

// Inclui a conexão com o banco de dados
include '../back/conexao.php';

// Verifica se o usuário está logado
$usuarioLogado = false;
$nomeUsuario = '';
$acessoEspecial = false;

// Caso o usuário esteja logado, recupera o nome da sessão e o nível de acesso
if (isset($_SESSION['usuario_id'])) {
    $usuarioLogado = true;
    $nomeUsuario = $_SESSION['snome'];
    $acessoEspecial = isset($_SESSION['acesso_especial']) && $_SESSION['acesso_especial'] == 1;
} else {
    // O usuário não está logado, redirecionar ou exibir uma mensagem
    echo "Você precisa estar logado para acessar esta página.";
    exit; // Interrompe a execução do script
}

// Buscar produtos
$query = "SELECT nome, preco, imagem_principal FROM tblproduto ORDER BY produto_id DESC LIMIT 4"; // Ajuste o LIMIT conforme necessário
$result = mysqli_query($conn, $query);

// Verifica se a consulta foi bem-sucedida
if (!$result) {
    die("Erro na consulta: " . mysqli_error($conn));
}

// Função para buscar produtos
function buscarProdutos($conn, $query = '') {
    if ($query) {
        $sql = "SELECT nome, preco, imagem_principal FROM tblproduto WHERE nome LIKE '%" . mysqli_real_escape_string($conn, $query) . "%' ORDER BY produto_id DESC";
    } else {
        $sql = "SELECT nome, preco, imagem_principal FROM tblproduto ORDER BY produto_id DESC";
    }

    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        die('Erro na consulta: ' . mysqli_error($conn)); // Mostra o erro da consulta
    }

    return $result;
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
    <link rel="stylesheet" href="../css/dados.css">
</head>
<body>

        <header>
            <h1>Pagamento - LojasNext</h1>
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

    <div class="payment-form">
    <h2>Complete suas informações</h2><br>
    <form action="../back/cartao_dados.php" method="post">
        <input type="hidden" name="nome" value="<?php echo htmlspecialchars($nome); ?>">
        <input type="hidden" name="preco" value="<?php echo htmlspecialchars($preco); ?>">
        <input type="hidden" name="total" value="<?php echo htmlspecialchars($total); ?>">

        <label for="nome">Nome Completo:</label>
        <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="exemplo@gmail.com" required><br><br>

        <label for="endereco">Endereço:</label>
        <input type="text" id="endereco" name="endereco" placeholder="Digite seu endereço" required><br><br>

        <label for="cartao">Número do Cartão:</label>
        <input type="number" id="cartao" name="cartao" placeholder="Número do cartão" required><br><br>

        <label for="validade">Validade (MM/AAAA):</label>
        <input type="text" id="validade" name="validade" placeholder="MM/AAAA" required><br><br>

        <label for="cvv">CVV:</label>
        <input type="number" id="cvv" name="cvv" placeholder="Código CVV" required><br><br>

        <!-- Novo contêiner para centralizar o botão -->
        <div class="button-container">
            <input class='buy-button' type="submit" value="Finalizar Compra">
        </div>
    </form>
</div>


    <footer>
        &copy; LojasNext 2024. Todos os direitos reservados.<br>
        Formas de pagamento: Cartão de Crédito, Boleto, PayPal.<br>
        Contato: LojasNextoficial@gmail.com.br
    </footer>
    
    <script src="../script/main.js" defer></script>
    <script src="../script/script.js"></script>
    <script src="../script/search.js"></script>
</body>
</html>
