(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    var textEl = $('#rc-text');
    if (!textEl) return;

    var analyzeBtn = $('#rc-analyze');
    var resultEl = $('#rc-result');
    var scoresEl = $('#rc-scores');
    var statsEl = $('#rc-stats');
    var tipsEl = $('#rc-tips');

    function countSentences(text) {
        return (text.match(/[.!?]+/g) || []).length || 1;
    }

    function countWords(text) {
        return (text.trim().match(/\b[a-zA-Z']+\b/g) || []).length;
    }

    function countSyllables(word) {
        word = word.toLowerCase().replace(/[^a-z]/g, '');
        if (word.length <= 3) return 1;
        word = word.replace(/(?:[^laeiouy]es|ed|[^laeiouy]e)$/, '');
        word = word.replace(/^y/, '');
        var m = word.match(/[aeiouy]{1,2}/g);
        return m ? m.length : 1;
    }

    function totalSyllables(text) {
        var words = text.trim().match(/\b[a-zA-Z']+\b/g) || [];
        var total = 0;
        words.forEach(function (w) { total += countSyllables(w); });
        return total || 1;
    }

    function countCharacters(text) {
        return text.replace(/\s/g, '').length;
    }

    function fleschReadingEase(words, sentences, syllables) {
        return Math.round(206.835 - 1.015 * (words / sentences) - 84.6 * (syllables / words));
    }

    function fleschKincaidGrade(words, sentences, syllables) {
        return (0.39 * (words / sentences) + 11.8 * (syllables / words) - 15.59).toFixed(1);
    }

    function gunningFog(text, words, sentences) {
        var complexWords = 0;
        text.trim().match(/\b[a-zA-Z']+\b/g).forEach(function (w) {
            if (countSyllables(w) >= 3) complexWords++;
        });
        return (0.4 * (words / sentences + 100 * (complexWords / words))).toFixed(1);
    }

    function colemanLiau(text, words, sentences) {
        var chars = countCharacters(text);
        var L = (chars / words) * 100;
        var S = (sentences / words) * 100;
        return (0.0588 * L - 0.296 * S - 15.59).toFixed(1);
    }

    function readingLevel(grade) {
        var g = parseFloat(grade);
        if (g <= 5) return { label: 'Elementary', color: '#22c55e' };
        if (g <= 8) return { label: 'Middle School', color: '#3b82f6' };
        if (g <= 12) return { label: 'High School', color: '#eab308' };
        if (g <= 16) return { label: 'College', color: '#f97316' };
        return { label: 'Graduate', color: '#ef4444' };
    }

    function easeLabel(score) {
        if (score >= 90) return { label: 'Very Easy', color: '#22c55e' };
        if (score >= 80) return { label: 'Easy', color: '#22c55e' };
        if (score >= 70) return { label: 'Fairly Easy', color: '#3b82f6' };
        if (score >= 60) return { label: 'Standard', color: '#3b82f6' };
        if (score >= 50) return { label: 'Fairly Difficult', color: '#eab308' };
        if (score >= 30) return { label: 'Difficult', color: '#f97316' };
        return { label: 'Very Confusing', color: '#ef4444' };
    }

    analyzeBtn.addEventListener('click', function () {
        var text = textEl.value.trim();
        if (!text) { TCTP.toast('Enter some text first', '\u26a0\ufe0f'); return; }

        var words = countWords(text);
        var sentences = countSentences(text);
        var syllables = totalSyllables(text);
        var chars = countCharacters(text);
        var paragraphs = (text.match(/\n\s*\n/g) || []).length + 1;
        var readingTime = Math.ceil(words / 200);
        var speakingTime = Math.ceil(words / 130);

        var fre = fleschReadingEase(words, sentences, syllables);
        var fkg = fleschKincaidGrade(words, sentences, syllables);
        var gf = gunningFog(text, words, sentences);
        var cl = colemanLiau(text, words, sentences);
        var ease = easeLabel(fre);
        var level = readingLevel(fkg);

        scoresEl.innerHTML =
            '<div style="text-align:center;margin-bottom:20px">' +
            '<div style="font-size:64px;font-weight:800;color:' + ease.color + '">' + Math.max(0, Math.min(100, fre)) + '</div>' +
            '<div style="font-size:18px;font-weight:600;color:' + ease.color + '">' + ease.label + '</div>' +
            '<div style="color:#94a3b8;font-size:13px">Flesch Reading Ease Score</div>' +
            '</div>' +
            '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px">' +
            '<div style="background:#0f172a;padding:14px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:22px;font-weight:700;color:' + level.color + '">' + fkg + '</div><div style="color:#94a3b8;font-size:11px">F-K Grade Level</div><div style="color:' + level.color + ';font-size:11px">' + level.label + '</div></div>' +
            '<div style="background:#0f172a;padding:14px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:22px;font-weight:700;color:#0b1220">' + gf + '</div><div style="color:#94a3b8;font-size:11px">Gunning Fog</div></div>' +
            '<div style="background:#0f172a;padding:14px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:22px;font-weight:700;color:#8b5cf6">' + cl + '</div><div style="color:#94a3b8;font-size:11px">Coleman-Liau</div></div>' +
            '</div>';

        statsEl.innerHTML =
            '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:12px">' +
            '<div style="background:#0f172a;padding:14px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:22px;font-weight:700;color:#e2e8f0">' + words + '</div><div style="color:#94a3b8;font-size:11px">Words</div></div>' +
            '<div style="background:#0f172a;padding:14px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:22px;font-weight:700;color:#e2e8f0">' + sentences + '</div><div style="color:#94a3b8;font-size:11px">Sentences</div></div>' +
            '<div style="background:#0f172a;padding:14px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:22px;font-weight:700;color:#e2e8f0">' + paragraphs + '</div><div style="color:#94a3b8;font-size:11px">Paragraphs</div></div>' +
            '<div style="background:#0f172a;padding:14px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:22px;font-weight:700;color:#e2e8f0">' + chars + '</div><div style="color:#94a3b8;font-size:11px">Characters</div></div>' +
            '<div style="background:#0f172a;padding:14px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:22px;font-weight:700;color:#0b1220">' + readingTime + ' min</div><div style="color:#94a3b8;font-size:11px">Reading Time</div></div>' +
            '<div style="background:#0f172a;padding:14px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:22px;font-weight:700;color:#8b5cf6">' + speakingTime + ' min</div><div style="color:#94a3b8;font-size:11px">Speaking Time</div></div>' +
            '<div style="background:#0f172a;padding:14px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:22px;font-weight:700;color:#e2e8f0">' + (words / Math.max(1, sentences)).toFixed(1) + '</div><div style="color:#94a3b8;font-size:11px">Words/Sentence</div></div>' +
            '<div style="background:#0f172a;padding:14px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:22px;font-weight:700;color:#e2e8f0">' + (syllables / words).toFixed(1) + '</div><div style="color:#94a3b8;font-size:11px">Syllables/Word</div></div>' +
            '</div>';

        var tipsArr = [];
        if (fre < 50) tipsArr.push('Your text is difficult to read. Try using shorter sentences and simpler words.');
        if (parseFloat(fkg) > 12) tipsArr.push('The grade level is high. Aim for 8th-9th grade for general audiences.');
        if (words / sentences > 20) tipsArr.push('Average sentence length is ' + Math.round(words / sentences) + ' words. Try to keep sentences under 20 words.');
        var avgSyl = syllables / words;
        if (avgSyl > 1.8) tipsArr.push('Words have many syllables on average. Replace complex words with simpler alternatives.');
        if (paragraphs === 1 && words > 100) tipsArr.push('Add paragraph breaks to improve visual readability.');
        if (readingTime > 10) tipsArr.push('Article is long (' + readingTime + ' min read). Consider adding subheadings and bullet points.');
        if (tipsArr.length === 0) tipsArr.push('Your text has good readability! Keep up the clear writing.');

        tipsEl.innerHTML = '<ul style="list-style:none;padding:0;margin:0">' + tipsArr.map(function (t) {
            return '<li style="padding:12px 16px;margin-bottom:8px;background:#0f172a;border-radius:10px;border:1px solid rgba(148,163,184,0.12);color:#cbd5e1;font-size:13px;line-height:1.6"><i class="fa-solid fa-lightbulb" style="color:#0b1220;margin-right:8px"></i>' + escHtml(t) + '</li>';
        }).join('') + '</ul>';

        resultEl.style.display = '';
        TCTP.initTabs(resultEl);
    });

    function escHtml(s) { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
})();
