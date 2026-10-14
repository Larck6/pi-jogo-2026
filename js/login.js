const button = document.getElementById('openLogin');
const loginBox = document.getElementById('loginBox');

// abrir ao clicar no botão
button.addEventListener('click', (e) => {
  e.stopPropagation(); // evita fechar imediatamente
  loginBox.style.display = 'block';
});

// evitar fechar ao clicar dentro do login
loginBox.addEventListener('click', (e) => {
  e.stopPropagation();
});

// clicar em qualquer lugar da página fecha
document.addEventListener('click', () => {
  loginBox.style.display = 'none';
});