/**
 * TCTP Shared — Common utilities for TextCraft tool widgets.
 *
 * Provides:
 *  - File upload/download helpers
 *  - Clipboard copy with toast feedback
 *  - Progress bar updates
 *  - Text statistics (chars, words, sentences, lines)
 *  - Toast notifications
 *  - Button group state management
 *
 * Usage: window.TCTP (global namespace)
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var TCTP = window.TCTP || {};

    // ═══════════════════════════════════════════════════════════
    //  TOAST NOTIFICATIONS
    // ═══════════════════════════════════════════════════════════

    TCTP.toast = function (msg, icon, duration) {
        icon = icon || '\u2705';
        duration = duration || 2800;
        var el = document.getElementById('tctp-toast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'tctp-toast';
            el.className = 'tctp-toast';
            el.innerHTML = '<span class="tctp-toast-icon"></span><span class="tctp-toast-msg"></span>';
            document.body.appendChild(el);
        }
        el.querySelector('.tctp-toast-icon').textContent = icon;
        el.querySelector('.tctp-toast-msg').textContent = msg;
        el.classList.add('tctp-toast--show');
        clearTimeout(TCTP._toastTimer);
        TCTP._toastTimer = setTimeout(function () {
            el.classList.remove('tctp-toast--show');
        }, duration);
    };

    // ═══════════════════════════════════════════════════════════
    //  CLIPBOARD
    // ═══════════════════════════════════════════════════════════

    TCTP.copyText = function (text, label) {
        if (!text || !text.trim()) {
            TCTP.toast('Nothing to copy \u2014 add some content first.', '\u26A0\uFE0F');
            return;
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                TCTP.toast((label || 'Content') + ' copied to clipboard!');
            }).catch(function () {
                fallbackCopy(text);
            });
        } else {
            fallbackCopy(text);
        }
    };

    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); TCTP.toast('Copied!'); }
        catch (e) { TCTP.toast('Failed to copy.', '\u274C'); }
        document.body.removeChild(ta);
    }

    // ═══════════════════════════════════════════════════════════
    //  FILE DOWNLOAD
    // ═══════════════════════════════════════════════════════════

    TCTP.downloadText = function (content, filename, mimeType) {
        if (!content || !content.trim()) {
            TCTP.toast('Nothing to download \u2014 add some content first.', '\u26A0\uFE0F');
            return;
        }
        mimeType = mimeType || 'text/plain;charset=utf-8';
        var blob = new Blob([content], { type: mimeType });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = filename || 'output.txt';
        a.click();
        URL.revokeObjectURL(url);
        TCTP.toast('Downloaded!', '\uD83D\uDCE5');
    };

    TCTP.downloadBlob = function (blob, filename) {
        if (!blob) return;
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = filename || 'output';
        a.click();
        URL.revokeObjectURL(url);
        TCTP.toast('Downloaded!', '\uD83D\uDCE5');
    };

    // ═══════════════════════════════════════════════════════════
    //  FILE UPLOAD / DRAG-DROP
    // ═══════════════════════════════════════════════════════════

    /**
     * Initialize a drop zone with file input.
     * @param {string} dropId   - ID of the .tctp-drop element
     * @param {string} inputId  - ID of the hidden file input
     * @param {Function} onFile - Callback receiving (File object)
     * @param {string} accept   - Accept attribute for file input
     */
    TCTP.initDropZone = function (dropId, inputId, onFile, accept) {
        var drop = document.getElementById(dropId);
        var input = document.getElementById(inputId);
        if (!drop || !input) return;

        if (accept) input.accept = accept;

        drop.addEventListener('click', function () { input.click(); });

        drop.addEventListener('dragover', function (e) {
            e.preventDefault();
            drop.classList.add('hot');
        });

        drop.addEventListener('dragleave', function () {
            drop.classList.remove('hot');
        });

        drop.addEventListener('drop', function (e) {
            e.preventDefault();
            drop.classList.remove('hot');
            var files = e.dataTransfer.files;
            if (files.length && onFile) onFile(files[0]);
        });

        input.addEventListener('change', function () {
            if (input.files.length && onFile) onFile(input.files[0]);
            input.value = '';
        });
    };

    /**
     * Show the file row with name and size after file selection.
     */
    TCTP.showFileRow = function (rowId, file) {
        var row = document.getElementById(rowId);
        if (!row) return;
        var nameEl = row.querySelector('.tctp-file-name, .tc-file-name');
        var sizeEl = row.querySelector('.tctp-file-size, .tc-file-size');
        if (nameEl) nameEl.textContent = file.name;
        if (sizeEl) sizeEl.textContent = TCTP.formatSize(file.size);
        row.style.display = '';
        row.classList.add('visible');
    };

    /**
     * Hide the file row.
     */
    TCTP.hideFileRow = function (rowId) {
        var row = document.getElementById(rowId);
        if (!row) return;
        row.style.display = 'none';
        row.classList.remove('visible');
    };

    // ═══════════════════════════════════════════════════════════
    //  PROGRESS BAR
    // ═══════════════════════════════════════════════════════════

    TCTP.showProgress = function (barId) {
        var bar = document.getElementById(barId);
        if (bar) bar.style.display = '';
    };

    TCTP.hideProgress = function (barId) {
        var bar = document.getElementById(barId);
        if (bar) bar.style.display = 'none';
    };

    TCTP.setProgress = function (barId, pct, label) {
        var bar = document.getElementById(barId);
        if (!bar) return;
        var fill = bar.querySelector('.tctp-bar-fill, .tc-bar-fill');
        var labelEl = bar.querySelector('.tctp-bar-label, .tc-bar-label');
        if (fill) fill.style.width = pct + '%';
        if (labelEl && label) {
            labelEl.innerHTML = label + ' <span class="tctp-bar-pct">' + pct + '%</span>';
        }
    };

    // ═══════════════════════════════════════════════════════════
    //  TEXT STATISTICS
    // ═══════════════════════════════════════════════════════════

    TCTP.getStats = function (text) {
        if (!text) return { chars: 0, words: 0, sentences: 0, lines: 0 };
        return {
            chars: text.length,
            words: text.trim() ? text.trim().split(/\s+/).length : 0,
            sentences: text.trim() ? (text.match(/[.!?]+/g) || []).length : 0,
            lines: text.split('\n').length
        };
    };

    TCTP.updateStats = function (prefix, text) {
        var s = TCTP.getStats(text);
        var set = function (id, v) {
            var el = document.getElementById(id);
            if (el) el.textContent = v;
        };
        set(prefix + '-chars', s.chars.toLocaleString() + ' chars');
        set(prefix + '-words', s.words.toLocaleString() + ' words');
        set(prefix + '-sentences', s.sentences.toLocaleString() + ' sentences');
        set(prefix + '-lines', s.lines.toLocaleString() + ' lines');
    };

    // ═══════════════════════════════════════════════════════════
    //  FILE SIZE FORMATTING
    // ═══════════════════════════════════════════════════════════

    TCTP.formatSize = function (bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    };

    // ═══════════════════════════════════════════════════════════
    //  BUTTON GROUP HELPERS
    // ═══════════════════════════════════════════════════════════

    /**
     * Activate a button in a group and deactivate siblings.
     */
    TCTP.activateBtn = function (btn) {
        var group = btn.closest('.tc-modes') || btn.closest('.tctp-modes');
        if (group) {
            group.querySelectorAll('.tc-btn, .tctp-btn').forEach(function (b) {
                b.classList.remove('sel');
            });
        }
        btn.classList.add('sel');
    };

    /**
     * Initialize a mode button group (click to activate).
     * @param {string} groupId - ID or CSS selector of the group container
     * @param {Function} onChange - Callback receiving (selected value)
     */
    TCTP.initModeGroup = function (groupId, onChange) {
        var group = typeof groupId === 'string' ? document.getElementById(groupId) || document.querySelector(groupId) : groupId;
        if (!group) return;
        group.querySelectorAll('.tc-btn, .tctp-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                TCTP.activateBtn(btn);
                if (onChange) onChange(btn.getAttribute('data-val') || btn.textContent.trim());
            });
        });
    };

    // ═══════════════════════════════════════════════════════════
    //  RESULT PANEL STATS (shared right-col panel)
    // ═══════════════════════════════════════════════════════════

    TCTP.updateResultPanel = function (orig, comp, saved, status) {
        var el = function (id, v) { var x = document.getElementById(id); if (x) x.textContent = v; };
        el('tc-stat-orig', orig);
        el('tc-stat-comp', comp);
        el('tc-stat-saved', saved);
        el('tc-status-chip', status || 'Done');
    };

    TCTP.setResultStatus = function (text) {
        var el = document.getElementById('tc-status-chip');
        if (el) el.textContent = text;
    };

    // ═══════════════════════════════════════════════════════════
    //  PREVIEW TABS
    // ═══════════════════════════════════════════════════════════

    TCTP.initTabs = function () {
        var tabBtns = document.querySelectorAll('.tc-tabs button');
        if (!tabBtns.length) return;
        tabBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                tabBtns.forEach(function (b) { b.classList.remove('on'); });
                btn.classList.add('on');
                var tab = btn.getAttribute('data-tab');
                var preview = document.getElementById('tc-preview');
                if (preview) {
                    preview.setAttribute('data-active-tab', tab || 'original');
                }
            });
        });
    };

    // Auto-init tabs on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', TCTP.initTabs);
    } else {
        TCTP.initTabs();
    }

    // ═══════════════════════════════════════════════════════════
    //  SCOPING HELPER
    // ═══════════════════════════════════════════════════════════

    /**
     * Scope selectors to a widget wrapper element.
     * Returns { wrap, $ } where $(sel) queries within the wrapper.
     */
    TCTP.scope = function (startEl, wrapSelector) {
        wrapSelector = wrapSelector || '.tctp-workspace-wrap';
        var wrap = startEl ? startEl.closest(wrapSelector) : document;
        return {
            wrap: wrap,
            $: function (sel) { return wrap.querySelector(sel); },
            $$: function (sel) { return wrap.querySelectorAll(sel); }
        };
    };

    // ═══════════════════════════════════════════════════════════
    //  EXPOSE
    // ═══════════════════════════════════════════════════════════

    window.TCTP = TCTP;

})();