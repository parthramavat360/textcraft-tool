/**
 * Page Speed Checker — Tool JS
 * Google PageSpeed Insights analyzer with scores and Core Web Vitals.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var urlInput = document.getElementById('tc-ps-url');
    if (!urlInput) return;

    var analyzeBtn = document.getElementById('tc-ps-analyze');
    var loadingSection = document.getElementById('tc-ps-loading');
    var progressBar = document.getElementById('tc-ps-progress');
    var loadingText = document.getElementById('tc-ps-loading-text');
    var scoresEl = document.getElementById('tc-ps-scores');
    var detailsEl = document.getElementById('tc-ps-details');

    var strategy = 'mobile';
    var isAnalyzing = false;

    // ── Strategy cards ───────────────────────────────────────

    document.querySelectorAll('.tc-ps-strategy-cards .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-ps-strategy-cards .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            strategy = card.getAttribute('data-val') || 'mobile';
        });
    });

    // ── Helpers ──────────────────────────────────────────────

    function getScoreColor(score) {
        if (score >= 90) return '#16a34a';
        if (score >= 50) return '#ea580c';
        return '#dc2626';
    }

    function getScoreLabel(score) {
        if (score >= 90) return 'Good';
        if (score >= 50) return 'Needs Work';
        return 'Poor';
    }

    function getScoreRing(score, size) {
        size = size || 120;
        var color = getScoreColor(score);
        var radius = (size - 12) / 2;
        var circumference = 2 * Math.PI * radius;
        var offset = circumference - (score / 100) * circumference;
        return '<svg width="' + size + '" height="' + size + '" viewBox="0 0 ' + size + ' ' + size + '">' +
            '<circle cx="' + (size/2) + '" cy="' + (size/2) + '" r="' + radius + '" fill="none" stroke="#e2e8f0" stroke-width="8"/>' +
            '<circle cx="' + (size/2) + '" cy="' + (size/2) + '" r="' + radius + '" fill="none" stroke="' + color + '" stroke-width="8" ' +
            'stroke-dasharray="' + circumference + '" stroke-dashoffset="' + offset + '" stroke-linecap="round" ' +
            'transform="rotate(-90 ' + (size/2) + ' ' + (size/2) + ')" style="transition: stroke-dashoffset 1s ease"/>' +
            '<text x="' + (size/2) + '" y="' + (size/2) + '" text-anchor="middle" dominant-baseline="central" ' +
            'font-size="' + (size * 0.28) + '" font-weight="800" fill="' + color + '">' + score + '</text>' +
            '</svg>';
    }

    function getBucketLabel(val, unit) {
        if (val === null || val === undefined) return { label: 'N/A', color: '#94a3b8' };
        if (unit === 'score') {
            if (val >= 90) return { label: 'Good', color: '#16a34a' };
            if (val >= 50) return { label: 'Needs Work', color: '#ea580c' };
            return { label: 'Poor', color: '#dc2626' };
        }
        // For time-based metrics (ms), lower is better
        if (unit === 'ms') {
            // CLS is unitless
            return { label: '', color: '#94a3b8' };
        }
        return { label: '', color: '#94a3b8' };
    }

    function getMetricBucket(metric, value) {
        var thresholds = {
            lcp: [2500, 4000],
            fcp: [1800, 3000],
            cls: [0.1, 0.25],
            inp: [200, 500],
            ttfb: [800, 1800],
            si: [3400, 5800],
            tti: [3800, 7300]
        };
        var t = thresholds[metric];
        if (!t) return { label: '', color: '#94a3b8' };
        if (value <= t[0]) return { label: 'Good', color: '#16a34a' };
        if (value <= t[1]) return { label: 'Needs Work', color: '#ea580c' };
        return { label: 'Poor', color: '#dc2626' };
    }

    function formatMetric(metric, value) {
        if (value === null || value === undefined) return 'N/A';
        if (metric === 'cls') return value.toFixed(3);
        return Math.round(value) + ' ms';
    }

    // ── Loading simulation ───────────────────────────────────

    var loadMessages = [
        'Connecting to Google PageSpeed Insights...',
        'Fetching page resources...',
        'Analyzing render-blocking resources...',
        'Measuring Core Web Vitals...',
        'Running Lighthouse audits...',
        'Generating recommendations...'
    ];
    var loadTimer = null;
    var loadStep = 0;

    function startLoading() {
        loadingSection.style.display = '';
        loadStep = 0;
        progressBar.style.width = '10%';
        loadingText.textContent = loadMessages[0];
        loadTimer = setInterval(function () {
            loadStep++;
            if (loadStep < loadMessages.length) {
                loadingText.textContent = loadMessages[loadStep];
                progressBar.style.width = Math.min(10 + (loadStep + 1) * 15, 90) + '%';
            }
        }, 3000);
    }

    function stopLoading() {
        if (loadTimer) { clearInterval(loadTimer); loadTimer = null; }
        progressBar.style.width = '100%';
        loadingText.textContent = 'Done!';
        setTimeout(function () { loadingSection.style.display = 'none'; }, 500);
    }

    // ── Analyze ──────────────────────────────────────────────

    var cooldownTimer = null;

    function startCooldown(seconds) {
        var remaining = seconds;
        loadingText.textContent = 'Rate limited. Cooldown: ' + remaining + 's...';
        progressBar.style.width = '30%';
        cooldownTimer = setInterval(function () {
            remaining--;
            if (remaining <= 0) {
                clearInterval(cooldownTimer);
                cooldownTimer = null;
                return;
            }
            loadingText.textContent = 'Rate limited. Cooldown: ' + remaining + 's...';
            progressBar.style.width = (30 + ((seconds - remaining) / seconds) * 60) + '%';
        }, 1000);
    }

    analyzeBtn.addEventListener('click', function () {
        if (isAnalyzing) return;

        var url = urlInput.value.trim();
        if (!url) { TCTP.toast('Please enter a URL', '\u26a0\ufe0f'); return; }
        if (!/^https?:\/\//i.test(url)) { url = 'https://' + url; urlInput.value = url; }
        if (!/^https?:\/\/.+\..+/.test(url)) { TCTP.toast('Please enter a valid URL', '\u26a0\ufe0f'); return; }

        isAnalyzing = true;
        analyzeBtn.disabled = true;
        analyzeBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Analyzing...';
        scoresEl.innerHTML = '<p style="color:#64748b;text-align:center;padding:40px 0">Analyzing... please wait</p>';
        detailsEl.innerHTML = '';
        startLoading();

        var apiUrl = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=' +
            encodeURIComponent(url) + '&strategy=' + strategy + '&category=performance&category=accessibility&category=seo&category=best-practices';

        var retries = 2;

        function attemptFetch(attempt) {
            fetch(apiUrl)
                .then(function (res) {
                    if (res.status === 429) {
                        if (cooldownTimer) clearInterval(cooldownTimer);
                        if (attempt < retries) {
                            var waitTime = 30 + (attempt * 15);
                            startCooldown(waitTime);
                            return new Promise(function (resolve) { setTimeout(resolve, waitTime * 1000); }).then(function () {
                                if (cooldownTimer) { clearInterval(cooldownTimer); cooldownTimer = null; }
                                loadingText.textContent = 'Retrying...';
                                return attemptFetch(attempt + 1);
                            });
                        }
                        throw new Error('Rate limited by Google. Please wait 1 minute and try again.');
                    }
                    if (!res.ok) throw new Error('API returned status ' + res.status);
                    return res.json();
                })
                .then(function (data) {
                    if (!data || !data.lighthouseResult) {
                        var errMsg = 'No results returned. The URL may be unreachable or blocked by the server.';
                        if (data && data.error && data.error.message) errMsg = data.error.message;
                        throw new Error(errMsg);
                    }
                    if (data.lighthouseResult.httpStatus && data.lighthouseResult.httpStatus >= 400) {
                        throw new Error('Page returned HTTP ' + data.lighthouseResult.httpStatus + '. Make sure the URL is accessible.');
                    }
                    stopLoading();
                    if (cooldownTimer) { clearInterval(cooldownTimer); cooldownTimer = null; }
                    renderResults(data, url);
                    isAnalyzing = false;
                    analyzeBtn.disabled = false;
                    analyzeBtn.innerHTML = '<i class="fa-solid fa-play"></i> Analyze';
                })
                .catch(function (err) {
                    stopLoading();
                    if (cooldownTimer) { clearInterval(cooldownTimer); cooldownTimer = null; }
                    scoresEl.innerHTML = '<div class="tc-ps-error"><i class="fa-solid fa-circle-exclamation"></i><p>Analysis failed: ' + (err.message || 'Unknown error') + '</p><p style="font-size:12px;color:#94a3b8">Google limits free PageSpeed API to ~1 request per minute per IP. Wait 60s and try again.</p></div>';
                    isAnalyzing = false;
                    analyzeBtn.disabled = false;
                    analyzeBtn.innerHTML = '<i class="fa-solid fa-play"></i> Analyze';
                });
        }

        attemptFetch(0);
    });

    // Enter key
    urlInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') analyzeBtn.click();
    });

    // ── Render results ───────────────────────────────────────

    function renderResults(data, url) {
        var lh = data.lighthouseResult || {};
        var cats = lh.categories || {};
        var audits = lh.audits || {};

        var perfScore = Math.round((cats.performance || {}).score * 100) || 0;
        var a11yScore = Math.round((cats.accessibility || {}).score * 100) || 0;
        var seoScore = Math.round((cats.seo || {}).score * 100) || 0;
        var bpScore = Math.round((cats['best-practices'] || {}).score * 100) || 0;

        var strategyLabel = strategy === 'mobile' ? 'Mobile' : 'Desktop';

        // Score gauges
        scoresEl.innerHTML =
            '<div class="tc-ps-url-bar">' +
                '<span class="tc-ps-url-text"><i class="fa-solid fa-globe"></i> ' + escapeHtml(url) + '</span>' +
                '<span class="tc-ps-strategy-badge">' + strategyLabel + '</span>' +
            '</div>' +
            '<div class="tc-ps-gauges">' +
                '<div class="tc-ps-gauge">' +
                    getScoreRing(perfScore) +
                    '<div class="tc-ps-gauge-label">Performance</div>' +
                    '<div class="tc-ps-gauge-status" style="color:' + getScoreColor(perfScore) + '">' + getScoreLabel(perfScore) + '</div>' +
                '</div>' +
                '<div class="tc-ps-gauge">' +
                    getScoreRing(a11yScore) +
                    '<div class="tc-ps-gauge-label">Accessibility</div>' +
                    '<div class="tc-ps-gauge-status" style="color:' + getScoreColor(a11yScore) + '">' + getScoreLabel(a11yScore) + '</div>' +
                '</div>' +
                '<div class="tc-ps-gauge">' +
                    getScoreRing(seoScore) +
                    '<div class="tc-ps-gauge-label">SEO</div>' +
                    '<div class="tc-ps-gauge-status" style="color:' + getScoreColor(seoScore) + '">' + getScoreLabel(seoScore) + '</div>' +
                '</div>' +
                '<div class="tc-ps-gauge">' +
                    getScoreRing(bpScore) +
                    '<div class="tc-ps-gauge-label">Best Practices</div>' +
                    '<div class="tc-ps-gauge-status" style="color:' + getScoreColor(bpScore) + '">' + getScoreLabel(bpScore) + '</div>' +
                '</div>' +
            '</div>';

        // Details tab
        var detailsHtml = '';

        // Core Web Vitals
        var cwvMetrics = [
            { key: 'largest-contentful-paint', name: 'LCP', full: 'Largest Contentful Paint', metric: 'lcp' },
            { key: 'cumulative-layout-shift', name: 'CLS', full: 'Cumulative Layout Shift', metric: 'cls' },
            { key: 'interaction-to-next-paint', name: 'INP', full: 'Interaction to Next Paint', metric: 'inp' },
            { key: 'first-contentful-paint', name: 'FCP', full: 'First Contentful Paint', metric: 'fcp' },
            { key: 'speed-index', name: 'SI', full: 'Speed Index', metric: 'si' },
            { key: 'total-blocking-time', name: 'TBT', full: 'Total Blocking Time', metric: 'tbt' }
        ];

        detailsHtml += '<div class="tc-ps-section"><h4 class="tc-ps-section-title"><i class="fa-solid fa-chart-line"></i> Core Web Vitals</h4><div class="tc-ps-metrics">';
        cwvMetrics.forEach(function (m) {
            var audit = audits[m.key] || {};
            var val = audit.numericValue || null;
            var bucket = getMetricBucket(m.metric, val);
            detailsHtml += '<div class="tc-ps-metric-card">' +
                '<div class="tc-ps-metric-name">' + m.name + '</div>' +
                '<div class="tc-ps-metric-full">' + m.full + '</div>' +
                '<div class="tc-ps-metric-value">' + formatMetric(m.metric, val) + '</div>' +
                '<div class="tc-ps-metric-badge" style="background:' + bucket.color + '20;color:' + bucket.color + '">' + (bucket.label || 'N/A') + '</div>' +
            '</div>';
        });
        detailsHtml += '</div></div>';

        // Opportunities
        var opps = [];
        Object.keys(audits).forEach(function (k) {
            var a = audits[k];
            if (a.details && a.details.type === 'opportunity' && a.score !== null && a.score < 1) {
                opps.push({ id: k, title: a.title, description: a.description, savings: a.displayValue || '' });
            }
        });
        if (opps.length > 0) {
            detailsHtml += '<div class="tc-ps-section"><h4 class="tc-ps-section-title"><i class="fa-solid fa-lightbulb"></i> Opportunities (' + opps.length + ')</h4><div class="tc-ps-list">';
            opps.forEach(function (o) {
                detailsHtml += '<div class="tc-ps-list-item"><div class="tc-ps-list-title">' + escapeHtml(o.title) + '</div>' +
                    '<div class="tc-ps-list-desc">' + escapeHtml(o.description).substring(0, 120) + '...</div>' +
                    (o.savings ? '<div class="tc-ps-list-saving">Potential savings: ' + escapeHtml(o.savings) + '</div>' : '') + '</div>';
            });
            detailsHtml += '</div></div>';
        }

        // Diagnostics
        var diags = [];
        Object.keys(audits).forEach(function (k) {
            var a = audits[k];
            if (a.details && a.details.type === 'table' && a.score !== null && a.score < 1 && !a.details.overallSavingsMs) {
                if (diags.length < 8) {
                    diags.push({ title: a.title, description: a.description });
                }
            }
        });
        if (diags.length > 0) {
            detailsHtml += '<div class="tc-ps-section"><h4 class="tc-ps-section-title"><i class="fa-solid fa-magnifying-glass"></i> Diagnostics</h4><div class="tc-ps-list">';
            diags.forEach(function (d) {
                detailsHtml += '<div class="tc-ps-list-item"><div class="tc-ps-list-title">' + escapeHtml(d.title) + '</div>' +
                    '<div class="tc-ps-list-desc">' + escapeHtml(d.description).substring(0, 120) + '...</div></div>';
            });
            detailsHtml += '</div></div>';
        }

        detailsEl.innerHTML = detailsHtml || '<p style="color:#64748b;padding:12px 0">No additional details available.</p>';

        // Update status chip
        var chip = document.getElementById('tc-status-chip');
        if (chip) { chip.textContent = 'Done'; chip.style.color = '#16a34a'; }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
})();
