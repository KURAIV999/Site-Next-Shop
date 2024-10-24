<?php
// Inicia a sessão apenas se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se as variáveis de sessão estão definidas
if (!isset($_SESSION["slogin"]) || !isset($_SESSION["snome"])) {
    session_destroy(); // Destrói a sessão
    unset($_SESSION['slogin']);
    unset($_SESSION['snome']);
    echo "<script>document.location.href='../pages/index.php';</script>";
    exit;
}
?>
