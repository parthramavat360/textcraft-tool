/**
 * Robots.txt Generator — Tool JS
 * Build robots.txt with a visual editor.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var rulesContainer = document.getElementById('tc-rt-rules');
    if (!rulesContainer) return;

    var sitemapInput = document.getElementById('tc-rt-sitemap');
    var delayInput = document.getElementById('tc-rt-delay');
    var codeOutput = document.getElementById('tc-rt-code');
    var previewEl = document.getElementById('tc-rt-preview');

    var rules = [];
    var ruleIdCounter = 0;

    var presets = {
        'allow-all': [
            { agent: '*', disallow: '', allow: '/' }
        ],
        'block-all': [
            { agent: '*', disallow: '/', allow: '' }
        ],
        'block-ai': [
            { agent: '*', disallow: '', allow: '/' },
            { agent: 'GPTBot', disallow: '/', allow: '' },
            { agent: 'ChatGPT-User', disallow: '/', allow: '' },
            { agent: 'CCBot', disallow: '/', allow: '' },
            { agent: 'anthropic-ai', disallow: '/', allow: '' },
            { agent: 'Google-Extended', disallow: '/', allow: '' }
        ],
        'standard': [
            { agent: '*', disallow: '/wp-admin/', allow: '/wp-admin/admin-ajax.php' },
            { agent: '*', disallow: '/wp-includes/', allow: '' },
            { agent: 'Googlebot', disallow: '', allow: '/' }
        ]
    };

    // ── Presets ──────────────────────────────────────────────

    document.querySelectorAll('.tc-rt-preset-cards .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-rt-preset-cards .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            var preset = card.getAttribute('data-preset');
            rules = presets[preset].map(function (r) {
                return { id: ++ruleIdCounter, agent: r.agent, disallow: r.disallow, allow: r.allow };
            });
            renderRules();
            updatePreview();
        });
    });

    // ── Add rule ─────────────────────────────────────────────

    document.getElementById('tc-rt-add-rule').addEventListener('click', function () {
        rules.push({ id: ++ruleIdCounter, agent: '*', disallow: '', allow: '/' });
        renderRules();
        updatePreview();
    });

    // ── Render rules ─────────────────────────────────────────

    function renderRules() {
        rulesContainer.innerHTML = '';
        rules.forEach(function (rule, idx) {
            var div = document.createElement('div');
            div.className = 'tc-rt-rule-row';
            div.innerHTML =
                '<div class="tc-rt-rule-fields">' +
                    '<div class="tc-input-group"><label class="tc-label">User-Agent</label>' +
                        '<input type="text" class="tc-input tc-rt-rule-input" data-idx="' + idx + '" data-prop="agent" value="' + rule.agent + '" placeholder="*"></div>' +
                    '<div class="tc-input-group"><label class="tc-label">Disallow</label>' +
                        '<input type="text" class="tc-input tc-rt-rule-input" data-idx="' + idx + '" data-prop="disallow" value="' + rule.disallow + '" placeholder="/path/"></div>' +
                    '<div class="tc-input-group"><label class="tc-label">Allow</label>' +
                        '<input type="text" class="tc-input tc-rt-rule-input" data-idx="' + idx + '" data-prop="allow" value="' + rule.allow + '" placeholder="/path/"></div>' +
                '</div>' +
                '<button class="tc-btn tc-btn--ghost tc-rt-rule-del" data-idx="' + idx + '" type="button"><i class="fa-solid fa-xmark"></i></button>';
            rulesContainer.appendChild(div);
        });

        rulesContainer.querySelectorAll('.tc-rt-rule-input').forEach(function (inp) {
            inp.addEventListener('input', function () {
                var i = parseInt(inp.getAttribute('data-idx'));
                var prop = inp.getAttribute('data-prop');
                rules[i][prop] = inp.value;
                updatePreview();
            });
        });

        rulesContainer.querySelectorAll('.tc-rt-rule-del').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var i = parseInt(btn.getAttribute('data-idx'));
                rules.splice(i, 1);
                renderRules();
                updatePreview();
            });
        });
    }

    // ── Update ───────────────────────────────────────────────

    sitemapInput.addEventListener('input', updatePreview);
    delayInput.addEventListener('input', updatePreview);

    function updatePreview() {
        var lines = [];
        var sitemap = sitemapInput.value.trim();
        var delay = delayInput.value.trim();

        // Group rules by user-agent
        var groups = {};
        rules.forEach(function (r) {
            var agent = r.agent.trim() || '*';
            if (!groups[agent]) groups[agent] = [];
            groups[agent].push(r);
        });

        Object.keys(groups).forEach(function (agent) {
            lines.push('User-agent: ' + agent);
            groups[agent].forEach(function (r) {
                if (r.disallow) lines.push('Disallow: ' + r.disallow);
                if (r.allow) lines.push('Allow: ' + r.allow);
            });
            if (delay && agent !== '*') {
                lines.push('Crawl-delay: ' + delay);
            }
            lines.push('');
        });

        if (delay && !groups['*']) {
            lines.push('User-agent: *');
            lines.push('Crawl-delay: ' + delay);
            lines.push('');
        }

        if (sitemap) {
            lines.push('Sitemap: ' + sitemap);
        }

        var text = lines.join('\n').replace(/\n{3,}/g, '\n\n').trim();
        codeOutput.value = text;

        // Preview
        if (previewEl) {
            previewEl.innerHTML = '<pre class="tc-rt-preview-code">' + text.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</pre>';
        }
    }

    // ── Copy ─────────────────────────────────────────────────

    document.getElementById('tc-rt-copy').addEventListener('click', function () {
        TCTP.copyText(codeOutput.value);
        TCTP.toast('robots.txt copied!', '\u2705');
    });

    // ── Init ─────────────────────────────────────────────────

    rules = presets['allow-all'].map(function (r) {
        return { id: ++ruleIdCounter, agent: r.agent, disallow: r.disallow, allow: r.allow };
    });
    renderRules();
    updatePreview();
})();
