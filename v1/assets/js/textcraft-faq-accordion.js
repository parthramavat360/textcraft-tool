(function() {
    'use strict';
    function initAllAccordions() {
        document.querySelectorAll('[data-tc-faq-accordion]').forEach(function(accordion) {
            var items = accordion.querySelectorAll('.tc-faq-item');
            items.forEach(function(item) {
                var button = item.querySelector('.tc-faq-question');
                var answer = item.querySelector('.tc-faq-answer');
                var icon = item.querySelector('.tc-faq-icon');
                if (!button || !answer) return;
                answer.hidden = true;
                button.setAttribute('aria-expanded', 'false');
                item.classList.remove('is-open', 'is-active');
                if (icon) icon.textContent = '+';
                button.addEventListener('click', function() {
                    var isOpen = item.classList.contains('is-open');
                    items.forEach(function(otherItem) {
                        var otherButton = otherItem.querySelector('.tc-faq-question');
                        var otherAnswer = otherItem.querySelector('.tc-faq-answer');
                        var otherIcon = otherItem.querySelector('.tc-faq-icon');
                        otherItem.classList.remove('is-open', 'is-active');
                        if (otherButton) otherButton.setAttribute('aria-expanded', 'false');
                        if (otherAnswer) otherAnswer.hidden = true;
                        if (otherIcon) otherIcon.textContent = '+';
                    });
                    if (!isOpen) {
                        item.classList.add('is-open', 'is-active');
                        button.setAttribute('aria-expanded', 'true');
                        answer.hidden = false;
                        if (icon) icon.textContent = '\u2212';
                    }
                });
            });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllAccordions);
    } else {
        initAllAccordions();
    }
})();
