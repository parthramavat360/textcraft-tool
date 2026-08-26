(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    var topic = $('#gg-topic');
    if (!topic) return;

    var type = 'explain';
    var modal = $('#gg-modal');
    var length = $('#gg-length');
    var reasoning = $('#gg-reason');
    var citations = $('#gg-citations');
    var variety = $('#gg-variety');
    var generateBtn = $('#gg-generate');
    var resultEl = $('#gg-result');
    var output = $('#gg-output');
    var tips = $('#gg-tips');

    TCTP.initModeGroup('gg-type', function (val) { type = val; });

    function buildPrompt() {
        var t = topic.value.trim();
        if (!t) { TCTP.toast('Enter your task or question', '\u26a0\ufe0f'); return null; }

        var parts = [];
        if (type === 'explain') { parts.push('Explain the following clearly and thoroughly: ' + t); }
        else if (type === 'create') { parts.push('Create the following from scratch: ' + t); }
        else if (type === 'plan') { parts.push('Create a detailed plan for: ' + t); parts.push('Include milestones, deliverables, and risks'); }
        else if (type === 'debug') { parts.push('Help me debug: ' + t); parts.push('Walk through step by step, identify root causes, suggest fixes'); }
        else if (type === 'learn') { parts.push('Teach me about: ' + t); parts.push('Use examples, analogies, and progressive complexity'); }
        else if (type === 'transform') { parts.push('Help me transform or improve: ' + t); parts.push('Suggest specific, actionable changes with explanations'); }

        if (modal.value !== 'text') parts.push('Consider multimodal context (' + modal.options[modal.selectedIndex].text + ')');
        if (length.value === 'concise') parts.push('Keep the response brief and to the point');
        else if (length.value === 'exhaustive') parts.push('Provide the most comprehensive response possible');
        if (reasoning.checked) parts.push('Show your reasoning step by step');
        if (citations.checked) parts.push('Cite sources with URLs where possible');
        if (variety.checked) parts.push('Provide 3 different approaches or variations');

        return parts.join('.\n\n') + '.';
    }

    function getTips(pt) {
        var m = {
            explain: ['Ask for "simple analogy" for relatable comparisons','Use "compare with [concept]" for deeper understanding','Request "key takeaways" for quick summary'],
            create: ['Be specific about format, length, audience','Provide reference examples','Specify constraints','Ask for "alternative versions"'],
            plan: ['Include timeline, budget, constraints','Ask for "what could go wrong"','Request "dependencies between steps"'],
            debug: ['Include error message, code, expected behavior','Mention what you already tried','Ask for "most likely cause first"','Request "prevention tips"'],
            learn: ['State your current level','Ask for "hands-on exercises"','Request "common mistakes to avoid"','Use "build on this by asking me questions"'],
            transform: ['Show current version and desired outcome','Specify what to prioritize','Ask for "before and after comparison"']
        };
        return m[pt] || m.explain;
    }

    generateBtn.addEventListener('click', function () {
        var prompt = buildPrompt();
        if (!prompt) return;

        TCTP.copyText(prompt);
        output.innerHTML = '<pre style="white-space:pre-wrap;background:#0f172a;color:#e2e8f0;padding:16px;border-radius:12px;border:1px solid rgba(148,163,184,0.15);font-size:13px;line-height:1.7;margin:0">' + escHtml(prompt) + '</pre>';

        var tipsArr = getTips(type);
        tips.innerHTML = '<ul style="list-style:none;padding:0;margin:0">' + tipsArr.map(function (tip) {
            return '<li style="padding:10px 14px;margin-bottom:8px;background:#0f172a;border-radius:10px;border:1px solid rgba(148,163,184,0.12);color:#cbd5e1;font-size:13px;line-height:1.6"><i class="fa-solid fa-lightbulb" style="color:#2563eb;margin-right:8px"></i>' + escHtml(tip) + '</li>';
        }).join('') + '</ul>';

        resultEl.style.display = '';
        TCTP.initTabs(resultEl);
        TCTP.toast('Prompt copied!', '\u2705');
    });

    function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
})();
