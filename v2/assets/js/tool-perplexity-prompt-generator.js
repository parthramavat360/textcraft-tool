(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    if (!$('.tc-palette') && !$('#pg-topic')) return;

    var type = 'research';
    var topic = $('#pg-topic');
    var depth = $('#pg-depth');
    var audience = $('#pg-audience');
    var citations = $('#pg-citations');
    var sources = $('#pg-sources');
    var followup = $('#pg-followup');
    var generateBtn = $('#pg-generate');
    var resultEl = $('#pg-result');
    var output = $('#pg-output');
    var tips = $('#pg-tips');

    if (!topic || !generateBtn) return;

    TCTP.initModeGroup('pg-type', function (val) { type = val; });

    function buildPrompt() {
        var t = topic.value.trim();
        if (!t) { TCTP.toast('Enter a topic or question', '\u26a0\ufe0f'); return null; }

        var parts = [];
        if (type === 'research') { parts.push('Provide a comprehensive, well-researched analysis of: ' + t); parts.push('Use credible, up-to-date sources'); }
        else if (type === 'summary') { parts.push('Provide a concise summary of: ' + t); parts.push('Focus on the key points and main takeaways'); }
        else if (type === 'explain') { parts.push('Explain ' + t + ' in a clear, accessible way'); parts.push('Use analogies and real-world examples'); }
        else if (type === 'compare') { parts.push('Compare and contrast: ' + t); parts.push('Include a detailed comparison table'); parts.push('Highlight key differences and similarities'); }
        else if (type === 'creative') { parts.push('Write a creative piece about: ' + t); parts.push('Use engaging, original language and fresh perspectives'); }
        else if (type === 'technical') { parts.push('Provide a technical deep-dive into: ' + t); parts.push('Include technical specifications and implementation notes'); }

        if (depth.value === 'quick') parts.push('Keep the answer brief (under 300 words)');
        else if (depth.value === 'comprehensive') parts.push('Provide the most thorough, in-depth analysis possible');

        if (audience.value === 'student') parts.push('Explain like you are teaching a college student');
        else if (audience.value === 'beginner') parts.push('Explain in simple terms without jargon');
        else if (audience.value === 'expert') parts.push('Assume the reader is a domain expert');

        if (citations.checked) parts.push('Include inline citations with source links');
        if (sources.checked) parts.push('Reference multiple diverse sources');
        if (followup.checked) parts.push('After your answer, suggest 3 related follow-up questions');

        return parts.join('.\n\n') + '.';
    }

    function getTips(pt) {
        var m = {
            research: ['Ask broad questions for synthesis','Add "cite your sources" for inline references','Use "as of [year]" for current info','Ask follow-up questions to drill deeper'],
            summary: ['Specify format: "key takeaways in bullets"','Mention the source type: paper, article, book','Add "compare to [topic]" for richer summaries'],
            explain: ['Use "ELI5" for simple explanations','Ask for "real-world examples"','Use "step by step" for processes','Ask "What are common misconceptions?"'],
            compare: ['Be specific about comparison criteria','Ask for "a comparison table"','Include your use case','Ask about pros AND cons'],
            creative: ['Specify the tone and audience','Provide style references','Ask for variations on the same topic'],
            technical: ['Be specific about versions/frameworks','Ask for "code examples"','Request "trade-offs and alternatives"']
        };
        return m[pt] || m.research;
    }

    generateBtn.addEventListener('click', function () {
        var prompt = buildPrompt();
        if (!prompt) return;

        TCTP.copyText(prompt);
        output.innerHTML = '<pre style="white-space:pre-wrap;background:#0f172a;color:#e2e8f0;padding:16px;border-radius:12px;border:1px solid rgba(148,163,184,0.15);font-size:13px;line-height:1.7;margin:0">' + escHtml(prompt) + '</pre>';

        var tipsArr = getTips(type);
        tips.innerHTML = '<ul style="list-style:none;padding:0;margin:0">' + tipsArr.map(function (tip) {
            return '<li style="padding:10px 14px;margin-bottom:8px;background:#0f172a;border-radius:10px;border:1px solid rgba(148,163,184,0.12);color:#cbd5e1;font-size:13px;line-height:1.6"><i class="fa-solid fa-lightbulb" style="color:#0b1220;margin-right:8px"></i>' + escHtml(tip) + '</li>';
        }).join('') + '</ul>';

        resultEl.style.display = '';
        TCTP.initTabs(resultEl);
        TCTP.toast('Prompt copied!', '\u2705');
    });

    function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
})();
