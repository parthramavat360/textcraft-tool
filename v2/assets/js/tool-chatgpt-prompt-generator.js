(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    var topic = $('#cgt-topic');
    if (!topic) return;

    var type = 'general';
    var role = $('#cgt-role');
    var detail = $('#cgt-detail');
    var persona = $('#cgt-persona');
    var fewshot = $('#cgt-fewshot');
    var chain = $('#cgt-chain');
    var generateBtn = $('#cgt-generate');
    var resultEl = $('#cgt-result');
    var output = $('#cgt-output');
    var tips = $('#cgt-tips');

    TCTP.initModeGroup('cgt-type', function (val) { type = val; });

    function buildPrompt() {
        var t = topic.value.trim();
        if (!t) { TCTP.toast('Enter your task or question', '\u26a0\ufe0f'); return null; }

        var parts = [];
        if (role.value !== 'none') parts.push('You are a ' + role.options[role.selectedIndex].text + ' with deep expertise.');

        if (type === 'general') { parts.push(t); }
        else if (type === 'writing') { parts.push('Help me write: ' + t); parts.push('Use clear, engaging prose'); }
        else if (type === 'coding') { parts.push('Help me with code: ' + t); parts.push('Write clean, well-commented code with error handling'); }
        else if (type === 'marketing') { parts.push('Create marketing content for: ' + t); parts.push('Focus on compelling headlines and clear calls to action'); }
        else if (type === 'education') { parts.push('Explain the following: ' + t); parts.push('Use real-world examples and build from simple to complex'); }
        else if (type === 'business') { parts.push('Provide business analysis for: ' + t); parts.push('Include data-driven insights and actionable next steps'); }

        if (detail.value === 'brief') parts.push('Keep response concise (under 200 words)');
        else if (detail.value === 'thorough') parts.push('Provide a thorough, comprehensive response');

        if (persona.checked) parts.push('Respond as a friendly, knowledgeable mentor');
        if (fewshot.checked) parts.push('Include 2-3 examples to illustrate your points');
        if (chain.checked) parts.push('Think step by step before giving the final answer');

        return parts.join('\n\n') + '.';
    }

    function getTips(pt) {
        var m = {
            general: ['Start with "You are..." to set context','Use delimiters to separate instructions from content','Specify output length','Ask "What am I missing?" for gaps'],
            writing: ['Provide a writing sample for style reference','Specify target audience and reading level','Ask for "3 variations" to get options'],
            coding: ['Always specify language and version','Include expected input/output format','Ask for "time and space complexity"','Request "test cases" alongside the solution'],
            marketing: ['Define target audience: demographics, pain points','Specify the platform: email, social, landing page','Ask for "A/B test variations" of headlines'],
            education: ["State the learner's current level",'Use "Explain as if teaching a beginner"','Ask for "quiz questions" to test understanding'],
            business: ['Include relevant data and context','Ask for "actionable recommendations with timelines"','Request "risk assessment" for each recommendation']
        };
        return m[pt] || m.general;
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
