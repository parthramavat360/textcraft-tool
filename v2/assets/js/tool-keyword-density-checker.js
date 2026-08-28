/**
 * tool-keyword-density-checker.js
 */
(function(){
  var $=jQuery, input=document.getElementById('kdc-text');
  if(!input)return;

  var btn=document.getElementById('kdc-analyze'), result=document.getElementById('kdc-result');
  var STOPWORDS=new Set('a about above after again against all am an and any are aren as at be because been before being below between both but by can could did do does doing down during each few for from further get got had has have having he he he her here hers herself him himself his how how i i if in is it its itself me more most my myself no nor not of off on once only or other our ours ourselves out over own same she should so some such than that the their theirs them themselves then there these they this those through to too under until up very was we were what when where which while who whom why will with you your yours yourself yourselves'.split(' '));

  input.addEventListener('input', function(){
    btn.disabled = !input.value.trim();
  });

  btn.addEventListener('click', function(){
    var text = input.value.trim();
    if(!text) return;

    window.TCTP.showProgress();
    btn.disabled = true;

    setTimeout(function(){
      var minLen = parseInt(document.getElementById('kdc-min-length').value) || 4;
      var filterStop = document.getElementById('kdc-stopwords').value === 'remove';

      // tokenize
      var words = text.toLowerCase().match(/[a-z0-9]+(?:['-][a-z0-9]+)*/g) || [];
      var meaningful = words.filter(function(w){
        if(w.length < minLen) return false;
        if(filterStop && STOPWORDS.has(w)) return false;
        return true;
      });

      // frequency map
      var freq = {};
      meaningful.forEach(function(w){
        freq[w] = (freq[w]||0) + 1;
      });

      // sorted by count desc
      var sorted = Object.keys(freq).map(function(w){
        return { word: w, count: freq[w], density: ((freq[w]/words.length)*100).toFixed(2) };
      }).sort(function(a,b){ return b.count - a.count; });

      // bigrams
      var bigrams = [];
      for(var i=0; i<meaningful.length-1; i++){
        bigrams.push(meaningful[i] + ' ' + meaningful[i+1]);
      }
      var bFreq = {};
      bigrams.forEach(function(b){ bFreq[b]=(bFreq[b]||0)+1; });
      var bSorted = Object.keys(bFreq).map(function(b){
        return { phrase: b, count: bFreq[b], density: ((bFreq[b]/words.length)*100).toFixed(2) };
      }).sort(function(a,b){ return b.count - a.count; }).slice(0, 30);

      // trigrams
      var trigrams = [];
      for(var j=0; j<meaningful.length-2; j++){
        trigrams.push(meaningful[j] + ' ' + meaningful[j+1] + ' ' + meaningful[j+2]);
      }
      var tFreq = {};
      trigrams.forEach(function(t){ tFreq[t]=(tFreq[t]||0)+1; });
      var tSorted = Object.keys(tFreq).map(function(t){
        return { phrase: t, count: tFreq[t], density: ((tFreq[t]/words.length)*100).toFixed(2) };
      }).sort(function(a,b){ return b.count - a.count; }).slice(0, 20);

      // density rating
      var topDensity = sorted.length ? parseFloat(sorted[0].density) : 0;
      var rating = '';
      if(topDensity < 0.5) rating = '<span style="color:#10b981;">Low Density — Add more keywords</span>';
      else if(topDensity < 2) rating = '<span style="color:#10b981;">Optimal Density — 1-2% is ideal</span>';
      else if(topDensity < 3) rating = '<span style="color:#f59e0b;">Moderate — Slightly high</span>';
      else rating = '<span style="color:#ef4444;">High Density — Over-optimized!</span>';

      // Summary
      document.getElementById('kdc-stats').innerHTML =
        '<div class="tctp-kdc-stat-grid">' +
        '<div class="tctp-kdc-stat-box"><div class="tctp-kdc-stat-val">' + words.length + '</div><div class="tctp-kdc-stat-lbl">Total Words</div></div>' +
        '<div class="tctp-kdc-stat-box"><div class="tctp-kdc-stat-val">' + sorted.length + '</div><div class="tctp-kdc-stat-lbl">Unique Keywords</div></div>' +
        '<div class="tctp-kdc-stat-box"><div class="tctp-kdc-stat-val">' + (sorted.length ? sorted[0].word : '-') + '</div><div class="tctp-kdc-stat-lbl">Top Keyword</div></div>' +
        '<div class="tctp-kdc-stat-box"><div class="tctp-kdc-stat-val">' + (sorted.length ? sorted[0].density + '%' : '-') + '</div><div class="tctp-kdc-stat-lbl">Top Density</div></div>' +
        '</div>' +
        '<div class="tctp-kdc-rating">' + rating + '</div>';

      // density bar
      var barHTML = '';
      sorted.slice(0, 10).forEach(function(k){
        var w = Math.min(parseFloat(k.density) * 30, 100);
        barHTML += '<div class="tctp-kdc-bar-row">' +
          '<div class="tctp-kdc-bar-word">' + k.word + '</div>' +
          '<div class="tctp-kdc-bar-track"><div class="tctp-kdc-bar-fill" style="width:' + w + '%"></div></div>' +
          '<div class="tctp-kdc-bar-pct">' + k.density + '%</div>' +
          '</div>';
      });
      document.getElementById('kdc-density-bar').innerHTML = '<h4 style="margin:12px 0 8px;">Keyword Density Chart</h4>' + barHTML;

      // Single words table
      document.getElementById('kdc-single-table').innerHTML = buildTable(sorted.slice(0,50), ['Keyword', 'Count', 'Density %'], 'word');

      // Phrases
      var phraseHTML = '<h4>2-Word Phrases (Bigrams)</h4>' + buildTable(bSorted, ['Phrase', 'Count', 'Density %'], 'phrase');
      phraseHTML += '<h4 style="margin-top:16px;">3-Word Phrases (Trigrams)</h4>' + buildTable(tSorted, ['Phrase', 'Count', 'Density %'], 'phrase');
      document.getElementById('kdc-phrases-container').innerHTML = phraseHTML;

      // Details
      var detailRows = sorted.slice(0, 100).map(function(k){
        return '<tr><td>' + k.word + '</td><td>' + k.count + '</td><td>' + k.density + '%</td><td>' +
          (parseFloat(k.density) < 1 ? '<span style="color:#10b981">Low</span>' : parseFloat(k.density) < 2 ? '<span style="color:#10b981">Good</span>' : parseFloat(k.density) < 3 ? '<span style="color:#f59e0b">Moderate</span>' : '<span style="color:#ef4444">High</span>') +
          '</td><td>' +
          (parseFloat(k.density) < 1 ? 'Add more' : parseFloat(k.density) < 2 ? 'Optimal' : parseFloat(k.density) < 3 ? 'Reduce slightly' : 'Reduce!') +
          '</td></tr>';
      }).join('');
      document.getElementById('kdc-details-table').innerHTML =
        '<table class="tctp-table"><thead><tr><th>Keyword</th><th>Count</th><th>Density</th><th>Level</th><th>Recommendation</th></tr></thead><tbody>' + detailRows + '</tbody></table>';

      // copy report
      window.TCTP.copyText = window.TCTP.copyText || function(){};
      document.querySelector('[data-copy="kdc-result"]').addEventListener('click', function(){
        var report = 'Keyword Density Report\n' +
          '='.repeat(40) + '\n\n' +
          'Total Words: ' + words.length + '\n' +
          'Unique Keywords: ' + sorted.length + '\n\n' +
          'Top Keywords:\n';
        sorted.slice(0, 20).forEach(function(k){
          report += '  ' + k.word + ': ' + k.count + ' (' + k.density + '%)\n';
        });
        report += '\nTop Bigrams:\n';
        bSorted.slice(0, 10).forEach(function(b){
          report += '  ' + b.phrase + ': ' + b.count + ' (' + b.density + '%)\n';
        });
        report += '\nTop Trigrams:\n';
        tSorted.slice(0, 10).forEach(function(t){
          report += '  ' + t.phrase + ': ' + t.count + ' (' + t.density + '%)\n';
        });
        window.TCTP.copyText(report);
      });

      result.style.display = 'block';
      window.TCTP.hideProgress();
      btn.disabled = false;
      window.TCTP.switchToResultTab();
    }, 100);
  });

  function buildTable(rows, headers, key){
    var html = '<table class="tctp-table"><thead><tr>';
    headers.forEach(function(h){ html += '<th>' + h + '</th>'; });
    html += '</tr></thead><tbody>';
    rows.forEach(function(r){
      html += '<tr><td>' + r[key] + '</td><td>' + r.count + '</td><td>' + r.density + '%</td></tr>';
    });
    if(!rows.length) html += '<tr><td colspan="3" style="text-align:center;">No data</td></tr>';
    html += '</tbody></table>';
    return html;
  }

  // paste handler
  input.addEventListener('paste', function(){
    setTimeout(function(){
      btn.disabled = !input.value.trim();
    }, 100);
  });

  // tabs
  document.querySelectorAll('.tctp-rsz-tab[data-tab]').forEach(function(tab){
    tab.addEventListener('click', function(){
      document.querySelectorAll('.tctp-rsz-tab').forEach(function(t){ t.classList.remove('sel'); });
      tab.classList.add('sel');
      var id = tab.getAttribute('data-tab');
      document.querySelectorAll('.tctp-rsz-tab-panel').forEach(function(p){ p.style.display='none'; });
      var map = { summary:'kdc-summary', single:'kdc-single', phrases:'kdc-phrases', details:'kdc-details' };
      var panel = document.getElementById(map[id]);
      if(panel) panel.style.display = '';
    });
  });
})();