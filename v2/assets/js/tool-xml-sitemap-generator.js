/**
 * XML Sitemap Generator — Build a valid XML sitemap from URLs and download it.
 */
(function () {
    'use strict';
    if (!document.getElementById('sitemap-base')) return;

    var baseInput = document.getElementById('sitemap-base');
    var urlsInput = document.getElementById('sitemap-urls');
    var generateBtn = document.getElementById('sitemap-generate');
    var downloadBtn = document.getElementById('sitemap-download');
    var copyBtn = document.getElementById('sitemap-copy');
    var resultPanel = document.getElementById('sitemap-result');
    var xmlOutput = document.getElementById('sitemap-xml');
    var statusEl = document.getElementById('sitemap-status');
    var currentXml = '';

    function escapeXml(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&apos;');
    }

    function normalizeUrl(raw, base) {
        raw = raw.trim();
        if (!raw) return null;
        if (raw.startsWith('http://') || raw.startsWith('https://')) return raw;
        var cleanBase = base.replace(/\/+$/, '');
        var path = raw.startsWith('/') ? raw : '/' + raw;
        return cleanBase + path;
    }

    function generate() {
        var base = baseInput.value.trim().replace(/\/+$/, '');
        var lines = urlsInput.value.split('\n').map(function (l) { return l.trim(); }).filter(Boolean);

        if (!base && !lines.some(function (l) { return l.startsWith('http'); })) {
            TCTP.toast('Please enter a base URL or at least one full URL.', '\u26A0\uFE0F');
            return;
        }

        var today = new Date();
        function pad(n) { return (n < 10 ? '0' : '') + n; }
        var dateStr = today.getFullYear() + '-' + pad(today.getMonth() + 1) + '-' + pad(today.getDate());

        var entries = [];
        var seen = {};
        var httpProtocols = lines.filter(function (l) { return /^https?:\/\//.test(l.trim()); });
        var effectiveBase = base || (httpProtocols[0] ? httpProtocols[0].replace(/\/[^\/]*$/, '') : '');

        lines.forEach(function (line) {
            var url = normalizeUrl(line, effectiveBase);
            if (!url) return;
            if (seen[url]) return;
            seen[url] = true;
            entries.push(url);
        });

        if (entries.length === 0) {
            TCTP.toast('Please add at least one valid URL or path.', '\u26A0\uFE0F');
            return;
        }

        var xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
        xml += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';
        entries.forEach(function (url) {
            xml += '  <url>\n';
            xml += '    <loc>' + escapeXml(url) + '</loc>\n';
            xml += '    <lastmod>' + dateStr + '</lastmod>\n';
            xml += '    <changefreq>weekly</changefreq>\n';
            xml += '    <priority>0.8</priority>\n';
            xml += '  </url>\n';
        });
        xml += '</urlset>';

        currentXml = xml;
        xmlOutput.textContent = xml;
        resultPanel.style.display = '';
        statusEl.textContent = entries.length + ' URL' + (entries.length === 1 ? '' : 's');
        downloadBtn.disabled = false;
        TCTP.toast('Sitemap generated!', '\u2705');
    }

    generateBtn.addEventListener('click', generate);
    urlsInput.addEventListener('keydown', function (e) { if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') generate(); });

    downloadBtn.addEventListener('click', function () {
        TCTP.downloadText(currentXml, 'sitemap.xml', 'application/xml;charset=utf-8');
    });

    copyBtn.addEventListener('click', function () {
        TCTP.copyText(currentXml, 'Sitemap XML');
    });
})();
