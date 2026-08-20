/**
 * Online Notepad — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var STORAGE_KEY = 'tctp_notepad_data';
    var textarea = document.getElementById('tc-onp-input');
    if (!textarea) return;

    var saveTimer = null;

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
    }

    function autoSave() {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(function () {
            try {
                localStorage.setItem(STORAGE_KEY, textarea.value);
                var savedEl = document.getElementById('tc-onp-saved');
                if (savedEl) {
                    savedEl.textContent = 'Auto-saved';
                    savedEl.style.color = '#22c55e';
                }
            } catch (e) {
                var savedEl = document.getElementById('tc-onp-saved');
                if (savedEl) {
                    savedEl.textContent = 'Storage full';
                    savedEl.style.color = '#ef4444';
                }
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
            try {
                localStorage.setItem(STORAGE_KEY, textarea.value);
                var savedEl = document.getElementById('tc-onp-saved');
                if (savedEl) {
                    savedEl.textContent = 'Saved';
                    savedEl.style.color = '#22c55e';
                }
                TCTP.toast('Notes saved!');
            } catch (e) {
                TCTP.toast('Failed to save — storage may be full.', '\u274C');
            }
        });
    }

    var loadBtn = document.getElementById('tc-onp-load');
    if (loadBtn) {
        loadBtn.addEventListener('click', function () {
            var data = localStorage.getItem(STORAGE_KEY);
            if (data !== null) {
                textarea.value = data;
                updateCounts();
                var savedEl = document.getElementById('tc-onp-saved');
                if (savedEl) {
                    savedEl.textContent = 'Loaded';
                    savedEl.style.color = '#3b82f6';
                }
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
            localStorage.removeItem(STORAGE_KEY);
            var savedEl = document.getElementById('tc-onp-saved');
            if (savedEl) {
                savedEl.textContent = 'Cleared';
                savedEl.style.color = '#f97316';
            }
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
        });
    }

    var savedData = localStorage.getItem(STORAGE_KEY);
    if (savedData !== null) {
        textarea.value = savedData;
        var savedEl = document.getElementById('tc-onp-saved');
        if (savedEl) {
            savedEl.textContent = 'Restored';
            savedEl.style.color = '#3b82f6';
        }
    }
    updateCounts();

})();
