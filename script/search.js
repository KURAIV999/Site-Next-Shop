// script.js

// Adiciona um listener para o campo de busca
document.getElementById('search-input').addEventListener('keyup', function() {
    let query = this.value;

    // Apenas buscar após 3 caracteres
    if (query.length > 2) {
        fetch(`../back/buscar.php?q=${encodeURIComponent(query)}`)
            .then(response => {
                // Verifica se a resposta é válida
                if (!response.ok) {
                    throw new Error('Erro ao buscar produtos');
                }
                return response.json();
            })
            .then(data => {
                // Atualizar a lista de sugestões
                const suggestionsList = document.getElementById('suggestions-list');
                suggestionsList.innerHTML = '';

                // Adiciona sugestões ao DOM
                data.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item.nome; // Ajuste conforme a estrutura do seu objeto
                    li.onclick = function() {
                        document.getElementById('search-input').value = item.nome;
                        suggestionsList.innerHTML = ''; // Limpar sugestões
                    };
                    suggestionsList.appendChild(li);
                });
            })
            .catch(error => {
                console.error('Erro:', error); // Log de erro no console
            });
    } else {
        // Limpa sugestões se menos de 3 caracteres
        document.getElementById('suggestions-list').innerHTML = '';
    }
});

// Adiciona um listener para o botão de busca
document.getElementById('search-button').addEventListener('click', function() {
    const query = document.getElementById('search-input').value;
    if (query) {
        // Redireciona para produtos.php com a query
        window.location.href = '../pages/produtos.php?query=' + encodeURIComponent(query);
    }
});

// Adiciona suporte ao pressionar Enter para buscar
document.getElementById('search-input').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        document.getElementById('search-button').click();
    }
});
