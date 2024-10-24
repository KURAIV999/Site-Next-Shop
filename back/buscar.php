<?php
// search_suggestions.php
include('conexao.php');

$q = isset($_GET['q']) ? $_GET['q'] : '';
$q = mysqli_real_escape_string($conn, $q);

$sql = "SELECT nome FROM tblproduto WHERE nome LIKE '%$q%' LIMIT 5"; // Limitar a 5 sugestões
$result = mysqli_query($conn, $sql);
$suggestions = [];

while ($row = mysqli_fetch_assoc($result)) {
    $suggestions[] = $row;
}

header('Content-Type: application/json');
echo json_encode($suggestions);

?>
