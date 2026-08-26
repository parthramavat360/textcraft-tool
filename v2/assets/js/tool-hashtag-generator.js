/**
 * Hashtag Generator — 100% client-side
 * Generates hashtags by platform and category
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);

  const PLATFORM_LIMITS = { instagram: 30, tiktok: 25, twitter: 5, linkedin: 5 };

  const HASHTAG_DB = {
    general: ['#love','#instagood','#photooftheday','#beautiful','#happy','#cute','#instadaily','#picoftheday','#fun','#amazing','#style','#smile','#travel','#food','#fitness','#motivation','#art','#music','#fashion','#nature','#life','#goodvibes','#trending','#viral','#explorepage','#reels','#instalike','#tbt','#follow','#like4like'],
    fitness: ['#fitness','#gym','#workout','#fitfam','#motivation','#bodybuilding','#healthylifestyle','#training','#exercise','#gains','#fitlife','#activelife','#cardio','#wellness','#healthyeating','#yoga','#crossfit','#running','#weightlifting','#fitmom','#personaltrainer','#gymlife','#strongnotskinny','#abs','#muscle'],
    food: ['#foodie','#foodporn','#instafood','#homecooking','#healthyfood','#foodlover','#yummy','#delicious','#cooking','#recipe','#homemade','#eatclean','#foodgasm','#cheflife','#mealprep','#instacook','#baking','#foodphotography','#brunch','#plantbased','#vegan','#glutenfree','#comfortfood','#dessert','#streetfood'],
    travel: ['#travel','#wanderlust','#instatravel','#travelgram','#explore','#vacation','#adventure','#beautifuldestinations','#travelphotography','#tourism','#globetrotter','#bucketlist','#roadtrip','#backpacking','#nomad','#travelblogger','#solotravel','#hiking','#beach','#island','#cruise','#citytrip','#europetravel','#digitalnomad','#traveladdict'],
    fashion: ['#fashion','#styleoftheday','#ootd','#fashionista','#outfitoftheday','#lookoftheday','#whatiwore','#fashionblogger','#stylish','#clothes','#trendy','#outfitinspo','#wardrobe','#accessories','#streetstyle','#vintage','#thrifted','#luxury','#sustainablefashion','#mensfashion','#womensfashion','#streetwear','#minimalist','#aesthetic','#capsulewardrobe'],
    business: ['#business','#entrepreneur','#marketing','#success','#startup','#motivation','#hustle','#mindset','#goals','#leadership','#branding','#socialmedia','#digitalmarketing','#growth','#innovation','#smallbusiness','#ecommerce','#sidehustle','#passiveincome','#realestate','#stocks','#investing','#freelancing','#remotework','#workfromhome'],
    tech: ['#tech','#technology','#coding','#programming','#developer','#ai','#machinelearning','#cybersecurity','#cloudcomputing','#datascience','#python','#javascript','#webdev','#appdev','#startup','#innovation','#iot','#blockchain','#techlife','#geek','#nerd','#software','#hardware','#gadget','#digital'],
    lifestyle: ['#lifestyle','#selfcare','#mindfulness','#wellness','#mentalhealth','#meditation','#journaling','#habits','#morningroutine','#productivity','#balance','#gratitude','#affirmations','#holistichealth','#wellbeing','#selflove','#healing','#energy','#spiritual','#growthmindset','#selfimprovement','#dailymotivation','#positivevibes','#goodenergy','#maincharacterenergy']
  };

  const NICHE_HASHTAGS = {
    instagram: ['#instagramtips','#igcommunity','#instatips','#instagramgrowth','#igreels','#instatrends','#instagrammarketing','#igfollowers','#instastrategy','#instagramgrowthhacks'],
    tiktok: ['#tiktokviral','#tiktoktrend','#fyp','#foryou','#foryoupage','#tiktoktips','#tiktokcreator','#viralvideo','#tiktokgrowing','#tiktokhack'],
    twitter: ['#tweet','#twittertips','#x','#thread','#twittermarketing','#tweetstorm','#xcommunity','#twittergrowth','#tweetorial','#twitterbrand'],
    linkedin: ['#linkedinmarketing','#linkedincontent','#personalbranding','#b2bmarketing','#linkedinposts','#careeradvice','#jobsearch','#professionaldevelopment','#networking','#linkedincreator']
  };

  const BRANDED_PATTERNS = ['yourbrand','brandname','official','team'];

  function getPlatform(){ return document.querySelector('.tc-modes[data-group="ht-platform"] .sel')?.dataset.val || 'instagram'; }
  function getCount(){ return parseInt($('#tc-ht-num')?.value || '20'); }
  function useNiche(){ return $('#tc-ht-niche')?.checked; }
  function useBranded(){ return $('#tc-ht-branded')?.checked; }
  function useCount(){ return $('#tc-ht-count')?.checked; }

  function extractKeywords(text){
    return text.toLowerCase().replace(/[^a-z0-9\s]/g,'').split(/\s+/).filter(w => w.length > 2 && !['the','and','for','with','that','this','from','are','was','have','has','you','your','but','not','all','can','had','her','his','our','its','who','get','may','new','now','old','see','way','who','did','get','let','say','she','too','use'].includes(w));
  }

  function generateHashtags(topic, platform){
    const keywords = extractKeywords(topic);
    const limit = PLATFORM_LIMITS[platform] || 30;
    let allTags = [];

    keywords.forEach(w => {
      allTags.push('#' + w);
      allTags.push('#' + w + 'tips');
      allTags.push('#' + w + 'life');
      allTags.push('#' + w + 'oftheday');
      allTags.push('#' + w + 'community');
      allTags.push('#' + w + 'gram');
    });

    const category = keywords.some(w => HASHTAG_DB[w]) ? keywords.find(w => HASHTAG_DB[w]) : 'general';
    allTags.push(...(HASHTAG_DB[category] || HASHTAG_DB.general));

    if(useNiche()){
      allTags.push(...(NICHE_HASHTAGS[platform] || []));
    }

    if(useBranded()){
      BRANDED_PATTERNS.forEach(p => allTags.push('#' + p));
    }

    const unique = [...new Set(allTags)].filter(t => t.length > 2 && t.length <= 30);
    const shuffled = unique.sort(() => Math.random() - 0.5);
    return shuffled.slice(0, Math.min(limit + 10, shuffled.length));
  }

  function categorize(hashtags){
    const cats = { popular: [], medium: [], niche: [], branded: [] };
    hashtags.forEach(tag => {
      if(tag.includes('brand') || tag.includes('official') || tag.includes('team')) cats.branded.push(tag);
      else if(tag.includes('tips') || tag.includes('community') || tag.includes('gram') || tag.includes('niche')) cats.niche.push(tag);
      else if(['#love','#instagood','#photooftheday','#beautiful','#happy','#cute','#trending','#viral','#fyp','#foryou','#foryoupage'].includes(tag)) cats.popular.push(tag);
      else cats.medium.push(tag);
    });
    return cats;
  }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = $('#tc-ht-generate');
    if(!btn) return;

    btn.addEventListener('click', function(){
      const topic = $('#tc-ht-topic')?.value?.trim();
      if(!topic){ TCTP.toast('Please enter a topic','warning'); return; }

      const platform = getPlatform();
      const count = getCount();
      TCTP.showProgress('tc-ht-progress', 'Generating hashtags...', 0);

      setTimeout(() => {
        TCTP.setProgress('tc-ht-progress', 50, 'Building hashtag sets...');
        const hashtags = generateHashtags(topic, platform);

        setTimeout(() => {
          TCTP.setProgress('tc-ht-progress', 100, 'Done!');
          const limit = PLATFORM_LIMITS[platform] || 30;

          const resultsEl = $('#tc-ht-results');
          if(resultsEl){
            const displayTags = hashtags.slice(0, count);
            resultsEl.innerHTML = `
              <div class="tc-ht-set-head">
                <span>${platform.charAt(0).toUpperCase() + platform.slice(1)} — ${displayTags.length} hashtags</span>
                <button class="tc-btn-sm" onclick="navigator.clipboard.writeText(this.closest('.tc-ht-set-head').nextElementSibling.textContent.trim());this.textContent='Copied!';setTimeout(()=>this.textContent='Copy All',1500)">Copy All</button>
              </div>
              <p class="tc-ht-all-tags">${displayTags.join(' ')}</p>
              <div class="tc-ht-remaining">${limit - displayTags.length} hashtag slots remaining for ${platform}</div>
            `;
          }

          const catsEl = $('#tc-ht-categories');
          if(catsEl){
            const cats = categorize(hashtags.slice(0, count));
            catsEl.innerHTML = Object.entries(cats).filter(([,v]) => v.length > 0).map(([cat, tags]) => `
              <div class="tc-ht-cat-group">
                <h4>${cat.charAt(0).toUpperCase() + cat.slice(1)} (${tags.length})</h4>
                <p>${tags.join(' ')}</p>
                <button class="tc-btn-sm" onclick="navigator.clipboard.writeText(this.previousElementSibling.textContent);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',1500)">Copy</button>
              </div>
            `).join('');
          }

          $('#tc-ht-total').textContent = hashtags.slice(0, count).length;
          TCTP.switchToResultTab();
          TCTP.toast(hashtags.slice(0, count).length + ' hashtags generated!','success');
        }, 300);
      }, 400);
    });

    document.querySelectorAll('.tc-modes[data-group="ht-platform"] .tc-btn').forEach(btn => {
      btn.addEventListener('click', function(){
        this.closest('.tc-modes').querySelectorAll('.tc-btn').forEach(b => b.classList.remove('sel'));
        this.classList.add('sel');
      });
    });

    $('#tc-ht-num')?.addEventListener('input', function(){
      $('#tc-ht-num-val').textContent = this.value;
    });
  });
})();
