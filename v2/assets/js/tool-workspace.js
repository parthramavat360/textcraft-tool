'use strict';
(function() {

/* ════════════════════════════════════════════════════════════════
   TextCraft Tools Pro — Unified Workspace Script
   Handles all 78 tool pages via data-tool-slug / data-tool-type
   ════════════════════════════════════════════════════════════════ */

// ── Shared Utilities ──

var TCTP = {};

TCTP.toast = function(msg, duration) {
    duration = duration || 2800;
    var el = document.querySelector('.tctp-toast');
    if (!el) {
        el = document.createElement('div');
        el.className = 'tctp-toast';
        document.body.appendChild(el);
    }
    el.textContent = msg;
    el.classList.add('show');
    clearTimeout(el._timer);
    el._timer = setTimeout(function() { el.classList.remove('show'); }, duration);
};

TCTP.copy = function(text, btn) {
    if (!text) { TCTP.toast('Nothing to copy.'); return; }
    navigator.clipboard.writeText(text).then(function() {
        if (btn) {
            var orig = btn.textContent;
            btn.textContent = '\u2713 Copied!';
            setTimeout(function() { btn.textContent = orig; }, 2000);
        }
        TCTP.toast('Copied to clipboard!');
    }).catch(function() {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        TCTP.toast('Copied!');
    });
};

TCTP.download = function(content, filename, type) {
    var blob = content instanceof Blob ? content : new Blob([content], { type: type || 'text/plain;charset=utf-8' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    setTimeout(function() { URL.revokeObjectURL(url); }, 5000);
};

TCTP.formatSize = function(bytes) {
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
    if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return bytes + ' B';
};

TCTP.readArrayBuffer = function(file) {
    return new Promise(function(resolve, reject) {
        var reader = new FileReader();
        reader.onload = function() { resolve(reader.result); };
        reader.onerror = reject;
        reader.readAsArrayBuffer(file);
    });
};

TCTP.readDataURL = function(file) {
    return new Promise(function(resolve, reject) {
        var reader = new FileReader();
        reader.onload = function() { resolve(reader.result); };
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
};

TCTP.readText = function(file) {
    return new Promise(function(resolve, reject) {
        var reader = new FileReader();
        reader.onload = function() { resolve(reader.result); };
        reader.onerror = reject;
        reader.readAsText(file);
    });
};

TCTP.loadScript = function(src) {
    return new Promise(function(resolve, reject) {
        var existing = document.querySelector('script[src="' + src + '"]');
        if (existing) {
            if (existing.dataset.loaded === 'true') { resolve(); return; }
            existing.addEventListener('load', resolve, { once: true });
            existing.addEventListener('error', reject, { once: true });
            return;
        }
        var script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.onload = function() { script.dataset.loaded = 'true'; resolve(); };
        script.onerror = reject;
        document.head.appendChild(script);
    });
};

TCTP.ensurePdfJs = function() {
    if (window.pdfjsLib) return Promise.resolve();
    return TCTP.loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js').then(function() {
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    });
};

TCTP.ensurePdfLib = function() {
    if (window.PDFLib) return Promise.resolve();
    return TCTP.loadScript('https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js');
};

TCTP.ensureJSZip = function() {
    if (window.JSZip) return Promise.resolve();
    return TCTP.loadScript('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js');
};

TCTP.ensureHeic2Any = function() {
    if (window.heic2any) return Promise.resolve();
    return TCTP.loadScript('https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js');
};

TCTP.getEls = function(ws) {
    var slug = ws.dataset.toolSlug;
    return {
        slug: slug,
        ws: ws,
        $: function(suffix) { return ws.querySelector('#tctp-' + slug + '-' + suffix); },
        $$: function(sel) { return ws.querySelectorAll(sel); }
    };
};

TCTP.countWords = function(text) {
    if (!text || !text.trim()) return 0;
    return text.trim().split(/\s+/).length;
};

TCTP.countSentences = function(text) {
    if (!text || !text.trim()) return 0;
    var matches = text.match(/[.!?]+/g);
    return matches ? matches.length : 0;
};

TCTP.countLines = function(text) {
    if (!text) return 0;
    return text.split('\n').length;
};

TCTP.setupDropZone = function(ws, e, opts) {
    var drop = e.$('drop');
    var fileInput = e.$('file');
    if (!drop || !fileInput) return;
    drop.addEventListener('click', function() { fileInput.click(); });
    drop.addEventListener('dragover', function(ev) { ev.preventDefault(); drop.classList.add('hot'); });
    drop.addEventListener('dragleave', function() { drop.classList.remove('hot'); });
    drop.addEventListener('drop', function(ev) {
        ev.preventDefault();
        drop.classList.remove('hot');
        if (opts.multiple) {
            opts.onFiles(ev.dataTransfer.files);
        } else {
            if (ev.dataTransfer.files[0]) opts.onFile(ev.dataTransfer.files[0]);
        }
    });
    fileInput.addEventListener('change', function() {
        if (opts.multiple) {
            opts.onFiles(fileInput.files);
        } else {
            if (fileInput.files[0]) opts.onFile(fileInput.files[0]);
        }
        fileInput.value = '';
    });
};

TCTP.showFileRow = function(e, name, size) {
    var fileRow = e.$('file-row');
    var fname = e.$('fname');
    var fmeta = e.$('fmeta');
    var drop = e.$('drop');
    if (fileRow) fileRow.classList.add('visible');
    if (drop) drop.style.display = 'none';
    if (fname) fname.textContent = name;
    if (fmeta) fmeta.textContent = TCTP.formatSize(size);
};

TCTP.resetDropZone = function(e) {
    var fileRow = e.$('file-row');
    var drop = e.$('drop');
    var fileInput = e.$('file');
    if (fileRow) fileRow.classList.remove('visible');
    if (drop) drop.style.display = '';
    if (fileInput) fileInput.value = '';
};

TCTP.updateBar = function(e, pct, text) {
    var barFill = e.$('bar-fill');
    var barText = e.$('bar-text');
    var barPct = e.$('bar-pct');
    if (barFill) barFill.style.width = pct + '%';
    if (barText && text) barText.textContent = text;
    if (barPct) barPct.textContent = pct + '%';
};

TCTP.clearBar = function(e) {
    TCTP.updateBar(e, 0, 'Ready');
};


// ═══════════════════════════════════════════════════════════════
// TEXT TOOL IMPLEMENTATIONS
// ═══════════════════════════════════════════════════════════════

function initCaseConverter(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var activeCase = null;
    var conversions = {
        uppercase: function(s) { return s.toUpperCase(); },
        lowercase: function(s) { return s.toLowerCase(); },
        sentence: function(s) { return s.replace(/(\s*[.!?]+\s+|^)([a-z])/g, function(m, pre, c) { return pre + c.toUpperCase(); }); },
        title: function(s) { return s.replace(/\b\w+/g, function(w) { return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase(); }); },
        capitalized: function(s) { return s.replace(/\b\w/g, function(c) { return c.toUpperCase(); }); },
        alternating: function(s) { var i=0; return s.split('').map(function(c) { return c===' ' ? ' ' : (i++%2===0 ? c.toLowerCase() : c.toUpperCase()); }).join(''); },
        inverse: function(s) { return s.split('').map(function(c) { return c===c.toUpperCase() ? c.toLowerCase() : c.toUpperCase(); }).join(''); }
    };
    ws.querySelectorAll('[data-case]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            activeCase = btn.dataset.case;
            ws.querySelectorAll('[data-case]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            if (inp.value) out.value = conversions[activeCase](inp.value);
        });
    });
    inp.addEventListener('input', function() {
        if (activeCase && conversions[activeCase]) out.value = conversions[activeCase](inp.value);
    });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        if (activeCase) out.value = conversions[activeCase](inp.value);
        else out.value = inp.value;
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'converted-text.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() {
        inp.value = ''; out.value = ''; activeCase = null;
        ws.querySelectorAll('[data-case]').forEach(function(b) { b.classList.remove('sel'); });
    });
}

function initSentenceCase(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    function doSentence(s) { return s.replace(/(\s*[.!?]+\s+|^)([a-z])/g, function(m, pre, c) { return pre + c.toUpperCase(); }); }
    inp.addEventListener('input', function() { out.value = doSentence(inp.value); });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        out.value = doSentence(inp.value);
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'sentence-case.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initTitleCase(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var minorWords = ['a','an','the','and','but','or','nor','for','yet','so','at','by','in','of','on','to','up','as','is','it','if','vs','vs.'];
    var titleMode = 'standard';
    ws.querySelectorAll('[data-title-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            titleMode = btn.dataset.titleMode;
            ws.querySelectorAll('[data-title-mode]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            if (inp.value) out.value = doTitle(inp.value);
        });
    });
    function doTitle(s) {
        if (titleMode === 'apa') {
            return s.replace(/\b\w+/g, function(w, offset) {
                if (offset > 0 && minorWords.indexOf(w.toLowerCase()) !== -1) return w.toLowerCase();
                return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase();
            });
        }
        return s.replace(/\b\w+/g, function(w) { return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase(); });
    }
    inp.addEventListener('input', function() { out.value = doTitle(inp.value); });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        out.value = doTitle(inp.value);
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'title-case.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initFindReplace(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    var findInp = e.$('find');
    var replaceInp = e.$('replace');
    if (!inp || !out || !findInp || !replaceInp) return;
    var caseSensitive = false, wholeWord = false, useRegex = false;
    var csToggle = e.$('case-sensitive');
    var wwToggle = e.$('whole-word');
    var rxToggle = e.$('regex');
    if (csToggle) csToggle.addEventListener('click', function() { caseSensitive = !caseSensitive; csToggle.classList.toggle('sel', caseSensitive); });
    if (wwToggle) wwToggle.addEventListener('click', function() { wholeWord = !wholeWord; wwToggle.classList.toggle('sel', wholeWord); });
    if (rxToggle) rxToggle.addEventListener('click', function() { useRegex = !useRegex; rxToggle.classList.toggle('sel', useRegex); });
    function buildRegex(find, flags) {
        var escaped = useRegex ? find : find.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        if (wholeWord) escaped = '\\b' + escaped + '\\b';
        return new RegExp(escaped, flags);
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!findInp.value) { TCTP.toast('Please enter text to find.'); return; }
        if (!inp.value) { TCTP.toast('Please enter some text.'); return; }
        var flags = caseSensitive ? 'g' : 'gi';
        try {
            var regex = buildRegex(findInp.value, flags);
            var matches = inp.value.match(regex);
            var count = matches ? matches.length : 0;
            out.value = inp.value.replace(regex, replaceInp.value);
            TCTP.toast(count + ' replacement' + (count !== 1 ? 's' : '') + ' made.');
        } catch(err) { TCTP.toast('Invalid regex: ' + err.message); }
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'find-replace-result.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() {
        inp.value = ''; out.value = ''; findInp.value = ''; replaceInp.value = '';
    });
}

function initCharacterRemover(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var customInp = e.$('custom');
    function process() {
        var text = inp.value;
        var toggles = { 'spaces': / /g, 'line-breaks': /\r?\n/g, 'tabs': /\t/g,
            'digits': /[0-9]/g, 'letters': /[a-zA-Z]/g, 'punctuation': /[^\w\s]/g, 'symbols': /[^a-zA-Z0-9]/g };
        Object.keys(toggles).forEach(function(key) {
            var sel = ws.querySelector('[data-remove="' + key + '"]');
            if (sel && sel.classList.contains('sel')) text = text.replace(toggles[key], '');
        });
        if (customInp && customInp.value) {
            try { text = text.replace(new RegExp(customInp.value, 'g'), ''); } catch(ex) {}
        }
        out.value = text;
    }
    ws.querySelectorAll('[data-remove]').forEach(function(btn) {
        btn.addEventListener('click', function() { btn.classList.toggle('sel'); if (inp.value) process(); });
    });
    if (customInp) customInp.addEventListener('input', function() { if (inp.value) process(); });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        process();
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'text-with-removals.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() {
        inp.value = ''; out.value = '';
        ws.querySelectorAll('[data-remove]').forEach(function(b) { b.classList.remove('sel'); });
    });
}

function initReverseText(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var upsideDownMap = {
        'a':'\u0250','b':'q','c':'\u0254','d':'p','e':'\u01DD','f':'\u025F','g':'\u0265',
        'h':'\u0266','i':'\u0131','j':'\u0279','k':'\u029E','l':'\u026C','m':'\u026F',
        'n':'u','o':'o','p':'d','q':'b','r':'\u0279','s':'s','t':'\u0287','u':'n',
        'v':'\u028C','w':'\u028D','x':'x','y':'\u028E','z':'\u017E',
        'A':'\u0220','B':'\u1431','C':'\u0186','D':'\u15E1','E':'\u018E','F':'\u2132',
        'G':'\u05E4','H':'H','I':'I','J':'\u017F','K':'\u22CA','L':'\u02E1',
        'M':'W','N':'\u0220','O':'O','P':'\u0500','Q':'\u053A','R':'\u0280',
        'S':'S','T':'\u0547','U':'\u0548','V':'\u039B','W':'M','X':'X',
        'Y':'\u04CB','Z':'Z','1':'\u2E19','2':'\u1614','3':'\u0190','4':'\u0584',
        '5':'\u03DB','6':'9','7':'\u02BB','8':'8','9':'6','0':'0',
        '.':'.',',':',','!':'\u00A1','?':'\u00BF','\'':',','\"':',,',
        '(': '[',')': ']','[': '(',']': ')','{': '}','}': '{','<': '>','>': '<'
    };
    var mode = 'characters';
    ws.querySelectorAll('[data-rev-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            mode = btn.dataset.revMode;
            ws.querySelectorAll('[data-rev-mode]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            if (inp.value) doReverse();
        });
    });
    function doReverse() {
        var text = inp.value;
        if (mode === 'characters') out.value = text.split('').reverse().join('');
        else if (mode === 'words') out.value = text.split(/\s+/).reverse().join(' ');
        else if (mode === 'lines') out.value = text.split('\n').reverse().join('\n');
        else if (mode === 'upside-down') out.value = text.split('').reverse().map(function(c) { return upsideDownMap[c] || c; }).join('');
    }
    inp.addEventListener('input', function() { if (ws.querySelector('[data-rev-mode].sel')) doReverse(); });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        doReverse();
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'reversed-text.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initSortWords(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var sortMode = 'alphabetical', sortDelim = 'words';
    ws.querySelectorAll('[data-sort-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            sortMode = btn.dataset.sortMode;
            ws.querySelectorAll('[data-sort-mode]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
        });
    });
    ws.querySelectorAll('[data-sort-delim]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            sortDelim = btn.dataset.sortDelim;
            ws.querySelectorAll('[data-sort-delim]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
        });
    });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        var sep = sortDelim === 'lines' ? '\n' : /\s+/;
        var items = inp.value.split(sep);
        if (sortMode === 'alphabetical') items.sort(function(a, b) { return a.localeCompare(b); });
        else if (sortMode === 'reverse') items.sort(function(a, b) { return b.localeCompare(a); });
        else if (sortMode === 'length') items.sort(function(a, b) { return a.length - b.length; });
        else if (sortMode === 'random') { for (var i = items.length - 1; i > 0; i--) { var j = Math.floor(Math.random() * (i + 1)); var tmp = items[i]; items[i] = items[j]; items[j] = tmp; } }
        else if (sortMode === 'unique') { var seen = {}; items = items.filter(function(it) { var k = it.trim().toLowerCase(); if (seen[k]) return false; seen[k] = true; return true; }); }
        out.value = items.join(sortDelim === 'lines' ? '\n' : ' ');
        TCTP.toast('Sorted ' + items.length + ' items.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'sorted-text.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initRepeatText(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var countInp = e.$('count');
    var sepInp = e.$('separator');
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        var count = parseInt(countInp ? countInp.value : '2', 10);
        if (isNaN(count) || count < 1) count = 1;
        if (count > 10000) count = 10000;
        var sep = sepInp ? sepInp.value : '\n';
        var arr = [];
        for (var i = 0; i < count; i++) arr.push(inp.value);
        out.value = arr.join(sep);
        TCTP.toast('Repeated ' + count + ' times.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'repeated-text.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initRemoveLineBreaks(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var mode = 'single';
    ws.querySelectorAll('[data-lb-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            mode = btn.dataset.lbMode;
            ws.querySelectorAll('[data-lb-mode]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
        });
    });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        if (mode === 'single') out.value = inp.value.replace(/\r?\n/g, ' ');
        else if (mode === 'double') out.value = inp.value.replace(/\r?\n\r?\n/g, '\n\n');
        else if (mode === 'all') out.value = inp.value.replace(/\r?\n/g, '');
        TCTP.toast('Line breaks removed.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'text-no-linebreaks.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initRemoveFormatting(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        var text = inp.value;
        text = text.replace(/<[^>]+>/g, '');
        text = text.replace(/&nbsp;/g, ' ');
        text = text.replace(/&amp;/g, '&');
        text = text.replace(/&lt;/g, '<');
        text = text.replace(/&gt;/g, '>');
        text = text.replace(/&quot;/g, '"');
        text = text.replace(/&#39;/g, "'");
        text = text.replace(/\u200B/g, '');
        text = text.replace(/\u00A0/g, ' ');
        text = text.replace(/[\t ]+/g, ' ');
        text = text.replace(/^\s+|\s+$/g, '');
        out.value = text;
        TCTP.toast('Formatting removed.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'plain-text.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initRemoveUnderscores(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var mode = 'replace-space';
    ws.querySelectorAll('[data-und-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            mode = btn.dataset.undMode;
            ws.querySelectorAll('[data-und-mode]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
        });
    });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        if (mode === 'replace-space') out.value = inp.value.replace(/_/g, ' ');
        else if (mode === 'remove') out.value = inp.value.replace(/_/g, '');
        else if (mode === 'replace-hyphen') out.value = inp.value.replace(/_/g, '-');
        else out.value = inp.value.replace(/_/g, ' ');
        TCTP.toast('Underscores processed.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'text-no-underscores.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initWhitespaceRemover(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        var text = inp.value;
        text = text.replace(/\t/g, ' ');
        text = text.replace(/[ ]+/g, ' ');
        text = text.replace(/^\s+|\s+$/g, '');
        text = text.split('\n').map(function(l) { return l.trim(); }).join('\n');
        out.value = text;
        TCTP.toast('Whitespace cleaned.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'text-clean.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initPlainText(ws) { initRemoveFormatting(ws); }

function initDuplicateLine(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        var lines = inp.value.split('\n');
        var result = [];
        lines.forEach(function(line) { result.push(line); result.push(line); });
        out.value = result.join('\n');
        TCTP.toast('Each line duplicated.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'duplicated-lines.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initDuplicateWord(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        var words = inp.value.split(/\s+/);
        var result = [];
        words.forEach(function(w) { result.push(w); result.push(w); });
        out.value = result.join(' ');
        TCTP.toast('Each word duplicated.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'duplicated-words.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initEmDashRemover(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var mode = 'em-dash';
    ws.querySelectorAll('[data-dash-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            mode = btn.dataset.dashMode;
            ws.querySelectorAll('[data-dash-mode]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
        });
    });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        if (mode === 'em-dash') out.value = inp.value.replace(/\u2014/g, ' ').replace(/\u2013/g, ' ').replace(/\u2012/g, ' ').replace(/\u2011/g, ' ').replace(/\u2010/g, ' ');
        else if (mode === 'smart-quotes') out.value = inp.value.replace(/[\u2018\u2019\u201A\u201B\u2032\u2035]/g, "'").replace(/[\u201C\u201D\u201E\u201F\u2033\u2036]/g, '"');
        else if (mode === 'ellipsis') out.value = inp.value.replace(/\u2026/g, '...');
        else if (mode === 'all-special') {
            out.value = inp.value.replace(/\u2014/g, ' ').replace(/\u2013/g, '-').replace(/\u2026/g, '...');
            out.value = out.value.replace(/[\u2018\u2019]/g, "'").replace(/[\u201C\u201D]/g, '"');
        }
        TCTP.toast('Special characters replaced.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'cleaned-text.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}


function initWordFrequency(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var tableBody = e.$('table-body');
    if (!inp) return;
    var caseSensitive = false, ignoreCommon = true, stripPunct = true;
    var commonWords = ['the','be','to','of','and','a','in','that','have','i','it','for','not','on','with','he','as','you','do','at','this','but','his','by','from','they','we','say','her','she','or','an','will','my','one','all','would','there','their','what','so','up','out','if','about','who','get','which','go','me','when','make','can','like','time','no','just','him','know','take','people','into','year','your','good','some','could','them','see','other','than','then','now','look','only','come','its','over','think','also','back','after','use','two','how','our','work','first','well','way','even','new','want','because','any','these','give','day','most','us'];
    var csToggle = e.$('case-sensitive');
    var icToggle = e.$('ignore-common');
    var spToggle = e.$('strip-punct');
    if (csToggle) csToggle.addEventListener('click', function() { caseSensitive = !caseSensitive; csToggle.classList.toggle('sel', caseSensitive); });
    if (icToggle) icToggle.addEventListener('click', function() { ignoreCommon = !ignoreCommon; icToggle.classList.toggle('sel', ignoreCommon); });
    if (spToggle) spToggle.addEventListener('click', function() { stripPunct = !stripPunct; spToggle.classList.toggle('sel', stripPunct); });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        var text = inp.value;
        if (!text.trim()) { TCTP.toast('Please enter some text.'); return; }
        if (stripPunct) text = text.replace(/[^\w\s]/g, ' ');
        var words = text.split(/\s+/).filter(function(w) { return w.length > 0; });
        if (!caseSensitive) words = words.map(function(w) { return w.toLowerCase(); });
        var freq = {};
        words.forEach(function(w) { freq[w] = (freq[w] || 0) + 1; });
        var entries = Object.keys(freq).map(function(w) { return { word: w, count: freq[w] }; });
        if (ignoreCommon) entries = entries.filter(function(en) { return commonWords.indexOf(en.word) === -1; });
        entries.sort(function(a, b) { return b.count - a.count; });
        if (tableBody) {
            tableBody.innerHTML = '';
            var maxCount = entries.length > 0 ? entries[0].count : 1;
            entries.forEach(function(entry) {
                var pct = Math.round((entry.count / maxCount) * 100);
                var row = document.createElement('tr');
                row.innerHTML = '<td>' + entry.word + '</td><td>' + entry.count + '</td><td>' + pct + '%</td>';
                tableBody.appendChild(row);
            });
        }
        var totalEl = e.$('total-words');
        var uniqueEl = e.$('unique-words');
        var topEl = e.$('top-word');
        if (totalEl) totalEl.textContent = words.length;
        if (uniqueEl) uniqueEl.textContent = entries.length;
        if (topEl) topEl.textContent = entries.length > 0 ? entries[0].word : '-';
        TCTP.toast('Found ' + entries.length + ' unique words.');
    });
    var exportBtn = e.$('export');
    if (exportBtn) exportBtn.addEventListener('click', function() {
        var rows = [];
        if (tableBody) tableBody.querySelectorAll('tr').forEach(function(tr) {
            var cells = tr.querySelectorAll('td');
            if (cells.length >= 2) rows.push(cells[0].textContent + ',' + cells[1].textContent);
        });
        if (!rows.length) { TCTP.toast('Nothing to export.'); return; }
        TCTP.download('Word,Count\n' + rows.join('\n'), 'word-frequency.csv', 'text/csv');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() {
        var text = '';
        if (tableBody) tableBody.querySelectorAll('tr').forEach(function(tr) {
            var cells = tr.querySelectorAll('td');
            if (cells.length >= 2) text += cells[0].textContent + '\t' + cells[1].textContent + '\n';
        });
        TCTP.copy(text, copyBtn);
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() {
        inp.value = '';
        if (tableBody) tableBody.innerHTML = '';
        var t = e.$('total-words'), u = e.$('unique-words'), top = e.$('top-word');
        if (t) t.textContent = '0';
        if (u) u.textContent = '0';
        if (top) top.textContent = '-';
    });
}

function initSentenceCounter(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    if (!inp) return;
    function update() {
        var text = inp.value;
        var sentences = TCTP.countSentences(text);
        var words = TCTP.countWords(text);
        var chars = text.length;
        var charsNoSpace = text.replace(/\s/g, '').length;
        var paragraphs = text.trim() ? text.split(/\n\s*\n/).length : 0;
        var avgWords = sentences > 0 ? Math.round(words / sentences) : 0;
        var sSentences = e.$('sentences'), sWords = e.$('words'), sChars = e.$('characters');
        var sCharsNo = e.$('characters-no-space'), sParas = e.$('paragraphs'), sAvg = e.$('avg-words');
        if (sSentences) sSentences.textContent = sentences;
        if (sWords) sWords.textContent = words;
        if (sChars) sChars.textContent = chars;
        if (sCharsNo) sCharsNo.textContent = charsNoSpace;
        if (sParas) sParas.textContent = paragraphs;
        if (sAvg) sAvg.textContent = avgWords;
    }
    inp.addEventListener('input', update);
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        update();
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; update(); });
}

function initPigLatin(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    function toPigLatin(word) {
        if (!word.match(/[a-zA-Z]/)) return word;
        var vowels = 'aeiouAEIOU';
        if (vowels.indexOf(word[0]) !== -1) return word + 'yay';
        var consonants = '';
        for (var i = 0; i < word.length; i++) {
            if (vowels.indexOf(word[i]) !== -1) break;
            consonants += word[i];
        }
        return word.slice(consonants.length) + consonants.toLowerCase() + 'ay';
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        out.value = inp.value.replace(/\b[a-zA-Z]+\b/g, function(w) {
            var pig = toPigLatin(w);
            if (w[0] === w[0].toUpperCase()) return pig.charAt(0).toUpperCase() + pig.slice(1);
            return pig;
        });
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'pig-latin.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initNatoPhonetic(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var nato = {
        'A':'Alpha','B':'Bravo','C':'Charlie','D':'Delta','E':'Echo','F':'Foxtrot',
        'G':'Golf','H':'Hotel','I':'India','J':'Juliet','K':'Kilo','L':'Lima',
        'M':'Mike','N':'November','O':'Oscar','P':'Papa','Q':'Quebec','R':'Romeo',
        'S':'Sierra','T':'Tango','U':'Uniform','V':'Victor','W':'Whiskey','X':'X-ray',
        'Y':'Yankee','Z':'Zulu','0':'Zero','1':'One','2':'Two','3':'Three','4':'Four',
        '5':'Five','6':'Six','7':'Seven','8':'Eight','9':'Niner'
    };
    var mode = 'nato';
    ws.querySelectorAll('[data-phonetic-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            mode = btn.dataset.phoneticMode;
            ws.querySelectorAll('[data-phonetic-mode]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            if (inp.value) run();
        });
    });
    function run() {
        var text = inp.value;
        if (!text.trim()) { TCTP.toast('Please enter some text.'); return; }
        if (mode === 'nato') {
            out.value = text.split('').map(function(c) { var u = c.toUpperCase(); return nato[u] || c; }).join(' ');
        } else if (mode === 'reverse') {
            out.value = text.split(' ').map(function(w) {
                var key = Object.keys(nato).find(function(k) { return nato[k].toLowerCase() === w.toLowerCase(); });
                return key || w;
            }).join('');
        } else {
            out.value = text.split('').join(' ');
        }
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', run);
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'phonetic.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initPhoneticSpelling(ws) { initNatoPhonetic(ws); }

function initWingdings(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var wingdingsMap = {
        'A':'\u2708','B':'\u2623','C':'\u2620','D':'\u2694','E':'\u2625','F':'\u2638',
        'G':'\u262F','H':'\u2600','I':'\u2602','J':'\u2614','K':'\u2604','L':'\u2743',
        'M':'\u2761','N':'\u2764','O':'\u2766','P':'\u2739','Q':'\u2756','R':'\u2767',
        'S':'\u2710','T':'\u270E','U':'\u270F','V':'\u270D','W':'\u2709','X':'\u2762',
        'Y':'\u2742','Z':'\u2741','0':'\u2776','1':'\u2777','2':'\u2778','3':'\u2779',
        '4':'\u277A','5':'\u277B','6':'\u277C','7':'\u277D','8':'\u277E','9':'\u277F'
    };
    var direction = 'to-wingdings';
    ws.querySelectorAll('[data-wing-dir]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            direction = btn.dataset.wingDir;
            ws.querySelectorAll('[data-wing-dir]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
        });
    });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        if (direction === 'to-wingdings') {
            out.value = inp.value.toUpperCase().split('').map(function(c) { return wingdingsMap[c] || c; }).join('');
        } else {
            out.value = inp.value.split('').map(function(c) {
                var key = Object.keys(wingdingsMap).find(function(k) { return wingdingsMap[k] === c; });
                return key || c;
            }).join('');
        }
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'wingdings.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initRomanNumeral(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    function intToRoman(num) {
        if (num <= 0 || num > 39999) return 'Out of range';
        var vals = [1000,900,500,400,100,90,50,40,10,9,5,4,1];
        var syms = ['M','CM','D','CD','C','XC','L','XL','X','IX','V','IV','I'];
        var result = '';
        for (var i = 0; i < vals.length; i++) { while (num >= vals[i]) { result += syms[i]; num -= vals[i]; } }
        return result;
    }
    function romanToInt(str) {
        var map = { 'I':1,'V':5,'X':10,'L':50,'C':100,'D':500,'M':1000 };
        var result = 0;
        for (var i = 0; i < str.length; i++) {
            var cur = map[str[i]] || 0;
            var next = (i + 1 < str.length) ? (map[str[i+1]] || 0) : 0;
            if (cur < next) result -= cur; else result += cur;
        }
        return result;
    }
    var mode = 'to-roman';
    ws.querySelectorAll('[data-roman-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            mode = btn.dataset.romanMode;
            ws.querySelectorAll('[data-roman-mode]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
        });
    });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter a value.'); return; }
        if (mode === 'to-roman') {
            var num = parseInt(inp.value.trim(), 10);
            if (isNaN(num)) { TCTP.toast('Please enter a valid number.'); return; }
            out.value = intToRoman(num);
        } else {
            out.value = String(romanToInt(inp.value.trim().toUpperCase()));
        }
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initWordCloud(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var canvas = e.$('canvas');
    if (!inp || !canvas) return;
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        var words = inp.value.replace(/[^\w\s]/g, '').toLowerCase().split(/\s+/).filter(function(w) { return w.length > 1; });
        var freq = {};
        words.forEach(function(w) { freq[w] = (freq[w] || 0) + 1; });
        var entries = Object.keys(freq).map(function(w) { return { word: w, count: freq[w] }; });
        entries.sort(function(a, b) { return b.count - a.count; });
        entries = entries.slice(0, 60);
        if (entries.length === 0) { TCTP.toast('No words found.'); return; }
        var maxCount = entries[0].count, minCount = entries[entries.length - 1].count;
        var ctx = canvas.getContext('2d');
        var w = canvas.width = canvas.parentElement ? canvas.parentElement.clientWidth : 600;
        var h = canvas.height = 400;
        ctx.clearRect(0, 0, w, h);
        ctx.fillStyle = '#f8f9fa';
        ctx.fillRect(0, 0, w, h);
        var colors = ['#3b82f6','#ef4444','#10b981','#f59e0b','#8b5cf6','#ec4899','#06b6d4','#f97316'];
        var placed = [];
        entries.forEach(function(entry) {
            var size = minCount === maxCount ? 24 : 14 + Math.round(((entry.count - minCount) / (maxCount - minCount)) * 42);
            ctx.font = 'bold ' + size + 'px Arial';
            var textW = ctx.measureText(entry.word).width;
            var textH = size;
            var attempts = 0;
            while (attempts < 200) {
                var x = Math.random() * (w - textW - 20) + 10;
                var y = Math.random() * (h - textH - 10) + textH + 5;
                var overlap = false;
                for (var p = 0; p < placed.length; p++) {
                    if (x < placed[p].x + placed[p].w + 4 && x + textW + 4 > placed[p].x && y - textH < placed[p].y && y > placed[p].y - placed[p].h) {
                        overlap = true; break;
                    }
                }
                if (!overlap) { placed.push({ x: x, y: y, w: textW, h: textH }); break; }
                attempts++;
            }
            if (attempts < 200) {
                ctx.fillStyle = colors[Math.floor(Math.random() * colors.length)];
                ctx.fillText(entry.word, x, y);
            }
        });
        TCTP.toast('Word cloud generated!');
    });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        canvas.toBlob(function(blob) { if (blob) TCTP.download(blob, 'word-cloud.png'); else TCTP.toast('Nothing to download.'); });
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() {
        inp.value = '';
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    });
}

function initOnlineNotepad(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    if (!inp) return;
    var key = 'tctp-notepad-' + (ws.dataset.toolSlug || 'default');
    var saved = localStorage.getItem(key);
    if (saved) inp.value = saved;
    var autoSave = e.$('autosave');
    function save() {
        localStorage.setItem(key, inp.value);
        if (autoSave) { autoSave.textContent = 'Saved'; setTimeout(function() { autoSave.textContent = 'Auto-save'; }, 1500); }
    }
    var saveTimer = null;
    inp.addEventListener('input', function() { clearTimeout(saveTimer); saveTimer = setTimeout(save, 1000); });
    var saveBtn = e.$('save');
    if (saveBtn) saveBtn.addEventListener('click', save);
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(inp.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!inp.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(inp.value, 'notepad.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() {
        if (!confirm('Clear all text?')) return;
        inp.value = '';
        localStorage.removeItem(key);
    });
}

function initApaFormat(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        var text = inp.value;
        text = text.replace(/([.!?])\s+/g, '$1  ');
        text = text.replace(/\b([a-z])/g, function(m, c, offset) {
            if (offset === 0 || text[offset - 1] === '.' || text[offset - 1] === '!' || text[offset - 1] === '?') return c.toUpperCase();
            return c;
        });
        text = text.replace(/\bi\b/g, 'I');
        out.value = text;
        TCTP.toast('APA formatting applied.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'apa-formatted.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initInvisibleText(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var mode = 'zero-width';
    ws.querySelectorAll('[data-invis-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            mode = btn.dataset.invisMode;
            ws.querySelectorAll('[data-invis-mode]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
        });
    });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        var chars = inp.value.split('');
        var result = [];
        if (mode === 'zero-width') chars.forEach(function(c) { result.push(c + '\u200B'); });
        else if (mode === 'zwsp') chars.forEach(function(c) { result.push(c + '\u200C'); });
        else if (mode === 'variation') {
            var useFirst = true;
            chars.forEach(function(c) { result.push(c); result.push(useFirst ? '\uFE00' : '\uFE01'); useFirst = !useFirst; });
        }
        out.value = result.join('');
        TCTP.toast('Invisible characters inserted.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initAsciiArt(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var fontSelect = e.$('font');
    var fonts = {
        'block': function(s) { return s.toUpperCase(); },
        'shadow': function(s) { return '\u2584'.repeat(s.length) + '\n' + s.toUpperCase() + '\n\u2580'.repeat(s.length); },
        'banner': function(s) {
            var top = ' _' + s.split('').map(function() { return '__'; }).join('') + '_';
            var mid = '|' + s.toUpperCase().split('').map(function(c) { return c + ' |'; }).join('');
            var bot = '|' + s.split('').map(function() { return '__|'; }).join('');
            return top + '\n' + mid + '\n' + bot;
        },
        'simple': function(s) { return '> ' + s + ' <'; },
        'stars': function(s) { return '* ' + s.toUpperCase().split('').join(' ') + ' *'; },
        'dots': function(s) { return s.toUpperCase().split('').join('.'); },
        'boxed': function(s) {
            var inner = ' ' + s.toUpperCase() + ' ';
            var line = '\u2500'.repeat(inner.length);
            return '\u256C' + line + '\u2563\n\u2502' + inner + '\u2502\n\u2560' + line + '\u2566';
        }
    };
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Please enter some text.'); return; }
        var font = fontSelect ? fontSelect.value : 'block';
        out.value = (fonts[font] || fonts['block'])(inp.value);
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'ascii-art.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ════════════════════════════════════════════════════════════════
   FILE TOOLS — Drop zone + upload + process + download
   ════════════════════════════════════════════════════════════════ */

/* ── Generic file tool handler ───────────────────────────────── */
function initGenericFile(ws) {
    var e = TCTP.getEls(ws);
    var drop = e.$('drop');
    var fileInput = e.$('file');
    var barFill = e.$('bar-fill');
    var barText = e.$('bar-text');
    var barPct = e.$('bar-pct');
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    var statusEl = e.$('status');
    var selectedFile = null;

    if (drop && fileInput) {
        drop.addEventListener('click', function() { fileInput.click(); });
        drop.addEventListener('keydown', function(ev) {
            if (ev.key === 'Enter' || ev.key === ' ') { ev.preventDefault(); fileInput.click(); }
        });
        drop.addEventListener('dragover', function(ev) { ev.preventDefault(); drop.classList.add('hot'); });
        drop.addEventListener('dragleave', function() { drop.classList.remove('hot'); });
        drop.addEventListener('drop', function(ev) {
            ev.preventDefault(); drop.classList.remove('hot');
            if (ev.dataTransfer.files[0]) selectFile(ev.dataTransfer.files[0]);
        });
        fileInput.addEventListener('change', function() {
            if (fileInput.files[0]) selectFile(fileInput.files[0]);
        });
    }

    function selectFile(f) {
        selectedFile = f;
        var fileRow = e.$('file-row');
        var fname = e.$('fname');
        var fmeta = e.$('fmeta');
        if (fileRow) fileRow.classList.add('visible');
        if (drop) drop.style.display = 'none';
        if (fname) fname.textContent = f.name;
        if (fmeta) fmeta.textContent = TCTP.formatSize(f.size);
        if (runBtn) runBtn.disabled = false;
        if (statusEl) statusEl.textContent = 'Ready';
    }

    if (clearBtn) clearBtn.addEventListener('click', function() {
        selectedFile = null;
        var fileRow = e.$('file-row');
        if (fileRow) fileRow.classList.remove('visible');
        if (drop) drop.style.display = '';
        if (barFill) barFill.style.width = '0';
        if (barText) barText.textContent = 'Ready';
        if (barPct) barPct.textContent = '0%';
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
        if (statusEl) statusEl.textContent = 'Idle';
        if (fileInput) fileInput.value = '';
    });
}

/* ── PDF Compressor ─────────────────────────────────────────── */
function initPdfCompressor(ws) {
    var e = TCTP.getEls(ws);
    initGenericFile(ws);
    var drop = e.$('drop');
    var fileInput = e.$('file');
    var barFill = e.$('bar-fill');
    var barText = e.$('bar-text');
    var barPct = e.$('bar-pct');
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var statusEl = e.$('status');
    var selectedFile = null;
    var originalBytes = null;
    var compressedBlob = null;
    var pdfDoc = null;

    function selectFile(f) {
        if (!f || !/\.pdf$/i.test(f.name)) { TCTP.toast('Please select a PDF file.'); return; }
        selectedFile = f;
        var fileRow = e.$('file-row');
        var fname = e.$('fname');
        var fmeta = e.$('fmeta');
        if (fileRow) fileRow.classList.add('visible');
        if (drop) drop.style.display = 'none';
        if (fname) fname.textContent = f.name;
        if (fmeta) fmeta.textContent = TCTP.formatSize(f.size);
        if (runBtn) runBtn.disabled = false;
        if (statusEl) statusEl.textContent = 'Ready';
    }

    if (drop && fileInput) {
        drop.addEventListener('click', function() { fileInput.click(); });
        drop.addEventListener('dragover', function(ev) { ev.preventDefault(); drop.classList.add('hot'); });
        drop.addEventListener('dragleave', function() { drop.classList.remove('hot'); });
        drop.addEventListener('drop', function(ev) {
            ev.preventDefault(); drop.classList.remove('hot');
            if (ev.dataTransfer.files[0]) selectFile(ev.dataTransfer.files[0]);
        });
        fileInput.addEventListener('change', function() {
            if (fileInput.files[0]) selectFile(fileInput.files[0]);
        });
    }

    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!selectedFile) return;
        runBtn.disabled = true;
        runBtn.textContent = 'Compressing...';
        if (statusEl) statusEl.textContent = 'Working';
        try {
            await TCTP.ensurePdfJs();
            await TCTP.ensurePdfLib();
            originalBytes = new Uint8Array(await TCTP.readArrayBuffer(selectedFile));
            pdfDoc = await window.pdfjsLib.getDocument({ data: originalBytes.slice(0) }).promise;
            var level = 2;
            var selLevel = ws.querySelector('.tctp-toggle.sel');
            if (selLevel) {
                var l = selLevel.dataset.level || selLevel.dataset.l;
                if (l === 'light') level = 1;
                else if (l === 'strong') level = 3;
            }
            var structBytes = null;
            try {
                var loaded = await window.PDFLib.PDFDocument.load(originalBytes.slice(0), { ignoreEncryption: true });
                loaded.setTitle(''); loaded.setAuthor(''); loaded.setSubject('');
                loaded.setKeywords([]); loaded.setProducer('TextCraft'); loaded.setCreator('TextCraft');
                structBytes = await loaded.save({ useObjectStreams: true });
            } catch(err) { console.warn('Structure pass failed', err); }
            var visualBytes = null;
            if (level >= 2) {
                var output = await window.PDFLib.PDFDocument.create();
                var scale = level === 3 ? 0.9 : 1.05;
                var quality = level === 3 ? 0.42 : 0.58;
                for (var i = 1; i <= pdfDoc.numPages; i++) {
                    var page = await pdfDoc.getPage(i);
                    var viewport = page.getViewport({ scale: scale });
                    var canvas = document.createElement('canvas');
                    canvas.width = viewport.width; canvas.height = viewport.height;
                    var ctx = canvas.getContext('2d');
                    ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height);
                    await page.render({ canvasContext: ctx, viewport: viewport }).promise;
                    var jpgBytes = await new Promise(function(resolve) {
                        canvas.toBlob(function(blob) { blob.arrayBuffer().then(function(b) { resolve(new Uint8Array(b)); }); }, 'image/jpeg', quality);
                    });
                    var jpg = await output.embedJpg(jpgBytes);
                    var p = output.addPage([page.getViewport({scale:1}).width, page.getViewport({scale:1}).height]);
                    p.drawImage(jpg, { x:0, y:0, width: p.getWidth(), height: p.getHeight() });
                }
                visualBytes = await output.save({ useObjectStreams: true });
            }
            var bestBytes = null;
            var bestSize = originalBytes.byteLength;
            [structBytes, visualBytes].forEach(function(b) {
                if (b && b.byteLength < bestSize) { bestSize = b.byteLength; bestBytes = b; }
            });
            compressedBlob = bestBytes ? new Blob([bestBytes], { type: 'application/pdf' }) : new Blob([originalBytes], { type: 'application/pdf' });
            var saved = selectedFile.size - compressedBlob.size;
            var savedPct = Math.round((saved / selectedFile.size) * 100);
            var sOrig = ws.querySelector('[data-stat="original"] b');
            var sComp = ws.querySelector('[data-stat="compressed"] b');
            var sSaved = ws.querySelector('[data-stat="saved"] b');
            if (sOrig) sOrig.textContent = TCTP.formatSize(selectedFile.size);
            if (sComp) sComp.textContent = TCTP.formatSize(compressedBlob.size);
            if (sSaved) sSaved.textContent = savedPct + '%';
            if (barFill) barFill.style.width = '100%';
            if (barText) barText.textContent = 'Done!';
            if (barPct) barPct.textContent = '100%';
            if (statusEl) statusEl.textContent = 'Complete';
            if (dlBtn) dlBtn.disabled = false;
        } catch(err) {
            console.error(err);
            TCTP.toast('Compression failed: ' + err.message);
            if (statusEl) statusEl.textContent = 'Error';
        } finally {
            runBtn.disabled = false;
            runBtn.textContent = 'Compress PDF';
        }
    });
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!compressedBlob || !selectedFile) return;
        TCTP.download(compressedBlob, selectedFile.name.replace(/\.pdf$/i, '') + '-compressed.pdf', 'application/pdf');
    });
}

/* ── Image Converter (generic for all jpg/png/webp/etc conversions) ── */
function initImageConverter(ws, sourceMime, targetExt) {
    var e = TCTP.getEls(ws);
    var drop = e.$('drop');
    var fileInput = e.$('file');
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    var files = [];
    var results = [];
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');

    if (drop && fileInput) {
        drop.addEventListener('click', function() { fileInput.click(); });
        drop.addEventListener('dragover', function(ev) { ev.preventDefault(); drop.classList.add('hot'); });
        drop.addEventListener('dragleave', function() { drop.classList.remove('hot'); });
        drop.addEventListener('drop', function(ev) {
            ev.preventDefault(); drop.classList.remove('hot');
            addFiles(ev.dataTransfer.files);
        });
        fileInput.addEventListener('change', function() { addFiles(fileInput.files); fileInput.value = ''; });
    }
    function addFiles(list) {
        var valid = Array.from(list).filter(function(f) { return f.type.startsWith('image/'); }).slice(0, 20 - files.length);
        files = files.concat(valid);
        if (runBtn) runBtn.disabled = files.length === 0;
    }
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!files.length) return;
        runBtn.disabled = true; runBtn.textContent = 'Converting...';
        results = [];
        var mimeType = 'image/' + targetExt;
        if (targetExt === 'jpg') mimeType = 'image/jpeg';
        for (var i = 0; i < files.length; i++) {
            var dataUrl = await TCTP.readDataURL(files[i]);
            var img = await new Promise(function(resolve) {
                var im = new Image(); im.onload = function() { resolve(im); }; im.src = dataUrl;
            });
            canvas.width = img.width; canvas.height = img.height;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);
            var blob = await new Promise(function(resolve) { canvas.toBlob(function(b) { resolve(b); }, mimeType, 0.92); });
            results.push({ name: files[i].name.replace(/\.[^.]+$/, '') + '.' + targetExt, blob: blob });
        }
        runBtn.textContent = '\u2713 Done!';
        if (dlBtn) dlBtn.disabled = false;
        setTimeout(function() { runBtn.textContent = 'Convert All'; runBtn.disabled = false; }, 2000);
    });
    if (dlBtn) dlBtn.addEventListener('click', async function() {
        if (!results.length) return;
        if (results.length === 1) { TCTP.download(results[0].blob, results[0].name); return; }
        await TCTP.ensureJSZip();
        var zip = new JSZip();
        results.forEach(function(r) { zip.file(r.name, r.blob); });
        var blob = await zip.generateAsync({ type: 'blob' });
        TCTP.download(blob, 'converted-images.zip');
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        files = []; results = [];
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ── Image Compressor ───────────────────────────────────────── */
function initImageCompressor(ws, mime) {
    var e = TCTP.getEls(ws);
    var drop = e.$('drop');
    var fileInput = e.$('file');
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    var files = [];
    var results = [];
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');

    if (drop && fileInput) {
        drop.addEventListener('click', function() { fileInput.click(); });
        drop.addEventListener('dragover', function(ev) { ev.preventDefault(); drop.classList.add('hot'); });
        drop.addEventListener('dragleave', function() { drop.classList.remove('hot'); });
        drop.addEventListener('drop', function(ev) { ev.preventDefault(); drop.classList.remove('hot'); addFiles(ev.dataTransfer.files); });
        fileInput.addEventListener('change', function() { addFiles(fileInput.files); fileInput.value = ''; });
    }
    function addFiles(list) {
        var valid = Array.from(list).filter(function(f) { return f.type.startsWith('image/'); }).slice(0, 20 - files.length);
        files = files.concat(valid);
        if (runBtn) runBtn.disabled = files.length === 0;
    }
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!files.length) return;
        runBtn.disabled = true; runBtn.textContent = 'Compressing...';
        results = [];
        var quality = 0.7;
        var qualitySlider = ws.querySelector('input[type="range"]');
        if (qualitySlider) quality = parseInt(qualitySlider.value) / 100;
        for (var i = 0; i < files.length; i++) {
            var dataUrl = await TCTP.readDataURL(files[i]);
            var img = await new Promise(function(resolve) {
                var im = new Image(); im.onload = function() { resolve(im); }; im.src = dataUrl;
            });
            canvas.width = img.width; canvas.height = img.height;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);
            var blob = await new Promise(function(resolve) { canvas.toBlob(function(b) { resolve(b); }, 'image/' + mime, quality); });
            results.push({ name: files[i].name, origSize: files[i].size, newSize: blob.size, blob: blob });
        }
        runBtn.textContent = '\u2713 Done!';
        if (dlBtn) dlBtn.disabled = false;
        setTimeout(function() { runBtn.textContent = 'Compress'; runBtn.disabled = false; }, 2000);
    });
    if (dlBtn) dlBtn.addEventListener('click', async function() {
        if (!results.length) return;
        if (results.length === 1) { TCTP.download(results[0].blob, results[0].name); return; }
        await TCTP.ensureJSZip();
        var zip = new JSZip();
        results.forEach(function(r) { zip.file(r.name, r.blob); });
        var blob = await zip.generateAsync({ type: 'blob' });
        TCTP.download(blob, 'compressed-images.zip');
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        files = []; results = [];
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ════════════════════════════════════════════════════════════════
   GENERATOR TOOLS — Settings → Generate → Output
   ════════════════════════════════════════════════════════════════ */

/* ── Random Number Generator ────────────────────────────────── */
function initRandomNumber(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var runBtn = e.$('run');
    var copyBtn = e.$('copy');
    var clearBtn = e.$('clear');

    if (runBtn) runBtn.addEventListener('click', function() {
        var minEl = ws.querySelector('[data-opt="min"]');
        var maxEl = ws.querySelector('[data-opt="max"]');
        var countEl = ws.querySelector('[data-opt="count"]');
        var min = parseFloat(minEl ? minEl.value : 1);
        var max = parseFloat(maxEl ? maxEl.value : 100);
        var count = parseInt(countEl ? countEl.value : 10);
        if (isNaN(min) || isNaN(max) || min > max) { TCTP.toast('Invalid range.'); return; }
        var nums = [];
        for (var i = 0; i < count; i++) {
            nums.push(Math.floor(Math.random() * (max - min + 1)) + min);
        }
        out.value = nums.join('\n');
    });
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ── UUID Generator ─────────────────────────────────────────── */
function initUuidGenerator(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var runBtn = e.$('run');
    var copyBtn = e.$('copy');
    var clearBtn = e.$('clear');
    var hex2 = function(b) { return b.toString(16).padStart(2, '0'); };
    function genV4() {
        var b = new Uint8Array(16);
        crypto.getRandomValues(b);
        b[6] = (b[6] & 0x0f) | 0x40;
        b[8] = (b[8] & 0x3f) | 0x80;
        return [Array.from(b.slice(0,4)), Array.from(b.slice(4,6)), Array.from(b.slice(6,8)), Array.from(b.slice(8,10)), Array.from(b.slice(10,16))].map(function(s) { return s.map(hex2).join(''); }).join('-');
    }
    if (runBtn) runBtn.addEventListener('click', function() {
        var countEl = ws.querySelector('[data-opt="count"]');
        var count = Math.max(1, Math.min(1000, parseInt(countEl ? countEl.value : 10)));
        var ids = [];
        for (var i = 0; i < count; i++) ids.push(genV4());
        out.value = ids.join('\n');
    });
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ── Password Generator ─────────────────────────────────────── */
function initPasswordGenerator(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var runBtn = e.$('run');
    var copyBtn = e.$('copy');
    var clearBtn = e.$('clear');
    var CHARS = { upper: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', lower: 'abcdefghijklmnopqrstuvwxyz', numbers: '0123456789', symbols: '!@#$%^&*()-_=+[]{}|;:,.<>?' };

    function buildPool() {
        var pool = '';
        var upper = ws.querySelector('[data-opt="upper"]');
        var lower = ws.querySelector('[data-opt="lower"]');
        var nums = ws.querySelector('[data-opt="numbers"]');
        var syms = ws.querySelector('[data-opt="symbols"]');
        if (!upper || upper.checked) pool += CHARS.upper;
        if (!lower || lower.checked) pool += CHARS.lower;
        if (!nums || nums.checked) pool += CHARS.numbers;
        if (!syms || syms.checked) pool += CHARS.symbols;
        var seen = {}, deduped = '';
        pool.split('').forEach(function(c) { if (!seen[c]) { seen[c] = true; deduped += c; } });
        return deduped;
    }
    function randomChar(pool) {
        var arr = new Uint32Array(1);
        var limit = Math.floor(4294967296 / pool.length) * pool.length;
        var idx;
        do { crypto.getRandomValues(arr); idx = arr[0]; } while (idx >= limit);
        return pool[idx % pool.length];
    }
    if (runBtn) runBtn.addEventListener('click', function() {
        var pool = buildPool();
        if (!pool) { TCTP.toast('Select at least one character set.'); return; }
        var lenEl = ws.querySelector('[data-opt="length"]');
        var countEl = ws.querySelector('[data-opt="count"]');
        var length = parseInt(lenEl ? lenEl.value : 16);
        var count = Math.max(1, Math.min(100, parseInt(countEl ? countEl.value : 1)));
        var passwords = [];
        for (var i = 0; i < count; i++) {
            var pw = '';
            for (var j = 0; j < length; j++) pw += randomChar(pool);
            passwords.push(pw);
        }
        out.value = passwords.join('\n');
    });
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ── Random Date Generator ──────────────────────────────────── */
function initRandomDate(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var runBtn = e.$('run');
    var copyBtn = e.$('copy');
    var clearBtn = e.$('clear');

    if (runBtn) runBtn.addEventListener('click', function() {
        var startEl = ws.querySelector('[data-opt="start"]');
        var endEl = ws.querySelector('[data-opt="end"]');
        var countEl = ws.querySelector('[data-opt="count"]');
        var start = startEl ? new Date(startEl.value).getTime() : new Date('1970-01-01').getTime();
        var end = endEl ? new Date(endEl.value).getTime() : Date.now();
        var count = Math.max(1, Math.min(1000, parseInt(countEl ? countEl.value : 10)));
        if (start > end) { TCTP.toast('Start date must be before end date.'); return; }
        var msPerDay = 86400000;
        var totalDays = Math.floor((end - start) / msPerDay) + 1;
        var dates = [];
        for (var i = 0; i < count; i++) {
            var randMs = start + Math.floor(Math.random() * totalDays) * msPerDay;
            var d = new Date(randMs);
            dates.push(d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0'));
        }
        out.value = dates.join('\n');
    });
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ── Random Letter Generator ────────────────────────────────── */
function initRandomLetter(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var runBtn = e.$('run');
    var copyBtn = e.$('copy');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', function() {
        var countEl = ws.querySelector('[data-opt="count"]');
        var count = Math.max(1, Math.min(1000, parseInt(countEl ? countEl.value : 10)));
        var letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        var result = [];
        for (var i = 0; i < count; i++) result.push(letters[Math.floor(Math.random() * letters.length)]);
        out.value = result.join('\n');
    });
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ── Random Month Generator ─────────────────────────────────── */
function initRandomMonth(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var runBtn = e.$('run');
    var copyBtn = e.$('copy');
    var clearBtn = e.$('clear');
    var MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    if (runBtn) runBtn.addEventListener('click', function() {
        var countEl = ws.querySelector('[data-opt="count"]');
        var count = Math.max(1, Math.min(12, parseInt(countEl ? countEl.value : 3)));
        var months = [];
        for (var i = 0; i < count; i++) months.push(MONTHS[Math.floor(Math.random() * 12)]);
        out.value = months.join('\n');
    });
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ── Random Choice Generator ────────────────────────────────── */
function initRandomChoice(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    var inp = e.$('input');
    if (!out) return;
    var runBtn = e.$('run');
    var copyBtn = e.$('copy');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp || !inp.value.trim()) { TCTP.toast('Enter choices above.'); return; }
        var choices = inp.value.split('\n').map(function(s) { return s.trim(); }).filter(Boolean);
        var countEl = ws.querySelector('[data-opt="count"]');
        var count = Math.max(1, parseInt(countEl ? countEl.value : 1));
        var picks = [];
        for (var i = 0; i < count; i++) picks.push(choices[Math.floor(Math.random() * choices.length)]);
        out.value = picks.join('\n');
    });
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; if (inp) inp.value = ''; });
}

/* ── Random IP Generator ────────────────────────────────────── */
function initRandomIp(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var runBtn = e.$('run');
    var copyBtn = e.$('copy');
    var clearBtn = e.$('clear');
    function genIPv4() {
        return [0,1,2,3].map(function() { return Math.floor(Math.random() * 256); }).join('.');
    }
    if (runBtn) runBtn.addEventListener('click', function() {
        var countEl = ws.querySelector('[data-opt="count"]');
        var count = Math.max(1, Math.min(100, parseInt(countEl ? countEl.value : 10)));
        var ips = [];
        for (var i = 0; i < count; i++) ips.push(genIPv4());
        out.value = ips.join('\n');
    });
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ════════════════════════════════════════════════════════════════
   TEXT TOOL IMPLEMENTATIONS (continued)
   ════════════════════════════════════════════════════════════════ */

/* ── Word Counter ────────────────────────────────────────────── */
function initWordCounter(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    if (!inp) return;
    function update() {
        var text = inp.value;
        var words = text.trim() ? text.trim().split(/\s+/).length : 0;
        var chars = text.length;
        var charsNo = text.replace(/\s/g, '').length;
        var sentences = (text.match(/[.!?]+/g) || []).length;
        var lines = text ? text.split('\n').length : 0;
        var paragraphs = text.trim() ? text.split(/\n\s*\n/).length : 0;
        var readMin = Math.ceil(words / 200);
        var speakMin = Math.ceil(words / 130);
        var set = function(id, v) { var el = e.$(id); if (el) el.textContent = v; };
        set('words', words); set('characters', chars); set('characters-no-space', charsNo);
        set('sentences', sentences); set('lines', lines); set('paragraphs', paragraphs);
        set('read-time', readMin + ' min'); set('speak-time', speakMin + ' min');
    }
    inp.addEventListener('input', update);
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; update(); });
}

/* ── Text Diff ───────────────────────────────────────────────── */
function initTextDiff(ws) {
    var e = TCTP.getEls(ws);
    var inpA = e.$('input-a');
    var inpB = e.$('input-b');
    var out = e.$('output');
    if (!inpA || !inpB || !out) return;
    function diff(a, b) {
        var linesA = a.split('\n'), linesB = b.split('\n');
        var result = [];
        var maxLen = Math.max(linesA.length, linesB.length);
        for (var i = 0; i < maxLen; i++) {
            var aLine = i < linesA.length ? linesA[i] : undefined;
            var bLine = i < linesB.length ? linesB[i] : undefined;
            if (aLine === undefined) result.push('+ ' + bLine);
            else if (bLine === undefined) result.push('- ' + aLine);
            else if (aLine !== bLine) { result.push('- ' + aLine); result.push('+ ' + bLine); }
            else result.push('  ' + aLine);
        }
        return result.join('\n');
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inpA.value && !inpB.value) { TCTP.toast('Enter text in both fields.'); return; }
        out.value = diff(inpA.value, inpB.value);
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inpA.value = ''; inpB.value = ''; out.value = ''; });
}

/* ── Remove Duplicates (deduplicate lines) ───────────────────── */
function initRemoveDuplicates(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var caseInsensitive = false, trimLines = true, removeEmpty = true;
    var csToggle = e.$('case-insensitive');
    var trimToggle = e.$('trim');
    var emptyToggle = e.$('remove-empty');
    if (csToggle) csToggle.addEventListener('click', function() { caseInsensitive = !caseInsensitive; csToggle.classList.toggle('sel', caseInsensitive); });
    if (trimToggle) trimToggle.addEventListener('click', function() { trimLines = !trimLines; trimToggle.classList.toggle('sel', trimLines); });
    if (emptyToggle) emptyToggle.addEventListener('click', function() { removeEmpty = !removeEmpty; emptyToggle.classList.toggle('sel', removeEmpty); });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter some text.'); return; }
        var lines = inp.value.split('\n');
        var seen = {};
        var result = [];
        lines.forEach(function(line) {
            var key = trimLines ? line.trim() : line;
            if (removeEmpty && !key) return;
            var compareKey = caseInsensitive ? key.toLowerCase() : key;
            if (!seen[compareKey]) { seen[compareKey] = true; result.push(line); }
        });
        out.value = result.join('\n');
        var removed = lines.length - result.length;
        TCTP.toast('Removed ' + removed + ' duplicate line' + (removed !== 1 ? 's' : '') + '.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'deduplicated.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── Camel Case ──────────────────────────────────────────────── */
function initCamelCase(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var mode = 'camel';
    ws.querySelectorAll('[data-case-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            mode = btn.dataset.caseMode;
            ws.querySelectorAll('[data-case-mode]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            if (inp.value) convert();
        });
    });
    function toWords(s) { return s.replace(/[^a-zA-Z0-9]+/g, ' ').trim().split(/\s+/); }
    function convert() {
        var words = toWords(inp.value);
        if (!words.length || !words[0]) { out.value = ''; return; }
        if (mode === 'camel') {
            out.value = words[0].toLowerCase() + words.slice(1).map(function(w) { return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase(); }).join('');
        } else if (mode === 'pascal') {
            out.value = words.map(function(w) { return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase(); }).join('');
        } else if (mode === 'snake') {
            out.value = words.map(function(w) { return w.toLowerCase(); }).join('_');
        } else if (mode === 'kebab') {
            out.value = words.map(function(w) { return w.toLowerCase(); }).join('-');
        } else if (mode === 'dot') {
            out.value = words.map(function(w) { return w.toLowerCase(); }).join('.');
        } else if (mode === 'path') {
            out.value = words.map(function(w) { return w.toLowerCase(); }).join('/');
        } else if (mode === 'const') {
            out.value = words.map(function(w) { return w.toUpperCase(); }).join('_');
        }
    }
    inp.addEventListener('input', function() { if (mode) convert(); });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter some text.'); return; }
        convert();
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── Kebab Case (alias) ─────────────────────────────────────── */
function initKebabCase(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    function convert() {
        var words = inp.value.replace(/[^a-zA-Z0-9]+/g, ' ').trim().split(/\s+/);
        out.value = words.map(function(w) { return w.toLowerCase(); }).join('-');
    }
    inp.addEventListener('input', function() { if (inp.value) convert(); });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter some text.'); return; }
        convert();
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── Snake Case ──────────────────────────────────────────────── */
function initSnakeCase(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    function convert() {
        var words = inp.value.replace(/[^a-zA-Z0-9]+/g, ' ').trim().split(/\s+/);
        out.value = words.map(function(w) { return w.toLowerCase(); }).join('_');
    }
    inp.addEventListener('input', function() { if (inp.value) convert(); });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter some text.'); return; }
        convert();
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── Text to ASCII Art ───────────────────────────────────────── */
function initTextToAscii(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var font = 'standard';
    ws.querySelectorAll('[data-ascii-font]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            font = btn.dataset.asciiFont;
            ws.querySelectorAll('[data-ascii-font]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            if (inp.value) generate();
        });
    });
    var FONT = {
        standard: { 'A':['  A  ',' A A ','AAAAA','A   A','A   A'], 'B':['BBBB ','B   B','BBBB ','B   B','BBBB '], 'C':[' CCCC','C    ','C    ','C    ',' CCCC'], 'D':['DDDD ','D   D','D   D','D   D','DDDD '], 'E':['EEEEE','E    ','EEE  ','E   ','EEEEE'], 'F':['FFFFF','F    ','FFF  ','F    ','F    '], 'G':[' GGGG','G    ','G  GG','G   G',' GGGG'], 'H':['H   H','H   H','HHHHH','H   H','H   H'], 'I':['IIIII','  I  ','  I  ','  I  ','IIIII'], 'J':['  JJJ','    J','    J','J   J',' JJJ '], 'K':['K   K','K  K ','KKK  ','K  K ','K   K'], 'L':['L    ','L    ','L    ','L    ','LLLLL'], 'M':['M   M','MM MM','M M M','M   M','M   M'], 'N':['N   N','NN  N','N N N','N  NN','N   N'], 'O':[' OOO ','O   O','O   O','O   O',' OOO '], 'P':['PPPP ','P   P','PPPP ','P    ','P    '], 'Q':[' QQQ ','Q   Q','Q   Q','Q  Q ',' QQ Q'], 'R':['RRRR ','R   R','RRRR ','R  R ','R   R'], 'S':[' SSSS','S    ',' SSS ','    S','SSSS '], 'T':['TTTTT','  T  ','  T  ','  T  ','  T  '], 'U':['U   U','U   U','U   U','U   U',' UUU '], 'V':['V   V','V   V','V   V',' V V ','  V  '], 'W':['W   W','W   W','W M W','WM MW','W   W'], 'X':['X   X',' X X ','  X  ',' X X ','X   X'], 'Y':['Y   Y',' Y Y ','  Y  ','  Y  ','  Y  '], 'Z':['ZZZZZ','   Z ','  Z  ',' Z   ','ZZZZZ'], '0':[' 000 ','0   0','0   0','0   0',' 000 '], '1':['  1  ',' 11  ','  1  ','  1  ','11111'], '2':[' 222 ','2   2','   2 ','  2  ','22222'], '3':[' 333 ','    3','  33 ','    3',' 333 '], '4':['   4 ','  4  ',' 444 ','    4','    4'], '5':['55555','5    ','555  ','    5','555  '], '6':[' 666 ','6    ','666  ','6   6',' 666 '], '7':['77777','   7 ','  7  ',' 7   ','7    '], '8':[' 888 ','8   8',' 888 ','8   8',' 888 '], '9':[' 999 ','9   9',' 9999','    9',' 999 '], ' ':['     ','     ','     ','     ','     '], '.':['     ','     ','     ','     ','  .  '], ',':['     ','     ','     ','  .  ',' .   '], '!':['  !  ','  !  ','  !  ','     ','  !  '], '?':['  ?  ',' ? ? ','  ?  ','     ','  ?  '], '-':['     ','     ','-----','     ','     '], ':':['     ','  .  ','     ','  .  ','     '], "'":['  .  ',' .   ','     ','     ','     '], '"':[' . . ',' . . ','     ','     ','     '], '/':['     ','    /','   / ','  /  ',' /   '], '@':[' @@@ ','@   @','@ @ @','@   ',' @@@ '], '#':['# # #','#####','# # #','#####','# # #'] } };
    function generate() {
        var text = inp.value.toUpperCase().substring(0, 12);
        if (!text) { out.value = ''; return; }
        var lines = ['', '', '', '', ''];
        for (var c = 0; c < text.length; c++) {
            var charLines = FONT[text[c]] || FONT[' '];
            for (var l = 0; l < 5; l++) lines[l] += (charLines[l] || '     ') + ' ';
        }
        out.value = lines.join('\n');
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter some text.'); return; }
        generate();
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'ascii-art.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── Text Formatter (strip HTML, normalize whitespace) ───────── */
function initTextFormatter(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter some text.'); return; }
        var text = inp.value;
        text = text.replace(/<[^>]+>/g, ' ');
        text = text.replace(/&nbsp;/g, ' ');
        text = text.replace(/&amp;/g, '&');
        text = text.replace(/&lt;/g, '<');
        text = text.replace(/&gt;/g, '>');
        text = text.replace(/&quot;/g, '"');
        text = text.replace(/&#39;/g, "'");
        text = text.replace(/\u200B/g, '');
        text = text.replace(/\t/g, '    ');
        text = text.replace(/[ ]+/g, ' ');
        text = text.replace(/\n\s*\n\s*\n/g, '\n\n');
        text = text.replace(/^\s+|\s+$/g, '');
        out.value = text;
        TCTP.toast('Text formatted.');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'formatted.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ════════════════════════════════════════════════════════════════
   DEVELOPER TOOL IMPLEMENTATIONS
   ════════════════════════════════════════════════════════════════ */

/* ── JSON Formatter ──────────────────────────────────────────── */
function initJsonFormatter(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var indentSel = e.$('indent');
    var sortChk = e.$('sort-keys');
    function beautify() {
        try {
            var obj = JSON.parse(inp.value);
            if (sortChk && sortChk.checked) obj = sortKeys(obj);
            var indent = indentSel ? (indentSel.value === 'tab' ? '\t' : parseInt(indentSel.value) || 2) : 2;
            out.value = JSON.stringify(obj, null, indent);
            var statusEl = e.$('status');
            if (statusEl) { statusEl.textContent = 'Valid JSON'; statusEl.style.color = '#10b981'; }
        } catch(err) {
            out.value = '';
            var statusEl = e.$('status');
            if (statusEl) { statusEl.textContent = 'Error: ' + err.message; statusEl.style.color = '#ef4444'; }
        }
    }
    function sortKeys(obj) {
        if (Array.isArray(obj)) return obj.map(sortKeys);
        if (obj && typeof obj === 'object') {
            var sorted = {};
            Object.keys(obj).sort().forEach(function(k) { sorted[k] = sortKeys(obj[k]); });
            return sorted;
        }
        return obj;
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', beautify);
    var minifyBtn = e.$('minify');
    if (minifyBtn) minifyBtn.addEventListener('click', function() {
        try {
            var obj = JSON.parse(inp.value);
            out.value = JSON.stringify(obj);
        } catch(err) { TCTP.toast('Invalid JSON: ' + err.message); }
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'formatted.json', 'application/json');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── XML Formatter ───────────────────────────────────────────── */
function initXmlFormatter(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    function formatXml(xml) {
        var pad = 0;
        var result = '';
        xml.replace(/(>)(<)(\/*)/g, '$1\n$2$3').split('\n').forEach(function(node) {
            if (node.match(/^<\/\w/)) pad--;
            result += '  '.repeat(Math.max(0, pad)) + node.trim() + '\n';
            if (node.match(/^<\w[^>]*[^\/]>.*$/) && !node.match(/^<\w[^>]*\/>/)) pad++;
        });
        return result.trim();
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter XML.'); return; }
        try { out.value = formatXml(inp.value); }
        catch(err) { TCTP.toast('Error formatting XML.'); }
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── HTML Formatter ──────────────────────────────────────────── */
function initHtmlFormatter(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    function formatHtml(html) {
        var pad = 0;
        var formatted = '';
        html = html.replace(/>\s*</g, '><');
        html.replace(/(<[^/][^>]*[^\/]>|<[^>]+\/>)/g, function(tag, p1, offset) {
            var before = html.substring(0, offset + tag.length);
            var selfClosing = tag.match(/\/>$/);
            var closing = tag.match(/^<\//);
            if (closing) pad--;
            formatted += '  '.repeat(Math.max(0, pad)) + tag + '\n';
            if (!closing && !selfClosing) pad++;
        });
        return formatted.trim();
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter HTML.'); return; }
        try { out.value = formatHtml(inp.value); }
        catch(err) { TCTP.toast('Error formatting HTML.'); }
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── CSS Formatter ───────────────────────────────────────────── */
function initCssFormatter(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    function formatCss(css) {
        var formatted = '';
        var pad = 0;
        css = css.replace(/\s+/g, ' ').replace(/\s*{\s*/g, ' {\n').replace(/\s*}\s*/g, '\n}\n').replace(/;\s*/g, ';\n');
        css.split('\n').forEach(function(line) {
            line = line.trim();
            if (!line) return;
            if (line === '}') pad--;
            formatted += '  '.repeat(Math.max(0, pad)) + line + '\n';
            if (line.endsWith('{')) pad++;
        });
        return formatted.trim();
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter CSS.'); return; }
        out.value = formatCss(inp.value);
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── JS Formatter ────────────────────────────────────────────── */
function initJsFormatter(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    function formatJs(js) {
        var formatted = '';
        var pad = 0;
        js = js.replace(/\s*{\s*/g, ' {\n').replace(/\s*}\s*/g, '\n}\n').replace(/;\s*/g, ';\n');
        js.split('\n').forEach(function(line) {
            line = line.trim();
            if (!line) return;
            if (line.match(/^}/)) pad--;
            formatted += '  '.repeat(Math.max(0, pad)) + line + '\n';
            if (line.match(/\{$/) && !line.match(/\/\*/)) pad++;
        });
        return formatted.trim();
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter JavaScript.'); return; }
        out.value = formatJs(inp.value);
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── Base64 Encode / Decode ──────────────────────────────────── */
function initBase64Encode(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter text.'); return; }
        try { out.value = btoa(unescape(encodeURIComponent(inp.value))); }
        catch(err) { TCTP.toast('Encoding error.'); }
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initBase64Decode(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter Base64.'); return; }
        try { out.value = decodeURIComponent(escape(atob(inp.value.trim()))); }
        catch(err) { TCTP.toast('Invalid Base64 string.'); }
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── URL Encode / Decode ─────────────────────────────────────── */
function initUrlEncode(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter text.'); return; }
        out.value = encodeURIComponent(inp.value);
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

function initUrlDecode(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        if (!inp.value.trim()) { TCTP.toast('Enter encoded URL.'); return; }
        try { out.value = decodeURIComponent(inp.value); }
        catch(err) { TCTP.toast('Invalid URL-encoded string.'); }
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── Hash Generator ──────────────────────────────────────────── */
function initHashGenerator(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var out = e.$('output');
    if (!inp || !out) return;
    var algo = 'SHA-256';
    ws.querySelectorAll('[data-algo]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            algo = btn.dataset.algo;
            ws.querySelectorAll('[data-algo]').forEach(function(b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
        });
    });
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!inp.value.trim()) { TCTP.toast('Enter text.'); return; }
        try {
            var encoder = new TextEncoder();
            var data = encoder.encode(inp.value);
            var hashBuffer = await crypto.subtle.digest(algo, data);
            var hashArray = Array.from(new Uint8Array(hashBuffer));
            out.value = hashArray.map(function(b) { return b.toString(16).padStart(2, '0'); }).join('');
        } catch(err) { TCTP.toast('Hash error: ' + err.message); }
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; out.value = ''; });
}

/* ── Regex Tester ────────────────────────────────────────────── */
function initRegexTester(ws) {
    var e = TCTP.getEls(ws);
    var inp = e.$('input');
    var patternEl = e.$('pattern');
    var out = e.$('output');
    if (!inp || !patternEl || !out) return;
    var flagsEl = e.$('flags');
    function test() {
        var pattern = patternEl.value;
        var flags = flagsEl ? flagsEl.value : 'g';
        if (!pattern || !inp.value) { out.innerHTML = ''; return; }
        try {
            var regex = new RegExp(pattern, flags);
            var matches = inp.value.match(regex);
            if (!matches) { out.innerHTML = '<p class="tctp-muted">No matches found.</p>'; return; }
            var html = '<p><strong>' + matches.length + ' match' + (matches.length !== 1 ? 'es' : '') + '</strong></p><ul>';
            var seen = {};
            matches.forEach(function(m) {
                var key = m + '_' + (seen[m] || 0);
                seen[m] = (seen[m] || 0) + 1;
                html += '<li><code>' + m.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code></li>';
            });
            html += '</ul>';
            out.innerHTML = html;
        } catch(err) {
            out.innerHTML = '<p style="color:#ef4444">Invalid regex: ' + err.message + '</p>';
        }
    }
    patternEl.addEventListener('input', test);
    inp.addEventListener('input', test);
    if (flagsEl) flagsEl.addEventListener('input', test);
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { inp.value = ''; patternEl.value = ''; out.innerHTML = ''; });
}

/* ── Lorem Ipsum Generator ───────────────────────────────────── */
function initLoremIpsum(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var words = 'lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ut enim ad minim veniam quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur excepteur sint occaecat cupidatat non proident sunt in culpa qui officia deserunt mollit anim id est laborum'.split(' ');
    function genSentence() {
        var len = 8 + Math.floor(Math.random() * 15);
        var result = [];
        for (var i = 0; i < len; i++) result.push(words[Math.floor(Math.random() * words.length)]);
        var s = result.join(' ');
        return s.charAt(0).toUpperCase() + s.slice(1) + '.';
    }
    function genParagraph() {
        var len = 4 + Math.floor(Math.random() * 6);
        var sentences = [];
        for (var i = 0; i < len; i++) sentences.push(genSentence());
        return sentences.join(' ');
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        var countEl = ws.querySelector('[data-opt="count"]');
        var typeEl = ws.querySelector('[data-opt="type"]');
        var count = Math.max(1, Math.min(50, parseInt(countEl ? countEl.value : 3)));
        var type = typeEl ? typeEl.value : 'paragraphs';
        var result = [];
        for (var i = 0; i < count; i++) {
            if (type === 'paragraphs') result.push(genParagraph());
            else if (type === 'sentences') result.push(genSentence());
            else result.push(words[Math.floor(Math.random() * words.length)]);
        }
        out.value = result.join(type === 'paragraphs' ? '\n\n' : ' ');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var dlBtn = e.$('download');
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (!out.value) { TCTP.toast('Nothing to download.'); return; }
        TCTP.download(out.value, 'lorem-ipsum.txt');
    });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ════════════════════════════════════════════════════════════════
   GENERATOR TOOL IMPLEMENTATIONS (continued)
   ════════════════════════════════════════════════════════════════ */

/* ── Random String Generator ─────────────────────────────────── */
function initRandomString(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        var lenEl = ws.querySelector('[data-opt="length"]');
        var countEl = ws.querySelector('[data-opt="count"]');
        var length = Math.max(1, Math.min(500, parseInt(lenEl ? lenEl.value : 16)));
        var count = Math.max(1, Math.min(100, parseInt(countEl ? countEl.value : 5)));
        var results = [];
        for (var i = 0; i < count; i++) {
            var s = '';
            for (var j = 0; j < length; j++) s += CHARS[Math.floor(Math.random() * CHARS.length)];
            results.push(s);
        }
        out.value = results.join('\n');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ── Random Name Generator ───────────────────────────────────── */
function initRandomName(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var firstNames = ['James','Mary','Robert','Patricia','John','Jennifer','Michael','Linda','David','Elizabeth','William','Barbara','Richard','Susan','Joseph','Jessica','Thomas','Sarah','Charles','Karen','Emma','Liam','Olivia','Noah','Ava','Sophia','Isabella','Mia','Charlotte','Amelia','Harper','Evelyn','Abigail','Emily','Ella','Madison','Avery','Sofia','Chloe','Ella'];
    var lastNames = ['Smith','Johnson','Williams','Brown','Jones','Garcia','Miller','Davis','Rodriguez','Martinez','Hernandez','Lopez','Gonzalez','Wilson','Anderson','Thomas','Taylor','Moore','Jackson','Martin','Lee','Perez','Thompson','White','Harris','Sanchez','Clark','Ramirez','Lewis','Robinson'];
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        var countEl = ws.querySelector('[data-opt="count"]');
        var count = Math.max(1, Math.min(100, parseInt(countEl ? countEl.value : 10)));
        var names = [];
        for (var i = 0; i < count; i++) {
            names.push(firstNames[Math.floor(Math.random() * firstNames.length)] + ' ' + lastNames[Math.floor(Math.random() * lastNames.length)]);
        }
        out.value = names.join('\n');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ── Random Address Generator ────────────────────────────────── */
function initRandomAddress(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var streets = ['Main St','Oak Ave','Pine Rd','Maple Dr','Cedar Ln','Elm St','Walnut Ave','1st Ave','2nd Ave','3rd Ave','Park Blvd','Lake Dr','Hill Rd','River Ln','Sunset Blvd'];
    var cities = ['New York','Los Angeles','Chicago','Houston','Phoenix','Philadelphia','San Antonio','San Diego','Dallas','San Jose','Austin','Jacksonville','Fort Worth','Columbus','Charlotte'];
    var states = ['NY','CA','IL','TX','AZ','PA','TX','CA','TX','CA','TX','FL','TX','OH','NC'];
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        var countEl = ws.querySelector('[data-opt="count"]');
        var count = Math.max(1, Math.min(50, parseInt(countEl ? countEl.value : 5)));
        var addresses = [];
        for (var i = 0; i < count; i++) {
            var idx = Math.floor(Math.random() * streets.length);
            addresses.push(Math.floor(Math.random() * 9999 + 1) + ' ' + streets[idx] + '\n' + cities[idx] + ', ' + states[idx] + ' ' + String(Math.floor(Math.random() * 90000 + 10000)));
        }
        out.value = addresses.join('\n\n');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ── Random Email Generator ──────────────────────────────────── */
function initRandomEmail(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    var domains = ['gmail.com','yahoo.com','outlook.com','hotmail.com','example.com','mail.com','protonmail.com','icloud.com','aol.com','zoho.com'];
    var adjectives = ['happy','cool','fast','smart','lucky','brave','calm','eager','fair','kind','bold','calm','deep','epic','fierce','gold','hazy','iron','jolly','keen'];
    var nouns = ['cat','dog','bird','fish','bear','lion','wolf','fox','hawk','deer','storm','flame','frost','blade','stone','pixel','orbit','quest','raven','tiger'];
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        var countEl = ws.querySelector('[data-opt="count"]');
        var count = Math.max(1, Math.min(50, parseInt(countEl ? countEl.value : 10)));
        var emails = [];
        for (var i = 0; i < count; i++) {
            var adj = adjectives[Math.floor(Math.random() * adjectives.length)];
            var noun = nouns[Math.floor(Math.random() * nouns.length)];
            var num = Math.floor(Math.random() * 999);
            var domain = domains[Math.floor(Math.random() * domains.length)];
            emails.push(adj + noun + num + '@' + domain);
        }
        out.value = emails.join('\n');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ── Random Color Generator ──────────────────────────────────── */
function initRandomColor(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    function randomHex() { return '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'); }
    function hexToRgb(hex) {
        var r = parseInt(hex.slice(1,3), 16), g = parseInt(hex.slice(3,5), 16), b = parseInt(hex.slice(5,7), 16);
        return { r: r, g: g, b: b };
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        var countEl = ws.querySelector('[data-opt="count"]');
        var count = Math.max(1, Math.min(50, parseInt(countEl ? countEl.value : 10)));
        var colors = [];
        for (var i = 0; i < count; i++) {
            var hex = randomHex();
            var rgb = hexToRgb(hex);
            colors.push(hex.toUpperCase() + '  |  rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')');
        }
        out.value = colors.join('\n');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ── Random UUID (enhanced) ──────────────────────────────────── */
function initRandomUuid(ws) {
    var e = TCTP.getEls(ws);
    var out = e.$('output');
    if (!out) return;
    function genV4() {
        var b = new Uint8Array(16);
        crypto.getRandomValues(b);
        b[6] = (b[6] & 0x0f) | 0x40;
        b[8] = (b[8] & 0x3f) | 0x80;
        return Array.from(b).map(function(x) { return x.toString(16).padStart(2, '0'); }).join('').replace(/^(.{8})(.{4})(.{4})(.{4})(.{12})$/, '$1-$2-$3-$4-$5');
    }
    var runBtn = e.$('run');
    if (runBtn) runBtn.addEventListener('click', function() {
        var countEl = ws.querySelector('[data-opt="count"]');
        var count = Math.max(1, Math.min(1000, parseInt(countEl ? countEl.value : 10)));
        var ids = [];
        for (var i = 0; i < count; i++) ids.push(genV4());
        out.value = ids.join('\n');
    });
    var copyBtn = e.$('copy');
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    var clearBtn = e.$('clear');
    if (clearBtn) clearBtn.addEventListener('click', function() { out.value = ''; });
}

/* ════════════════════════════════════════════════════════════════
   PDF TOOL IMPLEMENTATIONS
   ════════════════════════════════════════════════════════════════ */

/* ── PDF Merger ──────────────────────────────────────────────── */
function initPdfMerger(ws) {
    var e = TCTP.getEls(ws);
    var drop = e.$('drop');
    var fileInput = e.$('file');
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    var files = [];
    var mergedBlob = null;

    TCTP.setupDropZone(ws, e, {
        multiple: true,
        onFiles: function(list) {
            var valid = Array.from(list).filter(function(f) { return f.name.toLowerCase().endsWith('.pdf'); });
            files = files.concat(valid);
            if (runBtn) runBtn.disabled = files.length < 2;
            var fileRow = e.$('file-row');
            var fname = e.$('fname');
            var fmeta = e.$('fmeta');
            if (fileRow) fileRow.classList.add('visible');
            if (drop) drop.style.display = 'none';
            if (fname) fname.textContent = files.length + ' PDF files';
            if (fmeta) fmeta.textContent = files.reduce(function(sum, f) { return sum + f.size; }, 0) > 0 ? TCTP.formatSize(files.reduce(function(sum, f) { return sum + f.size; }, 0)) : '';
        }
    });

    if (runBtn) runBtn.addEventListener('click', async function() {
        if (files.length < 2) { TCTP.toast('Select at least 2 PDF files.'); return; }
        runBtn.disabled = true; runBtn.textContent = 'Merging...';
        try {
            await TCTP.ensurePdfLib();
            var merged = await window.PDFLib.PDFDocument.create();
            for (var i = 0; i < files.length; i++) {
                var bytes = new Uint8Array(await TCTP.readArrayBuffer(files[i]));
                var doc = await window.PDFLib.PDFDocument.load(bytes);
                var copiedPages = await merged.copyPages(doc, doc.getPageIndices());
                copiedPages.forEach(function(page) { merged.addPage(page); });
            }
            var pdfBytes = await merged.save();
            mergedBlob = new Blob([pdfBytes], { type: 'application/pdf' });
            runBtn.textContent = '\u2713 Done!';
            if (dlBtn) dlBtn.disabled = false;
            TCTP.toast('PDFs merged successfully!');
            setTimeout(function() { runBtn.textContent = 'Merge PDFs'; runBtn.disabled = false; }, 2000);
        } catch(err) { TCTP.toast('Merge failed: ' + err.message); runBtn.textContent = 'Merge PDFs'; runBtn.disabled = false; }
    });
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (mergedBlob) TCTP.download(mergedBlob, 'merged.pdf', 'application/pdf');
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        files = []; mergedBlob = null;
        TCTP.resetDropZone(e);
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ── PDF Splitter ────────────────────────────────────────────── */
function initPdfSplitter(ws) {
    var e = TCTP.getEls(ws);
    var selectedFile = null;
    var splitResults = [];
    TCTP.setupDropZone(ws, e, {
        multiple: false,
        onFile: function(f) {
            selectedFile = f;
            TCTP.showFileRow(e, f.name, f.size);
            if (e.$('run')) e.$('run').disabled = false;
        }
    });
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!selectedFile) return;
        runBtn.disabled = true; runBtn.textContent = 'Splitting...';
        try {
            await TCTP.ensurePdfLib();
            var bytes = new Uint8Array(await TCTP.readArrayBuffer(selectedFile));
            var doc = await window.PDFLib.PDFDocument.load(bytes);
            var numPages = doc.getPageCount();
            var rangeEl = e.$('range');
            var range = rangeEl ? rangeEl.value.trim() : '1';
            var pageLists = [];
            range.split(',').forEach(function(part) {
                part = part.trim();
                if (part.indexOf('-') !== -1) {
                    var bounds = part.split('-').map(function(n) { return parseInt(n.trim()); });
                    var pages = [];
                    for (var p = bounds[0]; p <= Math.min(bounds[1], numPages); p++) pages.push(p - 1);
                    pageLists.push(pages);
                } else {
                    var idx = parseInt(part) - 1;
                    if (idx >= 0 && idx < numPages) pageLists.push([idx]);
                }
            });
            splitResults = [];
            for (var i = 0; i < pageLists.length; i++) {
                var newDoc = await window.PDFLib.PDFDocument.create();
                var copied = await newDoc.copyPages(doc, pageLists[i]);
                copied.forEach(function(p) { newDoc.addPage(p); });
                var pdfBytes = await newDoc.save();
                splitResults.push({ name: 'split-part-' + (i + 1) + '.pdf', blob: new Blob([pdfBytes], { type: 'application/pdf' }) });
            }
            runBtn.textContent = '\u2713 Done!';
            if (dlBtn) dlBtn.disabled = false;
            TCTP.toast('PDF split into ' + splitResults.length + ' parts.');
            setTimeout(function() { runBtn.textContent = 'Split PDF'; runBtn.disabled = false; }, 2000);
        } catch(err) { TCTP.toast('Split failed: ' + err.message); runBtn.textContent = 'Split PDF'; runBtn.disabled = false; }
    });
    if (dlBtn) dlBtn.addEventListener('click', async function() {
        if (!splitResults.length) return;
        if (splitResults.length === 1) { TCTP.download(splitResults[0].blob, splitResults[0].name, 'application/pdf'); return; }
        await TCTP.ensureJSZip();
        var zip = new JSZip();
        splitResults.forEach(function(r) { zip.file(r.name, r.blob); });
        var blob = await zip.generateAsync({ type: 'blob' });
        TCTP.download(blob, 'split-pdfs.zip');
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        selectedFile = null; splitResults = [];
        TCTP.resetDropZone(e);
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ── PDF to Text ─────────────────────────────────────────────── */
function initPdfToText(ws) {
    var e = TCTP.getEls(ws);
    var selectedFile = null;
    var out = e.$('output');
    TCTP.setupDropZone(ws, e, {
        multiple: false,
        onFile: function(f) {
            selectedFile = f;
            TCTP.showFileRow(e, f.name, f.size);
            if (e.$('run')) e.$('run').disabled = false;
        }
    });
    var runBtn = e.$('run');
    var copyBtn = e.$('copy');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!selectedFile || !out) return;
        runBtn.disabled = true; runBtn.textContent = 'Extracting...';
        try {
            await TCTP.ensurePdfJs();
            var bytes = new Uint8Array(await TCTP.readArrayBuffer(selectedFile));
            var doc = await window.pdfjsLib.getDocument({ data: bytes }).promise;
            var textParts = [];
            for (var i = 1; i <= doc.numPages; i++) {
                var page = await doc.getPage(i);
                var content = await page.getTextContent();
                var pageText = content.items.map(function(item) { return item.str; }).join(' ');
                textParts.push('--- Page ' + i + ' ---\n' + pageText);
            }
            out.value = textParts.join('\n\n');
            runBtn.textContent = '\u2713 Done!';
            TCTP.toast('Text extracted from ' + doc.numPages + ' pages.');
            setTimeout(function() { runBtn.textContent = 'Extract Text'; runBtn.disabled = false; }, 2000);
        } catch(err) { TCTP.toast('Extraction failed: ' + err.message); runBtn.textContent = 'Extract Text'; runBtn.disabled = false; }
    });
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        selectedFile = null;
        TCTP.resetDropZone(e);
        if (out) out.value = '';
        if (runBtn) runBtn.disabled = true;
    });
}

/* ── PDF to Image ────────────────────────────────────────────── */
function initPdfToImage(ws) {
    var e = TCTP.getEls(ws);
    var selectedFile = null;
    var results = [];
    TCTP.setupDropZone(ws, e, {
        multiple: false,
        onFile: function(f) {
            selectedFile = f;
            TCTP.showFileRow(e, f.name, f.size);
            if (e.$('run')) e.$('run').disabled = false;
        }
    });
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!selectedFile) return;
        runBtn.disabled = true; runBtn.textContent = 'Converting...';
        try {
            await TCTP.ensurePdfJs();
            var bytes = new Uint8Array(await TCTP.readArrayBuffer(selectedFile));
            var doc = await window.pdfjsLib.getDocument({ data: bytes }).promise;
            results = [];
            for (var i = 1; i <= doc.numPages; i++) {
                var page = await doc.getPage(i);
                var viewport = page.getViewport({ scale: 2.0 });
                var canvas = document.createElement('canvas');
                canvas.width = viewport.width; canvas.height = viewport.height;
                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height);
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;
                var blob = await new Promise(function(resolve) { canvas.toBlob(function(b) { resolve(b); }, 'image/png'); });
                results.push({ name: 'page-' + i + '.png', blob: blob });
            }
            runBtn.textContent = '\u2713 Done!';
            if (dlBtn) dlBtn.disabled = false;
            TCTP.toast('Converted ' + results.length + ' pages to images.');
            setTimeout(function() { runBtn.textContent = 'Convert to Images'; runBtn.disabled = false; }, 2000);
        } catch(err) { TCTP.toast('Conversion failed: ' + err.message); runBtn.textContent = 'Convert to Images'; runBtn.disabled = false; }
    });
    if (dlBtn) dlBtn.addEventListener('click', async function() {
        if (!results.length) return;
        if (results.length === 1) { TCTP.download(results[0].blob, results[0].name); return; }
        await TCTP.ensureJSZip();
        var zip = new JSZip();
        results.forEach(function(r) { zip.file(r.name, r.blob); });
        var blob = await zip.generateAsync({ type: 'blob' });
        TCTP.download(blob, 'pdf-images.zip');
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        selectedFile = null; results = [];
        TCTP.resetDropZone(e);
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ── PDF to HTML ─────────────────────────────────────────────── */
function initPdfToHtml(ws) {
    var e = TCTP.getEls(ws);
    var selectedFile = null;
    var out = e.$('output');
    TCTP.setupDropZone(ws, e, {
        multiple: false,
        onFile: function(f) {
            selectedFile = f;
            TCTP.showFileRow(e, f.name, f.size);
            if (e.$('run')) e.$('run').disabled = false;
        }
    });
    var runBtn = e.$('run');
    var copyBtn = e.$('copy');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!selectedFile || !out) return;
        runBtn.disabled = true; runBtn.textContent = 'Converting...';
        try {
            await TCTP.ensurePdfJs();
            var bytes = new Uint8Array(await TCTP.readArrayBuffer(selectedFile));
            var doc = await window.pdfjsLib.getDocument({ data: bytes }).promise;
            var htmlParts = ['<!DOCTYPE html><html><head><meta charset="utf-8"><title>Converted PDF</title><style>body{font-family:sans-serif;max-width:800px;margin:0 auto;padding:20px;} .page{margin-bottom:40px;border-bottom:1px solid #ccc;padding-bottom:20px;} p{margin:0.2em 0;}</style></head><body>'];
            for (var i = 1; i <= doc.numPages; i++) {
                var page = await doc.getPage(i);
                var content = await page.getTextContent();
                htmlParts.push('<div class="page"><h3>Page ' + i + '</h3>');
                var lastY = null;
                content.items.forEach(function(item) {
                    if (lastY !== null && Math.abs(item.transform[5] - lastY) > 5) htmlParts.push('<br>');
                    htmlParts.push(item.str);
                    lastY = item.transform[5];
                });
                htmlParts.push('</div>');
            }
            htmlParts.push('</body></html>');
            out.value = htmlParts.join('\n');
            runBtn.textContent = '\u2713 Done!';
            TCTP.toast('Converted ' + doc.numPages + ' pages to HTML.');
            setTimeout(function() { runBtn.textContent = 'Convert to HTML'; runBtn.disabled = false; }, 2000);
        } catch(err) { TCTP.toast('Conversion failed: ' + err.message); runBtn.textContent = 'Convert to HTML'; runBtn.disabled = false; }
    });
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        selectedFile = null;
        TCTP.resetDropZone(e);
        if (out) out.value = '';
        if (runBtn) runBtn.disabled = true;
    });
}

/* ── Rotate PDF ──────────────────────────────────────────────── */
function initRotatePdf(ws) {
    var e = TCTP.getEls(ws);
    var selectedFile = null;
    var rotatedBlob = null;
    TCTP.setupDropZone(ws, e, {
        multiple: false,
        onFile: function(f) {
            selectedFile = f;
            TCTP.showFileRow(e, f.name, f.size);
            if (e.$('run')) e.$('run').disabled = false;
        }
    });
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!selectedFile) return;
        runBtn.disabled = true; runBtn.textContent = 'Rotating...';
        try {
            await TCTP.ensurePdfLib();
            var bytes = new Uint8Array(await TCTP.readArrayBuffer(selectedFile));
            var doc = await window.PDFLib.PDFDocument.load(bytes);
            var angleEl = ws.querySelector('[data-opt="angle"]');
            var angle = parseInt(angleEl ? angleEl.value : '90');
            doc.getPages().forEach(function(page) {
                var current = page.getRotation().angle;
                page.setRotation(window.PDFLib.degrees((current + angle) % 360));
            });
            var pdfBytes = await doc.save();
            rotatedBlob = new Blob([pdfBytes], { type: 'application/pdf' });
            runBtn.textContent = '\u2713 Done!';
            if (dlBtn) dlBtn.disabled = false;
            TCTP.toast('PDF rotated by ' + angle + ' degrees.');
            setTimeout(function() { runBtn.textContent = 'Rotate PDF'; runBtn.disabled = false; }, 2000);
        } catch(err) { TCTP.toast('Rotation failed: ' + err.message); runBtn.textContent = 'Rotate PDF'; runBtn.disabled = false; }
    });
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (rotatedBlob) TCTP.download(rotatedBlob, 'rotated.pdf', 'application/pdf');
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        selectedFile = null; rotatedBlob = null;
        TCTP.resetDropZone(e);
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ── Delete PDF Pages ────────────────────────────────────────── */
function initDeletePdfPages(ws) {
    var e = TCTP.getEls(ws);
    var selectedFile = null;
    var resultBlob = null;
    TCTP.setupDropZone(ws, e, {
        multiple: false,
        onFile: function(f) {
            selectedFile = f;
            TCTP.showFileRow(e, f.name, f.size);
            if (e.$('run')) e.$('run').disabled = false;
        }
    });
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!selectedFile) return;
        var pagesEl = e.$('pages');
        if (!pagesEl || !pagesEl.value.trim()) { TCTP.toast('Enter pages to delete (e.g. 1,3,5-7).'); return; }
        runBtn.disabled = true; runBtn.textContent = 'Deleting...';
        try {
            await TCTP.ensurePdfLib();
            var bytes = new Uint8Array(await TCTP.readArrayBuffer(selectedFile));
            var doc = await window.PDFLib.PDFDocument.load(bytes);
            var numPages = doc.getPageCount();
            var deleteSet = {};
            pagesEl.value.split(',').forEach(function(part) {
                part = part.trim();
                if (part.indexOf('-') !== -1) {
                    var bounds = part.split('-').map(function(n) { return parseInt(n.trim()); });
                    for (var p = bounds[0]; p <= Math.min(bounds[1], numPages); p++) deleteSet[p - 1] = true;
                } else {
                    var idx = parseInt(part) - 1;
                    if (idx >= 0 && idx < numPages) deleteSet[idx] = true;
                }
            });
            var keepIndices = [];
            for (var i = 0; i < numPages; i++) { if (!deleteSet[i]) keepIndices.push(i); }
            if (keepIndices.length === 0) { TCTP.toast('Cannot delete all pages.'); runBtn.disabled = false; runBtn.textContent = 'Delete Pages'; return; }
            var newDoc = await window.PDFLib.PDFDocument.create();
            var copied = await newDoc.copyPages(doc, keepIndices);
            copied.forEach(function(p) { newDoc.addPage(p); });
            var pdfBytes = await newDoc.save();
            resultBlob = new Blob([pdfBytes], { type: 'application/pdf' });
            runBtn.textContent = '\u2713 Done!';
            if (dlBtn) dlBtn.disabled = false;
            TCTP.toast('Deleted ' + Object.keys(deleteSet).length + ' page(s).');
            setTimeout(function() { runBtn.textContent = 'Delete Pages'; runBtn.disabled = false; }, 2000);
        } catch(err) { TCTP.toast('Failed: ' + err.message); runBtn.textContent = 'Delete Pages'; runBtn.disabled = false; }
    });
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (resultBlob) TCTP.download(resultBlob, 'pages-removed.pdf', 'application/pdf');
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        selectedFile = null; resultBlob = null;
        TCTP.resetDropZone(e);
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ════════════════════════════════════════════════════════════════
   IMAGE / MEDIA TOOL IMPLEMENTATIONS
   ════════════════════════════════════════════════════════════════ */

/* ── HEIC to JPG ─────────────────────────────────────────────── */
function initHeicToJpg(ws) {
    var e = TCTP.getEls(ws);
    var files = [];
    var results = [];
    TCTP.setupDropZone(ws, e, {
        multiple: true,
        onFiles: function(list) {
            var valid = Array.from(list).filter(function(f) { return /\.(heic|heif)$/i.test(f.name); });
            files = files.concat(valid);
            if (e.$('run')) e.$('run').disabled = files.length === 0;
            var fname = e.$('fname');
            var fmeta = e.$('fmeta');
            if (fname) fname.textContent = files.length + ' HEIC files';
        }
    });
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!files.length) return;
        runBtn.disabled = true; runBtn.textContent = 'Converting...';
        try {
            await TCTP.ensureHeic2Any();
            results = [];
            for (var i = 0; i < files.length; i++) {
                var blob = await window.heic2any({ blob: files[i], toType: 'image/jpeg', quality: 0.92 });
                if (Array.isArray(blob)) blob = blob[0];
                results.push({ name: files[i].name.replace(/\.(heic|heif)$/i, '.jpg'), blob: blob });
            }
            runBtn.textContent = '\u2713 Done!';
            if (dlBtn) dlBtn.disabled = false;
            TCTP.toast('Converted ' + results.length + ' files.');
            setTimeout(function() { runBtn.textContent = 'Convert to JPG'; runBtn.disabled = false; }, 2000);
        } catch(err) { TCTP.toast('Conversion failed: ' + err.message); runBtn.textContent = 'Convert to JPG'; runBtn.disabled = false; }
    });
    if (dlBtn) dlBtn.addEventListener('click', async function() {
        if (!results.length) return;
        if (results.length === 1) { TCTP.download(results[0].blob, results[0].name); return; }
        await TCTP.ensureJSZip();
        var zip = new JSZip();
        results.forEach(function(r) { zip.file(r.name, r.blob); });
        var blob = await zip.generateAsync({ type: 'blob' });
        TCTP.download(blob, 'heic-converted.zip');
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        files = []; results = [];
        TCTP.resetDropZone(e);
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ── Image to Text (OCR) ─────────────────────────────────────── */
function initImageToText(ws) {
    var e = TCTP.getEls(ws);
    var selectedFile = null;
    var out = e.$('output');
    TCTP.setupDropZone(ws, e, {
        multiple: false,
        onFile: function(f) {
            selectedFile = f;
            TCTP.showFileRow(e, f.name, f.size);
            if (e.$('run')) e.$('run').disabled = false;
        }
    });
    var runBtn = e.$('run');
    var copyBtn = e.$('copy');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!selectedFile || !out) return;
        runBtn.disabled = true; runBtn.textContent = 'Recognizing...';
        try {
            if (!window.Tesseract) {
                await TCTP.loadScript('https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js');
            }
            var langEl = ws.querySelector('[data-opt="lang"]');
            var lang = langEl ? langEl.value : 'eng';
            var result = await window.Tesseract.recognize(selectedFile, lang);
            out.value = result.data.text;
            runBtn.textContent = '\u2713 Done!';
            TCTP.toast('Text extracted successfully.');
            setTimeout(function() { runBtn.textContent = 'Extract Text'; runBtn.disabled = false; }, 2000);
        } catch(err) { TCTP.toast('OCR failed: ' + err.message); runBtn.textContent = 'Extract Text'; runBtn.disabled = false; }
    });
    if (copyBtn) copyBtn.addEventListener('click', function() { TCTP.copy(out.value, copyBtn); });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        selectedFile = null;
        TCTP.resetDropZone(e);
        if (out) out.value = '';
        if (runBtn) runBtn.disabled = true;
    });
}

/* ── Remove Background ───────────────────────────────────────── */
function initRemoveBackground(ws) {
    var e = TCTP.getEls(ws);
    var selectedFile = null;
    var resultBlob = null;
    TCTP.setupDropZone(ws, e, {
        multiple: false,
        onFile: function(f) {
            selectedFile = f;
            TCTP.showFileRow(e, f.name, f.size);
            if (e.$('run')) e.$('run').disabled = false;
        }
    });
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!selectedFile) return;
        runBtn.disabled = true; runBtn.textContent = 'Processing...';
        try {
            var mod = await import('https://cdn.jsdelivr.net/npm/@imgly/background-removal@1.5.8/dist/index.mjs');
            var bgRemoved = await mod.removeBackground(selectedFile);
            resultBlob = bgRemoved;
            runBtn.textContent = '\u2713 Done!';
            if (dlBtn) dlBtn.disabled = false;
            TCTP.toast('Background removed!');
            setTimeout(function() { runBtn.textContent = 'Remove Background'; runBtn.disabled = false; }, 2000);
        } catch(err) { TCTP.toast('Failed: ' + err.message); runBtn.textContent = 'Remove Background'; runBtn.disabled = false; }
    });
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (resultBlob) TCTP.download(resultBlob, 'no-background.png');
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        selectedFile = null; resultBlob = null;
        TCTP.resetDropZone(e);
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ── GIF Compressor ──────────────────────────────────────────── */
function initGifCompressor(ws) {
    var e = TCTP.getEls(ws);
    var files = [];
    var results = [];
    TCTP.setupDropZone(ws, e, {
        multiple: true,
        onFiles: function(list) {
            var valid = Array.from(list).filter(function(f) { return f.type === 'image/gif'; });
            files = files.concat(valid);
            if (e.$('run')) e.$('run').disabled = files.length === 0;
        }
    });
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!files.length) return;
        runBtn.disabled = true; runBtn.textContent = 'Compressing...';
        results = [];
        var qualitySlider = ws.querySelector('input[type="range"]');
        var quality = qualitySlider ? parseInt(qualitySlider.value) / 100 : 0.7;
        for (var i = 0; i < files.length; i++) {
            try {
                var dataUrl = await TCTP.readDataURL(files[i]);
                var img = await new Promise(function(resolve) { var im = new Image(); im.onload = function() { resolve(im); }; im.src = dataUrl; });
                var canvas = document.createElement('canvas');
                canvas.width = img.width; canvas.height = img.height;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);
                var blob = await new Promise(function(resolve) { canvas.toBlob(function(b) { resolve(b); }, 'image/gif', quality); });
                results.push({ name: files[i].name, origSize: files[i].size, newSize: blob.size, blob: blob });
            } catch(ex) { console.warn('GIF compress failed for', files[i].name, ex); }
        }
        runBtn.textContent = '\u2713 Done!';
        if (dlBtn) dlBtn.disabled = false;
        setTimeout(function() { runBtn.textContent = 'Compress'; runBtn.disabled = false; }, 2000);
    });
    if (dlBtn) dlBtn.addEventListener('click', async function() {
        if (!results.length) return;
        if (results.length === 1) { TCTP.download(results[0].blob, results[0].name); return; }
        await TCTP.ensureJSZip();
        var zip = new JSZip();
        results.forEach(function(r) { zip.file(r.name, r.blob); });
        var blob = await zip.generateAsync({ type: 'blob' });
        TCTP.download(blob, 'compressed-gifs.zip');
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        files = []; results = [];
        TCTP.resetDropZone(e);
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ── SVG Compressor ──────────────────────────────────────────── */
function initSvgCompressor(ws) {
    var e = TCTP.getEls(ws);
    var files = [];
    var results = [];
    TCTP.setupDropZone(ws, e, {
        multiple: true,
        onFiles: function(list) {
            var valid = Array.from(list).filter(function(f) { return f.type === 'image/svg+xml' || f.name.toLowerCase().endsWith('.svg'); });
            files = files.concat(valid);
            if (e.$('run')) e.$('run').disabled = files.length === 0;
        }
    });
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!files.length) return;
        runBtn.disabled = true; runBtn.textContent = 'Optimizing...';
        results = [];
        for (var i = 0; i < files.length; i++) {
            var text = await TCTP.readText(files[i]);
            var parser = new DOMParser();
            var doc = parser.parseFromString(text, 'image/svg+xml');
            var svgEl = doc.querySelector('svg');
            if (!svgEl) continue;
            doc.querySelectorAll('metadata, comment').forEach(function(n) { n.remove(); });
            var optimized = new XMLSerializer().serializeToString(svgEl);
            optimized = optimized.replace(/\s+/g, ' ').trim();
            var blob = new Blob([optimized], { type: 'image/svg+xml' });
            results.push({ name: files[i].name, origSize: files[i].size, newSize: blob.size, blob: blob });
        }
        runBtn.textContent = '\u2713 Done!';
        if (dlBtn) dlBtn.disabled = false;
        setTimeout(function() { runBtn.textContent = 'Optimize SVG'; runBtn.disabled = false; }, 2000);
    });
    if (dlBtn) dlBtn.addEventListener('click', async function() {
        if (!results.length) return;
        if (results.length === 1) { TCTP.download(results[0].blob, results[0].name); return; }
        await TCTP.ensureJSZip();
        var zip = new JSZip();
        results.forEach(function(r) { zip.file(r.name, r.blob); });
        var blob = await zip.generateAsync({ type: 'blob' });
        TCTP.download(blob, 'optimized-svgs.zip');
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        files = []; results = [];
        TCTP.resetDropZone(e);
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ── Video Converter (via FFmpeg.wasm) ───────────────────────── */
function initVideoConverter(ws) {
    var e = TCTP.getEls(ws);
    var selectedFile = null;
    var resultBlob = null;
    TCTP.setupDropZone(ws, e, {
        multiple: false,
        onFile: function(f) {
            selectedFile = f;
            TCTP.showFileRow(e, f.name, f.size);
            if (e.$('run')) e.$('run').disabled = false;
        }
    });
    var runBtn = e.$('run');
    var dlBtn = e.$('download');
    var clearBtn = e.$('clear');
    if (runBtn) runBtn.addEventListener('click', async function() {
        if (!selectedFile) return;
        runBtn.disabled = true; runBtn.textContent = 'Converting...';
        var barFill = e.$('bar-fill');
        var barText = e.$('bar-text');
        try {
            await TCTP.loadScript('https://unpkg.com/@ffmpeg/ffmpeg@0.12.6/dist/umd/ffmpeg.js');
            if (!window.FFmpeg) { TCTP.toast('FFmpeg failed to load.'); runBtn.disabled = false; runBtn.textContent = 'Convert Video'; return; }
            var { createFFmpeg } = window.FFmpeg;
            var ff = createFFmpeg({ log: true, progress: function(p) {
                if (barFill) barFill.style.width = Math.round(p.ratio * 100) + '%';
                if (barText) barText.textContent = Math.round(p.ratio * 100) + '%';
            }});
            await ff.load();
            var formatEl = ws.querySelector('[data-opt="format"]');
            var format = formatEl ? formatEl.value : 'mp4';
            var ext = format === 'gif' ? 'gif' : format === 'mp3' ? 'mp3' : format;
            ff.FS('writeFile', 'input.' + selectedFile.name.split('.').pop(), new Uint8Array(await TCTP.readArrayBuffer(selectedFile)));
            var args;
            if (format === 'mp3') args = ['-i', 'input.' + selectedFile.name.split('.').pop(), '-vn', '-q:a', '2', 'output.mp3'];
            else if (format === 'gif') args = ['-i', 'input.' + selectedFile.name.split('.').pop(), '-vf', 'fps=10,scale=480:-1:flags=lanczos', '-t', '10', 'output.gif'];
            else args = ['-i', 'input.' + selectedFile.name.split('.').pop(), 'output.' + ext];
            await ff.run.apply(ff, args);
            var data = ff.FS('readFile', 'output.' + ext);
            resultBlob = new Blob([data.buffer], { type: 'video/' + ext === 'mp3' ? 'audio' : 'video' });
            runBtn.textContent = '\u2713 Done!';
            if (dlBtn) dlBtn.disabled = false;
            TCTP.toast('Video converted!');
            setTimeout(function() { runBtn.textContent = 'Convert Video'; runBtn.disabled = false; }, 2000);
        } catch(err) { TCTP.toast('Conversion failed: ' + err.message); runBtn.textContent = 'Convert Video'; runBtn.disabled = false; }
    });
    if (dlBtn) dlBtn.addEventListener('click', function() {
        if (resultBlob && selectedFile) {
            var formatEl = ws.querySelector('[data-opt="format"]');
            var ext = formatEl ? formatEl.value : 'mp4';
            TCTP.download(resultBlob, selectedFile.name.replace(/\.[^.]+$/, '.' + ext));
        }
    });
    if (clearBtn) clearBtn.addEventListener('click', function() {
        selectedFile = null; resultBlob = null;
        TCTP.resetDropZone(e);
        if (runBtn) runBtn.disabled = true;
        if (dlBtn) dlBtn.disabled = true;
    });
}

/* ════════════════════════════════════════════════════════════════
   TOOL REGISTRY — Maps slug to init function
   ════════════════════════════════════════════════════════════════ */
var TOOL_REGISTRY = {
    /* ── Text Tools ── */
    'case-converter': initCaseConverter,
    'sentence-case': initSentenceCase,
    'title-case': initTitleCase,
    'find-replace': initFindReplace,
    'character-remover': initCharacterRemover,
    'reverse-text': initReverseText,
    'sort-words': initSortWords,
    'sort-lines': function(ws) { initSortWords(ws); },
    'repeat-text': initRepeatText,
    'remove-line-breaks': initRemoveLineBreaks,
    'line-breaker': initRemoveLineBreaks,
    'remove-formatting': initRemoveFormatting,
    'remove-underscores': initRemoveUnderscores,
    'whitespace-remover': initWhitespaceRemover,
    'plain-text': initPlainText,
    'duplicate-line': initDuplicateLine,
    'duplicate-word': initDuplicateWord,
    'em-dash-remover': initEmDashRemover,
    'word-frequency': initWordFrequency,
    'sentence-counter': initSentenceCounter,
    'text-statistics': initSentenceCounter,
    'word-counter': initWordCounter,
    'char-counter': initWordCounter,
    'text-diff': initTextDiff,
    'remove-duplicates': initRemoveDuplicates,
    'camel-case': initCamelCase,
    'kebab-case': initKebabCase,
    'snake-case': initSnakeCase,
    'case-changer': initCaseConverter,
    'text-to-ascii': initTextToAscii,
    'text-formatter': initTextFormatter,
    'pig-latin': initPigLatin,
    'nato-phonetic': initNatoPhonetic,
    'phonetic-spelling': initPhoneticSpelling,
    'wingdings': initWingdings,
    'roman-numeral': initRomanNumeral,
    'word-cloud': initWordCloud,
    'online-notepad': initOnlineNotepad,
    'apa-format': initApaFormat,
    'invisible-text': initInvisibleText,
    'ascii-art': initAsciiArt,
    /* ── Developer Tools ── */
    'json-formatter': initJsonFormatter,
    'xml-formatter': initXmlFormatter,
    'html-formatter': initHtmlFormatter,
    'css-formatter': initCssFormatter,
    'js-formatter': initJsFormatter,
    'base64-encode': initBase64Encode,
    'base64-decode': initBase64Decode,
    'url-encode': initUrlEncode,
    'url-decode': initUrlDecode,
    'hash-generator': initHashGenerator,
    'regex-tester': initRegexTester,
    'lorem-ipsum': initLoremIpsum,
    /* ── PDF Tools ── */
    'pdf-compressor': initPdfCompressor,
    'pdf-merger': initPdfMerger,
    'pdf-splitter': initPdfSplitter,
    'pdf-to-text': initPdfToText,
    'pdf-to-image': initPdfToImage,
    'pdf-to-html': initPdfToHtml,
    'rotate-pdf': initRotatePdf,
    'delete-pdf-pages': initDeletePdfPages,
    'pdf-to-jpg': initPdfToImage,
    'pdf-to-png': initPdfToImage,
    'pdf-to-word': initPdfToText,
    /* ── Image / Media Tools ── */
    'jpg-to-png': function(ws) { initImageConverter(ws, 'jpeg', 'png'); },
    'jpg-to-webp': function(ws) { initImageConverter(ws, 'jpeg', 'webp'); },
    'jpg-to-gif': function(ws) { initImageConverter(ws, 'jpeg', 'gif'); },
    'jpg-to-svg': initGenericFile,
    'jpg-to-heic': initGenericFile,
    'jpg-to-avif': initGenericFile,
    'jpg-to-pdf': initGenericFile,
    'png-to-jpg': function(ws) { initImageConverter(ws, 'png', 'jpg'); },
    'png-to-webp': function(ws) { initImageConverter(ws, 'png', 'webp'); },
    'png-to-svg': initGenericFile,
    'png-to-heic': initGenericFile,
    'png-to-pdf': initGenericFile,
    'heic-to-jpg': initHeicToJpg,
    'heic-to-png': initHeicToJpg,
    'heic-to-svg': initHeicToJpg,
    'webp-to-jpg': function(ws) { initImageConverter(ws, 'webp', 'jpg'); },
    'webp-to-png': function(ws) { initImageConverter(ws, 'webp', 'png'); },
    'image-compressor': function(ws) { initImageCompressor(ws, 'jpeg'); },
    'jpg-compressor': function(ws) { initImageCompressor(ws, 'jpeg'); },
    'png-compressor': function(ws) { initImageCompressor(ws, 'png'); },
    'webp-compressor': function(ws) { initImageCompressor(ws, 'webp'); },
    'gif-compressor': initGifCompressor,
    'svg-compressor': initSvgCompressor,
    'image-to-text': initImageToText,
    'remove-background': initRemoveBackground,
    'background-remover': initRemoveBackground,
    'video-converter': initVideoConverter,
    'video-compressor': initVideoConverter,
    'audio-compressor': initVideoConverter,
    'image-converter': function(ws) { initImageConverter(ws, 'jpeg', 'png'); },
    /* ── Generator Tools ── */
    'random-number': initRandomNumber,
    'random-date': initRandomDate,
    'random-letter': initRandomLetter,
    'random-month': initRandomMonth,
    'random-choice': initRandomChoice,
    'random-ip': initRandomIp,
    'random-string': initRandomString,
    'random-name': initRandomName,
    'random-address': initRandomAddress,
    'random-email': initRandomEmail,
    'random-color': initRandomColor,
    'random-uuid': initRandomUuid,
    'uuid-generator': initUuidGenerator,
    'password-generator': initPasswordGenerator
};

/* ════════════════════════════════════════════════════════════════
   INIT — Find all .tctp-ws and boot the right handler
   ════════════════════════════════════════════════════════════════ */
function initWorkspaces() {
    document.querySelectorAll('.tctp-ws').forEach(function(ws) {
        var slug = ws.dataset.toolSlug;
        if (TOOL_REGISTRY[slug]) {
            try { TOOL_REGISTRY[slug](ws); } catch(err) { console.error('Tool init error:', slug, err); }
        } else {
            var type = ws.dataset.toolType;
            if (type === 'text') initGenericFile(ws);
            else initGenericFile(ws);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWorkspaces);
} else {
    initWorkspaces();
}

})();

