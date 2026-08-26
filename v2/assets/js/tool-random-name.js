/**
 * Random Name Generator — 100% client-side
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);

  const MALE_FIRST = ['James','John','Robert','Michael','David','William','Richard','Joseph','Thomas','Charles','Christopher','Daniel','Matthew','Anthony','Mark','Donald','Steven','Paul','Andrew','Joshua','Kenneth','Kevin','Brian','George','Timothy','Ronald','Edward','Jason','Jeffrey','Ryan','Jacob','Gary','Nicholas','Eric','Jonathan','Stephen','Larry','Justin','Scott','Brandon','Benjamin','Samuel','Raymond','Gregory','Frank','Alexander','Patrick','Jack','Dennis','Jerry','Tyler','Aaron','Jose','Nathan','Henry','Douglas','Peter','Adam','Zachary','Harold','Carl','Arthur','Gerald','Roger','Keith','Jeremy','Terry','Lawrence','Sean','Justin','Brandon','Jesse','Dylan','Bryan','Joe','Jordan','Billy','Bruce','Gabriel','Vincent','Russell','Charlie','Louis','Philip','Harry'];
  const FEMALE_FIRST = ['Mary','Patricia','Jennifer','Linda','Barbara','Elizabeth','Susan','Jessica','Sarah','Karen','Lisa','Nancy','Betty','Margaret','Sandra','Ashley','Dorothy','Kimberly','Emily','Donna','Michelle','Carol','Amanda','Melissa','Deborah','Stephanie','Rebecca','Sharon','Laura','Cynthia','Kathleen','Amy','Angela','Shirley','Anna','Brenda','Pamela','Emma','Nicole','Helen','Samantha','Katherine','Christine','Debra','Rachel','Carolyn','Janet','Catherine','Maria','Heather','Diane','Ruth','Julie','Olivia','Joyce','Virginia','Victoria','Kelly','Lauren','Christina','Joan','Evelyn','Judith','Megan','Andrea','Cheryl','Hannah','Jacqueline','Martha','Gloria','Teresa','Ann','Sara','Madison','Frances','Kathryn','Janice','Jean','Abigail','Alice','Judy','Sophia','Grace','Denise','Amber','Doris','Marilyn','Danielle','Beverly','Isabella','Theresa','Diana','Natalie','Brittany','Charlotte','Marie','Kayla','Alexis','Lori'];
  const LAST = ['Smith','Johnson','Williams','Brown','Jones','Garcia','Miller','Davis','Rodriguez','Martinez','Hernandez','Lopez','Gonzalez','Wilson','Anderson','Thomas','Taylor','Moore','Jackson','Martin','Lee','Perez','Thompson','White','Harris','Sanchez','Clark','Ramirez','Lewis','Robinson','Walker','Young','Allen','King','Wright','Scott','Torres','Nguyen','Hill','Flores','Green','Adams','Nelson','Baker','Hall','Rivera','Campbell','Mitchell','Carter','Roberts','Gomez','Phillips','Evans','Turner','Diaz','Parker','Cruz','Edwards','Collins','Reyes','Stewart','Morris','Morales','Murphy','Cook','Rogers','Gutierrez','Ortiz','Morgan','Cooper','Peterson','Bailey','Reed','Kelly','Howard','Ramos','Kim','Cox','Ward','Richardson','Watson','Brooks','Chavez','Wood','James','Bennett','Gray','Mendoza','Ruiz','Hughes','Price','Alvarez','Castillo','Sanders','Patel','Myers','Long','Ross','Foster','Jimenez','Powell','Jenkins','Perry','Russell','Sullivan','Bell','Coleman','Butler','Henderson','Barnes','Gonzales','Fisher','Vasquez','Simmons','Patterson','Jordan','Reynolds','Hamilton','Graham','Wallace','Gibson','Bryan','Alexander','Tucker','Harvey','Marshall','Hunt','Freeman','Webb','Burns','Spencer','Stone','Hawkins','Dunn','Perkins','Hicks','Fox','Black','Holmes','Mason','Knight','Wells','Webb','Stone','Hawkins','Dunn','Perkins','Hicks','Fox','Black','Holmes','Mason','Knight','Rose','Fox','Bass','Hendricks','Douglas','Hart','Sullivan','Wells','Hogan','Page','Stone','Payne','Chapman','Ross','Hunter','Palmer','Moss','Brock','Ray','Ball','Perkins','Spencer','Hunt','Grant','Owens','Fisher','Webb','Sullivan','Porter','Spencer','Knight','Stone','Hawkins','Dunn','Perkins','Hicks','Fox','Black','Holmes'];
  const MIDDLE = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

  function pick(arr){ return arr[Math.floor(Math.random()*arr.length)]; }
  function getGender(){ return document.querySelector('.tc-modes[data-group="rn-gender"] .sel')?.dataset.val || 'any'; }
  function getFormat(){ return document.querySelector('.tc-modes[data-group="rn-format"] .sel')?.dataset.val || 'full'; }
  function getCount(){ return parseInt($('#tc-rn-num')?.value || '10'); }

  function genName(gender){
    let first;
    if(gender === 'male') first = pick(MALE_FIRST);
    else if(gender === 'female') first = pick(FEMALE_FIRST);
    else first = Math.random() > 0.5 ? pick(MALE_FIRST) : pick(FEMALE_FIRST);
    return { first, middle: pick(MIDDLE), last: pick(LAST) };
  }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = $('#tc-rn-generate');
    if(!btn) return;
    btn.addEventListener('click', function(){
      const gender = getGender(), format = getFormat(), count = getCount();
      TCTP.showProgress('tc-rn-progress', 'Generating...', 0);
      setTimeout(() => {
        TCTP.setProgress('tc-rn-progress', 50, 'Building names...');
        const names = [];
        for(let i = 0; i < count; i++){
          const n = genName(gender);
          if(format === 'full') names.push(n.first + ' ' + n.last);
          else if(format === 'first') names.push(n.first);
          else if(format === 'last') names.push(n.last);
          else names.push(n.first + ' ' + n.middle + '. ' + n.last);
        }
        TCTP.setProgress('tc-rn-progress', 100, 'Done!');
        const namesEl = $('#tc-rn-names');
        if(namesEl){
          namesEl.innerHTML = names.map((nm,i) => '<div class="tc-rn-card"><span class="tc-rn-num">#' + (i+1) + '</span><span class="tc-rn-name">' + nm + '</span><button class="tc-btn-sm" onclick="navigator.clipboard.writeText(this.previousElementSibling.textContent);this.textContent=\'Copied!\';setTimeout(()=>this.textContent=\'Copy\',1200)">Copy</button></div>').join('');
        }
        const copyEl = $('#tc-rn-copylist');
        if(copyEl){
          copyEl.innerHTML = '<textarea class="tc-textarea" readonly rows="8" onclick="this.select()">' + names.join('\n') + '</textarea><br><button class="tc-btn-sm" style="margin-top:8px" onclick="navigator.clipboard.writeText(this.previousElementSibling.value);this.textContent=\'Copied!\';setTimeout(()=>this.textContent=\'Copy All\',1200)">Copy All</button>';
        }
        TCTP.switchToResultTab();
        TCTP.toast(names.length + ' names generated!','success');
      }, 300);
    });

    var genderGroup = document.querySelector('[data-group="rn-gender"]');
    if(genderGroup){ TCTP.initModeGroup(genderGroup); }
    var formatGroup = document.querySelector('[data-group="rn-format"]');
    if(formatGroup){ TCTP.initModeGroup(formatGroup); }
    $('#tc-rn-num')?.addEventListener('input', function(){ $('#tc-rn-num-val').textContent = this.value; });
  });
})();
