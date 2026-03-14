/**
 * APS Advertising - Shared Header (Mobile Menu)
 * Use on all pages for homepage-style header
 */
(function () {
    'use strict';
    function setupMobileMenu() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuClose = document.getElementById('mobileMenuClose');
        const menuOverlay = document.getElementById('menuOverlay');
        const body = document.body;
        if (!mobileMenu || !menuOverlay) return;
        function openMenu() {
            mobileMenu.classList.add('active');
            menuOverlay.classList.add('active');
            body.classList.add('menu-open');
        }
        function closeMenu() {
            mobileMenu.classList.remove('active');
            menuOverlay.classList.remove('active');
            body.classList.remove('menu-open');
        }
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                openMenu();
            });
        }
        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                closeMenu();
            });
        }
        menuOverlay.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            closeMenu();
        });
        document.querySelectorAll('.mobile-nav-links a').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var href = this.getAttribute('href');
                if (href && href.startsWith('#')) {
                    var target = document.querySelector(href);
                    if (target) {
                        closeMenu();
                        setTimeout(function () {
                            window.scrollTo({ top: target.offsetTop - 60, behavior: 'smooth' });
                        }, 300);
                    }
                } else if (href) {
                    window.location.href = href;
                }
            });
        });
    }
    function setupNavbarScroll() {
        var navbar = document.getElementById('navbar');
        if (navbar) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }, { passive: true });
        }
    }
    function init() {
        setupMobileMenu();
        setupNavbarScroll();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
