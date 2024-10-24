// Função de login
function login(event) {
    event.preventDefault();
    console.log('Função login chamada');

    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    const errorMessage = document.getElementById('login-error-message');

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../pages/login.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            console.log('Resposta do servidor:', response);
            if (response.success) {
                window.location.href = response.redirect; // Redirecionar baseado na resposta do PHP
            } else {
                errorMessage.textContent = response.message;
            }
        }
    };

    xhr.send(`email=${encodeURIComponent(email)}&senha=${encodeURIComponent(password)}`);
}

// Alterna entre o modo escuro e claro
document.getElementById('theme-toggle').addEventListener('click', function() {
    const body = document.body;
    const themeIcon = document.getElementById('theme-icon');

    // Alterna a classe 'dark-mode'
    body.classList.toggle('dark-mode');

    // Salva o tema atual no localStorage
    const currentTheme = body.classList.contains('dark-mode') ? 'dark' : 'light';
    localStorage.setItem('theme', currentTheme);

    // Muda o src da imagem dependendo do tema atual
    themeIcon.src = currentTheme === 'dark' ? 
        "https://www.svgrepo.com/show/309493/dark-theme.svg" : 
        "https://www.svgrepo.com/show/309493/dark-theme.svg"; // Substitua pelo URL do ícone claro
});

// Função para aplicar o tema baseado no Local Storage
function applyTheme(theme) {
    document.body.classList.toggle('dark-mode', theme === 'dark');
}

// Configura o tema na inicialização
window.onload = () => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);
    document.getElementById('theme-icon').src = savedTheme === 'dark' ? 
        "https://www.svgrepo.com/show/309493/dark-theme.svg" : 
        "https://www.svgrepo.com/show/309493/dark-theme.svg"; // Substitua pelo URL do ícone claro
};

// Adicione a chamada da função de login nos eventos de submissão do formulário
document.getElementById('login-form').addEventListener('submit', login);
