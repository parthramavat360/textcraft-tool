/**
 * Font Generator — 25+ Unicode styles
 * @package TextCraft_Tools_Pro
 */
(function () {
  if (!window.TCTP) return;

  const input = document.getElementById('tc-fgen-input');
  const grid  = document.getElementById('tc-fgen-grid');
  const search = document.getElementById('tc-fgen-search');
  if (!input || !grid) return;

  /* ── Character maps ────────────────────────────────────────── */

  const MAPS = {
    bold: {
      name: 'Bold',
      map: (function(){const m={};const a='𝐚𝐛𝐜𝐝𝐞𝐟𝐠𝐡𝐢𝐣𝐤𝐥𝐦𝐧𝐨𝐩𝐪𝐫𝐬𝐭𝐮𝐯𝐰𝐱𝐲𝐳𝐀𝐁𝐂𝐃𝐄𝐅𝐆𝐇𝐈𝐉𝐊𝐋𝐌𝐍𝐎𝐏𝐐𝐑𝐒𝐓𝐔𝐕𝐖𝐗𝐘𝐙'.split('');'abcdefghijklmnopqrstuvwxyz'.split('').forEach((c,i)=>m[c]=a[i]);'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('').forEach((c,i)=>m[c]=a[26+i]);return m;})(),
      test: 'bold chars',
    },
    boldItalic: {
      name: 'Bold Italic',
      map: (function(){const m={};const a='𝐚𝐛𝐜𝐝𝐞𝐟𝐠𝐡𝐢𝐣𝐤𝐥𝐦𝐧𝐨𝐩𝐪𝐫𝐬𝐭𝐮𝐯𝐰𝐱𝐲𝐳'.split('');'abcdefghijklmnopqrstuvwxyz'.split('').forEach((c,i)=>m[c]=a[i]);const b='𝘈𝘉𝘊𝘋𝘌𝘍𝘎𝘏𝘐𝘑𝘒𝘓𝘔𝘕𝘖𝘗𝘘𝘙𝘚𝘛𝘝𝘞𝘟𝘠𝘡'.split('');'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('').forEach((c,i)=>m[c]=b[i]);return m;})(),
      test: 'bold italic',
    },
    italic: {
      name: 'Italic',
      map: (function(){const m={};const a='𝘢𝘣𝘤𝘥𝘦𝘧𝘨𝘩𝘪𝘫𝘬𝘭𝘮𝘯𝘰𝘱𝘲𝘳𝘴𝘵𝘶𝘷𝘸𝘹𝘺𝘻'.split('');'abcdefghijklmnopqrstuvwxyz'.split('').forEach((c,i)=>m[c]=a[i]);const b='𝘈𝘉𝘊𝘋𝘌𝘍𝘎𝘏𝘐𝘑𝘒𝘓𝘔𝘕𝘖𝘗𝘘𝘙𝘚𝘛𝘝𝘞𝘟𝘠𝘡'.split('');'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('').forEach((c,i)=>m[c]=b[i]);return m;})(),
      test: 'italic',
    },
    script: {
      name: 'Script / Cursive',
      map: (function(){const m={};'𝒶𝒷𝒸𝒹𝑒𝒻𝑔𝒽𝒾𝒿𝓀𝓁𝓂𝓃𝑜𝓅𝓆𝓇𝓈𝓉𝓊𝓋𝓌𝓍𝓎𝓏'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'𝒜ℬ𝒞𝒟ℰℱ𝒢ℋℐ𝒦ℒℳ𝒩𝒪𝒫𝒬ℛ𝒮𝒯𝒰𝒱𝒲𝒳𝒴ℤ'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'script',
    },
    scriptBold: {
      name: 'Bold Script',
      map: (function(){const m={};'𝓪𝓫𝓬𝓭𝓮𝓯𝓰𝓱𝓲𝓳𝓴𝓵𝓶𝓷𝓸𝓹𝓺𝓻𝓼𝓽𝓾𝓿𝔀𝔁𝔂𝔃'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'𝓐𝓑𝓒𝓔𝓕𝓖𝓗𝓘𝓙𝓚𝓛𝓜𝓝𝓞𝓟𝓠𝓡𝓢𝓣𝓤𝓥𝓦𝓧𝓨𝓩'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'bold script',
    },
    fraktur: {
      name: 'Gothic / Blackletter',
      map: (function(){const m={};'𝔞𝔟𝔠𝔡𝔢𝔣𝔤𝔥𝔦𝔧𝔨𝔩𝔪𝔫𝔬𝔭𝔮𝔯𝔰𝔱𝔲𝔳𝔴𝔵𝔶𝔷'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'𝔄𝔅ℭ𝔇𝔈𝔉𝔊ℌℑ𝔎𝔏𝔐𝔑𝔒𝔓𝔔ℜ𝔖𝔗𝔘𝔙𝔚𝔛𝔜ℨ'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'fraktur',
    },
    frakturBold: {
      name: 'Bold Gothic',
      map: (function(){const m={};'𝖆𝖇𝖈𝖉𝖊𝖋𝖌𝖍𝖎𝖏𝖐𝖑𝖒𝖓𝖔𝖕𝖖𝖗𝖘𝖙𝖚𝖛𝖜𝖝𝖞𝖟'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'𝕬𝕭𝕮𝕯𝕰𝕱𝕲𝕳𝕴𝕵𝕶𝕷𝕸𝕹𝕺𝕻𝕼𝕽𝕾𝕿𝖀𝖁𝖂𝖃𝖄𝖅'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'bold gothic',
    },
    doubleStruck: {
      name: 'Double-Struck',
      map: (function(){const m={};'𝕒𝕓𝕔𝕕𝕖𝕗𝕘𝕙𝕚𝕛𝕜𝕝𝕞𝕟𝕠𝕡𝕢𝕣𝕤𝕥𝕦𝕧𝕨𝕩𝕪𝕫'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'𝔸𝔹ℂ𝔻𝔼𝔽𝔾ℍ𝕀𝕁𝕂𝕃𝕄ℕ𝕆ℙℚℝ𝕊𝕋𝕌𝕍𝕎𝕏𝕐ℤ'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'double struck',
    },
    monospace: {
      name: 'Typewriter',
      map: (function(){const m={};'𝚊𝚋𝚌𝚍𝚎𝚏𝚐𝚑𝚒𝚓𝚔𝚕𝚖𝚗𝚘𝚙𝚚𝚛𝚜𝚝𝚞𝚟𝚠𝚡𝚢𝚣'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'𝙰𝙱𝙲𝙳𝙴𝙵𝙶𝙷𝙸𝙹𝙺𝙻𝙼𝙽𝙾𝙿𝚚𝚁𝚂𝚃𝚄𝚅𝚆𝚡𝚈𝚉'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'typewriter',
    },
    sansSerif: {
      name: 'Sans-Serif',
      map: (function(){const m={};'𝖺𝖻𝖼𝖽𝖾𝖿𝗀𝗁𝗂𝗃𝗄𝗅𝗆𝗇𝗈𝗉𝗊𝗋𝗌𝗍𝗎𝗎𝗏𝗐𝗑𝗒𝗉'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'𝖠𝖡𝖢𝖣𝖤𝖥𝖦𝖧𝖨𝖩𝖪𝖫𝖬𝖭𝖮𝖯𝖰𝖱𝖲𝖳𝖴𝖵𝖶𝖷𝖸𝖹'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'sans serif',
    },
    sansSerifBold: {
      name: 'Sans-Serif Bold',
      map: (function(){const m={};'𝗔𝗕𝗖𝗗𝗘𝗙𝗚𝗛𝗜𝗝𝗞𝗟𝗠𝗡𝗢𝗣𝗤𝗥𝗦𝗧𝗨𝗩𝗪𝗫𝗬𝗭'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'𝗔𝗕𝗖𝗗𝗘𝗙𝗚𝗛𝗜𝗝𝗞𝗟𝗠𝗡𝗢𝗣𝗤𝗥𝗦𝗧𝗨𝗩𝗪𝗫𝗬𝗭'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'sans bold',
    },
    sansSerifItalic: {
      name: 'Sans-Serif Italic',
      map: (function(){const m={};'𝘢𝘣𝘤𝘥𝘦𝘧𝘨𝘩𝘪𝘫𝘬𝘭𝘮𝘯𝘰𝘱𝘲𝘳𝘴𝘵𝘶𝘷𝘸𝘹𝘺𝘻'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'𝘈𝘉𝘊𝘋𝘌𝘍𝘎𝘏𝘐𝘑𝘒𝘓𝘔𝘕𝘖𝘗𝘘𝘙𝘚𝘛𝘝𝘞𝘟𝘠𝘡'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'sans italic',
    },
    sansSerifBoldItalic: {
      name: 'Sans-Serif Bold Italic',
      map: (function(){const m={};'𝙖𝙗𝙘𝙙𝙚𝙛𝙜𝙝𝙞𝙟𝙠𝙡𝙢𝙣𝙤𝙥𝙦𝙧𝙨𝙩𝙪𝙫𝙬𝙭𝙮𝙯'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'𝘼𝘽𝘾𝘿𝙀𝙁𝙂𝙃𝙄𝙅𝙆𝙇𝙈𝙉𝙊𝙋𝙌𝙍𝙎𝙏𝙒𝙒𝙓𝙔𝙕'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'sans bold italic',
    },
    circled: {
      name: 'Circled',
      map: (function(){const m={};'ⓐⓑⓒⓓⓔⓕⓖⓗⓘⓙⓚⓛⓜⓝⓞⓟⓠⓡⓢⓣⓤⓥⓦⓧⓨⓩ'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'ⒶⒷⒸⒹⒺⒻⒼⒽⒾⒿⓀⓁⓂⓃⓄⓅⓆⓇⓈⓉⓊⓋⓌⓍⓎⓏ'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'circled',
    },
    parenthesized: {
      name: 'Parenthesized',
      map: (function(){const m={};'⒜⒝⒞⒟⒠⒡⒢⒣⒤⒥⒦⒧⒨⒩⒪⒫⒬⒭⒮⒯⒰⒱⒲⒳⒴⒵'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);return m;})(),
      test: 'parenthesized',
    },
    squared: {
      name: 'Squared',
      map: (function(){const m={};'🄰🄱🄲🄳🄴🄵🄶🄷🄸🄹🄺🄻🄼🄽🄾🄿🅀🅁🅂🅃🅄🅅🅆🅇🅈🅉'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'🅐🅑🅒🅓🅔🅕🅖ⓗ🅘🅙🅚🅛ⵎ🅝🅞🅟🅠🅡🅢🅣🅤🅥🅦🅧🅨🅩'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'squared',
    },
    filled: {
      name: 'Filled',
      map: (function(){const m={};'𝐚𝐛𝐜𝐝𝐞𝐟𝐠𝐡𝐢𝐣𝐤𝐥𝐦𝐧𝐨𝐩𝐪𝐫𝐬𝐭𝐮𝐯𝐰𝐱𝐲𝐳'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);'𝐀𝐁𝐂𝐃𝐄𝐅𝐆𝐇𝐈𝐉𝐊𝐋𝐌𝐍𝐎𝐏𝐐𝐑𝐒𝐓𝐔𝐕𝐖𝐗𝐘𝐙'.split('').forEach((c,i)=>m['ABCDEFGHIJKLMNOPQRSTUVWXYZ'[i]]=c);return m;})(),
      test: 'filled',
    },
    smallCaps: {
      name: 'Small Caps',
      map: (function(){const m={};'ᴀʙᴄᴅᴇꜰɢʜɪᴊᴋʟᴍɴᴏᴘQʀꜱᴛᴜᴠᴡxʏᴢ'.split('').forEach((c,i)=>m['abcdefghijklmnopqrstuvwxyz'[i]]=c);return m;})(),
      test: 'small caps',
    },
    backwards: {
      name: 'Mirror / Backwards',
      map: (function(){
        const m = {
          'a':'\u0250','b':'q','c':'\u0254','d':'p','e':'\u01DD','f':'\u025F','g':'\u0183','h':'\u0265',
          'i':'\u0131','j':'\u0279','k':'\u028E','l':'l','m':'\u026F','n':'u','o':'o','p':'d',
          'q':'b','r':'\u0279','s':'s','t':'\u0287','u':'n','v':'\u028C','w':'\u028D','x':'x',
          'y':'\u028E','z':'z',
          'A':'\u2200','B':'q','C':'\u0186','D':'p','E':'\u018E','F':'\u025F','G':'\u0183','H':'H',
          'I':'I','J':'\u2142','K':'\u028E','L':'\u02E5','M':'W','N':'N','O':'O','P':'\u0500',
          'Q':'Q','R':'\u0279','S':'S','T':'\u22A5','U':'\u2229','V':'\u039B','W':'M','X':'X',
          'Y':'\u2144','Z':'Z',
          '1':'\u0196','2':'\u3132','3':'\u0190','4':'\u3134','5':'\u03DB','6':'9','9':'6',
          '!':'\u00A1','?':'\u00BF',':':'.','.',',',
          '(':')',')':'(',
          '[':']',']':'[',
          '{':'}','}':'{',
          '<':'>','>':'<',
          "'":',',',':"'",
          '"':',,',
          '_':'\u203E',
        };
        return m;
      })(),
      test: 'mirror',
    },
  };

  /* ── Zalgo generator (special — not a map) ─────────────────── */

  const ZALGO_UP   = ['\u0300','\u0301','\u0302','\u0303','\u0304','\u0305','\u0306','\u0307','\u0308','\u0309','\u030A','\u030B','\u030C','\u030D','\u030E','\u030F','\u0310','\u0311','\u0312','\u0313','\u0314','\u0315','\u031A','\u033D','\u033E','\u033F','\u0340','\u0341','\u0342','\u0343','\u0344','\u0346','\u034A','\u034B','\u034C','\u0350','\u0351','\u0352','\u0357','\u0358','\u035C','\u035D','\u035E','\u0360','\u0361'];
  const ZALGO_DOWN = ['\u0316','\u0317','\u0318','\u0319','\u031C','\u031D','\u031E','\u031F','\u0320','\u0321','\u0322','\u0323','\u0324','\u0325','\u0326','\u0327','\u0328','\u0329','\u032A','\u032B','\u032C','\u032D','\u032E','\u032F','\u0330','\u0331','\u0332','\u0333','\u0339','\u033A','\u033B','\u033C','\u0345','\u0347','\u0348','\u0349','\u034D','\u034E','\u0353','\u0354','\u0355','\u0356','\u0359','\u035A','\u0362'];
  const ZALGO_MID  = ['\u0310','\u0312','\u0313','\u0314','\u033D','\u033E','\u033F','\u0340','\u0341','\u0342','\u0343','\u0344','\u0346','\u034A','\u034B','\u034C'];

  function randFrom(arr) { return arr[Math.floor(Math.random() * arr.length)]; }

  function makeZalgo(text, intensity) {
    const n = intensity || 4;
    let out = '';
    for (const ch of text) {
      if (ch === ' ' || ch === '\n') { out += ch; continue; }
      out += ch;
      for (let i = 0; i < n; i++) {
        out += randFrom(ZALGO_UP);
        out += randFrom(ZALGO_DOWN);
        if (Math.random() > 0.5) out += randFrom(ZALGO_MID);
      }
    }
    return out;
  }

  /* ── Upside-down (special — not a map) ─────────────────────── */

  const UPSIDE = {'a':'\u0250','b':'q','c':'\u0254','d':'p','e':'\u01DD','f':'\u025F','g':'\u0183','h':'\u0265','i':'\u0131','j':'\u0279','k':'\u028E','l':'l','m':'\u026F','n':'u','o':'o','p':'d','q':'b','r':'\u0279','s':'s','t':'\u0287','u':'n','v':'\u028C','w':'\u028D','x':'x','y':'\u028E','z':'z','A':'\u2200','B':'q','C':'\u0186','D':'p','E':'\u018E','F':'\u025F','G':'\u0183','H':'H','I':'I','J':'\u2142','K':'\u028E','L':'\u02E5','M':'W','N':'N','O':'O','P':'\u0500','Q':'Q','R':'\u0279','S':'S','T':'\u22A5','U':'\u2229','V':'\u039B','W':'M','X':'X','Y':'\u2144','Z':'Z','1':'\u0196','2':'\u3132','3':'\u0190','4':'\u3134','5':'\u03DB','6':'9','9':'6','!':'\u00A1','?':'\u00BF',':':'.','.',',',"'":',',',':"'",'"':',,','(':')',')':'(','[':']',']':'[','{':'}','}':'{','<':'>','>':'<','_':'\u203E'};
  function makeUpside(text) { return text.split('').reverse().map(c => UPSIDE[c] || c).join(''); }

  /* ── Wide / Aesthetic ───────────────────────────────────────── */

  function makeWide(text) {
    return text.split('').map(c => {
      const code = c.charCodeAt(0);
      if (code >= 33 && code <= 126) return String.fromCharCode(code + 0xFEE0);
      if (c === ' ') return '\u3000';
      return c;
    }).join('');
  }

  /* ── Bubble ─────────────────────────────────────────────────── */

  const BUBBLE = {'a':'ⓐ','b':'ⓑ','c':'ⓒ','d':'ⓓ','e':'ⓔ','f':'ⓕ','g':'ⓖ','h':'ⓗ','i':'ⓘ','j':'ⓙ','k':'ⓚ','l':'ⓛ','m':'ⓜ','n':'ⓝ','o':'ⓞ','p':'ⓟ','q':'ⓠ','r':'ⓡ','s':'ⓢ','t':'ⓣ','u':'ⓤ','v':'ⓥ','w':'ⓦ','x':'ⓧ','y':'ⓨ','z':'ⓩ','A':'Ⓐ','B':'Ⓑ','C':'Ⓒ','D':'Ⓓ','E':'Ⓔ','F':'Ⓕ','G':'Ⓖ','H':'Ⓗ','I':'Ⓘ','J':'Ⓙ','K':'Ⓚ','L':'Ⓛ','M':'Ⓜ','N':'Ⓝ','O':'Ⓞ','P':'Ⓟ','Q':'Ⓠ','R':'Ⓡ','S':'Ⓢ','T':'Ⓣ','U':'Ⓤ','V':'Ⓥ','W':'Ⓦ','X':'Ⓧ','Y':'Ⓨ','Z':'Ⓩ','0':'⓪','1':'①','2':'②','3':'③','4':'④','5':'⑤','6':'⑥','7':'⑦','8':'⑧','9':'⑨'};
  function makeBubble(text) { return text.split('').map(c => BUBBLE[c] || c).join(''); }

  /* ── Underline ──────────────────────────────────────────────── */

  const UNDER = {'a':'a̲','b':'b̲','c':'c̲','d':'d̲','e':'e̲','f':'f̲','g':'g̲','h':'h̲','i':'i̲','j':'j̲','k':'k̲','l':'l̲','m':'m̲','n':'n̲','o':'o̲','p':'p̲','q':'q̲','r':'r̲','s':'s̲','t':'t̲','u':'u̲','v':'v̲','w':'w̲','x':'x̲','y':'y̲','z':'z̲','A':'A̲','B':'B̲','C':'C̲','D':'D̲','E':'E̲','F':'F̲','G':'G̲','H':'H̲','I':'I̲','J':'J̲','K':'K̲','L':'L̲','M':'M̲','N':'N̲','O':'O̲','P':'P̲','Q':'Q̲','R':'R̲','S':'S̲','T':'T̲','U':'U̲','V':'V̲','W':'W̲','X':'X̲','Y':'Y̲','Z':'Z̲'};
  function makeUnderline(text) { return text.split('').map(c => UNDER[c] || c).join(''); }

  /* ── Strikethrough ─────────────────────────────────────────── */

  const STRIKE = {'a':'a̶','b':'b̶','c':'c̶','d':'d̶','e':'e̶','f':'f̶','g':'g̶','h':'h̶','i':'i̶','j':'j̶','k':'k̶','l':'l̶','m':'m̶','n':'n̶','o':'o̶','p':'p̶','q':'q̶','r':'r̶','s':'s̶','t':'t̶','u':'u̶','v':'v̶','w':'w̶','x':'x̶','y':'y̶','z':'z̶','A':'A̶','B':'B̶','C':'C̶','D':'D̶','E':'E̶','F':'F̶','G':'G̶','H':'H̶','I':'I̶','J':'J̶','K':'K̶','L':'L̶','M':'M̶','N':'N̶','O':'O̶','P':'P̶','Q':'Q̶','R':'R̶','S':'S̶','T':'T̶','U':'U̶','V':'V̶','W':'W̶','X':'X̶','Y':'Y̶','Z':'Z̶'};
  function makeStrikethrough(text) { return text.split('').map(c => STRIKE[c] || c).join(''); }

  /* ── Superscript ───────────────────────────────────────────── */

  const SUPER = {'0':'⁰','1':'¹','2':'²','3':'³','4':'⁴','5':'⁵','6':'⁶','7':'⁷','8':'⁸','9':'⁹','-':'⁻','=':'⁼','(':'⁽',')':'⁾','a':'ᵃ','b':'ᵇ','c':'ᶜ','d':'ᵈ','e':'ᵉ','f':'ᶠ','g':'ᵍ','h':'ʰ','i':'ⁱ','j':'ʲ','k':'ᵏ','l':'ˡ','m':'ᵐ','n':'ⁿ','o':'ᵒ','p':'ᵖ','q':'q','r':'ʳ','s':'ˢ','t':'ᵗ','u':'ᵘ','v':'ᵛ','w':'ʷ','x':'ˣ','y':'ʸ','z':'ᶻ','A':'ᴬ','B':'ᴮ','C':'ᶜ','D':'ᴰ','E':'ᴱ','F':'ᶠ','G':'ᴳ','H':'ᴴ','I':'ᴵ','J':'ᴶ','K':'ᴷ','L':'ᴸ','M':'ᴹ','N':'ᴺ','O':'ᴼ','P':'ᴾ','Q':'Q','R':'ᴿ','T':'ᵀ','U':'ᵁ','V':'ⱽ','W':'ᵂ'};
  function makeSuperscript(text) { return text.split('').map(c => SUPER[c.toLowerCase()] || c).join(''); }

  /* ── Subscript ─────────────────────────────────────────────── */

  const SUB = {'0':'₀','1':'₁','2':'₂','3':'₃','4':'₄','5':'₅','6':'₆','7':'₇','8':'₈','9':'₉','-':'₋','=':'₌','(':'₍',')':'₎','a':'ₐ','e':'ₑ','o':'ₒ','x':'ₓ','h':'ₕ','k':'ₖ','l':'ₗ','m':'ₘ','n':'ₙ','p':'ₚ','s':'ₛ','t':'ₜ'};
  function makeSubscript(text) { return text.split('').map(c => SUB[c.toLowerCase()] || c).join(''); }

  /* ── Big / Fullwidth ───────────────────────────────────────── */

  const BIG = {'a':'🇦','b':'🇧','c':'🇨','d':'🇩','e':'🇪','f':'🇫','g':'🇬','h':'🇭','i':'🇮','j':'🇯','k':'🇰','l':'🇱','m':'🇲','n':'🇳','o':'🇴','p':'🇵','q':'🇶','r':'🇷','s':'🇸','t':'🇹','u':'🇺','v':'🇻','w':'🇼','x':'🇽','y':'🇾','z':'🇿'};
  function makeBig(text) { return text.split('').map(c => BIG[c.toLowerCase()] || c).join(' '); }

  /* ── Cute / Rounded ────────────────────────────────────────── */

  const CUTE = {'a':'ᴀ','b':'ʙ','c':'ᴄ','d':'ᴅ','e':'ᴇ','f':'ꜰ','g':'ɢ','h':'ʜ','i':'ɪ','j':'ᴊ','k':'ᴋ','l':'ʟ','m':'ᴍ','n':'ɴ','o':'ᴏ','p':'ᴘ','q':'ǫ','r':'ʀ','s':'ꜱ','t':'ᴛ','u':'ᴜ','v':'ᴠ','w':'ᴡ','x':'x','y':'ʏ','z':'ᴢ'};
  function makeCute(text) { return text.split('').map(c => CUTE[c.toLowerCase()] || c).join(''); }

  /* ── Style definitions ──────────────────────────────────────── */

  const STYLES = [
    { id: 'bold',           name: 'Bold',              fn: t => applyMap(t, MAPS.bold.map) },
    { id: 'italic',         name: 'Italic',            fn: t => applyMap(t, MAPS.italic.map) },
    { id: 'boldItalic',     name: 'Bold Italic',       fn: t => applyMap(t, MAPS.boldItalic.map) },
    { id: 'script',         name: 'Script / Cursive',  fn: t => applyMap(t, MAPS.script.map) },
    { id: 'scriptBold',     name: 'Bold Script',       fn: t => applyMap(t, MAPS.scriptBold.map) },
    { id: 'fraktur',        name: 'Gothic / Blackletter', fn: t => applyMap(t, MAPS.fraktur.map) },
    { id: 'frakturBold',    name: 'Bold Gothic',       fn: t => applyMap(t, MAPS.frakturBold.map) },
    { id: 'doubleStruck',   name: 'Double-Struck',     fn: t => applyMap(t, MAPS.doubleStruck.map) },
    { id: 'monospace',      name: 'Typewriter',        fn: t => applyMap(t, MAPS.monospace.map) },
    { id: 'sansSerif',      name: 'Sans-Serif',        fn: t => applyMap(t, MAPS.sansSerif.map) },
    { id: 'sansSerifBold',  name: 'Sans-Serif Bold',   fn: t => applyMap(t, MAPS.sansSerifBold.map) },
    { id: 'sansSerifItalic', name: 'Sans-Serif Italic', fn: t => applyMap(t, MAPS.sansSerifItalic.map) },
    { id: 'sansSerifBoldItalic', name: 'Sans-Serif Bold Italic', fn: t => applyMap(t, MAPS.sansSerifBoldItalic.map) },
    { id: 'circled',        name: 'Circled',           fn: t => applyMap(t, MAPS.circled.map) },
    { id: 'parenthesized',  name: 'Parenthesized',     fn: t => applyMap(t, MAPS.parenthesized.map) },
    { id: 'squared',        name: 'Squared',           fn: t => applyMap(t, MAPS.squared.map) },
    { id: 'filled',         name: 'Filled',            fn: t => applyMap(t, MAPS.filled.map) },
    { id: 'smallCaps',      name: 'Small Caps',        fn: t => applyMap(t, MAPS.smallCaps.map) },
    { id: 'upsideDown',     name: 'Upside Down',       fn: makeUpside },
    { id: 'mirror',         name: 'Mirror / Backwards', fn: t => applyMap(t, MAPS.backwards.map) },
    { id: 'wide',           name: 'Wide / Aesthetic',   fn: makeWide },
    { id: 'bubble',         name: 'Bubble Text',       fn: makeBubble },
    { id: 'underline',      name: 'Underline',         fn: makeUnderline },
    { id: 'strikethrough',  name: 'Strikethrough',     fn: makeStrikethrough },
    { id: 'superscript',    name: 'Superscript',       fn: makeSuperscript },
    { id: 'subscript',      name: 'Subscript',         fn: makeSubscript },
    { id: 'big',            name: 'Big Text',          fn: makeBig },
    { id: 'cute',           name: 'Cute Font',         fn: makeCute },
    { id: 'zalgo',          name: 'Zalgo / Glitch',    fn: t => makeZalgo(t, 3) },
  ];

  function applyMap(text, map) {
    return text.split('').map(c => map[c] || map[c.toLowerCase()] || c).join('');
  }

  /* ── Render ─────────────────────────────────────────────────── */

  function render() {
    const text = input.value || 'Hello World';
    grid.innerHTML = '';

    STYLES.forEach(style => {
      let output;
      try { output = style.fn(text); } catch(e) { output = text; }

      const card = document.createElement('div');
      card.className = 'tc-font-card';
      card.setAttribute('data-style', style.name.toLowerCase());
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

    /* Update result panel if present */
    const result = document.getElementById('tc-fgen-result');
    if (result) {
      result.innerHTML = '<p class="tc-fgen-hint">Click any card above to copy the styled text.</p>';
    }
  }

  function escapeHtml(text) {
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
  }

  /* ── Events ─────────────────────────────────────────────────── */

  input.addEventListener('input', render);

  if (search) {
    search.addEventListener('input', function() {
      const q = this.value.toLowerCase();
      grid.querySelectorAll('.tc-font-card').forEach(card => {
        const name = card.getAttribute('data-style') || '';
        card.style.display = name.includes(q) ? '' : 'none';
      });
    });
  }

  /* ── Init ───────────────────────────────────────────────────── */

  render();
})();
