/**
 * Social Media Character Counter — 100% client-side
 * Real-time character counts across platforms
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);

  const PLATFORMS = {
    twitter:   { name: 'Twitter / X', limit: 280, icon: '𝕏' },
    instagram: { name: 'Instagram', limit: 2200, icon: '📸' },
    facebook:  { name: 'Facebook', limit: 63206, icon: '📘' },
    linkedin:  { name: 'LinkedIn', limit: 3000, icon: '💼' },
    tiktok:    { name: 'TikTok', limit: 2200, icon: '🎵' },
    youtube:   { name: 'YouTube', limit: 100, icon: '▶️' }
  };

  const TIPS = {
    twitter: [
      'Twitter limits posts to 280 characters.',
      'URLs always count as 23 characters regardless of length.',
      'Images and polls don\'t count toward the character limit.',
      'Thread your thoughts if 280 isn\'t enough — use 🧵.',
      'Short tweets often get more engagement than long ones.'
    ],
    instagram: [
      'Instagram captions can be up to 2,200 characters.',
      'Only the first 125 characters show before "...more".',
      'Line breaks and emojis help readability.',
      'Hashtags can go in the caption or first comment.',
      'Best caption length: 138-150 characters for engagement.'
    ],
    facebook: [
      'Facebook posts can be up to 63,206 characters.',
      'Posts under 250 characters get 60% more engagement.',
      'Link posts should have short, punchy descriptions.',
      'Facebook Stories have a 1,000 character limit.',
      'Longer posts work well for storytelling.'
    ],
    linkedin: [
      'LinkedIn posts can be up to 3,000 characters.',
      'Posts under 1,000 characters get 3x more engagement.',
      'Start with a hook — first 2 lines matter most.',
      'Line breaks and white space improve readability.',
      'LinkedIn articles can be up to 110,000 characters.'
    ],
    tiktok: [
      'TikTok captions can be up to 2,200 characters.',
      'Keep captions under 150 characters for best view rates.',
      'Use 3-5 relevant hashtags for discoverability.',
      'Question-based captions boost comments.',
      'First 100 characters are most visible.'
    ],
    youtube: [
      'YouTube titles can be up to 100 characters.',
      'Optimal title length: 60-70 characters.',
      'Titles under 60 characters display fully in search.',
      'Include your primary keyword near the start.',
      'Use numbers and power words for higher CTR.'
    ]
  };

  function getPlatform(){ return document.querySelector('.tc-modes[data-group="sc-platform"] .sel')?.dataset.val || 'twitter'; }

  function countStats(text){
    return {
      chars: text.length,
      words: text.trim() ? text.trim().split(/\s+/).length : 0,
      lines: text ? text.split('\n').length : 0
    };
  }

  function updateDisplay(platform){
    const text = $('#tc-sc-text')?.value || '';
    const stats = countStats(text);
    const p = PLATFORMS[platform];
    const remaining = Math.max(0, p.limit - stats.chars);
    const pct = Math.min(100, Math.round((stats.chars / p.limit) * 100));
    const isOver = stats.chars > p.limit;

    $('#tc-sc-chars').textContent = stats.chars.toLocaleString();
    $('#tc-sc-words').textContent = stats.words.toLocaleString();
    $('#tc-sc-remaining').textContent = (isOver ? '-' : '') + remaining.toLocaleString();
    $('#tc-sc-remaining').style.color = isOver ? '#ef4444' : remaining < 20 ? '#f59e0b' : '';

    const platformsEl = $('#tc-sc-platforms');
    if(platformsEl){
      platformsEl.innerHTML = Object.entries(PLATFORMS).map(([key, plat]) => {
        const rem = Math.max(0, plat.limit - stats.chars);
        const pctVal = Math.min(100, Math.round((stats.chars / plat.limit) * 100));
        const over = stats.chars > plat.limit;
        const barColor = over ? '#ef4444' : pctVal > 90 ? '#f59e0b' : '#2563eb';
        return `
          <div class="tc-sc-platform-row ${key === platform ? 'active' : ''}">
            <div class="tc-sc-platform-info">
              <span class="tc-sc-platform-icon">${plat.icon}</span>
              <span class="tc-sc-platform-name">${plat.name}</span>
              <span class="tc-sc-platform-chars">${stats.chars.toLocaleString()} / ${plat.limit.toLocaleString()}</span>
            </div>
            <div class="tc-sc-platform-bar">
              <div class="tc-sc-platform-fill" style="width:${pctVal}%;background:${barColor}"></div>
            </div>
            <div class="tc-sc-platform-remaining ${over ? 'over' : ''}">
              ${over ? 'Over by ' + Math.abs(rem).toLocaleString() : rem.toLocaleString() + ' remaining'}
            </div>
          </div>
        `;
      }).join('');
    }

    const tipsEl = $('#tc-sc-tips');
    if(tipsEl){
      const tips = TIPS[platform] || TIPS.twitter;
      tipsEl.innerHTML = `<div class="tc-sc-tips-list">${tips.map(t => `<div class="tc-sc-tip-item">💡 ${t}</div>`).join('')}</div>`;
    }
  }

  document.addEventListener('DOMContentLoaded', function(){
    const textarea = $('#tc-sc-text');
    if(!textarea) return;

    textarea.addEventListener('input', function(){
      const platform = getPlatform();
      updateDisplay(platform);
    });

    document.querySelectorAll('.tc-modes[data-group="sc-platform"] .tc-btn').forEach(btn => {
      btn.addEventListener('click', function(){
        this.closest('.tc-modes').querySelectorAll('.tc-btn').forEach(b => b.classList.remove('sel'));
        this.classList.add('sel');
        updateDisplay(this.dataset.val);
      });
    });

    const countBtn = $('#tc-sc-count');
    if(countBtn){
      countBtn.addEventListener('click', function(){
        const platform = getPlatform();
        updateDisplay(platform);
        TCTP.switchToResultTab();
        TCTP.toast('Character counts updated!','success');
      });
    }

    const clearBtn = $('#tc-sc-clear');
    if(clearBtn){
      clearBtn.addEventListener('click', function(){
        textarea.value = '';
        updateDisplay(getPlatform());
      });
    }

    updateDisplay('twitter');
  });
})();
