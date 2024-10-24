<?php
session_start(); // Inicia a sessão para acessar as variáveis de sessão
include '../back/conexao.php'; // Inclui a conexão com o banco de dados

// Verifica se o usuário está logado
$usuarioLogado = false;
$nomeUsuario = '';
if (isset($_SESSION['usuario_id'])) {
    $usuarioLogado = true;
    $nomeUsuario = $_SESSION['snome'];
} else {
    header("Location: login.php"); // Redireciona para a página de login se não estiver logado
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Minha Conta - Next Shop</title>
</head>
<body class="bg-gray-100">
    <header class="bg-gray-800 text-white p-4">
        <h1 class="text-center text-3xl font-bold">Minha Conta</h1>
    </header>

    <nav class="flex justify-between bg-gray-800 text-white p-4">
        <a href="index.php" class="hover:underline">Início</a>
        <span> > Minha Conta > </span>
    </nav>

    <div class="flex">
        <!-- Menu Lateral -->
        <aside class="w-1/4 bg-white p-4 shadow-md">
            <h2 class="text-xl font-bold mb-4">Menu</h2>
            <ul class="space-y-2">
                <li><a href="../pas-perfil/meu-perfil.php" class="text-blue-600 hover:underline">Meu Perfil</a></li>
                <li><a href="pedidos.php" class="text-blue-600 hover:underline">Pedidos</a></li>
                <li><a href="pagamento.php" class="text-blue-600 hover:underline">Pagamento</a></li>
                <li><a href="reembolsos.php" class="text-blue-600 hover:underline">Reembolsos e Devoluções</a></li>
                <li><a href="comentarios.php" class="text-blue-600 hover:underline">Comentários</a></li>
                <li><a href="ajustes.php" class="text-blue-600 hover:underline">Ajustes</a></li>
                <li><a href="endereco.php" class="text-blue-600 hover:underline">Endereço de Entrega</a></li>
                <li><a href="central_mensagens.php" class="text-blue-600 hover:underline">Central de Mensagens</a></li>
                <li><a href="convidar_amigos.php" class="text-blue-600 hover:underline">Convidar Amigos</a></li>
                <li><a href="central_ajuda.php" class="text-blue-600 hover:underline">Central de Ajuda</a></li>
                <li><a href="gerenciar_reportagens.php" class="text-blue-600 hover:underline">Gerencie Reportagens</a></li>
                <li><a href="sugestao.php" class="text-blue-600 hover:underline">Sugestão</a></li>
                <li><a href="centro_dropshipping.php" class="text-blue-600 hover:underline">Centro de Dropshipping</a></li>
                <li><a href="info_penalidades.php" class="text-blue-600 hover:underline">Info sobre Penalidades</a></li>
            </ul>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="flex-1 p-4">
            <h2 class="text-2xl font-bold">Bem-vindo, <?php echo htmlspecialchars($nomeUsuario); ?>!</h2>
            <p class="mt-2 text-gray-700">Esta é sua conta. Aqui você pode gerenciar suas informações e pedidos.</p>
            <hr class="my-4">

            <h3 class="text-xl font-bold">Informações da Conta</h3>
            <p class="mt-1">Atualize suas informações pessoais e configurações de conta aqui.</p>
            
            <div class="mt-4 bg-white p-4 rounded shadow">
                <h4 class="text-lg font-bold">Detalhes Pessoais</h4>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($nomeUsuario); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                <a href="editar_informacoes.php" class="text-blue-600 hover:underline mt-2 inline-block">Editar Informações</a>
            </div>

            <div class="mt-4 bg-white p-4 rounded shadow">
                <h4 class="text-lg font-bold">Histórico de Pedidos</h4>
                <p>Veja seu histórico de pedidos e detalhes.</p>
                <a href="pedidos.php" class="text-blue-600 hover:underline mt-2 inline-block">Ver Pedidos</a>
            </div>
        </main>
    </div>

    <footer class="bg-gray-800 text-white text-center p-4 mt-4">
        &copy; LojasNext 2024. Todos os direitos reservados.
    </footer>

    <script src="../script/main.js" defer></script>
    <script src="../script/script.js"></script>
    <script src="../script/search.js"></script>
</body>
</html>
