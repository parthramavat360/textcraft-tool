(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    if (!$('.tc-gemini-gen')) return;

    var type = 'explain';
    var topic = $('#gg-topic');
    var modal = $('#gg-modal');
    var length = $('#gg-length');
    var reasoning = $('#gg-reason');
    var citations = $('#gg-citations');
    var variety = $('#gg-variety');
    var generateBtn = $('#gg-generate');
    var output = $('#gg-output');
    var tips = $('#gg-tips');

    TCTP.initModeGroup('type', function (val) { type = val; });

    function buildPrompt() {
        var t = topic.value.trim();
        if (!t) { TCTP.toast('Enter your task or question', '\u26a0\ufe0f'); return null; }

        var parts = [];

        if (type === 'explain') {
            parts.push('Explain the following concept clearly and thoroughly: ' + t);
        } else if (type === 'create') {
            parts.push('Create the following from scratch: ' + t);
        } else if (type === 'plan') {
            parts.push('Create a detailed plan for: ' + t);
            parts.push('Include milestones, deliverables, and potential risks');
        } else if (type === 'debug') {
            parts.push('Help me debug: ' + t);
            parts.push('Walk through the problem step by step, identify root causes, and suggest fixes');
        } else if (type === 'learn') {
            parts.push('Teach me about: ' + t);
            parts.push('Use examples, analogies, and progressive complexity');
        } else if (type === 'transform') {
            parts.push('Help me transform or improve: ' + t);
            parts.push('Suggest specific, actionable changes with explanations');
        }

        if (modal.value !== 'text') {
            parts.push('Consider multimodal context (' + modal.options[modal.selectedIndex].text + ')');
        }
        if (length.value === 'concise') {
            parts.push('Keep the response brief and to the point');
        } else if (length.value === 'exhaustive') {
            parts.push('Provide the most comprehensive, exhaustive response possible');
        }
        if (reasoning.checked) {
            parts.push('Show your reasoning step by step before the final answer');
        }
        if (citations.checked) {
            parts.push('Cite your sources with URLs where possible');
        }
        if (variety.checked) {
            parts.push('Provide 3 different approaches or variations');
        }

        return parts.join('.\n\n') + '.';
    }

    function getTips(promptType) {
        var tipsMap = {
            explain: [
                'Gemini excels at breaking down complex topics with visual aids',
                'Ask for "simple analogy" to get relatable comparisons',
                'Use "compare with [related concept]" for deeper understanding',
                'Request "key takeaways" for a quick summary'
            ],
            create: [
                'Be specific about format, length, and target audience',
                'Provide reference examples of what you like',
                'Specify constraints: word count, style, tone',
                'Ask for "alternative versions" to get variety'
            ],
            plan: [
                'Include your timeline, budget, and constraints',
                'Ask for "what could go wrong" to identify risks',
                'Request "dependencies between steps" for project clarity',
                'Ask for "decision criteria" to evaluate options'
            ],
            debug: [
                'Include the error message, code, and expected behavior',
                'Mention what you\'ve already tried',
                'Ask for "most likely cause first" to save time',
                'Request "prevention tips" to avoid future issues'
            ],
            learn: [
                'Specify your current level: beginner, intermediate, advanced',
                'Ask for "hands-on exercises" to practice',
                'Request "common mistakes to avoid"',
                'Use "build on this by asking me questions" for interactive learning'
            ],
            transform: [
                'Show the current version and describe the desired outcome',
                'Specify what aspects to prioritize: clarity, performance, style',
                'Ask for "before and after comparison" to see changes clearly',
                'Request "rationale for each change" to learn the reasoning'
            ]
        };
        return tipsMap[promptType] || tipsMap.explain;
    }

    generateBtn.addEventListener('click', function () {
        var prompt = buildPrompt();
        if (!prompt) return;

        TCTP.copyText(prompt);

        output.innerHTML = '<div style="white-space:pre-wrap;font-size:14px;line-height:1.7;color:#e2e8f0;background:#0f172a;padding:16px;border-radius:12px;border:1px solid rgba(148,163,184,0.15)">' + escHtml(prompt) + '</div>' +
            '<button class="tc-btn tc-btn--ghost tc-copy-btn" data-copy="' + escAttr(prompt) + '" style="margin-top:12px"><i class="fa-regular fa-copy"></i> Copy Prompt</button>';

        var tipsArr = getTips(type);
        tips.innerHTML = '<ul style="list-style:none;padding:0;margin:0">' + tipsArr.map(function (tip) {
            return '<li style="padding:10px 14px;margin-bottom:8px;background:#0f172a;border-radius:10px;border:1px solid rgba(148,163,184,0.12);color:#cbd5e1;font-size:13px;line-height:1.6"><i class="fa-solid fa-lightbulb" style="color:#2563eb;margin-right:8px"></i>' + escHtml(tip) + '</li>';
        }).join('') + '</ul>';

        TCTP.initTabs();
        TCTP.switchToResultTab();
        TCTP.toast('Prompt copied to clipboard!', '\u2705');
    });

    output.addEventListener('click', function (e) {
        var btn = e.target.closest('.tc-copy-btn');
        if (btn) { TCTP.copyText(btn.dataset.copy); }
    });

    function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    function escAttr(s) { return s.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/\n/g,' '); }
})();
