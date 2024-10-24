<?php
// Incluir a conexão com o banco de dados
include('conexao.php');

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
    $preco = mysqli_real_escape_string($conn, $_POST['preco']);
    $imagem = mysqli_real_escape_string($conn, $_POST['imagem']);

    // Preparar a consulta SQL para inserir o produto
    $sql = "INSERT INTO tblproduto (nome, descricao, preco, imagem) VALUES ('$nome', '$descricao', '$preco', '$imagem')";

    // Executar a consulta e verificar se foi bem-sucedida
    if (mysqli_query($conn, $sql)) {
        echo "Produto cadastrado com sucesso!";
        // Redirecionar para a página de produtos ou outra página, se desejado
        header("Location: ../pages/produtos.php");
        exit();
    } else {
        echo "Erro ao cadastrar produto: " . mysqli_error($conn);
    }

    // Fechar a conexão com o banco de dados
    mysqli_close($conn);
} else {
    echo "Método de requisição inválido.";
}
?>
