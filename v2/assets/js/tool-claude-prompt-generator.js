(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    var topic = $('#cg-topic');
    if (!topic) return;

    var type = 'write';
    var format = $('#cg-format');
    var tone = $('#cg-tone');
    var stepByStep = $('#cg-step');
    var examples = $('#cg-examples');
    var constraints = $('#cg-constraints');
    var generateBtn = $('#cg-generate');
    var resultEl = $('#cg-result');
    var output = $('#cg-output');
    var tips = $('#cg-tips');

    TCTP.initModeGroup('cg-type', function (val) { type = val; });

    function buildPrompt() {
        var t = topic.value.trim();
        if (!t) { TCTP.toast('Enter your task or topic', '\u26a0\ufe0f'); return null; }

        var parts = [];
        if (type === 'write') { parts.push('I need you to write: ' + t); }
        else if (type === 'analyze') { parts.push('Analyze the following in depth: ' + t); parts.push('Consider multiple perspectives and provide nuanced insights'); }
        else if (type === 'code') { parts.push('Help me with code: ' + t); parts.push('Provide clean, well-documented code with comments'); }
        else if (type === 'brainstorm') { parts.push('Brainstorm ideas about: ' + t); parts.push('Generate at least 10 diverse ideas'); }
        else if (type === 'roleplay') { parts.push('Act as an expert in: ' + t); parts.push('Respond in character with depth and authority'); }
        else if (type === 'socratic') { parts.push('Help me understand: ' + t); parts.push("Don't give the answer directly. Guide me with thoughtful questions."); }

        if (format.value !== 'none') parts.push('Format as: ' + format.options[format.selectedIndex].text);
        if (tone.value !== 'none') parts.push('Use a ' + tone.value + ' tone');
        if (stepByStep.checked) parts.push('Think step-by-step before giving the final answer');
        if (examples.checked) parts.push('Include 2-3 concrete examples');
        if (constraints.checked) parts.push('Keep response under 500 words. Focus on actionable advice.');

        return parts.join('\n\n') + '.';
    }

    function getTips(pt) {
        var m = {
            write: ['Be specific about format, length, and audience','Provide style examples you admire','Specify the purpose: persuade, inform, entertain'],
            analyze: ['Specify what aspects to focus on','Ask for a framework to structure the analysis','Request comparisons with alternatives'],
            code: ['Specify language, framework, and version','Mention constraints: performance, readability','Ask for error handling and edge cases','Request test cases alongside the code'],
            brainstorm: ['Set constraints: "10 practical, low-cost ideas"','Ask for ideas across different domains','Request feasibility and impact ratings'],
            roleplay: ["Define the character's background and personality","Set the scenario clearly","Ask for specific actions in character"],
            socratic: ['Start with a genuine question','Tell Claude to challenge your assumptions','Request progressive building on answers']
        };
        return m[pt] || m.write;
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
