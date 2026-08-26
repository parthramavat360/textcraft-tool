/**
 * Instagram Caption Generator — 100% client-side
 * Generates captions with emojis, hashtags, CTAs
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);

  const EMOJIS = {
    casual: ['😊','✨','🙌','💪','🎉','❤️','🔥','💯','😎','👏','💪','🌟','💫','⭐️','💫','🎯','💯'],
    professional: ['📈','✅','💼','🎯','📊','💡','🚀','⭐️','🏆','📌','🔑','📊','💬','✨','📉','🎯'],
    funny: ['😂','🤣','😅','😬','💀','🤷','😤','🫠','🙃','😏','🤪','😜','🥳','🤡','😏','🫡'],
    inspirational: ['✨','🌟','💫','💪','🙌','🔥','⭐️','🦋','🌈','💜','🌱','💎','🕊️','🌅','🌸','💫'],
    educational: ['📚','📝','💡','🧠','🎓','📖','✏️','🔬','📐','🧪','🎓','💡','📊','🎯','📌','📝'],
    promotional: ['🛒','💰','🎁','🏷️','📦','🎪','🛍️','💳','🏷️','🔥','⚡️','🎉','💎','🏆','✅','⭐️']
  };

  const HASHTAGS = {
    general: ['#love','#instagood','#photooftheday','#beautiful','#happy','#cute','#instadaily','#picoftheday','#fun','#amazing','#style','#smile','#travel','#food','#fitness','#motivation','#art','#music','#fashion','#nature'],
    food: ['#foodie','#foodporn','#instafood','#homecooking','#healthyfood','#foodlover','#yummy','#delicious','#cooking','#recipe','#homemade','#eatclean','#foodgasm','#cheflife','#mealprep'],
    fitness: ['#fitness','#gym','#workout','#fitfam','#motivation','#bodybuilding','#healthylifestyle','#training','#exercise','#gains','#fitlife','#strongnotskinny','#activelife','#cardio','#wellness'],
    travel: ['#travel','#wanderlust','#instatravel','#travelgram','#explore','#vacation','#adventure','#beautifuldestinations','#travelphotography','#tourism','#globetrotter','#bucketlist','#roadtrip','#backpacking','#nomad'],
    fashion: ['#fashion','#styleoftheday','#ootd','#fashionista','#outfitoftheday','#lookoftheday','#whatiwore','#fashionblogger','#stylish','#clothes','#trendy','#outfitinspo','#wardrobe','#accessories','#streetstyle'],
    business: ['#business','#entrepreneur','#marketing','#success','#startup','#motivation','#hustle','#mindset','#goals','#leadership','#branding','#socialmedia','#digitalmarketing','#growth','#innovation']
  };

  const CTA = {
    casual: ['Double tap if you agree! 💕','Tag someone who needs this! 🏷️','Drop a 🔥 if you love this!','Save this for later! 📌','Share with a friend! 💫'],
    professional: ['Connect for more insights. 💼','Follow for industry updates. 📊','Share your thoughts below. 💬','Learn more at the link in bio. 🔗','Stay tuned for more content. 📈'],
    funny: ['Tag your bestie who does this! 😂','Tag someone who needs to see this! 🤣','Share if this is SO you! 💀','Tag someone who can relate! 😅','Double tap if you felt this! 🫠'],
    inspirational: ['Believe in yourself. ✨','Share this with someone who needs it. 💪','Save this reminder. 📌','You are enough. 🌟','Keep going. 🔥'],
    educational: ['Save this for later reference! 📚','Share with students! 🎓','Bookmark for study time! 📝','Follow for more tips! 💡','Tag a learner! 🧠'],
    promotional: ['Shop now — link in bio! 🛒','Limited time offer! ⚡️','Don\'t miss out! 🎁','Order today! 📦','Get yours now! 💰']
  };

  function getTone(){ return document.querySelector('.tc-modes[data-group="ig-tone"] .sel')?.dataset.val || 'creative'; }
  function getLength(){ return parseInt($('#tc-ig-length')?.value || '2'); }
  function useEmojis(){ return $('#tc-ig-emojis')?.checked; }
  function useHashtags(){ return $('#tc-ig-hashtags')?.checked; }
  function useCTA(){ return $('#tc-ig-cta')?.checked; }
  function useLineBreaks(){ return $('#tc-ig-linebreaks')?.checked; }

  function generateCaption(topic, tone, length){
    const nl = useLineBreaks() ? '\n\n' : '\n';
    let parts = [];

    if(useEmojis()){
      const pool = EMOJIS[tone] || EMOJIS.casual;
      const emoji1 = pool[Math.floor(Math.random()*pool.length)];
      const emoji2 = pool[Math.floor(Math.random()*pool.length)];
      const emoji3 = pool[Math.floor(Math.random()*pool.length)];
      parts.push(emoji1 + ' ' + topic + ' ' + emoji2);
    } else {
      parts.push(topic);
    }

    if(length >= 2){
      const hooks = {
        casual: ["Here's something I've been thinking about... " + topic, "You know what I love? " + topic + "!", "Can we talk about " + topic + " for a sec? 😊", "POV: You just discovered " + topic],
        professional: ["Exploring the impact of " + topic + " on our industry.", "Key insights on " + topic + " you should know.", "Here's what " + topic + " means for your strategy.", "Breaking down " + topic + ": What you need to know."],
        funny: ["Me learning about " + topic + " like... 🤓", "When someone says they don't know about " + topic + " 😬", "Nobody: \nAbsolutely nobody: \nMe: *talks about " + topic + "*", topic + " hits different at 2am"],
        inspirational: [topic + " taught me that growth comes from stepping outside your comfort zone.", "The power of " + topic + " changed my perspective entirely.", "When you embrace " + topic + ", magic happens. ✨", "Start your journey with " + topic + " today."],
        educational: ["Did you know? " + topic + " is more important than you think.", "Here's a quick breakdown of " + topic + " 📚", "3 things you should know about " + topic + ":", "Let's dive into " + topic + " — thread 🧵"],
        promotional: ["Introducing our take on " + topic + "! 🚀", "Your search for " + topic + " ends here.", "Level up your " + topic + " game with our guide.", "The ultimate " + topic + " resource is finally here! 🎉"]
      };
      const pool = hooks[tone] || hooks.casual;
      parts.push(pool[Math.floor(Math.random()*pool.length)]);
    }

    if(length >= 3){
      const details = {
        casual: ["Drop your thoughts below! I'd love to hear your experience with this. 💬", "Have you tried this before? Let me know in the comments! 👇", "This is one of my favorite things to share. Hope you enjoy it! ❤️"],
        professional: ["We've seen incredible results by focusing on " + topic + ". Here's what the data shows.", "Our team has been researching " + topic + " extensively. The findings are clear.", topic + " is evolving rapidly. Stay ahead of the curve."],
        funny: ["If you laughed, you're legally obligated to share this with a friend. It's in the terms and conditions. I don't make the rules. 😂", "If this isn't the most relatable thing you've seen today, I don't know what is.", "Tag someone who NEEDS to see this immediately. No, not next week. NOW."],
        inspirational: ["Remember: Every expert was once a beginner. Start where you are, use what you have, do what you can. 💪", "The best time to start was yesterday. The second best time is now. 🌟", "Your journey with " + topic + " starts with a single step. Take it today."],
        educational: ["Save this post for later — you'll want to reference it! 📌", "Follow for more educational content like this. New posts every week! 🎓", "Found this helpful? Share it with someone who could use this info! 💡"],
        promotional: ["Limited time: Get our premium " + topic + " guide — link in bio! 🔗", "Don't miss out on our " + topic + " masterclass. Seats are filling up! 🎫", "Try it free for 7 days. No credit card required. 💳"]
      };
      const pool = details[tone] || details.casual;
      parts.push(pool[Math.floor(Math.random()*pool.length)]);
    }

    let caption = parts.join(nl);

    if(useCTA()){
      const ctaPool = CTA[tone] || CTA.casual;
      caption += nl + ctaPool[Math.floor(Math.random()*ctaPool.length)];
    }

    if(useHashtags()){
      const htPool = (HASHTAGS.general || []).slice(0, Math.min(15, 10 + length * 5));
      const tonePool = HASHTAGS[tone] || HASHTAGS.general;
      const combined = [...htPool, ...tonePool.slice(0, 8)];
      const shuffled = combined.sort(() => Math.random() - 0.5);
      const count = length === 1 ? 8 : length === 2 ? 15 : 25;
      caption += nl + shuffled.slice(0, count).join(' ');
    }

    return caption;
  }

  function getHashtagSets(topic){
    const words = topic.toLowerCase().split(/\s+/).filter(w => w.length > 2);
    const base = words.map(w => '#' + w);
    const sets = [
      { label: 'High Volume (Mega)', tags: [...base.slice(0, 3), ...HASHTAGS.general.slice(0, 10)] },
      { label: 'Medium Volume (Niche)', tags: [...base.map(w => w + 'tips'), ...base.map(w => w + 'life'), ...HASHTAGS.general.slice(5, 12)] },
      { label: 'Low Volume (Micro)', tags: [...base.map(w => w + 'community'), ...base.map(w => w + 'gram'), ...base.map(w => w + 'oftheday')] }
    ];
    return sets.map(s => ({ ...s, tags: s.tags.slice(0, 15) }));
  }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = $('#tc-ig-generate');
    if(!btn) return;

    btn.addEventListener('click', function(){
      const topic = $('#tc-ig-topic')?.value?.trim();
      if(!topic){ TCTP.toast('Please enter a topic','warning'); return; }

      const tone = getTone();
      const length = getLength();
      TCTP.showProgress('tc-ig-progress', 'Generating captions...', 0);

      setTimeout(() => {
        TCTP.setProgress('tc-ig-progress', 50, 'Building captions...');
        const captions = [];
        const count = 5 + (length - 1) * 2;
        for(let i = 0; i < count; i++){
          captions.push(generateCaption(topic, tone, length));
        }
        TCTP.setProgress('tc-ig-progress', 80, 'Generating hashtags...');
        const hashtagSets = getHashtagSets(topic);

        setTimeout(() => {
          TCTP.setProgress('tc-ig-progress', 100, 'Done!');

          const captionsEl = $('#tc-ig-captions');
          if(captionsEl){
            captionsEl.innerHTML = captions.map((c, i) => `
              <div class="tc-ig-caption-card">
                <div class="tc-ig-caption-head">
                  <span class="tc-ig-caption-num">Caption ${i + 1}</span>
                  <button class="tc-btn-sm" onclick="navigator.clipboard.writeText(this.closest('.tc-ig-caption-card').querySelector('pre').textContent);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',1500)">Copy</button>
                </div>
                <pre class="tc-ig-caption-text">${c.replace(/</g,'&lt;')}</pre>
                <span class="tc-ig-caption-chars">${c.length} chars</span>
              </div>
            `).join('');
          }

          const htEl = $('#tc-ig-hashtag-sets');
          if(htEl){
            htEl.innerHTML = hashtagSets.map(s => `
              <div class="tc-ig-ht-set">
                <h4>${s.label}</h4>
                <p class="tc-ig-ht-tags">${s.tags.join(' ')}</p>
                <button class="tc-btn-sm" onclick="navigator.clipboard.writeText(this.previousElementSibling.textContent);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy All',1500)">Copy All</button>
              </div>
            `).join('');
          }

          $('#tc-ig-count').textContent = captions.length;
          const avgLen = Math.round(captions.reduce((a,c) => a + c.length, 0) / captions.length);
          $('#tc-ig-avglen').textContent = avgLen + ' chars';

          TCTP.switchToResultTab();
          TCTP.toast(captions.length + ' captions generated!','success');
        }, 300);
      }, 400);
    });

    document.querySelectorAll('.tc-modes[data-group="ig-tone"] .tc-btn').forEach(btn => {
      btn.addEventListener('click', function(){
        this.closest('.tc-modes').querySelectorAll('.tc-btn').forEach(b => b.classList.remove('sel'));
        this.classList.add('sel');
      });
    });

    $('#tc-ig-length')?.addEventListener('input', function(){
      const v = parseInt(this.value);
      const labels = {1:'Short',2:'Medium',3:'Long'};
      $('#tc-ig-length-val').textContent = labels[v] || 'Medium';
    });
  });
})();
