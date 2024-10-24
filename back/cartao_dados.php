<?php 
session_start();
include '../back/conexao.php'; // Inclua seu arquivo de conexão

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "Você precisa estar logado para fazer um pedido.";
    exit;
}

// Recupera o ID do usuário da sessão
$usuario_id = $_SESSION['usuario_id'];

// Recupera o nome do usuário da tabela tblregistrar
$queryNome = "SELECT nome FROM tblregistrar WHERE usuario_id = ?";
$stmtNome = mysqli_prepare($conn, $queryNome);

if ($stmtNome === false) {
    die('Erro ao preparar a consulta: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmtNome, 'i', $usuario_id);
mysqli_stmt_execute($stmtNome);
mysqli_stmt_bind_result($stmtNome, $nomeUsuario);
mysqli_stmt_fetch($stmtNome);
mysqli_stmt_close($stmtNome);

// Verifica se o nome do usuário foi encontrado
if (!$nomeUsuario) {
    die("Usuário não encontrado.");
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera os dados do carrinho
    $queryCarrinho = "SELECT * FROM tblcarrinho WHERE usuario_id = ?";
    $stmtCarrinho = mysqli_prepare($conn, $queryCarrinho);

    if ($stmtCarrinho === false) {
        die('Erro ao preparar a consulta do carrinho: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmtCarrinho, 'i', $usuario_id);
    mysqli_stmt_execute($stmtCarrinho);
    $resultCarrinho = mysqli_stmt_get_result($stmtCarrinho);
    
    // Inicializa a variável total da compra
    $totalCompra = 0;

    // Loop pelos itens do carrinho
    while ($item = mysqli_fetch_assoc($resultCarrinho)) {
        $quantidade = $item['quantidade'];
        $preco = $item['preco'];
        $totalItem = $quantidade * $preco;
        $totalCompra += $totalItem;

        // Insere cada item na tabela tblhistorico
        $queryHistorico = "INSERT INTO tblhistorico (usuario_id, nome, produto, quantidade, preco, total, data) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmtHistorico = mysqli_prepare($conn, $queryHistorico);

        if ($stmtHistorico === false) {
            die('Erro ao preparar a consulta do histórico: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmtHistorico, 'issiid', $usuario_id, $nomeUsuario, $item['nome'], $quantidade, $preco, $totalItem);

        if (!mysqli_stmt_execute($stmtHistorico)) {
            echo "Erro ao registrar o pedido. Tente novamente mais tarde.";
            exit;
        }

        mysqli_stmt_close($stmtHistorico);
    }

    // Limpa o carrinho após o pedido ser realizado
    $queryDeleteCarrinho = "DELETE FROM tblcarrinho WHERE usuario_id = ?";
    $stmtDeleteCarrinho = mysqli_prepare($conn, $queryDeleteCarrinho);

    if ($stmtDeleteCarrinho === false) {
        die('Erro ao preparar a consulta de exclusão do carrinho: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmtDeleteCarrinho, 'i', $usuario_id);
    mysqli_stmt_execute($stmtDeleteCarrinho);
    mysqli_stmt_close($stmtDeleteCarrinho);

    // Recebe os dados do formulário para o cartão
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $endereco = mysqli_real_escape_string($conn, $_POST['endereco']);
    $cartao = mysqli_real_escape_string($conn, $_POST['cartao']);
    $validade = mysqli_real_escape_string($conn, $_POST['validade']);
    $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);
    
    // Validação dos dados do cartão
    if (empty($nome) || empty($email) || empty($endereco) || empty($cartao) || empty($validade) || empty($cvv)) {
        die('Por favor, preencha todos os campos do cartão.');
    }

    // Preparar a inserção dos dados do cartão
    $sql = "INSERT INTO tblcartao (usuario_id, nome, email, endereco, cartao, validade, cvv) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtCartao = mysqli_prepare($conn, $sql);

    if ($stmtCartao === false) {
        die('Erro ao preparar a consulta do cartão: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmtCartao, 'issssss', $usuario_id, $nome, $email, $endereco, $cartao, $validade, $cvv);

    if (mysqli_stmt_execute($stmtCartao)) {
        // Redirecionar para uma página de confirmação
        header("Location: ../pages/obrigado.php"); // Crie uma página de agradecimento
        exit();
    } else {
        die('Erro ao registrar os dados do cartão. Tente novamente mais tarde.');
    }

    mysqli_stmt_close($stmtCartao);
}

mysqli_close($conn);
?>
