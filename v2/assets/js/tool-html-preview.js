/**
 * HTML Preview / Live Editor — Split-pane HTML/CSS/JS editor with live preview.
 */
(function () {
    'use strict';
    var htmlEl = document.getElementById('hp-html');
    if (!htmlEl) return;

    var cssEl = document.getElementById('hp-css');
    var jsEl = document.getElementById('hp-js');
    var frame = document.getElementById('hp-frame');
    var statusEl = document.getElementById('hp-status');
    var metaEl = document.getElementById('hp-meta');
    var runBtn = document.getElementById('hp-run');
    var copyBtn = document.getElementById('hp-copy');
    var openBtn = document.getElementById('hp-open');
    var exportBtn = document.getElementById('hp-export');
    var autolabel = document.getElementById('hp-autolabel');
    var mode = 'auto';

    var codeEls = {
        html: htmlEl,
        css: cssEl,
        js: jsEl
    };
    var activeFile = 'html';

    // Seed the editors with a nice starting template
    htmlEl.value = '<!DOCTYPE html>\n<html lang="en">\n<head>\n  <meta charset="UTF-8">\n  <meta name="viewport" content="width=device-width, initial-scale=1.0">\n  <title>My Project</title>\n  <link rel="stylesheet" href="*.css">\n</head>\n<body>\n  <div class="card">\n    <h1>Hello, World!</h1>\n    <p>Edit the HTML, CSS, and JS tabs to see live changes.</p>\n    <button onclick="handleClick()">Click Me</button>\n    <p id="output"></p>\n  </div>\n  <script src="*.js"><\/script>\n</body>\n</html>';
    cssEl.value = 'body {\n  margin: 0;\n  font-family: system-ui, sans-serif;\n  display: grid;\n  place-items: center;\n  min-height: 100vh;\n  background: linear-gradient(135deg, #667eea, #764ba2);\n}\n\n.card {\n  background: #fff;\n  padding: 3rem;\n  border-radius: 16px;\n  box-shadow: 0 20px 40px rgba(0,0,0,0.25);\n  text-align: center;\n  max-width: 420px;\n}\n\nh1 {\n  color: #1e293b;\n  margin: 0 0 0.5rem;\n}\n\np {\n  color: #64748b;\n  line-height: 1.6;\n}\n\nbutton {\n  background: #667eea;\n  color: #fff;\n  border: 0;\n  padding: 0.75rem 1.5rem;\n  border-radius: 8px;\n  font-size: 1rem;\n  cursor: pointer;\n  margin-top: 0.5rem;\n}';
    jsEl.value = 'function handleClick() {\n  document.getElementById("output").textContent = "You clicked the button at " + new Date().toLocaleTimeString();\n}';

    function switchTab(file) {
        activeFile = file;
        Object.keys(codeEls).forEach(function (k) {
            codeEls[k].style.display = (k === file) ? '' : 'none';
        });
        document.querySelectorAll('.tctp-editor__tab').forEach(function (b) {
            b.classList.toggle('sel', b.getAttribute('data-file') === file);
        });
        document.querySelectorAll('.tctp-editor__file').forEach(function (b) {
            b.classList.toggle('sel', b.getAttribute('data-file') === file);
        });
    }

    function readGlob(src, content, type) {
        // Replace '*.<ext>' references in HTML with actual content snippets
        return src;
    }

    function buildDocument() {
        // If HTML references style.css / script.js by those EXACT names, inline them.
        var html = htmlEl.value || '';
        // Replace <link rel="stylesheet" href="*.css"> and <script src="*.js"> with inline content
        html = html.replace(/<link[^>]*href=["']\*\.css["'][^>]*>/gi, '<style>\n' + cssEl.value + '\n</style>');
        html = html.replace(/<script[^>]*src=["']\*\.js["'][^>]*>\s*<\/script>/gi, '<script>\n' + jsEl.value + '\n</' + 'script>');
        // Also support style.css and script.js bare names
        html = html.replace(/<link[^>]*href=["']style\.css["'][^>]*>/gi, '<style>\n' + cssEl.value + '\n</style>');
        html = html.replace(/<script[^>]*src=["']script\.js["'][^>]*>\s*<\/script>/gi, '<script>\n' + jsEl.value + '\n</' + 'script>');
        return html;
    }

    function countMeta() {
        metaEl.textContent = 'HTML ' + (htmlEl.value.match(/\n/g) || '').length +
            ' · CSS ' + (cssEl.value.match(/\n/g) || '').length +
            ' · JS ' + (jsEl.value.match(/\n/g) || '').length;
    }

    var runTimer = null;
    function scheduleRun() {
        clearTimeout(runTimer);
        statusEl.className = 'tctp-editor__status';
        statusEl.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Rendering...';
        countMeta();
        if (mode !== 'auto') return;
        runTimer = setTimeout(run, 250);
    }

    function run() {
        var src = buildDocument();
        var srcdoc = src;
        frame.srcdoc = srcdoc;
        statusEl.className = 'tctp-editor__status tctp-editor__status--ok';
        statusEl.innerHTML = '<i class="fa-solid fa-circle-check"></i> Ready';
        autolabel.textContent = mode === 'auto' ? '\u26A1 Auto-run' : '\u26A1 Auto-run';
    }

    ['html', 'css', 'js'].forEach(function (f) {
        if (codeEls[f]) codeEls[f].addEventListener('input', scheduleRun);
    });

    runBtn.addEventListener('click', run);
    copyBtn.addEventListener('click', function () {
        var content = codeEls[activeFile].value;
        TCTP.copyText(content, activeFile.toUpperCase() + ' code');
    });
    openBtn.addEventListener('click', function () {
        var src = buildDocument();
        var win = window.open();
        if (win) { win.document.write(src); win.document.close(); }
    });
    exportBtn.addEventListener('click', function () {
        var src = buildDocument();
        TCTP.downloadText(src, 'index.html', 'text/html;charset=utf-8');
    });

    // Tabs
    document.querySelectorAll('.tctp-editor__tab').forEach(function (b) {
        b.addEventListener('click', function () { switchTab(b.getAttribute('data-file')); });
    });
    document.querySelectorAll('.tctp-editor__file').forEach(function (b) {
        b.addEventListener('click', function () { switchTab(b.getAttribute('data-file')); });
    });

    // Mode toggle (auto / manual)
    document.querySelectorAll('#hp-mode .tctp-editor__seg-btn').forEach(function (b) {
        b.addEventListener('click', function () {
            mode = b.getAttribute('data-val');
            document.querySelectorAll('#hp-mode .tctp-editor__seg-btn').forEach(function (x) { x.classList.remove('sel'); });
            b.classList.add('sel');
            if (mode === 'auto') run();
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'Enter')) {
            if (document.querySelector('.tctp-editor')) {
                e.preventDefault();
                run();
            }
        }
    });

    countMeta();
    scheduleRun();
})();
