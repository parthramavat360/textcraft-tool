/**
 * Small Text Generator
 * @package TextCraft_Tools_Pro
 */
(function () {
  if (!window.TCTP) return;

  const input = document.getElementById('tc-sm-input');
  const grid  = document.getElementById('tc-sm-grid');
  if (!input || !grid) return;

  /* Superscript map */
  const SUP = {'a':'ᵃ','b':'ᵇ','c':'ᶜ','d':'ᵈ','e':'ᵉ','f':'ᶠ','g':'ᵍ','h':'ʰ','i':'ⁱ','j':'ʲ','k':'ᵏ','l':'ˡ','m':'ᵐ','n':'ⁿ','o':'ᵒ','p':'ᵖ','q':'q','r':'ʳ','s':'ˢ','t':'ᵗ','u':'ᵘ','v':'ᵛ','w':'ʷ','x':'ˣ','y':'ʸ','z':'ᶻ','A':'ᴬ','B':'ᴮ','C':'ᶜ','D':'ᴰ','E':'ᴱ','F':'ᶠ','G':'ᴳ','H':'ᴴ','I':'ᴵ','J':'ᴶ','K':'ᴷ','L':'ᴸ','M':'ᴹ','N':'ᴺ','O':'ᴼ','P':'ᴾ','Q':'Q','R':'ᴿ','S':'ˢ','T':'ᵀ','U':'ᵁ','V':'ⱽ','W':'ᵂ','0':'⁰','1':'¹','2':'²','3':'³','4':'⁴','5':'⁵','6':'⁶','7':'⁷','8':'⁸','9':'⁹'};

  /* Subscript map */
  const SUB = {'a':'ₐ','b':'b','c':'c','d':'d','e':'ₑ','f':'f','g':'g','h':'ₕ','i':'ᵢ','j':'ⱼ','k':'ₖ','l':'ₗ','m':'ₘ','n':'ₙ','o':'ₒ','p':'ₚ','q':'q','r':'r','s':'ₛ','t':'ₜ','u':'ᵤ','v':'ᵥ','w':'w','x':'ₓ','y':'ᵧ','z':'z','0':'₀','1':'₁','2':'₂','3':'₃','4':'₄','5':'₅','6':'₆','7':'₇','8':'₈','9':'₉'};

  /* Small Caps map */
  const SC = {'a':'ᴀ','b':'ʙ','c':'ᴄ','d':'ᴅ','e':'ᴇ','f':'ꜰ','g':'ɢ','h':'ʜ','i':'ɪ','j':'ᴊ','k':'ᴋ','l':'ʟ','m':'ᴍ','n':'ɴ','o':'ᴏ','p':'ᴘ','q':'ǫ','r':'ʀ','s':'ꜱ','t':'ᴛ','u':'ᴜ','v':'ᴠ','w':'ᴡ','x':'x','y':'ʏ','z':'ᴢ'};

  const STYLES = [
    { name: 'Superscript', fn: t => t.split('').map(c => SUP[c] || SUP[c.toLowerCase()] || c).join('') },
    { name: 'Subscript', fn: t => t.split('').map(c => SUB[c] || SUB[c.toLowerCase()] || c).join('') },
    { name: 'Small Caps', fn: t => t.split('').map(c => SC[c.toLowerCase()] || c).join('') },
    { name: 'Tiny (Lower)', fn: t => t.split('').map(c => {
      const code = c.toLowerCase().charCodeAt(0);
      if (code >= 97 && code <= 122) return String.fromCharCode(0x1D00 + (code - 97));
      return c;
    }).join('') },
    { name: 'Small Superscript', fn: t => {
      const sup2 = {'a':'ᴬ','b':'ᴮ','c':'ᶜ','d':'ᴰ','e':'ᴱ','f':'ᶠ','g':'ᴳ','h':'ᴴ','i':'ᴵ','j':'ᴶ','k':'ᴷ','l':'ᴸ','m':'ᴹ','n':'ᴺ','o':'ᴼ','p':'ᴾ','r':'ᴿ','s':'ˢ','t':'ᵀ','u':'ᵁ','v':'ⱽ','w':'ᵂ'};
      return t.split('').map(c => sup2[c] || sup2[c.toLowerCase()] || c).join('');
    }},
    { name: 'Micro Text', fn: t => t.split('').map(c => {
      const map = {'a':'ᴀ','b':'ʙ','c':'ᴄ','d':'ᴅ','e':'ᴇ','f':'ꜰ','g':'ɢ','h':'ʜ','i':'ɪ','j':'ᴊ','k':'ᴋ','l':'ʟ','m':'ᴍ','n':'ɴ','o':'ᴏ','p':'ᴘ','q':'ǫ','r':'ʀ','s':'ꜱ','t':'ᴛ','u':'ᴜ','v':'ᴠ','w':'ᴡ','x':'x','y':'ʏ','z':'ᴢ'};
      return map[c.toLowerCase()] || c;
    }).join('') },
  ];

  function render() {
    const text = input.value || 'Hello World';
    grid.innerHTML = '';

    STYLES.forEach(style => {
      let output;
      try { output = style.fn(text); } catch(e) { output = text; }

      const card = document.createElement('div');
      card.className = 'tc-font-card';
      card.innerHTML =
        '<div class="tc-font-card-head">' +
          '<span class="tc-font-name">' + style.name + '</span>' +
          '<button class="tc-font-copy" type="button" title="Copy">' +
            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>' +
          '</button>' +
        '</div>' +
        '<div class="tc-font-preview">' + escapeHtml(output) + '</div>';

      card.querySelector('.tc-font-copy').addEventListener('click', function(e) {
        e.stopPropagation();
        navigator.clipboard.writeText(output).then(() => {
          TCTP.toast('Copied! ' + style.name);
          this.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
          setTimeout(() => {
            this.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>';
          }, 1500);
        });
      });

      grid.appendChild(card);
    });
  }

  function escapeHtml(text) {
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
  }

  input.addEventListener('input', render);
  render();
})();
