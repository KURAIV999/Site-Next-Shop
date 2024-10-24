// Função para alternar o menu lateral (abre ou fecha)
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('open'); // Alterna a classe 'open' no elemento do menu lateral
    } else {
        console.error('Elemento com o id "sidebar" não encontrado.');
    }
}

// Função para fechar o menu lateral
function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.remove('open'); // Remove a classe 'open' do menu lateral para fechá-lo
    } else {
        console.error('Elemento com o id "sidebar" não encontrado.');
    }
}
