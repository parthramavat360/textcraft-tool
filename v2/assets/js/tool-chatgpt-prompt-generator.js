(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    if (!$('.tc-chatgpt-gen')) return;

    var type = 'general';
    var topic = $('#cgt-topic');
    var role = $('#cgt-role');
    var detail = $('#cgt-detail');
    var persona = $('#cgt-persona');
    var fewshot = $('#cgt-fewshot');
    var chain = $('#cgt-chain');
    var generateBtn = $('#cgt-generate');
    var output = $('#cgt-output');
    var tips = $('#cgt-tips');

    TCTP.initModeGroup('type', function (val) { type = val; });

    function buildPrompt() {
        var t = topic.value.trim();
        if (!t) { TCTP.toast('Enter your task or question', '\u26a0\ufe0f'); return null; }

        var parts = [];
        var roleText = role.options[role.selectedIndex].text;

        if (role.value !== 'none') {
            parts.push('You are a ' + roleText + ' with deep expertise in this area.');
        }

        if (type === 'general') {
            parts.push(t);
        } else if (type === 'writing') {
            parts.push('Help me write: ' + t);
            parts.push('Use clear, engaging prose with good flow');
        } else if (type === 'coding') {
            parts.push('Help me with code: ' + t);
            parts.push('Write clean, well-commented code with error handling');
        } else if (type === 'marketing') {
            parts.push('Create marketing content for: ' + t);
            parts.push('Focus on compelling headlines and clear calls to action');
        } else if (type === 'education') {
            parts.push('Explain the following concept: ' + t);
            parts.push('Use real-world examples and build from simple to complex');
        } else if (type === 'business') {
            parts.push('Provide business analysis and recommendations for: ' + t);
            parts.push('Include data-driven insights and actionable next steps');
        }

        if (detail.value === 'brief') {
            parts.push('Keep the response concise (under 200 words)');
        } else if (detail.value === 'thorough') {
            parts.push('Provide a thorough, comprehensive response covering all angles');
        }

        if (persona.checked) {
            parts.push('Respond in character as a friendly, knowledgeable mentor');
        }
        if (fewshot.checked) {
            parts.push('Include 2-3 examples to illustrate your points');
        }
        if (chain.checked) {
            parts.push('Think step by step through the problem before giving the final answer');
        }

        return parts.join('\n\n') + '.';
    }

    function getTips(promptType) {
        var tipsMap = {
            general: [
                'Start with "You are..." to set the context for ChatGPT',
                'Use delimiters (---, """, ###) to separate instructions from content',
                'Specify output length: "Respond in 3 sentences" or "Write 500 words"',
                'Ask "What am I missing?" to get ChatGPT to identify gaps in your prompt'
            ],
            writing: [
                'Provide a writing sample for style reference',
                'Specify the target audience and reading level',
                'Use "Rewrite this in [style]" for quick style transformations',
                'Ask for "3 variations" to get options to choose from'
            ],
            coding: [
                'Always specify the programming language and version',
                'Include the expected input/output format',
                'Ask for "time and space complexity analysis"',
                'Request "test cases" alongside the solution'
            ],
            marketing: [
                'Define the target audience: demographics, pain points, desires',
                'Specify the platform: email, social media, landing page',
                'Ask for "A/B test variations" of headlines',
                'Include brand voice guidelines for consistency'
            ],
            education: [
                'State the learner\'s current level explicitly',
                'Use the Feynman technique: "Explain as if teaching a beginner"',
                'Ask for "quiz questions" to test understanding',
                'Request "common misconceptions" about the topic'
            ],
            business: [
                'Include relevant data and context in the prompt',
                'Ask for "actionable recommendations with timelines"',
                'Request "risk assessment" for each recommendation',
                'Specify the business context: startup, enterprise, industry'
            ]
        };
        return tipsMap[promptType] || tipsMap.general;
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
