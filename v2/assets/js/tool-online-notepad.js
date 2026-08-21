/**
 * Online Notepad — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var STORAGE_KEY = 'tctp_notepad_data';

    function readStorage() {
        try { return localStorage.getItem(STORAGE_KEY); } catch (e) { return null; }
    }

    function writeStorage(value) {
        try {
            localStorage.setItem(STORAGE_KEY, value);
            return true;
        } catch (e) {
            return false;
        }
    }

    function clearStorage() {
        try { localStorage.removeItem(STORAGE_KEY); } catch (e) { /* ignore */ }
    }

    function init() {
        var textarea = document.getElementById('tc-onp-input');
        if (!textarea || textarea.dataset.tcInit) return;
        textarea.dataset.tcInit = '1';

        var saveTimer = null;

        function setSaved(text, color) {
            var savedEl = document.getElementById('tc-onp-saved');
            if (savedEl) {
                savedEl.textContent = text;
                savedEl.style.color = color;
            }
        }

        function updateCounts() {
            var text = textarea.value;
            var words = text.trim() ? text.trim().split(/\s+/).length : 0;
            var chars = text.length;
            var lines = text.split('\n').length;

            var setEl = function (id, val) {
                var el = document.getElementById(id);
                if (el) el.textContent = val;
            };

            setEl('tc-onp-words', words.toLocaleString());
            setEl('tc-onp-chars', chars.toLocaleString());
            setEl('tc-onp-lines', lines.toLocaleString());

            // Keep the Notes Preview panel in sync (#tc-onp-output)
            var preview = document.getElementById('tc-onp-output');
            if (preview && preview.value !== text) preview.value = text;
        }

        function autoSave() {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(function () {
                if (writeStorage(textarea.value)) {
                    setSaved('Auto-saved', '#22c55e');
                    TCTP.updateResultPanel(textarea.value.length.toLocaleString() + ' chars', (textarea.value.trim() ? textarea.value.trim().split(/\s+/).length : 0).toLocaleString() + ' words', '\u2014', 'Saved');
                } else {
                    setSaved('Storage full', '#ef4444');
                }
            }, 500);
        }

        textarea.addEventListener('input', function () {
            updateCounts();
            autoSave();
        });

        var saveBtn = document.getElementById('tc-onp-save');
        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                if (writeStorage(textarea.value)) {
                    setSaved('Saved', '#22c55e');
                    TCTP.updateResultPanel(textarea.value.length.toLocaleString() + ' chars', (textarea.value.trim() ? textarea.value.trim().split(/\s+/).length : 0).toLocaleString() + ' words', '\u2014', 'Saved');
                    TCTP.toast('Notes saved!');
                } else {
                    TCTP.toast('Failed to save \u2014 storage may be full.', '\u274C');
                }
            });
        }

        var loadBtn = document.getElementById('tc-onp-load');
        if (loadBtn) {
            loadBtn.addEventListener('click', function () {
                var data = readStorage();
                if (data !== null) {
                    textarea.value = data;
                    updateCounts();
                    setSaved('Loaded', '#3b82f6');
                    TCTP.toast('Notes loaded from storage!');
                } else {
                    TCTP.toast('No saved notes found.', '\u26A0\uFE0F');
                }
            });
        }

        var clearBtn = document.getElementById('tc-onp-clear');
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (!textarea.value.trim()) {
                    TCTP.toast('Notepad is already empty.', '\u26A0\uFE0F');
                    return;
                }
                textarea.value = '';
                updateCounts();
                clearStorage();
                setSaved('Cleared', '#f97316');
                TCTP.toast('Notepad cleared!');
            });
        }

        var exportBtn = document.getElementById('tc-onp-export');
        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                var text = textarea.value;
                if (!text.trim()) {
                    TCTP.toast('Nothing to export.', '\u26A0\uFE0F');
                    return;
                }
                TCTP.downloadText(text, 'notepad.txt', 'text/plain;charset=utf-8');
                TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', (text.trim() ? text.trim().split(/\s+/).length : 0).toLocaleString() + ' words', '\u2014', 'Exported');
            });
        }

        // Restore previous session notes
        var savedData = readStorage();
        if (savedData !== null) {
            textarea.value = savedData;
            setSaved('Restored', '#3b82f6');
        }
        updateCounts();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Re-init after Elementor AJAX re-render
    new MutationObserver(function () { init(); })
        .observe(document.documentElement, { childList: true, subtree: true });
})();
