<?php
session_start(); // Inicia a sessão para acessar as variáveis de sessão

// Verifica se o usuário está logado
$usuarioLogado = false;
$nomeUsuario = '';
$acessoEspecial = false; // Adiciona variável para verificar o acesso especial

// Caso o usuário esteja logado, recupera o nome da sessão e o nível de acesso
if (isset($_SESSION['usuario_id'])) {
    $usuarioLogado = true;
    $nomeUsuario = $_SESSION['snome'];
    $acessoEspecial = isset($_SESSION['acesso_especial']) && $_SESSION['acesso_especial'] == 1; // Verifica se o usuário tem acesso especial
}

// Se o usuário não estiver logado ou não tiver acesso especial, redireciona para a página de login
if (!$usuarioLogado || !$acessoEspecial) {
    header("Location: login.php"); // Redireciona para a página de login
    exit(); // Encerra a execução do script
}

// Conectar ao banco de dados
include('../back/conexao.php'); 

// Verificar se o ID do produto foi passado
if (isset($_POST['produto_id'])) {
    $produto_id = intval($_POST['produto_id']); // Garantir que é um inteiro

    // Consulta para buscar as informações do produto
    $sql = "SELECT * FROM tblproduto WHERE produto_id = $produto_id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $produto = mysqli_fetch_assoc($result);
    } else {
        echo "Produto não encontrado.";
        exit;
    }
} else {
    echo "ID do produto não especificado.";
    exit;
}

// Verifique se o formulário foi enviado para salvar as alterações
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['salvar'])) {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
    $preco = floatval($_POST['preco']);
    $imagem_principal = mysqli_real_escape_string($conn, $_POST['imagem_principal']);
    $imagem_secundaria_1 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_1']);
    $imagem_secundaria_2 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_2']);
    $imagem_secundaria_3 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_3']);
    $imagem_secundaria_4 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_4']);
    $imagem_secundaria_5 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_5']);
    $imagem_secundaria_6 = mysqli_real_escape_string($conn, $_POST['imagem_secundaria_6']);
    
    // Novos campos de especificações e informações do vendedor
    $marca = mysqli_real_escape_string($conn, $_POST['marca']);
    $modelo = mysqli_real_escape_string($conn, $_POST['modelo']);
    $cor = mysqli_real_escape_string($conn, $_POST['cor']);
    $dimensoes = mysqli_real_escape_string($conn, $_POST['dimensoes']);
    $nome_vendedor = mysqli_real_escape_string($conn, $_POST['nome_vendedor']);
    $reputacao_vendedor = mysqli_real_escape_string($conn, $_POST['reputacao_vendedor']);
    $localizacao_vendedor = mysqli_real_escape_string($conn, $_POST['localizacao_vendedor']);

    // Atualiza o produto no banco de dados
    $sql_update = "UPDATE tblproduto SET 
        nome='$nome', 
        descricao='$descricao', 
        preco='$preco', 
        imagem_principal='$imagem_principal', 
        imagem_secundaria_1='$imagem_secundaria_1', 
        imagem_secundaria_2='$imagem_secundaria_2', 
        imagem_secundaria_3='$imagem_secundaria_3', 
        imagem_secundaria_4='$imagem_secundaria_4', 
        imagem_secundaria_5='$imagem_secundaria_5', 
        imagem_secundaria_6='$imagem_secundaria_6', 
        marca='$marca', 
        modelo='$modelo', 
        cor='$cor', 
        dimensoes='$dimensoes', 
        nome_vendedor='$nome_vendedor', 
        reputacao_vendedor='$reputacao_vendedor', 
        localizacao_vendedor='$localizacao_vendedor' 
        WHERE produto_id=$produto_id"; 

    if (mysqli_query($conn, $sql_update)) {
        $_SESSION['message'] = "Produto atualizado com sucesso!";
        header("Location: pesquisa-produto.php"); // Redireciona para a página de produtos
        exit();
    } else {
        echo "Erro ao atualizar o produto: " . mysqli_error($conn);
    }
}

// Buscar produtos
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

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/edt-pdt.css">
    <title>Editar Produto</title>
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
        
        <div>
            <button id="theme-toggle">
                <img id="theme-icon" src="https://www.svgrepo.com/show/309493/dark-theme.svg" alt="Theme Icon" style="width: 20px; height: 20px;">
            </button>
        </div>
        <div>
            <span class="menu-toggle" onclick="toggleSidebar()">☰</span>
        </div>
    </nav>

    <div class="product-management-container">
        <h1>Editar Produto</h1>
        <form method="POST" action="editar-produto.php">
            <input type="hidden" name="produto_id" value="<?php echo $produto['produto_id']; ?>">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
            <label>Descrição:</label>
            <textarea name="descricao" required><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
            <label>Preço:</label>
            <input type="number" step="0.01" name="preco" value="<?php echo htmlspecialchars($produto['preco']); ?>" required>
            <label>Imagem Principal:</label>
            <input type="text" name="imagem_principal" value="<?php echo htmlspecialchars($produto['imagem_principal']); ?>" required>
            <label>Imagem Secundária 1:</label>
            <input type="text" name="imagem_secundaria_1" value="<?php echo htmlspecialchars($produto['imagem_secundaria_1']); ?>" required>
            <label>Imagem Secundária 2:</label>
            <input type="text" name="imagem_secundaria_2" value="<?php echo htmlspecialchars($produto['imagem_secundaria_2']); ?>" required>
            <label>Imagem Secundária 3:</label>
            <input type="text" name="imagem_secundaria_3" value="<?php echo htmlspecialchars($produto['imagem_secundaria_3']); ?>" required>
            <label>Imagem Secundária 4:</label>
            <input type="text" name="imagem_secundaria_4" value="<?php echo htmlspecialchars($produto['imagem_secundaria_4']); ?>" required>
            <label>Imagem Secundária 5:</label>
            <input type="text" name="imagem_secundaria_5" value="<?php echo htmlspecialchars($produto['imagem_secundaria_5']); ?>" required>
            <label>Imagem Secundária 6:</label>
            <input type="text" name="imagem_secundaria_6" value="<?php echo htmlspecialchars($produto['imagem_secundaria_6']); ?>" required>

            <!-- Campos adicionais -->
            <label>Marca:</label>
            <input type="text" name="marca" value="<?php echo htmlspecialchars($produto['marca']); ?>" required>
            <label>Modelo:</label>
            <input type="text" name="modelo" value="<?php echo htmlspecialchars($produto['modelo']); ?>" required>
            <label>Cor:</label>
            <input type="text" name="cor" value="<?php echo htmlspecialchars($produto['cor']); ?>" required>
            <label>Dimensões:</label>
            <input type="text" name="dimensoes" value="<?php echo htmlspecialchars($produto['dimensoes']); ?>" required>
            <label>Nome do Vendedor:</label>
            <input type="text" name="nome_vendedor" value="<?php echo htmlspecialchars($produto['nome_vendedor']); ?>" required>
            <label>Reputação do Vendedor:</label>
            <input type="text" name="reputacao_vendedor" value="<?php echo htmlspecialchars($produto['reputacao_vendedor']); ?>" required>
            <label>Localização do Vendedor:</label>
            <input type="text" name="localizacao_vendedor" value="<?php echo htmlspecialchars($produto['localizacao_vendedor']); ?>" required>
            
            <button type="submit" name="salvar">Salvar Alterações</button>
        </form>
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
