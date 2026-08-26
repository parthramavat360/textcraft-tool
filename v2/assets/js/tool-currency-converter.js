/**
 * Currency Converter — live rates from fawazahmed0/exchange-api
 * All 341 currencies hardcoded in HTML. JS handles search, rates, and conversion.
 * CDN: cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/{base}.json
 * Fallback: latest.currency-api.pages.dev/v1/currencies/{base}.json
 */
(function(){
  'use strict';

  var API_PRIMARY = 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/';
  var API_FALLBACK = 'https://latest.currency-api.pages.dev/v1/currencies/';
  var CACHE_KEY = 'tctp_cc_rates_';
  var CACHE_TTL = 60 * 60 * 1000;

  var fromSel   = document.querySelector('.tc-cc-from');
  var toSel     = document.querySelector('.tc-cc-to');
  var fromSearch = document.querySelector('.tc-cc-from-search');
  var toSearch   = document.querySelector('.tc-cc-to-search');
  var amountIn  = document.querySelector('.tc-cc-amount');
  var swapBtn   = document.querySelector('.tc-cc-swap');
  var convertBtn= document.getElementById('tc-cc-convert');
  var copyBtn   = document.getElementById('tc-cc-copy');
  var toggleRate= document.getElementById('tc-cc-toggle-rate');
  var customRateRow = document.querySelector('.tc-cc-manual-rate');
  var customRateIn  = document.querySelector('.tc-cc-custom-rate');
  var statusEl  = document.getElementById('tc-cc-status');
  var resultPanel = document.getElementById('tc-cc-result-panel');
  var resultFrom = document.getElementById('tc-cc-result-from');
  var resultTo   = document.getElementById('tc-cc-result-to');
  var resultRate = document.getElementById('tc-cc-result-rate');
  var resultDetails = document.getElementById('tc-cc-result-details');
  var statOrig  = document.getElementById('tc-stat-orig');
  var statComp  = document.getElementById('tc-stat-comp');
  var statSaved = document.getElementById('tc-stat-saved');
  var statusChip= document.getElementById('tc-status-chip');

  if(!fromSel || !toSel || !amountIn) return;

  var currentRates = null;
  var useCustomRate = false;

  // ── Cache helpers ────────────────────────────────────────
  function getCached(base){
    try{
      var raw = localStorage.getItem(CACHE_KEY + base);
      if(!raw) return null;
      var obj = JSON.parse(raw);
      if(Date.now() - obj.ts > CACHE_TTL) return null;
      return obj.data;
    }catch(e){ return null; }
  }

  function setCache(base, data){
    try{
      localStorage.setItem(CACHE_KEY + base, JSON.stringify({ ts: Date.now(), data: data }));
    }catch(e){}
  }

  // ── Search filtering ─────────────────────────────────────
  function setupSearch(searchInput, selectEl){
    if(!searchInput || !selectEl) return;

    searchInput.addEventListener('input', function(){
      var q = this.value.toLowerCase().trim();
      var options = selectEl.options;
      var foundFirst = false;
      for(var i = 0; i < options.length; i++){
        var text = options[i].textContent.toLowerCase();
        var val = options[i].value.toLowerCase();
        var match = !q || text.indexOf(q) !== -1 || val.indexOf(q) !== -1;
        options[i].style.display = match ? '' : 'none';
        if(match && !foundFirst && !q){
          foundFirst = true;
        }
      }
      // Auto-select first visible if current is hidden
      var cur = selectEl.options[selectEl.selectedIndex];
      if(cur && cur.style.display === 'none'){
        for(var j = 0; j < options.length; j++){
          if(options[j].style.display !== 'none'){
            selectEl.value = options[j].value;
            break;
          }
        }
      }
    });

    searchInput.addEventListener('focus', function(){
      this.select();
    });

    selectEl.addEventListener('change', function(){
      if(searchInput) searchInput.value = '';
      var opts = selectEl.options;
      for(var i = 0; i < opts.length; i++) opts[i].style.display = '';
    });
  }

  setupSearch(fromSearch, fromSel);
  setupSearch(toSearch, toSel);

  // ── Fetch rates ──────────────────────────────────────────
  function fetchRates(base){
    var cached = getCached(base);
    if(cached){
      currentRates = cached;
      currentRates._base = base;
      setStatus('Rates loaded (cached)', 'ok');
      return Promise.resolve(cached);
    }

    setStatus('Fetching live rates...', 'loading');
    return fetch(API_PRIMARY + base + '.json')
      .then(function(r){ if(!r.ok) throw new Error('Primary failed'); return r.json(); })
      .catch(function(){ return fetch(API_FALLBACK + base + '.json').then(function(r){ if(!r.ok) throw new Error('Fallback failed'); return r.json(); }); })
      .then(function(data){
        var rates = data[base] || data;
        rates._base = base;
        setCache(base, rates);
        currentRates = rates;
        setStatus('Live rates loaded \u2014 ' + (data.date || 'today'), 'ok');
        return rates;
      })
      .catch(function(){
        setStatus('Could not fetch rates. Check your connection.', 'error');
        return null;
      });
  }

  // ── Convert ──────────────────────────────────────────────
  function convert(){
    var from = fromSel.value.toLowerCase();
    var to   = toSel.value.toLowerCase();
    var amt  = parseFloat(amountIn.value);
    if(isNaN(amt) || amt < 0){
      setStatus('Enter a valid amount', 'error');
      return;
    }

    if(useCustomRate){
      var cr = parseFloat(customRateIn.value);
      if(isNaN(cr) || cr <= 0){
        setStatus('Enter a valid custom rate', 'error');
        return;
      }
      showResult(amt, from, amt * cr, to, cr);
      return;
    }

    if(!currentRates){
      fetchRates(from).then(function(rates){
        if(rates) doConvert(amt, from, to, rates);
      });
      return;
    }

    if(from !== currentRates._base){
      fetchRates(from).then(function(rates){
        if(rates) doConvert(amt, from, to, rates);
      });
    } else {
      doConvert(amt, from, to, currentRates);
    }
  }

  function doConvert(amt, from, to, rates){
    if(!rates || rates[to] === undefined){
      setStatus('Currency "' + to.toUpperCase() + '" not found in rates', 'error');
      return;
    }
    showResult(amt, from, amt * rates[to], to, rates[to]);
  }

  function showResult(amt, from, result, to, rate){
    var fUp = from.toUpperCase();
    var tUp = to.toUpperCase();
    var rStr = fmt(result);
    var aStr = fmt(amt);

    resultFrom.textContent = aStr + ' ' + fUp;
    resultTo.textContent = rStr + ' ' + tUp;
    resultRate.textContent = '1 ' + fUp + ' = ' + fmt(rate) + ' ' + tUp;
    resultPanel.style.display = '';
    resultDetails.innerHTML =
      '<div class="tc-cc-detail-row"><span>From</span><b>' + aStr + ' ' + fUp + '</b></div>' +
      '<div class="tc-cc-detail-row"><span>To</span><b>' + rStr + ' ' + tUp + '</b></div>' +
      '<div class="tc-cc-detail-row"><span>Rate</span><b>' + fmt(rate) + '</b></div>' +
      '<div class="tc-cc-detail-row"><span>Inverse</span><b>1 ' + tUp + ' = ' + fmt(1/rate) + ' ' + fUp + '</b></div>';

    statOrig.textContent = aStr + ' ' + fUp;
    statComp.textContent = rStr + ' ' + tUp;
    statSaved.textContent = fmt(rate);

    if(statusChip){ statusChip.textContent = 'Converted'; statusChip.className = 'tc-chip tc-chip--ok'; }

    document.querySelectorAll('.tc-cc-quick-pairs .tc-btn').forEach(function(b){ b.classList.remove('sel'); });
    document.querySelectorAll('.tc-cc-quick-pairs .tc-btn').forEach(function(b){
      if(b.dataset.from === from && b.dataset.to === to) b.classList.add('sel');
    });

    setStatus('Converted successfully', 'ok');
  }

  function fmt(n){
    if(Math.abs(n) >= 1) return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    if(Math.abs(n) >= 0.01) return n.toLocaleString('en-US', { minimumFractionDigits: 4, maximumFractionDigits: 6 });
    return n.toLocaleString('en-US', { minimumFractionDigits: 6, maximumFractionDigits: 8 });
  }

  function setStatus(msg, type){
    if(!statusEl) return;
    statusEl.textContent = msg;
    statusEl.className = 'tc-cc-status tc-cc-status--' + (type || '');
  }

  // ── Quick pairs ──────────────────────────────────────────
  document.querySelectorAll('.tc-cc-quick-pairs .tc-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.querySelectorAll('.tc-cc-quick-pairs .tc-btn').forEach(function(b){ b.classList.remove('sel'); });
      this.classList.add('sel');
      fromSel.value = this.dataset.from;
      toSel.value = this.dataset.to;
      fetchRates(this.dataset.from).then(function(){ convert(); });
    });
  });

  // ── Swap ─────────────────────────────────────────────────
  if(swapBtn){
    swapBtn.addEventListener('click', function(){
      var tmp = fromSel.value;
      fromSel.value = toSel.value;
      toSel.value = tmp;
      fetchRates(fromSel.value).then(function(){ convert(); });
    });
  }

  // ── Custom rate toggle ───────────────────────────────────
  if(toggleRate){
    toggleRate.addEventListener('click', function(){
      useCustomRate = !useCustomRate;
      customRateRow.style.display = useCustomRate ? '' : 'none';
      this.textContent = useCustomRate ? 'Use Live Rate' : 'Use Custom Rate';
    });
  }

  // ── Copy ─────────────────────────────────────────────────
  if(copyBtn){
    copyBtn.addEventListener('click', function(){
      var from = fromSel.value.toUpperCase();
      var to = toSel.value.toUpperCase();
      var amt = parseFloat(amountIn.value) || 0;
      var text = '';
      if(resultTo.textContent && resultTo.textContent !== '\u2014'){
        text = resultFrom.textContent + ' = ' + resultTo.textContent + '\n' + resultRate.textContent;
      } else {
        text = amt + ' ' + from + ' \u2192 ' + to;
      }
      if(navigator.clipboard){
        navigator.clipboard.writeText(text).then(function(){
          if(typeof TCTP !== 'undefined' && TCTP.toast) TCTP.toast('Copied to clipboard');
        });
      }
    });
  }

  // ── Auto-convert on input change ─────────────────────────
  var debounceTimer;
  amountIn.addEventListener('input', function(){
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(convert, 300);
  });
  fromSel.addEventListener('change', function(){
    fetchRates(fromSel.value).then(function(){ convert(); });
  });
  toSel.addEventListener('change', convert);

  // ── Initialize: fetch rates for default USD ──────────────
  fetchRates('usd').then(function(){ convert(); });

})();
