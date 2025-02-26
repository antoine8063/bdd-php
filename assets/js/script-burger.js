const menuHamburger = document.querySelector(".menu-hamburger");
        const navLinks = document.querySelector('.navbar');

        menuHamburger.addEventListener('click', () => {
            navLinks.classList.toggle('mobile-menu');
        });