/**
 * Meta Description Generator — 100% client-side
 * SEO-optimized meta descriptions
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);

  const MAX_CHARS = 160;
  const OPTIMAL_MIN = 140;
  const OPTIMAL_MAX = 160;

  const TEMPLATES = {
    informative: [
      'Learn everything about {topic}. Our comprehensive guide covers key benefits, tips, and expert insights to help you make informed decisions.',
      'Discover {topic} — what it is, why it matters, and how to get started. Expert tips and practical advice inside.',
      'Looking for {topic}? We break down the essentials, compare options, and share expert recommendations to guide your choice.',
      '{topic}: A complete guide covering benefits, strategies, and expert tips. Everything you need in one place.',
      'Explore {topic} with our detailed guide. Get expert insights, practical tips, and everything you need to succeed.'
    ],
    persuasive: [
      'Ready to master {topic}? Our proven guide shows you exactly how — step by step. Start seeing results today.',
      'Don\'t miss out on {topic}. Join thousands who\'ve already transformed their approach. Get started now!',
      'Transform your {topic} game with our expert guide. Proven strategies, real results. Start today!',
      'Unlock the full potential of {topic}. Our guide reveals the strategies top performers use. Try it now!',
      'Why settle for less? Our {topic} guide shows you the proven path to success. Start your journey today.'
    ],
    question: [
      'Struggling with {topic}? Our guide answers the most common questions and shows you the proven path forward.',
      'What is {topic} and why does it matter? Find expert answers, tips, and practical advice in our detailed guide.',
      'Is {topic} right for you? Get expert insights, comparisons, and recommendations to make the best decision.',
      'Want to learn about {topic}? Our guide covers everything from basics to advanced strategies. Start learning!',
      'Confused about {topic}? We break it down in simple terms with actionable steps you can follow today.'
    ],
    urgency: [
      'Act now — learn {topic} before it\'s too late! Expert guide with proven strategies. Get started today!',
      'Don\'t fall behind on {topic}! Our guide shows you the fastest path to results. Start now!',
      'Time-sensitive: Master {topic} with our expert guide. Proven strategies that work. Start today!',
      'Stop waiting — start mastering {topic} today! Our proven guide shows you how in simple steps.',
      'The clock is ticking on {topic}! Get ahead with our expert guide. Strategies that deliver results.'
    ]
  };

  const CTAS = [
    'Start now!', 'Learn more today!', 'Get started!', 'Try it free!', 'Discover more!',
    'Read the full guide!', 'See how it works!', 'Get your copy!', 'Start your journey!',
    'Join now!', 'Explore now!', 'Get started today!', 'Learn more!', 'See the results!'
  ];

  function getTone(){ return document.querySelector('.tc-modes[data-group="md-tone"] .sel')?.dataset.val || 'professional'; }
  function getCount(){ return parseInt($('#tc-md-num')?.value || '5'); }
  function useCTA(){ return $('#tc-md-cta')?.checked; }
  function useEmoji(){ return $('#tc-md-emoji')?.checked; }
  function useFocus(){ return $('#tc-md-focus')?.checked; }

  function generateDescription(template, topic){
    let desc = template.replace(/{topic}/g, topic);
    if(useCTA()){
      const cta = CTAS[Math.floor(Math.random()*CTAS.length)];
      desc = desc.replace(/\.$/, ' ' + cta);
    }
    if(useEmoji()){
      const emojis = ['✨','💡','🚀','📌','⭐️','🔥','💎','🎯'];
      const emoji = emojis[Math.floor(Math.random()*emojis.length)];
      desc = emoji + ' ' + desc;
    }
    if(desc.length > MAX_CHARS) desc = desc.substring(0, MAX_CHARS - 3) + '...';
    return desc;
  }

  function analyzeLength(desc){
    const len = desc.length;
    let status, color;
    if(len >= OPTIMAL_MIN && len <= OPTIMAL_MAX){ status = 'Optimal'; color = '#22c55e'; }
    else if(len >= 120 && len <= 170){ status = 'Good'; color = '#f59e0b'; }
    else if(len < 70){ status = 'Too Short'; color = '#ef4444'; }
    else if(len > MAX_CHARS){ status = 'Too Long'; color = '#ef4444'; }
    else { status = 'Acceptable'; color = '#f59e0b'; }
    return { len, status, color, pct: Math.min(100, Math.round((len / MAX_CHARS) * 100)) };
  }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = $('#tc-md-generate');
    if(!btn) return;

    btn.addEventListener('click', function(){
      const topic = $('#tc-md-topic')?.value?.trim();
      if(!topic){ TCTP.toast('Please enter a page topic','warning'); return; }

      const tone = getTone();
      const count = getCount();
      TCTP.showProgress('tc-md-progress', 'Generating descriptions...', 0);

      setTimeout(() => {
        TCTP.setProgress('tc-md-progress', 50, 'Building descriptions...');
        const templates = TEMPLATES[tone] || TEMPLATES.informative;
        const descriptions = [];
        const shuffled = [...templates].sort(() => Math.random() - 0.5);
        for(let i = 0; i < Math.min(count, shuffled.length + 5); i++){
          descriptions.push(generateDescription(shuffled[i % shuffled.length], topic));
        }
        const unique = [...new Set(descriptions)].slice(0, count);

        setTimeout(() => {
          TCTP.setProgress('tc-md-progress', 100, 'Done!');

          const resultsEl = $('#tc-md-results');
          if(resultsEl){
            resultsEl.innerHTML = unique.map((d, i) => {
              const analysis = analyzeLength(d);
              return `
                <div class="tc-md-card">
                  <div class="tc-md-card-head">
                    <span class="tc-md-card-num">Description ${i + 1}</span>
                    <span class="tc-md-card-badge" style="color:${analysis.color}">${analysis.status} (${analysis.len} chars)</span>
                    <button class="tc-btn-sm" onclick="navigator.clipboard.writeText(this.closest('.tc-md-card').querySelector('pre').textContent);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',1500)">Copy</button>
                  </div>
                  <pre class="tc-md-card-text">${d.replace(/</g,'&lt;')}</pre>
                </div>
              `;
            }).join('');
          }

          const analysisEl = $('#tc-md-analysis');
          if(analysisEl){
            analysisEl.innerHTML = unique.map((d, i) => {
              const a = analyzeLength(d);
              return `
                <div class="tc-md-analysis-row">
                  <span class="tc-md-analysis-num">#${i + 1}</span>
                  <div class="tc-md-analysis-bar-wrap">
                    <div class="tc-md-analysis-bar" style="width:${a.pct}%;background:${a.color}"></div>
                  </div>
                  <span class="tc-md-analysis-info" style="color:${a.color}">${a.len} / ${MAX_CHARS} — ${a.status}</span>
                </div>
              `;
            }).join('') + `
              <div class="tc-md-seo-tips">
                <h4>SEO Best Practices</h4>
                <ul>
                  <li>Keep between ${OPTIMAL_MIN}-${MAX_CHARS} characters</li>
                  <li>Include your primary keyword naturally</li>
                  <li>Write a clear call-to-action</li>
                  <li>Make each description unique from page title</li>
                  <li>Avoid keyword stuffing</li>
                </ul>
              </div>
            `;
          }

          $('#tc-md-count').textContent = unique.length;
          const bestLen = unique.reduce((best, d) => {
            const a = analyzeLength(d);
            return a.len >= OPTIMAL_MIN && a.len <= OPTIMAL_MAX ? d : best;
          }, unique[0]);
          $('#tc-md-bestlen').textContent = bestLen ? analyzeLength(bestLen).len + ' chars' : '—';

          TCTP.switchToResultTab();
          TCTP.toast(unique.length + ' descriptions generated!','success');
        }, 300);
      }, 400);
    });

    document.querySelectorAll('.tc-modes[data-group="md-tone"] .tc-btn').forEach(btn => {
      btn.addEventListener('click', function(){
        this.closest('.tc-modes').querySelectorAll('.tc-btn').forEach(b => b.classList.remove('sel'));
        this.classList.add('sel');
      });
    });

    $('#tc-md-num')?.addEventListener('input', function(){
      $('#tc-md-num-val').textContent = this.value;
    });
  });
})();
