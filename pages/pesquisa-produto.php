<?php
session_start(); // Inicia a sessão para acessar as variáveis de sessão

// Verifica se o usuário está logado
$usuarioLogado = false;
$nomeUsuario = '';
$acessoEspecial = false; // Adiciona esta variável para verificar o acesso especial

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

// Incluir arquivo de conexão
include('../back/conexao.php'); // Conecta ao banco de dados

// Função para buscar produtos
function buscarProdutos($conn, $query = '') {
    if ($query) {
        $sql = "SELECT produto_id, nome, descricao, preco, imagem_principal FROM tblproduto WHERE nome LIKE '%" . mysqli_real_escape_string($conn, $query) . "%' ORDER BY produto_id DESC";
    } else {
        $sql = "SELECT produto_id, nome, descricao, preco, imagem_principal FROM tblproduto ORDER BY produto_id DESC LIMIT 4"; // Ajuste o LIMIT conforme necessário
    }
    
    return mysqli_query($conn, $sql);
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
    <link rel="stylesheet" href="../css/pesquisa-produto.css">
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

        <button id="theme-toggle">
            <img id="theme-icon" src="https://www.svgrepo.com/show/309493/dark-theme.svg" alt="Theme Icon" style="width: 20px; height: 20px;">
        </button>

        <div>
            <span class="menu-toggle" onclick="toggleSidebar()">☰</span>
        </div>
    </nav>

    <div class="product-management-container">
        <h2>Gerenciar Produtos</h2>
        <!-- Formulário de Pesquisa -->
        <form method="POST" action="">
            <input type="text" name="search" placeholder="Pesquisar produtos por nome..." required>
            <button type="submit">Pesquisar</button>
        </form>

        <!-- Tabela de Resultados -->
        <table class="product-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Imagem</th>
                    <th>Editar</th>
                    <th>Deletar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['search'])) {
                    // Pesquisa por nome do produto
                    $search = mysqli_real_escape_string($conn, $_POST['search']);
                    $sql = "SELECT * FROM tblproduto WHERE nome LIKE '%$search%'";
                } else {
                    // Consulta todos os produtos caso não haja pesquisa
                    $sql = "SELECT * FROM tblproduto";
                }

                $result = mysqli_query($conn, $sql);

                // Exibir produtos encontrados
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['produto_id'] . "</td>"; // Alterado para produto_id
                        echo "<td>" . $row['nome'] . "</td>";
                        
                        // Verificar se a coluna 'descricao' existe no array antes de acessá-la
                        echo "<td>" . (isset($row['descricao']) ? $row['descricao'] : 'Descrição não disponível') . "</td>";

                        echo "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
                        echo "<td><img src='" . $row['imagem_principal'] . "' alt='" . $row['nome'] . "' style='width: 50px; height: 50px;'></td>"; // Alterado para imagem_principal
                        echo "<td>";
                        echo "<form method='POST' action='editar-produto.php' style='display:inline;'>";
                        echo "<input type='hidden' name='produto_id' value='" . $row['produto_id'] . "'>"; // Alterado para produto_id
                        echo "<button type='submit' class='edit-button'>Editar</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "<td>";
                        echo "<form method='POST' action='../back/deletar-produto.php' style='display:inline;'>";
                        echo "<input type='hidden' name='produto_id' value='" . $row['produto_id'] . "'>"; // Alterado para produto_id
                        echo "<button type='submit' class='delete-button'>Deletar</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Nenhum produto encontrado.</td></tr>";
                }

                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>

    <footer class="footer">
        &copy; LojasNext 2024. Todos os direitos reservados.<br>
        Formas de pagamento: Cartão de Crédito, Boleto, PayPal
    </footer>

    <script src="../script/main.js" defer></script>
    <script src="../script/script.js"></script>
    <script src="../script/search.js"></script>
    <script>
        // Função de Filtro para Produtos
        function filterProducts() {
            const searchValue = document.getElementById("search-input").value.toLowerCase();
            const rows = document.querySelectorAll(".product-table tbody tr");

            rows.forEach(row => {
                const productName = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
                row.style.display = productName.includes(searchValue) ? "" : "none";
            });
        }
    </script>
</body>
</html>
