/**
 * YouTube Title Generator — 100% client-side
 * SEO-optimized title templates
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);

  const POWER_WORDS = ['Best','Easy','Simple','Secret','Ultimate','Essential','Proven','Amazing','Incredible','Hidden','Complete','Essential','Powerful','Stunning','Mind-Blowing'];
  const YEARS = ['2024','2025'];

  const TEMPLATES = {
    howto: [
      'How to {topic} (Step-by-Step Guide)',
      'How to {topic} for Beginners in {year}',
      'How to {topic} Like a Pro — {power} Tips',
      'How I {topic} — My Exact Process',
      '{power} Way to {topic} (Works Every Time)',
      'How to {topic}: {power} Guide for {year}',
      'The RIGHT Way to {topic} (Beginners Watch This)',
      'How to {topic} — {power} Secrets Revealed',
      '{topic}: How to Get Started Today'
    ],
    listicle: [
      '{number} {power} Tips for {topic} in {year}',
      '{number} Mistakes You\'re Making with {topic}',
      '{number} {topic} Hacks That Actually Work',
      'Top {number} {topic} Tips Nobody Tells You',
      '{number} Ways to {topic} (Save This!)',
      '{number} {topic} Secrets the Experts Use',
      'The {number} BEST {topic} Tips for {year}',
      '{number} {topic} Rules You Need to Follow',
      'I Tried {number} {topic} Methods — Here\'s What Works'
    ],
    question: [
      'Is {topic} Worth It in {year}? (Honest Review)',
      'Why Is {topic} So Popular Right Now?',
      'What Nobody Tells You About {topic}',
      'Can You Really {topic}? (I Tested It)',
      'Does {topic} Actually Work? (Here\'s the Truth)',
      'Why Everyone Is Talking About {topic} in {year}',
      'Are You Making These {topic} Mistakes?',
      'How Good Is {topic} Really? (My {year} Review)',
      'Should You {topic}? (Pros and Cons)'
    ],
    ultimate: [
      'The Ultimate Guide to {topic} in {year}',
      '{topic}: The Complete Guide for {year}',
      'Everything You Need to Know About {topic}',
      'The {power} Guide to {topic} (Don\'t Skip This)',
      '{topic} MASTERCLASS — {power} Tutorial for {year}',
      'The ONLY {topic} Guide You\'ll Ever Need',
      '{topic} A to Z: Complete {year} Guide',
      'ULTIMATE {topic} Guide (Start to Finish)',
      'Master {topic} in {year} — Complete Guide'
    ],
    clickbait: [
      'I Can\'t Believe {topic} Actually Works...',
      'This {topic} Trick Changed Everything',
      'Stop Doing {topic} Wrong (Do This Instead)',
      '{topic}: What They Don\'t Want You to Know',
      'The {topic} Secret Nobody Is Talking About',
      'Watch This Before You {topic}',
      'I Discovered the {power} {topic} Method',
      'You\'re Losing Money If You Don\'t Know This About {topic}',
      'This One {topic} Hack Will Blow Your Mind'
    ],
    review: [
      '{topic} Review — {year} Honest Take',
      'Is {topic} the BEST in {year}? (My Review)',
      '{topic}: {power} Review After 30 Days',
      'Honest {topic} Review — Is It Worth It?',
      '{topic} — {power} Review for {year}',
      'I Tried {topic} for 30 Days — Here\'s What Happened',
      '{topic} Full Review: {power} Pros and Cons',
      'The TRUTH About {topic} (Honest {year} Review)',
      '{topic} Review: Should You Buy It?'
    ]
  };

  function getStyle(){ return document.querySelector('.tc-modes[data-group="yt-style"] .sel')?.dataset.val || 'howto'; }
  function getCount(){ return parseInt($('#tc-yt-num')?.value || '10'); }
  function useEmoji(){ return $('#tc-yt-emoji')?.checked; }
  function useYear(){ return $('#tc-yt-year')?.checked; }
  function usePower(){ return $('#tc-yt-power')?.checked; }

  function generateTitle(template, topic, opts){
    let title = template.replace(/{topic}/g, topic);
    if(useYear()) title = title.replace(/{year}/g, YEARS[Math.floor(Math.random()*YEARS.length)]);
    if(usePower()) title = title.replace(/{power}/g, POWER_WORDS[Math.floor(Math.random()*POWER_WORDS.length)]);
    title = title.replace(/\s*-\s*\(([^)]+)\)/, ' ($1)');
    title = title.replace(/\s{2,}/g, ' ').trim();
    if(title.length > 100) title = title.substring(0, 97) + '...';
    return title;
  }

  function analyzeSEO(title){
    const checks = [];
    checks.push({ pass: title.length <= 60, label: 'Length ≤ 60 chars', detail: title.length + ' chars' });
    checks.push({ pass: title.length >= 30, label: 'Length ≥ 30 chars', detail: title.length + ' chars' });
    checks.push({ pass: /[A-Z]/.test(title[0]), label: 'Starts with capital', detail: title[0] });
    checks.push({ pass: /\d/.test(title), label: 'Contains number', detail: /\d/.exec(title)?.[0] || 'none' });
    checks.push({ pass: /[?!]/.test(title), label: 'Contains punctuation', detail: /[?!]/.exec(title)?.[0] || 'none' });
    checks.push({ pass: title.length <= 70, label: 'Under 70 chars (full display)', detail: title.length + ' chars' });
    return checks;
  }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = $('#tc-yt-generate');
    if(!btn) return;

    btn.addEventListener('click', function(){
      const topic = $('#tc-yt-topic')?.value?.trim();
      if(!topic){ TCTP.toast('Please enter a video topic','warning'); return; }

      const style = getStyle();
      const count = getCount();
      TCTP.showProgress('tc-yt-progress', 'Generating titles...', 0);

      setTimeout(() => {
        TCTP.setProgress('tc-yt-progress', 50, 'Building titles...');
        const templates = TEMPLATES[style] || TEMPLATES.howto;
        const titles = [];
        const shuffled = [...templates].sort(() => Math.random() - 0.5);
        for(let i = 0; i < Math.min(count, shuffled.length); i++){
          titles.push(generateTitle(shuffled[i], topic));
        }

        setTimeout(() => {
          TCTP.setProgress('tc-yt-progress', 100, 'Done!');

          const titlesEl = $('#tc-yt-titles');
          if(titlesEl){
            titlesEl.innerHTML = titles.map((t, i) => {
              const len = t.length;
              const color = len <= 60 ? '#22c55e' : len <= 70 ? '#f59e0b' : '#ef4444';
              return `
                <div class="tc-yt-title-card">
                  <div class="tc-yt-title-head">
                    <span class="tc-yt-title-num">Title ${i + 1}</span>
                    <span class="tc-yt-title-len" style="color:${color}">${len} chars</span>
                    <button class="tc-btn-sm" onclick="navigator.clipboard.writeText(this.closest('.tc-yt-title-card').querySelector('.tc-yt-title-text').textContent);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',1500)">Copy</button>
                  </div>
                  <div class="tc-yt-title-text">${t}</div>
                </div>
              `;
            }).join('');
          }

          const seoEl = $('#tc-yt-seo');
          if(seoEl){
            seoEl.innerHTML = titles.map((t, i) => {
              const checks = analyzeSEO(t);
              const passCount = checks.filter(c => c.pass).length;
              return `
                <div class="tc-yt-seo-item">
                  <h4>Title ${i + 1} <span class="tc-yt-seo-score ${passCount >= 4 ? 'good' : passCount >= 2 ? 'warn' : 'bad'}">${passCount}/6</span></h4>
                  <p class="tc-yt-seo-title">${t}</p>
                  <div class="tc-yt-seo-checks">
                    ${checks.map(c => `<span class="tc-yt-check ${c.pass ? 'pass' : 'fail'}">${c.pass ? '✓' : '✗'} ${c.label} (${c.detail})</span>`).join('')}
                  </div>
                </div>
              `;
            }).join('');
          }

          $('#tc-yt-count').textContent = titles.length;
          const avgLen = Math.round(titles.reduce((a,t) => a + t.length, 0) / titles.length);
          $('#tc-yt-avglen').textContent = avgLen + ' chars';

          TCTP.switchToResultTab();
          TCTP.toast(titles.length + ' titles generated!','success');
        }, 300);
      }, 400);
    });

    document.querySelectorAll('.tc-modes[data-group="yt-style"] .tc-btn').forEach(btn => {
      btn.addEventListener('click', function(){
        this.closest('.tc-modes').querySelectorAll('.tc-btn').forEach(b => b.classList.remove('sel'));
        this.classList.add('sel');
      });
    });

    $('#tc-yt-num')?.addEventListener('input', function(){
      $('#tc-yt-num-val').textContent = this.value;
    });
  });
})();
