<?php 
session_start(); // Inicia a sessão para acessar as variáveis de sessão

// Inclui a conexão com o banco de dados
include '../back/conexao.php';

// Verifica se o usuário está logado
$usuarioLogado = false;
$nomeUsuario = '';
$acessoEspecial = false;

// Caso o usuário esteja logado, recupera o nome da sessão e o nível de acesso
if (isset($_SESSION['usuario_id'])) { // Use 'usuario_id' conforme o seu sistema de login
    $usuarioLogado = true;
    $nomeUsuario = $_SESSION['snome']; // Altere 'snome' para o nome correto da variável de sessão
    $acessoEspecial = isset($_SESSION['acesso_especial']) && $_SESSION['acesso_especial'] == 1;
}

// Verifica se o parâmetro 'produto_id' foi passado na URL
if (!isset($_GET['produto_id'])) {
    die('Produto não encontrado.'); // Se não houver ID, exiba a mensagem
}

$produto_id = intval($_GET['produto_id']); // Converte para inteiro para segurança

// Prepara a consulta SQL para pegar todas as informações do produto
$sql = "SELECT produto_id, nome, descricao, preco, imagem_principal, imagem_secundaria_1, imagem_secundaria_2, imagem_secundaria_3, imagem_secundaria_4, imagem_secundaria_5, imagem_secundaria_6, marca, modelo, cor, dimensoes, nome_vendedor, reputacao_vendedor, localizacao_vendedor FROM tblproduto WHERE produto_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die('Erro ao preparar a consulta: ' . $conn->error); // Mostra erro ao preparar a consulta
}

// Associa o parâmetro e executa a consulta
$stmt->bind_param("i", $produto_id);
$stmt->execute();
$result = $stmt->get_result(); // Obtém o resultado

// Verifica se o produto foi encontrado
if ($result->num_rows > 0) {
    $produto = $result->fetch_assoc(); // Pega os dados do produto
} else {
    die('Produto não encontrado.'); // Se não houver resultados
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
    <title><?php echo htmlspecialchars($produto['nome']); ?> - LojasNext</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Inclua seu CSS -->
    <link rel="stylesheet" href="../css/produtos.css"> <!-- Inclua o CSS específico para produtos -->
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

            <div class="product-detail">
                <div class="product-images">
                    <div class="product-images">
                        <!-- Exibe a imagem principal do produto -->
                        <img src="<?php echo htmlspecialchars($produto['imagem_principal']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="main-image" id="main-image">
                        <div class="image-gallery">
                            <img src="<?php echo htmlspecialchars($produto['imagem_principal']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="thumbnail">

                            <?php for ($i = 1; $i <= 6; $i++): ?>
                                <?php if (!empty($produto["imagem_secundaria_$i"])): ?>
                                    <img src="<?php echo htmlspecialchars($produto["imagem_secundaria_$i"]); ?>" alt="Imagem Secundária <?php echo $i; ?>" class="thumbnail">
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="product-info">
                        <h2><?php echo htmlspecialchars($produto['nome']); ?></h2>
                        <p class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>

                        <div class="product-specifications">
                            <h3>Especificações</h3>
                            <ul>
                                <li><strong>Marca:</strong> <?php echo htmlspecialchars($produto['marca']); ?></li>
                                <li><strong>Modelo:</strong> <?php echo htmlspecialchars($produto['modelo']); ?></li>
                                <li><strong>Cor:</strong> <?php echo htmlspecialchars($produto['cor']); ?></li>
                                <li><strong>Dimensões:</strong> <?php echo htmlspecialchars($produto['dimensoes']); ?></li>
                            </ul>
                        </div>

                        <div class="product-seller">
                            <h3>Informações do Vendedor</h3>
                            <p><strong>Nome:</strong> <?php echo htmlspecialchars($produto['nome_vendedor']); ?></p>
                            <p><strong>Reputação:</strong> <?php echo htmlspecialchars($produto['reputacao_vendedor']); ?></p>
                            <p><strong>Localização:</strong> <?php echo htmlspecialchars($produto['localizacao_vendedor']); ?></p>
                        </div>

                        <form action="adicionar_carrinho.php" method="POST">
                        <input type="hidden" name="produto_id" value="<?php echo $produto['produto_id']; ?>">
                        <input type="hidden" name="nome" value="<?php echo $produto['nome']; ?>">
                        <input type="hidden" name="preco" value="<?php echo $produto['preco']; ?>">
                        <input type="hidden" name="imagem" value="<?php echo $produto['imagem_principal']; ?>"> <!-- Alterado para imagem_principal -->
                        <label for="quantidade">Quantidade:</label>
                        <input type="number" id="quantidade" name="quantidade" value="1" min="1"><br><br>
                        <button class="buy-button" type="submit">Comprar</button>
                        </form>
                        </div>
                        </div>
                        </div>

    <footer class="footer">
        &copy; LojasNext 2024. Todos os direitos reservados.<br>
        Formas de pagamento: Cartão de Crédito, Boleto, PayPal
    </footer>

    <!-- Scripts -->
    <script src="../script/main.js" defer></script>
    <script src="../script/script.js"></script>
    <script src="../script/search.js"></script>
    <script>
        // Função para trocar a imagem principal ao clicar nas miniaturas
        document.querySelectorAll('.thumbnail').forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                document.getElementById('main-image').src = this.src;
            });
        });

        // Função para adicionar produto ao carrinho
        function addToCart(nome, preco, imagem, quantidade) {
            // Aqui você pode implementar a lógica para adicionar o produto ao carrinho
            alert(`Adicionado ao carrinho: ${quantidade}x ${nome} por R$ ${preco}`);
            // Adicionar lógica para persistir no banco de dados ou na sessão
        }
    </script>
</body>
</html>
