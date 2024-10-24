<?php 
session_start(); // Inicia a sessão

// Verifica se o usuário está logado e se possui acesso especial
$usuarioLogado = isset($_SESSION['usuario_id']); // Define se o usuário está logado
$acessoEspecial = isset($_SESSION['acesso_especial']) && $_SESSION['acesso_especial'] == 1; // Define se o usuário tem acesso especial

if (!$usuarioLogado || !$acessoEspecial) {
    header("Location: login.php"); // Redireciona para a página de login se o usuário não tiver acesso especial
    exit(); // Encerra a execução do script
}

// Conectar ao banco de dados
include('../back/conexao.php');

// Obtém o nome do usuário da sessão
$nomeUsuario = isset($_SESSION['snome']) ? $_SESSION['snome'] : 'Usuário'; // Alterado para usar $_SESSION['snome']

// Verifica se os dados do formulário foram enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
    $preco = mysqli_real_escape_string($conn, $_POST['preco']);
    $imagem_principal = mysqli_real_escape_string($conn, $_POST['imagem_principal']);
    $imagem_secundaria_1 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_1']);
    $imagem_secundaria_2 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_2']);
    $imagem_secundaria_3 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_3']);
    $imagem_secundaria_4 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_4']);
    $imagem_secundaria_5 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_5']);
    $imagem_secundaria_6 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_6']);
    $marca = mysqli_real_escape_string($conn, $_POST['marca']);
    $modelo = mysqli_real_escape_string($conn, $_POST['modelo']);
    $cor = mysqli_real_escape_string($conn, $_POST['cor']);
    $dimensoes = mysqli_real_escape_string($conn, $_POST['dimensoes']);
    $nome_vendedor = mysqli_real_escape_string($conn, $_POST['nome_vendedor']);
    $reputacao_vendedor = mysqli_real_escape_string($conn, $_POST['reputacao_vendedor']);
    $localizacao_vendedor = mysqli_real_escape_string($conn, $_POST['localizacao_vendedor']);

    // Inserir o produto no banco de dados com novas informações
    $sql = "INSERT INTO tblproduto (nome, descricao, preco, imagem_principal, imagem_secundaria_1, imagem_secundaria_2, imagem_secundaria_3, imagem_secundaria_4, imagem_secundaria_5, imagem_secundaria_6, marca, modelo, cor, dimensoes, nome_vendedor, reputacao_vendedor, localizacao_vendedor) 
VALUES ('$nome', '$descricao', '$preco', '$imagem_principal', '$imagem_secundaria_1', '$imagem_secundaria_2', '$imagem_secundaria_3', '$imagem_secundaria_4', '$imagem_secundaria_5', '$imagem_secundaria_6', '$marca', '$modelo', '$cor', '$dimensoes', '$nome_vendedor', '$reputacao_vendedor', '$localizacao_vendedor')";

    
    if (mysqli_query($conn, $sql)) {
        header("Location: produtos.php?success=Produto cadastrado com sucesso!"); // Redireciona após o cadastro
        exit();
    } else {
        echo "Erro ao cadastrar produto: " . mysqli_error($conn);
    }
}

// Fechar a conexão com o banco de dados
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/pg-cadastro.css">
    <title>Cadastrar Produto</title>
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
                <a href="editar-produto.php" style="color: #228B22;">Editar Produto</a>
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

                <!-- Conteúdo Principal -->
            <div class="form-container">
                <h2>Cadastrar Produto</h2>
                <form action="cadastrar-produto.php" method="POST">
                    
                    <!-- Grupo de Inputs do Produto -->
                    <div class="input-group">
                    <label for="nome">Nome do Produto:</label>
                    <input type="text" id="nome" name="nome" required><br><br>
                    
                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" required></textarea><br><br>
                    
                    <label for="preco">Preço:</label>
                    <input type="text" id="preco" name="preco" required><br><br>
                    
                    <label for="imagem_principal">URL da Imagem Principal:</label>
                    <input type="text" id="imagem_principal" name="imagem_principal" required><br><br>

                    <label for="imagem2">URL da Imagem 1</label>
                    <input type="text" id="imagem_secundaria_1" name="imagem_secundaria_1"><br><br>

                    <label for="imagem3">URL da Imagem 2</label>
                    <input type="text" id="imagem_secundaria_2" name="imagem_secundaria_2"><br><br>

                    <label for="imagem3">URL da Imagem 3</label>
                    <input type="text" id="imagem_secundaria_3" name="imagem_secundaria_3"><br><br>

                    <label for="imagem3">URL da Imagem 4</label>
                    <input type="text" id="imagem_secundaria_4" name="imagem_secundaria_4"><br><br>

                    <label for="imagem3">URL da Imagem 5</label>
                    <input type="text" id="imagem_secundaria_5" name="imagem_secundaria_5"><br><br>

                    <label for="imagem3">URL da Imagem 6</label>
                    <input type="text" id="imagem_secundaria_6" name="imagem_secundaria_6"><br><br>

                    <!-- Campos para Especificações -->
                    <div class="form-section">
                    <h3>Especificações do Produto</h3>
                    <div class="input-group">
                        <label for="marca">Marca:</label>
                        <input type="text" id="marca" name="marca" required>
                    </div>

                    <div class="input-group">
                        <label for="modelo">Modelo:</label>
                        <input type="text" id="modelo" name="modelo" required>
                    </div>

                    <div class="input-group">
                        <label for="cor">Cor:</label>
                        <input type="text" id="cor" name="cor" required>
                    </div>

                    <div class="input-group">
                        <label for="dimensoes">Dimensões:</label>
                        <input type="text" id="dimensoes" name="dimensoes" required>
                    </div>

                    <!-- Campos para Informações do Vendedor -->
                    <h3>Informações do Vendedor</h3>
                    <div class="input-group">
                        <label for="nome_vendedor">Nome do Vendedor:</label>
                        <input type="text" id="nome_vendedor" name="nome_vendedor" required>
                    </div>

                    <div class="input-group">
                        <label for="reputacao_vendedor">Reputação do Vendedor:</label>
                        <input type="text" id="reputacao_vendedor" name="reputacao_vendedor" required>
                    </div>

                    <div class="input-group">
                        <label for="localizacao_vendedor">Localização do Vendedor:</label>
                        <input type="text" id="localizacao_vendedor" name="localizacao_vendedor" required>
                    </div>

                    <!-- Div para centralizar o botão -->
                    <div style="text-align: center;">
                        <button type="submit">Cadastrar Produto</button>
                    </div>
                </form>
            </div>
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