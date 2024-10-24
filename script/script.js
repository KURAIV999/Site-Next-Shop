// Adiciona um item ao carrinho
function addToCart(name, price, image) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.push({ name: name, price: price, image: image });
    localStorage.setItem('cart', JSON.stringify(cart));

    let messageElement = document.getElementById('message');
    if (messageElement) {
        messageElement.textContent = `${name} foi adicionado ao carrinho!`;
    } else {
        console.error('Elemento com o id "message" não encontrado.');
    }

    alert(`${name} foi adicionado ao carrinho!`);
}

// Carrega os itens do carrinho na página
function loadCartItems() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let cartItemsContainer = document.getElementById('cart-items');
    let totalPrice = 0;

    if (!cartItemsContainer) {
        console.error('Elemento com o id "cart-items" não encontrado.');
        return;
    }

    cartItemsContainer.innerHTML = '';

    cart.forEach((item, index) => {
        let itemDiv = document.createElement('div');
        itemDiv.classList.add('cart-item');
        itemDiv.innerHTML = `
            <img src="${item.image}" alt="${item.name}" style="width: 100px; height: 100px;">
            <div class="item-details">
                <h3>${item.name}</h3>
                <p>Preço: R$ ${item.price.toFixed(2)}</p>
            </div>
            <button class="remove-btn" data-index="${index}">Remover</button>
        `;
        cartItemsContainer.appendChild(itemDiv);
        totalPrice += item.price;
    });

    document.getElementById('total-price').textContent = totalPrice.toFixed(2);

    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function() {
            removeFromCart(this.dataset.index);
        });
    });
}

// Remove um item do carrinho
function removeFromCart(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCartItems();
}

// Carrega os itens do carrinho quando a página é carregada
window.onload = loadCartItems;


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

// CARRINHO
function addToCart(nome, preco, imagem, quantidade) {
    // Cria um objeto com os detalhes do produto
    const produto = {
        nome: nome,
        preco: preco,
        imagem: imagem,
        quantidade: quantidade
    };

    // Salva os produtos no localStorage (ou faça uma chamada AJAX para o backend)
    let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    carrinho.push(produto);
    localStorage.setItem('carrinho', JSON.stringify(carrinho));

    // Redireciona para a página do carrinho
    window.location.href = '../pages/carrinho.php';
}

// OVERLAY DEPOIS QUE ABRE O SIDEBAR, BARA OS BURROS: AO CLICAR NO BOTAO DE ABRIR O MENU LATERAL ESCUREÇERA A TELA DE TUDO QUE ESTA FORA DO MENU LATERAL E ETC...
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    if (sidebar.classList.contains('open')) {
        sidebar.classList.remove('open');
        overlay.style.display = 'none'; // Esconde o overlay
    } else {
        sidebar.classList.add('open');
        overlay.style.display = 'block'; // Mostra o overlay
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    sidebar.classList.remove('open');
    overlay.style.display = 'none'; // Esconde o overlay
}
