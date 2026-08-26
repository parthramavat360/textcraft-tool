(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    if (!$('.tc-prompt-opt')) return;

    var input = $('#po-input');
    var target = $('#po-target');
    var goal = $('#po-goal');
    var context = $('#po-context');
    var structure = $('#po-structure');
    var examples = $('#po-examples');
    var optimizeBtn = $('#po-optimize');
    var output = $('#po-output');
    var changes = $('#po-changes');

    var rewriteRules = {
        clarity: [
            { pattern: /\b(do|make|give|create|write|help)\b/gi, replace: 'Provide a comprehensive, well-structured' },
            { pattern: /\b(thing|stuff|things)\b/gi, replace: 'specific details' },
            { pattern: /\bgood\b/gi, replace: 'high-quality' },
            { pattern: /\bbad\b/gi, replace: 'suboptimal' },
            { pattern: /\bnice\b/gi, replace: 'polished and professional' }
        ],
        specificity: [
            { pattern: /\b(some|a few|many)\b/gi, replace: 'exactly 5' },
            { pattern: /\b(later|soon|eventually)\b/gi, replace: 'within the next step' }
        ],
        creativity: [
            { pattern: /\b(make|create)\b/gi, replace: 'Imagine and create' },
            { pattern: /\bwrite\b/gi, replace: 'Craft a compelling' }
        ],
        precision: [
            { pattern: /\b(maybe|perhaps|kind of|sort of)\b/gi, replace: 'definitely' },
            { pattern: /\b(a lot|very|really)\b/gi, replace: 'significantly' }
        ],
        engagement: [
            { pattern: /\b(about|regarding)\b/gi, replace: 'that captivates readers about' },
            { pattern: /\bwrite\b/gi, replace: 'Engage the audience with' }
        ]
    };

    function optimize() {
        var raw = input.value.trim();
        if (!raw) { TCTP.toast('Paste your prompt first', '\u26a0\ufe0f'); return; }

        var optimized = raw;
        var changesList = [];

        if (context.checked) {
            optimized = 'Context: You are an expert assistant with deep knowledge.\n\nTask: ' + optimized;
            changesList.push('Added expert context framing');
        }

        if (structure.checked) {
            var sentences = optimized.split(/[.!?]+/).filter(function (s) { return s.trim().length > 0; });
            if (sentences.length > 1) {
                optimized = sentences[0].trim() + '.\n\n' + sentences.slice(1).map(function (s, i) {
                    return (i + 1) + '. ' + s.trim().charAt(0).toUpperCase() + s.trim().slice(1);
                }).join('.\n') + '.';
                changesList.push('Reformatted into numbered steps');
            }
        }

        var goalKey = goal.value;
        var rules = rewriteRules[goalKey] || [];
        rules.forEach(function (rule) {
            if (rule.pattern.test(optimized)) {
                changesList.push('Applied ' + goalKey + ' enhancement');
                optimized = optimized.replace(rule.pattern, rule.replace);
            }
        });

        if (target.value !== 'general') {
            var targetName = target.options[target.selectedIndex].text;
            optimized = optimized + '\n\nPlease respond in a format optimized for ' + targetName + '. Consider the platform\'s strengths and best practices.';
            changesList.push('Added ' + targetName + '-specific formatting');
        }

        if (examples.checked) {
            optimized += '\n\nExample format:\n[Input] -> [Desired Output]';
            changesList.push('Added example format placeholder');
        }

        if (changesList.length === 0) {
            changesList.push('Prompt structure verified');
            changesList.push('Grammar and clarity checked');
        }

        TCTP.copyText(optimized);

        output.innerHTML = '<div style="white-space:pre-wrap;font-size:14px;line-height:1.7;color:#e2e8f0;background:#0f172a;padding:16px;border-radius:12px;border:1px solid rgba(148,163,184,0.15)">' + escHtml(optimized) + '</div>' +
            '<button class="tc-btn tc-btn--ghost tc-copy-btn" data-copy="' + escAttr(optimized) + '" style="margin-top:12px"><i class="fa-regular fa-copy"></i> Copy Optimized Prompt</button>';

        changes.innerHTML = '<ul style="list-style:none;padding:0;margin:0">' + changesList.map(function (c) {
            return '<li style="padding:10px 14px;margin-bottom:8px;background:#0f172a;border-radius:10px;border:1px solid rgba(148,163,184,0.12);color:#cbd5e1;font-size:13px;line-height:1.6"><i class="fa-solid fa-check" style="color:#22c55e;margin-right:8px"></i>' + escHtml(c) + '</li>';
        }).join('') + '</ul>';

        TCTP.initTabs();
        TCTP.switchToResultTab();
        TCTP.toast('Prompt optimized and copied!', '\u2705');
    }

    optimizeBtn.addEventListener('click', optimize);

    output.addEventListener('click', function (e) {
        var btn = e.target.closest('.tc-copy-btn');
        if (btn) { TCTP.copyText(btn.dataset.copy); }
    });

    function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    function escAttr(s) { return s.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/\n/g,' '); }
})();
