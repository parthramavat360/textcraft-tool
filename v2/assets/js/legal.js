/**
 * TCTP: Legal & Policies — "On this page" scroll-spy.
 *
 * Marks the sidebar anchor whose doc-card is currently in view, the same
 * way the reference textcraft-legal-policies.html does with an
 * IntersectionObserver. Scoped to .tclp-legal so it can't collide with
 * anything else on the page.
 */
(function () {
	'use strict';

	function init() {
		var root = document.querySelector('.tclp-legal');
		if (!root) {
			return;
		}

		var links = root.querySelectorAll('.tclp-rel');
		var cards = root.querySelectorAll('.tclp-doc-card');
		if (!links.length || !cards.length) {
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				var visible = entries
					.filter(function (e) { return e.isIntersecting; })
					.sort(function (a, b) {
						return a.boundingClientRect.top - b.boundingClientRect.top;
					})[0];

				if (!visible) {
					return;
				}

				var target = '#' + visible.target.id;
				links.forEach(function (l) {
					l.dataset.active = l.getAttribute('href') === target ? 'true' : 'false';
				});
			},
			{ rootMargin: '-100px 0px -65% 0px' }
		);

		cards.forEach(function (s) { observer.observe(s); });
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
