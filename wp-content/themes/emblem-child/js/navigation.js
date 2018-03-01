/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */
(function ($) {
		var container, searchEl;
		
		console.log($)

		container = document.getElementById('site-navigation-mobile');
		if (!container) {
				return;
		}

		searchEl = container.getElementsByClassName('menu-search')[0];
		if ('undefined' === typeof searchEl) {
				return;
		}
		
		searchEl.onclick = function () {
				if (-1 !== container.className.indexOf('toggled')) {
						container.className = container.className.replace(' toggled', '');
						searchEl.setAttribute('aria-expanded', 'false');
				} else {
						container.className += ' toggled';
						searchEl.setAttribute('aria-expanded', 'true');
				}
		};
})(jQuery);
