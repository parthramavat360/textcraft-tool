/**
 * Coin Flipper — 100% client-side
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);

  let heads = 0, tails = 0, total = 0;

  function getCount(){ return parseInt($('#tc-cf-count')?.value || '1'); }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = $('#tc-cf-flip');
    if(!btn) return;
    btn.addEventListener('click', function(){
      const count = getCount();
      TCTP.showProgress('tc-cf-progress', 'Flipping...', 0);
      setTimeout(() => {
        const results = [];
        for(let i = 0; i < count; i++){
          const isHeads = Math.random() > 0.5;
          results.push(isHeads ? 'Heads' : 'Tails');
          if(isHeads) heads++; else tails++;
          total++;
        }
        TCTP.setProgress('tc-cf-progress', 100, 'Done!');
        const resultsEl = $('#tc-cf-results');
        if(resultsEl){
          resultsEl.innerHTML = results.map((r, i) => {
            const isH = r === 'Heads';
            return '<div class="tc-cf-card ' + (isH ? 'tc-cf-heads' : 'tc-cf-tails') + '"><span class="tc-cf-icon">' + (isH ? '🪙' : '🔴') + '</span><span class="tc-cf-label">' + r + '</span></div>';
          }).join('');
        }
        const statsEl = $('#tc-cf-stats');
        if(statsEl){
          const hPct = total > 0 ? (heads/total*100).toFixed(1) : '0.0';
          const tPct = total > 0 ? (tails/total*100).toFixed(1) : '0.0';
          statsEl.innerHTML = '<div class="tc-cf-stat-grid"><div class="tc-cf-stat"><span>Heads</span><b>' + heads + ' (' + hPct + '%)</b></div><div class="tc-cf-stat"><span>Tails</span><b>' + tails + ' (' + tPct + '%)</b></div><div class="tc-cf-stat"><span>Total</span><b>' + total + '</b></div></div>';
        }
        $('#tc-cf-heads').textContent = heads;
        $('#tc-cf-tails').textContent = tails;
        $('#tc-cf-total').textContent = total;
        TCTP.switchToResultTab();
        const last = results[results.length - 1];
        TCTP.toast('Last flip: ' + last,'success');
      }, 200);
    });
    $('#tc-cf-count')?.addEventListener('input', function(){ $('#tc-cf-count-val').textContent = this.value; });
  });
})();
