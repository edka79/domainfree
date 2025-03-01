/******/ (() => { // webpackBootstrap
    var __webpack_exports__ = {};
    /*!******************************!*\
      !*** ./resources/js/menu.js ***!
      \******************************/
    window.addEventListener('DOMContentLoaded', function (event) {
        // Toggle nav
        var sidebarToggle = document.body.querySelector('#sidebarToggle');

        if (sidebarToggle) {
            if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
                document.body.classList.toggle('sb-sidenav-toggled');
            }

            sidebarToggle.addEventListener('click', function (event) {
                event.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
                localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
            });
        }
    });
    /******/ })()
;
