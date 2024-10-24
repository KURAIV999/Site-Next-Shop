
<?php
//SISTEMA DE PROIBIÇÃO DE USUARIOS NAO REGISTRADOS/LOGADOS ACESSEM TAL PAGINA
session_start(); // Inicia a sessão para acessar as variáveis de sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php"); // Redireciona para a página de login se não estiver logado
    exit();
}
?>

<?php 
session_start(); // Inicia a sessão para acessar as variáveis de sessão

// Verifica se o usuário está logado
$usuarioLogado = isset($_SESSION['usuario_id']);

// Verifica se o usuário possui acesso especial, se estiver logado
$acessoEspecial = $usuarioLogado && $_SESSION['acesso_especial'] == 1;

// Obtém o nome do usuário
$nomeUsuario = $usuarioLogado ? $_SESSION['snome'] : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - LojasNext</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/cor-mod.css">
</head>
<body>
    <header>
        <h1>Painel de Administração LojasNext</h1>
        <span class="menu-toggle" onclick="toggleSidebar()">☰</span>
    </header>

    <!-- Menu Lateral -->
<aside id="sidebar">
    <button class="closebtn" onclick="closeSidebar()">×</button>
    <?php if ($usuarioLogado): ?>
        <a style="color: #228B22;">Bem-Vindo, 
            <strong class="user-name" style="color: <?php echo $acessoEspecial ? '#228B22' : '#FFFFFF'; ?>;">
                <?php echo $_SESSION['snome']; ?>
            </strong>
        </a>
        <a href="../back/logout.php" style="color: #8B0000;">Sair</a>
    <?php else: ?>
        <a href="login.php" style="color: #FFA500;">Faça seu Login</a>
    <?php endif; ?>

    <?php if ($acessoEspecial): ?>
        <!-- Menu para usuários com acesso especial -->
        <h2 style="color: #228B22;">Menu Especial</h2>
        <a href="cadastrar-produto.php" style="color: #228B22;">Cadastrar Produto</a>
        <a href="pesquisa-produto.php" style="color: #228B22;">Pesquisar Produtos</a>
        <a href="produtos.php">Produtos</a>
        <a href="carrinho.php">Carrinho</a>
        <a href="historico.php">Histórico</a>
    <?php else: ?>
        <!-- Menu normal para usuários comuns -->
        <h2 style="color: #FFA500;">Menu Comum</h2>
        <a href="produtos.php">Produtos</a>
        <a href="carrinho.php">Carrinho</a>
        <a href="historico.php">Histórico</a>
    <?php endif; ?>
</aside>

<!-- A -->

<link rel="stylesheet" href="../css/pesquisa-produto.css">
<link rel="stylesheet" href="../css/cor-mod.css">
