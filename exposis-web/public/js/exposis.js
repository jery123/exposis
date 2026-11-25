(function () {
    var menuButton = document.getElementById('mobile-menu-toggle');
    var mobileMenu = document.getElementById('mobile-menu');
    var accountButton = document.getElementById('account-menu-toggle');
    var accountMenu = document.getElementById('account-menu-options');

    function setMenu(button, menu, open, openLabel, closeLabel) {
        if (!button || !menu) return;

        menu.hidden = !open;
        button.setAttribute('aria-expanded', String(open));
        if (openLabel && closeLabel) {
            button.setAttribute('aria-label', open ? closeLabel : openLabel);
        }
    }

    if (menuButton && mobileMenu) {
        menuButton.addEventListener('click', function () {
            setMenu(menuButton, mobileMenu, mobileMenu.hidden, 'Ouvrir le menu', 'Fermer le menu');
        });

        mobileMenu.addEventListener('click', function (event) {
            if (event.target.closest('a')) {
                setMenu(menuButton, mobileMenu, false, 'Ouvrir le menu', 'Fermer le menu');
            }
        });
    }

    if (accountButton && accountMenu) {
        accountButton.addEventListener('click', function () {
            setMenu(accountButton, accountMenu, accountMenu.hidden);
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        setMenu(menuButton, mobileMenu, false, 'Ouvrir le menu', 'Fermer le menu');
        setMenu(accountButton, accountMenu, false);
    });

    document.addEventListener('click', function (event) {
        if (accountMenu && accountButton && !accountMenu.contains(event.target) && !accountButton.contains(event.target)) {
            setMenu(accountButton, accountMenu, false);
        }
    });
})();
