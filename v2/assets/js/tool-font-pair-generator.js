/**
 * Font Pair Generator — Beautiful font pairings with CSS/Google Fonts export.
 */
(function () {
    'use strict';
    if (!document.getElementById('fp-preview-text')) return;

    var previewText = document.getElementById('fp-preview-text');
    var pairDisplay = document.getElementById('fp-pair-display');
    var refreshBtn = document.getElementById('fp-refresh');
    var copyCssBtn = document.getElementById('fp-copy-css');
    var style = 'serif';
    var googleToggle = document.getElementById('fp-google');

    TCTP.initModeGroup('.tc-modes[data-group="fp-style"]', function (val) {
        style = val;
        shuffle();
    });

    var fontPairs = {
        serif: [
            ['Playfair Display', 'Source Serif Pro'],
            ['Merriweather', 'Lato'],
            ['Lora', 'Open Sans'],
            ['PT Serif', 'PT Sans'],
            ['Libre Baskerville', 'Montserrat'],
            ['DM Serif Display', 'DM Sans'],
            ['EB Garamond', 'Inter'],
            ['Crimson Text', 'Work Sans'],
        ],
        sans: [
            ['Inter', 'Roboto'],
            ['Poppins', 'Open Sans'],
            ['Montserrat', 'Lato'],
            ['Raleway', 'Source Sans Pro'],
            ['Nunito', 'Ubuntu'],
            ['Quicksand', 'Nunito Sans'],
            ['Outfit', 'Work Sans'],
            ['Plus Jakarta Sans', 'Inter'],
        ],
        mono: [
            ['JetBrains Mono', 'Inter'],
            ['Fira Code', 'Fira Sans'],
            ['Space Mono', 'Space Grotesk'],
            ['IBM Plex Mono', 'IBM Plex Sans'],
            ['Source Code Pro', 'Source Sans Pro'],
            ['Inconsolata', 'Roboto'],
            ['Roboto Mono', 'Roboto'],
            ['Cascadia Code', 'Segoe UI'],
        ],
        display: [
            ['Abril Fatface', 'Lato'],
            ['Righteous', 'Open Sans'],
            ['Bungee', 'Nunito'],
            ['Fredoka One', 'Quicksand'],
            ['Comfortaa', 'Raleway'],
            ['Pacifico', 'Montserrat'],
            ['Lobster', 'Lato'],
            ['Permanent Marker', 'Nunito Sans'],
        ]
    };

    function shuffle() {
        var pairs = fontPairs[style];
        var pair = pairs[Math.floor(Math.random() * pairs.length)];
        render(pair[0], pair[1]);
    }

    function render(heading, body) {
        var text = previewText.value || 'The quick brown fox jumps over the lazy dog';
        var useGoogle = googleToggle.checked;
        var headingUrl = useGoogle ? 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(heading) + ':wght@400;700&display=swap' : '';
        var bodyUrl = useGoogle ? 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(body) + ':wght@400;700&display=swap' : '';

        var css = '';
        if (headingUrl) css += '<link href="' + headingUrl + '" rel="stylesheet">\n';
        if (bodyUrl) css += '<link href="' + bodyUrl + '" rel="stylesheet">';

        var styleAttr = useGoogle ? '' : '';
        var headingStyle = useGoogle ? 'font-family: \'' + heading + '\', serif;' : 'font-family: serif;';
        var bodyStyle = useGoogle ? 'font-family: \'' + body + '\', sans-serif;' : 'font-family: sans-serif;';

        pairDisplay.innerHTML =
            '<div style="border:1px solid rgba(128,128,128,0.2);border-radius:8px;overflow:hidden">' +
            '<div style="padding:24px;border-bottom:1px solid rgba(128,128,128,0.2)">' +
            '<p style="margin:0 0 8px;color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:1px">Heading Font</p>' +
            '<p style="margin:0;font-size:28px;font-weight:700;' + headingStyle + '">' + text + '</p>' +
            '</div>' +
            '<div style="padding:24px;border-bottom:1px solid rgba(128,128,128,0.2)">' +
            '<p style="margin:0 0 8px;color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:1px">Body Font</p>' +
            '<p style="margin:0;font-size:16px;line-height:1.6;' + bodyStyle + '">' + text + '</p>' +
            '</div>' +
            '<div style="padding:16px;background:#f9fafb">' +
            '<p style="margin:0;font-size:13px;color:#374151"><strong>' + heading + '</strong> + <strong>' + body + '</strong></p>' +
            (useGoogle ? '<p style="margin:4px 0 0;font-size:12px;color:#6b7280;font-family:monospace;word-break:break-all">' + css + '</p>' : '') +
            '</div>' +
            '</div>';

        window._fpCSS = css;
        window._fpFonts = heading + ' + ' + body;
        document.getElementById('fp-status').textContent = heading + ' + ' + body;
    }

    refreshBtn.addEventListener('click', shuffle);

    copyCssBtn.addEventListener('click', function () {
        var css = window._fpCSS || '';
        if (!css) { TCTP.toast('Enable Google Fonts to copy CSS.', '\u26A0\uFE0F'); return; }
        TCTP.copyText(css, 'CSS');
    });

    previewText.addEventListener('input', function () {
        var fonts = (window._fpFonts || '').split(' + ');
        if (fonts.length === 2) render(fonts[0], fonts[1]);
    });

    googleToggle.addEventListener('change', function () {
        var fonts = (window._fpFonts || '').split(' + ');
        if (fonts.length === 2) render(fonts[0], fonts[1]);
    });

    shuffle();
})();
