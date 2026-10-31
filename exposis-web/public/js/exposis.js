(function () {
    var menuButton = document.getElementById('bg');
    var navigation = document.getElementById('nv');

    if (menuButton && navigation) {
        menuButton.addEventListener('click', function () {
            var isOpen = navigation.classList.toggle('open');
            menuButton.setAttribute('aria-expanded', String(isOpen));
        });
    }

    var illustrations = [
        '<rect x="70" y="20" width="60" height="80" rx="10"/><circle cx="100" cy="55" r="9"/><path d="M100 64v18"/>',
        '<path d="M40 60L100 25 160 60 100 95zM40 60h120M100 25v70"/><circle cx="40" cy="60" r="8"/><circle cx="160" cy="60" r="8"/><circle cx="100" cy="25" r="8"/><circle cx="100" cy="95" r="8"/>',
        '<path d="M75 35L45 60l30 25M125 35l30 25-30 25M112 25L88 95"/>',
        '<circle cx="55" cy="85" r="12"/><circle cx="145" cy="85" r="12"/><circle cx="100" cy="35" r="12"/><path d="M63 77L92 45M137 77L108 45M67 85h66"/>',
        '<path d="M45 95V75M80 95V60M115 95V42M150 95V25"/>',
    ];

    function illustration(index) {
        return '<svg class="art" viewBox="0 0 200 120" role="img" aria-label="Illustration du domaine"><g fill="none" stroke="#14110F" stroke-width="7" stroke-linecap="round" stroke-linejoin="round">' + illustrations[index] + '</g></svg>';
    }

    var flexibility = document.getElementById('fx');
    if (flexibility) {
        var offers = [
            ['Étude de faisabilité', 'Savoir avant d’investir : ce qui est réalisable, à quelles conditions.', 0],
            ['Prototype / MVP', 'Une première version utilisable, à tester avec de vrais usages.', 2],
            ['Collaboration de recherche', 'Chercher ensemble sur des questions appliquées.', 1],
        ];

        flexibility.innerHTML = offers.map(function (offer) {
            return '<article class="fx">' + illustration(offer[2]) + '<div><h3>' + offer[0] + '</h3><p>' + offer[1] + '</p><a href="/services">En savoir plus</a></div></article>';
        }).join('');
    }

    var tabs = document.getElementById('tl');
    var panel = document.getElementById('tp');
    if (tabs && panel) {
        var steps = [
            ['Décrire', ['Un formulaire court : problème, description, délai, budget indicatif', 'Option « information confidentielle »', 'Aucun droit transféré'], 3],
            ['Évaluer', ['Lecture attentive de votre demande', 'Contraintes réelles : réseau, coûts, sécurité', 'NDA avant tout travail approfondi'], 0],
            ['Prototyper', ['Une première version (MVP) utilisable', 'Tests avec de vrais usages', 'Choix techniques documentés'], 2],
            ['Partager', ['Documentation et enseignements', 'Code ouvert quand c’est possible', 'Résultats diffusés'], 4],
        ];

        function selectStep(index) {
            Array.prototype.forEach.call(tabs.children, function (button, buttonIndex) {
                var selected = buttonIndex === index;
                button.setAttribute('aria-selected', String(selected));
                button.tabIndex = selected ? 0 : -1;
            });

            var step = steps[index];
            panel.innerHTML = illustration(step[2]) + '<div class="tx"><h3>' + (index + 1) + '. ' + step[0] + '</h3><ul>' + step[1].map(function (item) {
                return '<li>' + item + '</li>';
            }).join('') + '</ul></div>';
        }

        steps.forEach(function (step, index) {
            var button = document.createElement('button');
            button.type = 'button';
            button.id = 'step-tab-' + index;
            button.setAttribute('role', 'tab');
            button.setAttribute('aria-controls', 'step-panel');
            button.textContent = (index + 1) + '. ' + step[0];
            button.addEventListener('click', function () {
                selectStep(index);
            });
            tabs.appendChild(button);
        });

        panel.id = 'step-panel';
        panel.setAttribute('role', 'tabpanel');
        panel.setAttribute('aria-labelledby', 'step-tab-0');
        tabs.addEventListener('click', function (event) {
            if (event.target.matches('[role="tab"]')) {
                panel.setAttribute('aria-labelledby', event.target.id);
            }
        });
        selectStep(0);
    }
})();
