document.addEventListener('DOMContentLoaded', function() {
    const menuIcon = document.querySelector('.menu-icon');
    const navbarItems = document.querySelector('.navbar ul');
    const overlay = document.querySelector('.overlay');

    menuIcon.addEventListener('click', function() {
        menuIcon.classList.toggle('open'); // Adiciona/Remove a classe para animação
        if (navbarItems.style.display === 'flex') {
            navbarItems.style.display = 'none';
            overlay.classList.remove('menu-open');
        } else {
            navbarItems.style.display = 'flex';
            overlay.classList.add('menu-open');
        }
    });

    overlay.addEventListener('click', function() {
        navbarItems.style.display = 'none';
        menuIcon.classList.remove('open');
        overlay.classList.remove('menu-open');
    });
});
