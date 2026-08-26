/**
 * Meta Tag Generator — Tool JS
 * SEO meta tag builder with Open Graph and Twitter Cards.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var titleInput = document.getElementById('tc-mtg-title');
    if (!titleInput) return;

    var descInput = document.getElementById('tc-mtg-description');
    var keywordsInput = document.getElementById('tc-mtg-keywords');
    var urlInput = document.getElementById('tc-mtg-url');
    var authorInput = document.getElementById('tc-mtg-author');
    var imageInput = document.getElementById('tc-mtg-image');
    var siteNameInput = document.getElementById('tc-mtg-site-name');
    var twitterInput = document.getElementById('tc-mtg-twitter');
    var canonicalInput = document.getElementById('tc-mtg-canonical');
    var themeColorInput = document.getElementById('tc-mtg-theme-color');
    var codeOutput = document.getElementById('tc-mtg-code');
    var previewEl = document.getElementById('tc-mtg-preview');
    var titleCount = document.getElementById('tc-mtg-title-count');
    var descCount = document.getElementById('tc-mtg-desc-count');

    var ogType = 'website';
    var cardType = 'summary_large_image';
    var robots = 'index, follow';

    // ── Mode cards ───────────────────────────────────────────

    function bindCards(selector, callback) {
        document.querySelectorAll(selector + ' .tc-rsz-mode-card').forEach(function (card) {
            card.addEventListener('click', function () {
                document.querySelectorAll(selector + ' .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
                card.classList.add('sel');
                callback(card.getAttribute('data-val'));
            });
        });
    }

    bindCards('.tc-mtg-type-cards', function (v) { ogType = v; updatePreview(); });
    bindCards('.tc-mtg-card-cards', function (v) { cardType = v; updatePreview(); });
    bindCards('.tc-mtg-robots-cards', function (v) { robots = v; updatePreview(); });

    // ── All inputs ───────────────────────────────────────────

    var allInputs = [titleInput, descInput, keywordsInput, urlInput, authorInput, imageInput, siteNameInput, twitterInput, canonicalInput, themeColorInput];
    allInputs.forEach(function (inp) {
        inp.addEventListener('input', updatePreview);
    });

    // ── Generate HTML ────────────────────────────────────────

    function escapeHtml(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function updatePreview() {
        var title = titleInput.value;
        var desc = descInput.value;
        var keywords = keywordsInput.value;
        var url = urlInput.value;
        var author = authorInput.value;
        var image = imageInput.value;
        var siteName = siteNameInput.value;
        var twitter = twitterInput.value;
        var canonical = canonicalInput.value;
        var themeColor = themeColorInput.value;

        titleCount.textContent = title.length + ' chars';
        descCount.textContent = desc.length + ' chars';

        var lines = [];
        lines.push('<!-- Primary Meta Tags -->');
        lines.push('<title>' + escapeHtml(title) + '</title>');
        lines.push('<meta name="title" content="' + escapeHtml(title) + '">');
        lines.push('<meta name="description" content="' + escapeHtml(desc) + '">');
        if (keywords) lines.push('<meta name="keywords" content="' + escapeHtml(keywords) + '">');
        if (author) lines.push('<meta name="author" content="' + escapeHtml(author) + '">');
        lines.push('<meta name="robots" content="' + escapeHtml(robots) + '">');
        lines.push('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
        lines.push('<meta name="theme-color" content="' + themeColor + '">');

        if (canonical) {
            lines.push('');
            lines.push('<!-- Canonical -->');
            lines.push('<link rel="canonical" href="' + escapeHtml(canonical) + '">');
        }

        lines.push('');
        lines.push('<!-- Open Graph / Facebook -->');
        lines.push('<meta property="og:type" content="' + ogType + '">');
        if (siteName) lines.push('<meta property="og:site_name" content="' + escapeHtml(siteName) + '">');
        if (url) lines.push('<meta property="og:url" content="' + escapeHtml(url) + '">');
        lines.push('<meta property="og:title" content="' + escapeHtml(title) + '">');
        lines.push('<meta property="og:description" content="' + escapeHtml(desc) + '">');
        if (image) lines.push('<meta property="og:image" content="' + escapeHtml(image) + '">');

        lines.push('');
        lines.push('<!-- Twitter -->');
        lines.push('<meta property="twitter:card" content="' + cardType + '">');
        if (twitter) lines.push('<meta property="twitter:site" content="' + escapeHtml(twitter) + '">');
        if (url) lines.push('<meta property="twitter:url" content="' + escapeHtml(url) + '">');
        lines.push('<meta property="twitter:title" content="' + escapeHtml(title) + '">');
        lines.push('<meta property="twitter:description" content="' + escapeHtml(desc) + '">');
        if (image) lines.push('<meta property="twitter:image" content="' + escapeHtml(image) + '">');

        var code = lines.join('\n');
        codeOutput.value = code;

        // Update preview card
        if (previewEl) {
            previewEl.innerHTML =
                '<div class="tc-mtg-card">' +
                    (image ? '<img src="' + escapeHtml(image) + '" class="tc-mtg-card-img" onerror="this.style.display=\'none\'">' : '') +
                    '<div class="tc-mtg-card-body">' +
                        '<div class="tc-mtg-card-url">' + escapeHtml(url || 'example.com') + '</div>' +
                        '<div class="tc-mtg-card-title">' + escapeHtml(title || 'Page Title') + '</div>' +
                        '<div class="tc-mtg-card-desc">' + escapeHtml(desc || 'Page description will appear here') + '</div>' +
                    '</div>' +
                '</div>';
        }
    }

    // ── Copy ─────────────────────────────────────────────────

    document.getElementById('tc-mtg-copy').addEventListener('click', function () {
        TCTP.copyText(codeOutput.value);
        TCTP.toast('Meta tags copied!', '\u2705');
    });

    // ── Init ─────────────────────────────────────────────────

    updatePreview();
})();
