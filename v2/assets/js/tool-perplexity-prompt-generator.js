(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    var $$ = function (s, p) { return (p || document).querySelectorAll(s); };
    if (!$('.tc-prompt-gen')) return;

    var type = 'research';
    var topic = $('#pg-topic');
    var depth = $('#pg-depth');
    var audience = $('#pg-audience');
    var citations = $('#pg-citations');
    var sources = $('#pg-sources');
    var followup = $('#pg-followup');
    var generateBtn = $('#pg-generate');
    var output = $('#pg-output');
    var tips = $('#pg-tips');

    TCTP.initModeGroup('type', function (val) { type = val; });

    function buildPrompt() {
        var t = topic.value.trim();
        if (!t) { TCTP.toast('Enter a topic or question', '\u26a0\ufe0f'); return; }

        var depthLabel = depth.options[depth.selectedIndex].text;
        var audienceLabel = audience.options[audience.selectedIndex].text;
        var parts = [];

        if (type === 'research') {
            parts.push('Provide a comprehensive, well-researched analysis of: ' + t);
            parts.push('Use credible, up-to-date sources');
        } else if (type === 'summary') {
            parts.push('Provide a concise summary of: ' + t);
            parts.push('Focus on the key points and main takeaways');
        } else if (type === 'explain') {
            parts.push('Explain ' + t + ' in a clear, accessible way');
            parts.push('Use analogies and real-world examples');
        } else if (type === 'compare') {
            parts.push('Compare and contrast: ' + t);
            parts.push('Include a detailed comparison table');
            parts.push('Highlight key differences and similarities');
        } else if (type === 'creative') {
            parts.push('Write a creative piece about: ' + t);
            parts.push('Use engaging, original language and fresh perspectives');
        } else if (type === 'technical') {
            parts.push('Provide a technical deep-dive into: ' + t);
            parts.push('Include technical specifications, architecture details, and implementation notes');
            parts.push('Reference official documentation and industry standards');
        }

        if (depth.value === 'quick') {
            parts.push('Keep the answer brief and to the point (under 300 words)');
        } else if (depth.value === 'comprehensive') {
            parts.push('Provide the most thorough, in-depth analysis possible');
            parts.push('Cover multiple angles, edge cases, and nuances');
        }

        if (audience.value === 'student') {
            parts.push('Explain like you are teaching a college student');
        } else if (audience.value === 'beginner') {
            parts.push('Explain in simple terms without jargon');
        } else if (audience.value === 'expert') {
            parts.push('Assume the reader is a domain expert');
        }

        if (citations.checked) {
            parts.push('Include inline citations with source links');
        }
        if (sources.checked) {
            parts.push('Reference multiple diverse sources to ensure balanced coverage');
        }
        if (followup.checked) {
            parts.push('After your answer, suggest 3 related follow-up questions to explore');
        }

        return parts.join('.\n\n') + '.';
    }

    function getTips(promptType) {
        var tipsMap = {
            research: [
                'Perplexity excels at synthesizing multiple sources — ask broad questions',
                'Add "cite your sources" to get inline references',
                'Use "as of [year]" to get the most current information',
                'Ask follow-up questions to drill deeper into specific aspects'
            ],
            summary: [
                'Specify the format: "key takeaways in bullet points"',
                'Mention the source: "Summarize this paper/article/book"',
                'For academic papers, ask for: abstract, methods, results, and conclusion',
                'Add "compare to [related topic]" for richer summaries'
            ],
            explain: [
                'Use "ELI5" (Explain Like I am 5) for very simple explanations',
                'Ask for "with real-world examples" to make it concrete',
                'Use "step by step" for process-based explanations',
                'Ask "What are the common misconceptions about..." for deeper insight'
            ],
            compare: [
                'Be specific about what to compare: features, pricing, performance',
                'Ask for "a comparison table with columns for each option"',
                'Include your use case: "for a small business" or "for personal use"',
                'Ask about pros AND cons for each option'
            ],
            creative: [
                'Specify the tone: formal, casual, humorous, inspiring',
                'Give context: who is the audience, what is the purpose',
                'Provide examples of writing you like as reference',
                'Ask for "variations" or "different angles" on the same topic'
            ],
            technical: [
                'Be specific about the technology version or framework',
                'Ask for "code examples in [language]" for practical output',
                'Request "architecture diagrams described in text"',
                'Include "trade-offs and alternatives" for comprehensive analysis'
            ]
        };
        return tipsMap[promptType] || tipsMap.research;
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
