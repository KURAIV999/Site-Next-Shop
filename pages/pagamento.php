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
}

// Buscar produtos
$query = "SELECT nome, preco, imagem_principal FROM tblproduto ORDER BY produto_id DESC LIMIT 4"; // Ajuste o LIMIT conforme necessário
$result = mysqli_query($conn, $query);

// Verifica se a consulta foi bem-sucedida
if (!$result) {
    die("Erro na consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/pagamento.css">
</head>
<body>
    <header>
        <h1>Opções de Pagamento - LojasNext</h1>
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

    <div class="payment-container">
    <h2>Escolha uma forma de pagamento</h2>
    <div class="payment-methods">
        <div onclick="window.location.href='finalizar_compra.php';" class="payment-method">
            <img src="https://i.pinimg.com/originals/56/65/ac/5665acfeb0668fe3ffdeb3168d3b38a4.png" alt="MasterCard">
            <p>MasterCard</p>
        </div>
        <div onclick="window.location.href='finalizar_compra.php';" class="payment-method">
            <img src="https://www.visa.com.br/dam/VCOM/regional/lac/brazil/newsroom/visa-logo-800x450.jpeg" alt="Visa">
            <p>Visa</p>
        </div>
        <div onclick="window.location.href='finalizar_compra.php';" class="payment-method">
            <img src="https://newsroom.br.paypal-corp.com/image/pp_h_rgb_logo_tn.jpg" alt="PayPal">
            <p>PayPal</p>
        </div>
        <div onclick="window.location.href='finalizar_compra.php';" class="payment-method">
            <img src="https://lh3.googleusercontent.com/IgqiYqtowoVwAsoBQ9WEfG8HOBF75kbpyaE2CEdSZZsrSZDMjvzPk0uJb79ERq5_2co" alt="Boleto">
            <p>Boleto</p>
        </div>
        <div onclick="window.location.href='finalizar_compra.php';" class="payment-method">
            <img src="https://geradornv.com.br/wp-content/themes/v1.34.2/assets/images/logos/pix/logo-pix-1024x1024.png" alt="Pix">
            <p>Pix</p>
        </div>
    </div>
    <p>Para mais informações sobre o processo de pagamento, entre em contato com nosso suporte.</p>
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
