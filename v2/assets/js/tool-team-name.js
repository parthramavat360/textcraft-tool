/**
 * Team Name Generator — 100% client-side
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);

  const ADJ = ['Wild','Crazy','Mighty','Epic','Lucky','Savage','Golden','Iron','Silver','Turbo','Ultra','Mega','Ninja','Secret','Lost','Brave','Rogue','Phantom','Stealth','Atomic','Cosmic','Epic','Fierce','Hyper','Lazer','Mystic','Neon','Nuclear','Omega','Quantum','Radical','Rapid','Shadow','Sonic','Thunder','Turbo','Venom','Vicious','Voltage','Wicked'];
  const NOUN = ['Tigers','Eagles','Wolves','Panthers','Dragons','Knights','Warriors','Titans','Phoenix','Hawks','Bears','Cobras','Falcons','Sharks','Lions','Vikings','Ninjas','Pirates','Rebels','Rockets','Storm','Bolt','Cannons','Claw','Fang','Fist','Fury','Guard','Hunter','Raiders','Renegades','Sabers','Spartans','Strike','Vipers','Wolves','Blaze','Crush','Force','Impact'];
  const TOPIC_ADJ = { coding:['Compile','Debug','Recursive','Stack','Binary','Boolean','Lambda','Async','Syntax','Pixel'], soccer:['Goal','Kick','Pass','Dribble','Strike','Foot','Field','Net','Sprint','Tackle'], marketing:['Brand','Buzz','Viral','Convert','Engage','Funnel','Reach','Target','Growth','Campaign'], trivia:['Brain','Quiz','Genius','Master','Wizard','Nerd','Fact','Trivia','Smart','Quick'], gaming:['Loot','XP','Boss','Respawn','Clutch','Carry','AFK','GG','Noob','Pro'], cooking:['Spice','Grill','Sear','Chop','Whisk','Flavor','Sauté','Bake','Blend','Simmer'], music:['Beat','Rhythm','Melody','Bass','Tune','Harmony','Tempo','Groove','Chord','Note'] };
  const TOPIC_NOUN = { coding:['Bugs','Functions','Arrays','Loops','Coders','Hackers','Scripts','Modules','Deploy','Merge'], soccer:['Goals','Boots','Cleats','Net','Pitch','Whistle','Cards','Penalty','Corner','Match'], marketing:['Leads','Sales','Clicks','Impressions','Content','Strategy','Analytics','SEO','Ads','ROI'], trivia:['Questions','Answers','Facts','Knowledge','Winners','Champs','Rounds','Bonus','Prizes','Score'], gaming:['Pixels','Loot','XP','Raid','Guild','Clan','Squad','Lobby','Meta','Patch'], cooking:['Recipes','Ingredients','Pots','Pans','Flames','Ovens','Knives','Plates','Mitts','Menus'], music:['Beats','Notes','Chords','Tracks','Albums','Songs','Vinyl','Keys','Drums','Bass'] };

  function pick(arr){ return arr[Math.floor(Math.random()*arr.length)]; }
  function getStyle(){ return document.querySelector('.tc-modes[data-group="tn-style"] .sel')?.dataset.val || 'funny'; }
  function getCount(){ return parseInt($('#tc-tn-num')?.value || '10'); }

  const templates = {
    funny: [()=>pick(ADJ)+' '+pick(NOUN),()=>pick(NOUN)+' of '+pick(ADJ),()=>pick(ADJ)+' '+pick(NOUN)+' FC',()=>pick(NOUN)+' United',()=>'The '+pick(ADJ)+' '+pick(NOUN),()=>'Team '+pick(ADJ),()=>'The '+pick(NOUN),()=>'No '+pick(NOUN)+' Allowed',()=>'We Love '+pick(NOUN),()=>'Just '+pick(NOUN)+' Things',()=>'Absolute '+pick(ADJ)+' '+pick(NOUN),()=>'The '+pick(NOUN)+' Experience',()=>'Born to be '+pick(ADJ),()=>'Professional '+pick(NOUN),()=>'Sir '+pick(NOUN)+' a Lot'],
    cool: [()=>pick(ADJ)+' '+pick(NOUN),()=>'The '+pick(ADJ)+' '+pick(NOUN),()=>'Team '+pick(ADJ),()=>'The '+pick(NOUN),()=>'Alpha '+pick(NOUN),()=>'Elite '+pick(NOUN),()=>'Prime '+pick(NOUN),()=>'The '+pick(ADJ)+' Force',()=>'Shadow '+pick(NOUN),()=>'Thunder '+pick(NOUN),()=>'Steel '+pick(NOUN),()=>'Titan '+pick(NOUN),()=>'Apex '+pick(NOUN),()=>'Nova '+pick(NOUN),()=>'Vanguard '+pick(NOUN)],
    professional: [()=>'The '+pick(NOUN)+' Group',()=>'Team '+pick(NOUN),()=>'The '+pick(NOUN)+' Collective',()=>''+pick(NOUN)+' Alliance',()=>'The '+pick(NOUN)+' Initiative',()=>'Project '+pick(NOUN),()=>'The '+pick(NOUN)+' Division',()=>'The '+pick(NOUN)+' Network',()=>'Institute of '+pick(NOUN),()=>'The '+pick(NOUN)+' Council',()=>'Academy of '+pick(NOUN),()=>'The '+pick(NOUN)+' Partners',()=>'The '+pick(NOUN)+' Standard',()=>'The '+pick(NOUN)+' Standard',()=>'The '+pick(NOUN)+' Syndicate'],
    creative: [()=>pick(ADJ)+' '+pick(NOUN),()=>pick(NOUN)+' in '+pick(ADJ),()=>'The '+pick(NOUN)+' Workshop',()=>'Canvas of '+pick(NOUN),()=>'The '+pick(ADJ)+' Lab',()=>'Studio '+pick(NOUN),()=>'The '+pick(NOUN)+' Factory',()=>'Forge of '+pick(NOUN),()=>'The '+pick(ADJ)+' Engine',()=>'Vault of '+pick(NOUN),()=>'The '+pick(NOUN)+' Blueprint',()=>'The '+pick(ADJ)+' Compass',()=>'The '+pick(NOUN)+' Compass',()=>'The '+pick(ADJ)+' Spectrum',()=>'The '+pick(NOUN)+' Archive'],
    punny: [()=>'Game of '+pick(NOUN),()=>'Lord of the '+pick(NOUN),()=>'The '+pick(NOUN)+' Awakens',()=>'No '+pick(NOUN)+' No Gain',()=>'May the '+pick(NOUN)+' Be with You',()=>'To Infinity and '+pick(NOUN),()=>'Keep Calm and '+pick(NOUN),()=>''+pick(NOUN)+' Mc'+pick(NOUN)+'face',()=>'The '+pick(NOUN)+'father',()=>'Mission Im'+pick(NOUN)+'ble',()=>'The '+pick(NOUN)+' Identity',()=>'Indiana '+pick(NOUN)+' and the Temple of '+pick(NOUN),()=>''+pick(NOUN)+' patrol',()=>'Breaking '+pick(NOUN),()=>'The Good, The Bad, and The '+pick(NOUN)]
  };

  function genName(topic, style){
    const tpl = templates[style] || templates.funny;
    let name = pick(tpl)();
    if(topic && TOPIC_ADJ[topic]){
      name = pick(TOPIC_ADJ[topic]) + ' ' + pick(TOPIC_NOUN[topic]);
    }
    return name;
  }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = $('#tc-tn-generate');
    if(!btn) return;
    btn.addEventListener('click', function(){
      const topic = $('#tc-tn-topic')?.value?.trim().toLowerCase() || '';
      const style = getStyle(), count = getCount();
      TCTP.showProgress('tc-tn-progress', 'Generating...', 0);
      setTimeout(() => {
        TCTP.setProgress('tc-tn-progress', 50, 'Building names...');
        const names = new Set();
        let tries = 0;
        while(names.size < count && tries < count * 3){ names.add(genName(topic, style)); tries++; }
        const arr = [...names];
        TCTP.setProgress('tc-tn-progress', 100, 'Done!');
        const namesEl = $('#tc-tn-names');
        if(namesEl){
          namesEl.innerHTML = arr.map((n,i) => '<div class="tc-rn-card"><span class="tc-rn-num">#' + (i+1) + '</span><span class="tc-rn-name">' + n + '</span><button class="tc-btn-sm" onclick="navigator.clipboard.writeText(this.previousElementSibling.textContent);this.textContent=\'Copied!\';setTimeout(()=>this.textContent=\'Copy\',1200)">Copy</button></div>').join('');
        }
        const copyEl = $('#tc-tn-copylist');
        if(copyEl){
          copyEl.innerHTML = '<textarea class="tc-textarea" readonly rows="8" onclick="this.select()">' + arr.join('\n') + '</textarea><br><button class="tc-btn-sm" style="margin-top:8px" onclick="navigator.clipboard.writeText(this.previousElementSibling.value);this.textContent=\'Copied!\';setTimeout(()=>this.textContent=\'Copy All\',1200)">Copy All</button>';
        }
        TCTP.switchToResultTab();
        TCTP.toast(arr.length + ' team names generated!','success');
      }, 300);
    });
    var styleGroup = document.querySelector('[data-group="tn-style"]');
    if(styleGroup){ TCTP.initModeGroup(styleGroup); }
    $('#tc-tn-num')?.addEventListener('input', function(){ $('#tc-tn-num-val').textContent = this.value; });
  });
})();
