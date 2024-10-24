<?php
session_start(); // Inicia a sessão para acessar as variáveis de sessão

// Verifica se o usuário está logado
$usuarioLogado = false;
$nomeUsuario = '';

// Caso o usuário esteja logado, recupera o nome da sessão
if (isset($_SESSION['usuario_id'])) {
    $usuarioLogado = true;
    $nomeUsuario = $_SESSION['snome'];
}

// Buscar produtos
function buscarProdutos($conn, $query = '') {
    if ($query) {
        $sql = "SELECT nome, preco, imagem FROM tblproduto WHERE nome LIKE '%" . mysqli_real_escape_string($conn, $query) . "%' ORDER BY produto_id DESC"; // Alterado para produto_id
    } else {
        $sql = "SELECT nome, preco, imagem FROM tblproduto ORDER BY produto_id DESC"; // Alterado para produto_id
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
</head>
<body>
    <header>
        <h1>Resultados da Busca</h1>
    </header>
    <div class="container">
        <div id="search-results">
            <?php
            // Conectar ao banco de dados
            include('../back/conexao.php'); // Certifique-se de que o arquivo de conexão está configurado corretamente

            // Obter o termo de busca
            $searchTerm = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

            if ($searchTerm) {
                // Consultar produtos que correspondem ao termo de busca
                $sql = "SELECT * FROM tblproduto WHERE nome LIKE '%$searchTerm%' OR descricao LIKE '%$searchTerm%'";
                $result = mysqli_query($conn, $sql);

                // Verificar se há produtos correspondentes
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='product'>";
                        echo "<img src='" . $row['imagem'] . "' alt='" . $row['nome'] . "'>";
                        echo "<h3>" . $row['nome'] . "</h3>";
                        echo "<p>" . $row['descricao'] . "</p>";
                        echo "<p class='price'>R$ " . number_format($row['preco'], 2, ',', '.') . "</p>";
                        echo "<a href='produtos/" . strtolower(str_replace(' ', '-', $row['nome'])) . ".php' class='buy-button'>Ver Mais</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Nenhum resultado encontrado para <strong>" . htmlspecialchars($searchTerm) . "</strong>.</p>";
                }
            } else {
                echo "<p>Por favor, insira um termo de busca.</p>";
            }

            // Fechar a conexão com o banco de dados
            mysqli_close($conn);
            ?>
        </div>
    </div>
    <footer>
        &copy; LojasNext 2024. Todos os direitos reservados.<br>
        Formas de pagamento: Cartão de Crédito, Boleto, PayPal
    </footer>
</body>
</html>
