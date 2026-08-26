/**
 * Dice Roller — 100% client-side
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);

  let rollCount = 0, rollHistory = [];

  function getSides(){ return parseInt(document.querySelector('.tc-modes[data-group="dr-sides"] .sel')?.dataset.val || '6'); }
  function getCount(){ return parseInt($('#tc-dr-count')?.value || '1'); }
  function getMod(){ return parseInt($('#tc-dr-mod')?.value || '0'); }

  function rollDice(sides, count, mod){
    const rolls = [];
    for(let i = 0; i < count; i++) rolls.push(Math.floor(Math.random() * sides) + 1);
    const total = rolls.reduce((a,b) => a + b, 0) + mod;
    return { rolls, total, sides, mod };
  }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = $('#tc-dr-roll');
    if(!btn) return;
    btn.addEventListener('click', function(){
      const sides = getSides(), count = getCount(), mod = getMod();
      TCTP.showProgress('tc-dr-progress', 'Rolling...', 0);
      setTimeout(() => {
        const result = rollDice(sides, count, mod);
        rollCount++;
        rollHistory.unshift(result);
        if(rollHistory.length > 50) rollHistory.pop();
        TCTP.setProgress('tc-dr-progress', 100, 'Done!');
        const resultsEl = $('#tc-dr-results');
        if(resultsEl){
          resultsEl.innerHTML = rollHistory.map((r, i) => {
            const modStr = r.mod !== 0 ? (r.mod > 0 ? ' + ' + r.mod : ' - ' + Math.abs(r.mod)) : '';
            return '<div class="tc-dr-roll-card"><div class="tc-dr-roll-head"><span class="tc-dr-roll-num">Roll ' + (rollCount - i) + '</span><span class="tc-dr-roll-total">' + r.total + '</span></div><div class="tc-dr-roll-detail">D' + r.sides + ': [' + r.rolls.join(', ') + ']' + modStr + ' = <b>' + r.total + '</b></div></div>';
          }).join('');
        }
        const statsEl = $('#tc-dr-stats');
        if(statsEl){
          const allTotals = rollHistory.map(r => r.total);
          const sum = allTotals.reduce((a,b) => a+b, 0);
          const avg = (sum / allTotals.length).toFixed(1);
          const min = Math.min(...allTotals);
          const max = Math.max(...allTotals);
          statsEl.innerHTML = '<div class="tc-dr-stat-grid"><div class="tc-dr-stat"><span>Total Rolls</span><b>' + rollCount + '</b></div><div class="tc-dr-stat"><span>Average</span><b>' + avg + '</b></div><div class="tc-dr-stat"><span>Min</span><b>' + min + '</b></div><div class="tc-dr-stat"><span>Max</span><b>' + max + '</b></div></div>';
        }
        $('#tc-dr-total').textContent = rollCount;
        $('#tc-dr-last').textContent = result.total;
        TCTP.switchToResultTab();
        TCTP.toast('Rolled! Total: ' + result.total,'success');
      }, 200);
    });
    var sidesGroup = document.querySelector('[data-group="dr-sides"]');
    if(sidesGroup){ TCTP.initModeGroup(sidesGroup); }
    $('#tc-dr-count')?.addEventListener('input', function(){ $('#tc-dr-count-val').textContent = this.value; });
    $('#tc-dr-mod')?.addEventListener('input', function(){ const v = parseInt(this.value); $('#tc-dr-mod-val').textContent = (v >= 0 ? '+' : '') + v; });
  });
})();
