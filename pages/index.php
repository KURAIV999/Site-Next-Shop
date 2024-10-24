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
$query = "SELECT produto_id, nome, preco, imagem_principal FROM tblproduto ORDER BY produto_id DESC LIMIT 4";
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
</head>
<body>
    <header>
    <html>
<head>
            <!-- Importando Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Importando Font Awesome para ícones -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

        <!-- Fonte Roboto do Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
                
        <div class="flex items-center justify-center mb-4 relative">
        <i class="absolute bottom-0 left-1/2 -translate-x-1/2">
        <svg width="30" height="50" viewBox="1 4 20 15" fill="#104E8B" xmlns="http://www.w3.org/2000/svg" transform="rotate(15 -15 12) scale(3.5)">
            <path d="M13 2L3 14H10L7 22L17 10H10L13 2Z" />
        </svg>
        </i>
        <span class="text-5xl font-bold text-white relative z-10 mr-2.5" style="font-family: 'Roboto', sans-serif;">NEXT</span>
        <span class="text-5xl font-bold text-white relative z-10 ml-2.5" style="font-family: 'Roboto', sans-serif;">SHOP</span>
    </div>

    </header>
        <!-- MENU LATERAL -->
        <div id="overlay" class="overlay"></div>
            <aside id="sidebar" class="sidebar">
                    <button class="closebtn" onclick="closeSidebar()">×</button>
                    <div class="menu-content">
                        <?php if ($usuarioLogado): ?>
                        <!-- Adicionando a imagem do usuário e a palavra 'Perfil' -->
                        <div class="perfil-container">
                            <img src="https://img2.gratispng.com/20180802/xaw/b93ece3bcef87fe775756ac6b9171d6f.webp" alt="Foto do Usuário" class="perfil-foto">
                            <a href="perfil.php" class="perfil-texto">Perfil</a>
                        </div>
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
        
        <div>
        <button id="theme-toggle">
            <img id="theme-icon" src="https://www.svgrepo.com/show/309493/dark-theme.svg" alt="Theme Icon" style="width: 20px; height: 20px;">
        </button>
    </div>
    <div>
        <span class="menu-toggle" onclick="toggleSidebar()">☰</span>
    </div>
    </nav>

            <div class="carousel">
            <img src="https://image.freepik.com/vetores-gratis/banner-de-onda-azul-fluindo-moderno-sobre-fundo-branco_1035-18545.jpg" alt="Banner de boas-vindas">
            <div class="carousel-caption">
                <h3 class="font-bold text-white" style="font-family: 'Roboto', sans-serif;">
                    BEM-VINDO À
                    <span class="font-bold text-white relative z-10 ml-1" style="font-family: 'Roboto', sans-serif;">NEXT</span>
                    <i class="inline-block relative">
                        <svg width="25" height="25" viewBox="1 4 20 15" fill="#0011ff" xmlns="http://www.w3.org/2000/svg" transform="rotate(22 -15.5 12) scale(1.1)"> 
                            <path d="M13 2L3 14H10L7 22L17 10H10L13 2Z" />
                        </svg>
                    </i> 
                    <span class="font-bold text-white relative z-10 mr-1" style="font-family: 'Roboto', sans-serif;">SHOP</span>
                </h3>
                <p>ONDE O FUTURO DAS COMPRAS COMEÇA!</p>
            </div>
        </div>


    <div class="section">
    <h2>Produtos em Destaque</h2>
        <div class="product-gallery">
            <?php
            if (mysqli_num_rows($result) > 0) {
                // Exibe cada produto
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="product">';
                    echo '<a href="ver-mais.php?produto_id=' . $row['produto_id'] . '">';
                    echo '<img src="' . $row['imagem_principal'] . '" alt="' . $row['nome'] . '">';
                    echo '<h3>' . $row['nome'] . '</h3>';
                    echo '<p>R$ ' . number_format($row['preco'], 2, ',', '.') . '</p>';
                    echo '</a>'; // Fecha o link aqui
                    echo '</div>';
                }
            } else {
                echo '<p>Nenhum produto encontrado.</p>';
            }
            ?>
        </div>
    </div>

    <div class="section">
    <h2>Categorias Populares</h2>
    <div class="category-gallery">
        <div class="category">
            <img src="https://ibpt.com.br/wp-content/uploads/2020/07/2354873-1024x683.jpg" alt="Eletrônicos">
            <h3>Eletrônicos</h3>
        </div>
        <div class="category">
            <img src="https://img.freepik.com/fotos-gratis/jovem-bonito-escolhendo-sapatos-em-uma-loja_1303-19708.jpg" alt="Roupas">
            <h3>Roupas</h3>
        </div>
        <div class="category">
            <img src="https://judicearaujo.com.br/blog/wp-content/uploads/2023/10/stylish-composition-modern-living-room-interior-with-structure-painting-lot-cacti-plants-armchair-wooden-shelves-accessories-creative-wall-carpet-floor-template.jpg" alt="Casa">
            <h3>Casa</h3>
        </div>
        <div class="category">
            <img src="https://sqquimica.com/wp-content/uploads/2023/03/lay-out-huge-decorative-cosmetic-1.jpg" alt="Beleza">
            <h3>Beleza</h3>
        </div>
    </div>
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
