(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    if (!$('.tc-claude-gen')) return;

    var type = 'write';
    var topic = $('#cg-topic');
    var format = $('#cg-format');
    var tone = $('#cg-tone');
    var stepByStep = $('#cg-step');
    var examples = $('#cg-examples');
    var constraints = $('#cg-constraints');
    var generateBtn = $('#cg-generate');
    var output = $('#cg-output');
    var tips = $('#cg-tips');

    TCTP.initModeGroup('type', function (val) { type = val; });

    function buildPrompt() {
        var t = topic.value.trim();
        if (!t) { TCTP.toast('Enter your task or topic', '\u26a0\ufe0f'); return null; }

        var parts = [];

        if (type === 'write') {
            parts.push('I need you to write: ' + t);
        } else if (type === 'analyze') {
            parts.push('Please analyze the following in depth: ' + t);
            parts.push('Consider multiple perspectives, identify key patterns, and provide nuanced insights');
        } else if (type === 'code') {
            parts.push('Help me with code: ' + t);
            parts.push('Provide clean, well-documented code with comments');
        } else if (type === 'brainstorm') {
            parts.push('Brainstorm ideas about: ' + t);
            parts.push('Generate at least 10 diverse ideas, ranging from conventional to unconventional');
        } else if (type === 'roleplay') {
            parts.push('Act as an expert in the field related to: ' + t);
            parts.push('Respond in character with depth, authority, and nuance');
        } else if (type === 'socratic') {
            parts.push('Help me understand: ' + t);
            parts.push('Don\'t give me the answer directly. Instead, guide me with thoughtful questions that help me discover the answer myself');
        }

        if (format.value !== 'none') {
            parts.push('Format the response as: ' + format.options[format.selectedIndex].text);
        }
        if (tone.value !== 'none') {
            parts.push('Use a ' + tone.value + ' tone throughout');
        }
        if (stepByStep.checked) {
            parts.push('Think step-by-step through your reasoning before giving the final answer');
        }
        if (examples.checked) {
            parts.push('Include 2-3 concrete examples to illustrate your points');
        }
        if (constraints.checked) {
            parts.push('Keep your response under 500 words');
            parts.push('Focus on actionable, practical advice');
        }

        return parts.join('.\n\n') + '.';
    }

    function getTips(promptType) {
        var tipsMap = {
            write: [
                'Be specific about the format, length, and audience',
                'Provide examples of writing you admire for style reference',
                'Specify the purpose: persuade, inform, entertain, or educate',
                'Mention what NOT to include to avoid irrelevant content'
            ],
            analyze: [
                'Specify what aspects to focus on: strengths, weaknesses, patterns',
                'Ask for a framework or methodology to structure the analysis',
                'Request comparisons with relevant alternatives or benchmarks',
                'Ask for confidence levels on your conclusions'
            ],
            code: [
                'Specify the language, framework, and version',
                'Mention constraints: performance, readability, compatibility',
                'Ask for error handling and edge cases to be addressed',
                'Request unit tests or usage examples alongside the code'
            ],
            brainstorm: [
                'Set constraints: "Give me 10 ideas that are practical and low-cost"',
                'Ask for ideas across different categories or domains',
                'Request the most creative/unconventional idea as #10',
                'Ask Claude to rate each idea on feasibility and impact'
            ],
            roleplay: [
                'Define the character\'s background, expertise, and personality',
                'Set the scenario: "You are a senior engineer reviewing my architecture"',
                'Ask for specific actions or advice in character',
                'Tell Claude to stay in character throughout the conversation'
            ],
            socratic: [
                'Start with a genuine question you\'re struggling with',
                'Tell Claude to challenge your assumptions',
                'Ask Claude to identify what you already know vs what you don\'t',
                'Request that Claude builds on your answers progressively'
            ]
        };
        return tipsMap[promptType] || tipsMap.write;
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
