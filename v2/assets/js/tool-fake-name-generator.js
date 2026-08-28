/**
 * tool-fake-name-generator.js
 */
(function(){
  var $=jQuery, genBtn=document.getElementById('fng-generate');
  if(!genBtn)return;

  var NAMES={
    us:{male:['James','John','Robert','Michael','David','William','Richard','Joseph','Thomas','Christopher','Charles','Daniel','Matthew','Anthony','Mark','Donald','Steven','Paul','Andrew','Joshua','Kenneth','Kevin','Brian','George','Timothy','Ronald','Edward','Jason','Jeffrey','Ryan','Jacob','Gary','Nicholas','Eric','Jonathan','Stephen','Larry','Justin','Scott','Brandon','Benjamin','Samuel','Raymond','Gregory','Frank','Alexander','Patrick','Jack','Dennis','Jerry','Tyler','Aaron','Jose','Nathan','Henry','Douglas','Peter','Adam','Zachary','Noah','Harold','Kyle','Carl','Arthur','Gerald','Roger','Keith','Jeremy','Terry','Lawrence','Sean','Christian','Austin','Jesse','Dylan','Bryan','Joe','Bruce','Gabriel','Vincent','Russell','Lucas','Elijah','Logan','Jordan','Billy','Steve','Walter','Ray','Johnny','Philip','Bobby','Wayne','Luis','Alan','Randy','Howard','Carlos','Roy','Tim','Jimmy','Glenn','Jorge','Benny',' maurice','Sam','Leo','Clarence','Edwin','cody','Lynn','Mitchell','Marcus','Leon','Arnold','Bobby','Brad','Ricky','Colton','Lucas','Cody','Travis','Jake','Chad','Brett','Joel','Vincent','Derek','Spencer','Trent','Dominic','Cody','Damian','Maxwell','Dean','Leroy','Jerry','Dustin','Bryce','Dylan','Malcolm','Curtis','Troy','Roland','Darren','Lee','Victor','Jesse','Ethan','Dennis','Jeremy','Aiden','Calvin','Ruben','Adrian','Vincent','Nathan','Luis','Randy','Marvin','Brett','Jesse','Bryant','Derek','Spencer','Colin','Grant','Collin','Dominic','Maxwell','Dean','Leroy','Benny','Bryce','Malcolm','Curtis','Troy','Roland','Darren','Lee','Victor','Jesse','Ethan','Dennis','Aiden','Calvin','Ruben','Adrian','Vincent','Nathan','Luis','Randy','Marvin','Brett','Jesse','Bryant'],
    female:['Mary','Patricia','Jennifer','Linda','Barbara','Elizabeth','Susan','Jessica','Sarah','Karen','Lisa','Nancy','Betty','Margaret','Sandra','Ashley','Dorothy','Kimberly','Emily','Donna','Michelle','Carol','Amanda','Melissa','Deborah','Stephanie','Rebecca','Sharon','Laura','Cynthia','Kathleen','Amy','Angela','Shirley','Anna','Brenda','Pamela','Emma','Nicole','Helen','Samantha','Katherine','Christine','Debra','Rachel','Carolyn','Janet','Catherine','Maria','Heather','Diane','Ruth','Julie','Olivia','Joyce','Virginia','Victoria','Kelly','Lauren','Christina','Joan','Evelyn','Judith','Megan','Andrea','Cheryl','Hannah','Jacqueline','Martha','Gloria','Teresa','Ann','Sara','Madison','Frances','Kathryn','Janice','Jean','Abigail','Alice','Judy','Sophia','Grace','Denise','Amber','Doris','Marilyn','Danielle','Beverly','Isabella','Theresa','Diana','Natalie','Brittany','Charlotte','Marie','Kayla','Alexis','Lori']},
    uk:['Oliver','George','Harry','Jack','Jacob','Charlie','Thomas','Oscar','William','James','Alfie','Leo','Arthur','Henry','Freddie','Ella','Olivia','Isla','Emily','Isabella','Mia','Poppy','Ava','Amelia','Jessica','Nancy','Sophie','Grace','Lily','Phoebe','Evie','Scarlett','Ruby','Chloe','Daisy'],
    ca:['Liam','Noah','Oliver','Ethan','Lucas','Logan','Jacob','Aiden','Muhammad','Alexander','William','James','Benjamin','Leo','Theodore','Emma','Olivia','Charlotte','Amelia','Sophia','Mia','Isla','Harper','Evelyn','Avery','Luna','Camila','Gianna','Ella','Scarlett','Chloe','Penelope','Layla','Riley','Zoey'],
    au:['Oliver','Noah','William','Liam','Jack','Henry','Lucas','Alexander','Ethan','James','Mia','Olivia','Charlotte','Amelia','Isla','Sophia','Chloe','Grace','Lily','Ella','Emily','Ava','Harper','Scarlett','Evelyn','Isabella','Ruby','Willow','Matilda','Ivy'],
    de:['Hans','Peter','Werner','Karl','Thomas','Wolfgang','Klaus','Manfred','Dieter','Helmut','Jürgen','Michael','Stefan','Bernd','Frank','Erika','Gisela','Monika','Ingeborg','Christa','Ute','Brigitte','Renate','Petra','Sabine','Angelika','Andrea','Claudia','Birgit','Susanne'],
    fr:['Jean','Pierre','Michel','Philippe','André','Marie','Françoise','Monique','Catherine','Nathalie','Sophie','Isabelle','Sylvie','Véronique','Valérie','Laurent','Olivier','Nicolas','Christophe','Bernard','Juliette','Camille','Léa','Chloé','Inès','Manon','Emma','Jade','Ingrid','Lucie'],
    in:['Aarav','Vivaan','Aditya','Arjun','Sai','Rohan','Vihaan','Armaan','Reyansh','Krishna','Ishaan','Shaurya','Atharv','Advik','Pranav','Aanya','Ananya','Diya','Saisha','Myra','Aaradhya','Saanvi','Pari','Anvi','Aadhya','Priya','Neha','Sneha','Pooja','Riya'],
    br:['Miguel','João','Gabriel','Pedro','Lucas','Matheus','Arthur','Bernardo','Rafael','Felipe','Maria','Julia','Ana','Isabela','Laura','Camila','Lorena','Livia','Giovanna','Beatriz','Fernando','Carlos','Paulo','Marcos','Ricardo','Fernanda','Adriana','Patricia','Sandra','Camila']
  };

  var STREETS={
    us:['Main St','Oak Ave','Maple Dr','Cedar Ln','Elm St','Pine Rd','Walnut St','1st Ave','2nd Ave','3rd Ave','4th Ave','Park Blvd','Washington St','Lincoln Ave','Jefferson Dr','Franklin St','Adams Way','Madison Ave','Monroe St','Harrison Blvd'],
    uk:['High Street','Church Road','Station Road','London Road','Mill Lane','Victoria Street','Queens Road','Kings Avenue','Broadway','Market Street','Park Lane','Green Lane','Church Street','Queen Street','King Street','Station Street','Bridge Road','North Road','South Road','East Lane'],
    ca:['Yonge St','Queen St','King St','Bay St','Bloor St','Dundas St','College St','Adelaide St','Richmond St','Wellington St','Sherbourne St','Parliament St','Church St','Jarvis St','George St','Bond St','Temple St','Grace St','York St','John St'],
    au:['George St','Pitt St','Elizabeth St','Castlereagh St','Macquarie St','Clarence St','Kent St','Bathurst St','Goulburn St','Campbell St','Liverpool St','Haymarket','Sussex St','Druitt St','Market St','Phillip St','Hunter St','Martin Place','Circular Quay','Darling Harbour'],
    de:['Hauptstraße','Schillerstraße','Goethestraße','Bahnhofstraße','Kirchstraße','Schulstraße','Lindenstraße','Gartenstraße','Waldstraße','Bergstraße','Wiesenstraße','Sonnenstraße','Blumenstraße','Birkenstraße','Fichtenstraße','Tannenstraße','Eichenstraße','Ahornstraße','Weidenstraße','Pappelstraße'],
    fr:['Rue de la Paix','Rue de Rivoli','Champs-Élysées','Boulevard Saint-Germain','Rue de Ménilmontant','Rue Oberkampf','Rue du Faubourg','Rue Saint-Antoine','Rue de Temple','Rue de Belleville','Rue des Martyrs','Rue Lepic','Rue de Charonne','Rue de Vaugirard','Avenue des Champs','Boulevard Voltaire','Rue de la Roquette','Rue de Bretagne','Rue du Chemin Vert','Rue de la Folie'],
    in:['MG Road','Park Street','MG Road','Linking Road','Hill Road','SV Road','DN Nagar','Andheri Kurla Road','LBS Road','SVS Road','Station Road','Gandhi Road','Nehru Road','Patel Road','Subhash Road','Tilak Road','Gokhale Road','Dadar TT Road','Bandra Kurla Complex','Juhu Lane'],
    br:['Rua das Flores','Rua da Paz','Rua do Sol','Rua da Lua','Rua Augusta','Rua Oscar Freire','Rua Liberdade','Rua Consolação','Rua Augusta','Rua 13 de Maio','Rua XV de Novembro','Rua da República','Rua Marechal Deodoro','Rua Barão do Rio Branco','Rua São José']
  };

  var DOMAINS=['gmail.com','yahoo.com','outlook.com','hotmail.com','mail.com','protonmail.com','icloud.com','aol.com','zoho.com','yandex.com'];
  var CITIES={
    us:['New York','Los Angeles','Chicago','Houston','Phoenix','Philadelphia','San Antonio','San Diego','Dallas','San Jose','Austin','Jacksonville','Fort Worth','Columbus','Charlotte','Indianapolis','San Francisco','Seattle','Denver','Washington DC','Nashville','Oklahoma City','El Paso','Boston','Portland','Las Vegas','Memphis','Louisville','Baltimore','Milwaukee','Albuquerque','Tucson','Fresno','Sacramento','Mesa','Kansas City','Atlanta','Omaha','Colorado Springs','Raleigh','Long Beach','Virginia Beach','Miami','Oakland','Minneapolis','Tulsa','Tampa','Arlington','New Orleans'],
    uk:['London','Manchester','Birmingham','Leeds','Glasgow','Edinburgh','Liverpool','Bristol','Sheffield','Leicester','Coventry','Bradford','Cardiff','Nottingham','Hull','Newcastle','Stoke','Southampton','Derby','Portsmouth','Brighton','Plymouth','Wolverhampton','Derby','Nottingham'],
    ca:['Toronto','Montreal','Vancouver','Calgary','Edmonton','Ottawa','Winnipeg','Quebec City','Hamilton','Kitchener','London ON','Victoria','Halifax','Oshawa','Windsor','Saskatoon','Regina','St. John\'s','Kelowna','Barrie'],
    au:['Sydney','Melbourne','Brisbane','Perth','Adelaide','Gold Coast','Newcastle','Canberra','Sunshine Coast','Wollongong','Hobart','Geelong','Townsville','Cairns','Darwin','Toowoomba','Ballarat','Bendigo','Launceston','Mackay'],
    de:['Berlin','Hamburg','Munich','Cologne','Frankfurt','Stuttgart','Düsseldorf','Dortmund','Essen','Leipzig','Bremen','Dresden','Hannover','Nuremberg','Duisburg','Bochum','Wuppertal','Bielefeld','Bonn','Mannheim'],
    fr:['Paris','Marseille','Lyon','Toulouse','Nice','Nantes','Strasbourg','Montpellier','Bordeaux','Lille','Rennes','Reims','Saint-Étienne','Toulon','Le Havre','Grenoble','Dijon','Angers','Nîmes','Clermont-Ferrand'],
    in:['Mumbai','Delhi','Bangalore','Hyderabad','Chennai','Kolkata','Pune','Ahmedabad','Jaipur','Lucknow','Kanpur','Nagpur','Indore','Thane','Bhopal','Visakhapatnam','Patna','Vadodara','Ghaziabad','Ludhiana'],
    br:['São Paulo','Rio de Janeiro','Brasília','Salvador','Fortaleza','Belo Horizonte','Manaus','Curitiba','Recife','Porto Alegre','Belém','Goiânia','Guarulhos','Campinas','São Luís','São Bernardo','Maceió','Campo Grande','Teresina','João Pessoa']
  };

  var STATES={
    us:['Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington','West Virginia','Wisconsin','Wyoming'],
    uk:['England','Scotland','Wales','Northern Ireland'],
    ca:['Ontario','Quebec','British Columbia','Alberta','Manitoba','Saskatchewan','Nova Scotia','New Brunswick','Newfoundland','Prince Edward Island'],
    au:['New South Wales','Victoria','Queensland','South Australia','Western Australia','Tasmania','Northern Territory','ACT'],
    de:['Baden-Württemberg','Bavaria','Berlin','Brandenburg','Bremen','Hamburg','Hesse','Lower Saxony','Mecklenburg-Vorpommern','North Rhine-Westphalia','Rhineland-Palatinate','Saarland','Saxony','Saxony-Anhalt','Schleswig-Holstein','Thuringia'],
    fr:['Île-de-France','Provence-Alpes-Côte d\'Azur','Auvergne-Rhône-Alpes','Nouvelle-Aquitaine','Occitanie','Hauts-de-France','Normandie','Grand Est','Bretagne','Pays de la Loire','Centre-Val de Loire','Corse'],
    in:['Maharashtra','Delhi','Karnataka','Tamil Nadu','Gujarat','Rajasthan','Uttar Pradesh','West Bengal','Madhya Pradesh','Telangana','Kerala','Punjab','Haryana','Bihar','Odisha','Assam','Jharkhand','Chhattisgarh','Uttarakhand','Himachal Pradesh'],
    br:['São Paulo','Rio de Janeiro','Minas Gerais','Bahia','Paraná','Rio Grande do Sul','Pernambuco','Ceará','Pará','Santa Catarina','Maranhão','Goiás','Amazonas','Espírito Santo','Mato Grosso','Paraíba','Rio Grande do Norte','Alagoas','Piauí','Mato Grosso do Sul']
  };

  var NAMES_NOW={
    jp:{male:['Haruto','Ren','Yuto','Sota','Hinata','Kaito','Riku','Sora','Takumi','Itsuki','Yuki','Kosuke','Ryusei','Shota','Daiki','Aoi','Yuna','Sakura','Hana','Mio','Rin','Mei','Koharu','Momoka','Natsuki','Haruka','Mai','Sayuri','Riko','Tomoka'],
    female:['Aoi','Yuna','Sakura','Hana','Mio','Rin','Mei','Koharu','Momoka','Natsuki','Haruka','Mai','Sayuri','Riko','Tomoka','Misaki','Emiri','Kaede','Nana','Saki','Rina','Rui','Yui','Hinata','Kokona','Ichika','Mikoto','Reina','Ayaka','Suzu']}};

  var cities_jp=['Tokyo','Osaka','Yokohama','Nagoya','Sapporo','Fukuoka','Kobe','Kyoto','Kawasaki','Hiroshima','Sendai','Kitakyushu','Sakai','Niigata','Hamamatsu','Kumamoto','Sagamihara','Shizuoka','Okayama','Kagoshima'];
  var streets_jp=['Chūō-dōri','Ginza','Shibuya','Shinjuku','Roppongi','Akihabara','Ikebukuro','Ueno','Asakusa','Harajuku','Omotesandō','Meguro','Setagaya','Nakano','Suginami','Toshima','Kita','Bunkyo','Chiyoda','Minato'];

  function pick(arr){return arr[Math.floor(Math.random()*arr.length)];}
  function randInt(a,b){return Math.floor(Math.random()*(b-a+1))+a;}
  function padZero(n){return n<10?'0'+n:''+n;}

  function generateOne(region, gender){
    var names=NAMES[region]||NAMES.us;
    var g=gender==='random'?pick(['male','female']):gender;
    var first=pick(names[g]);
    var lastNames={
      us:['Smith','Johnson','Williams','Brown','Jones','Garcia','Miller','Davis','Rodriguez','Martinez','Hernandez','Lopez','Gonzalez','Wilson','Anderson','Thomas','Taylor','Moore','Jackson','Martin','Lee','Perez','Thompson','White','Harris','Sanchez','Clark','Ramirez','Lewis','Robinson'],
      uk:['Smith','Jones','Williams','Brown','Taylor','Davies','Wilson','Evans','Thomas','Roberts','Johnson','Walker','Wright','Robinson','Thompson','Green','Hall','Baker','Clarke','Hall','Mitchell','Young','King','Hill','Scott','Adams','Baker','Green','Nelson','Carter'],
      ca:['Smith','Brown','Tremblay','Gagnon','Roy','Côté','Bouchard','Gauthier','Morin','Lavoie','Fortin','Gagné','Ouellet','Perrault','Belanger','Bouchard','Dionne','Fortin','Gagnon','Lavigne','Lee','Campbell','Stewart','Morrison','MacDonald','Wilson','Campbell','Murray','Robertson','Graham'],
      au:['Smith','Jones','Williams','Brown','Wilson','Taylor','Johnson','White','Martin','Anderson','Thompson','Nguyen','Thomas','Walker','Robinson','Clark','Lewis','Lee','Hall','Allen','Young','King','Wright','Scott','Green','Baker','Adams','Hill','Mitchell','Roberts'],
      de:['Müller','Schmidt','Schneider','Fischer','Weber','Meyer','Wagner','Becker','Schulz','Koch','Richter','Klein','Wolf','Schröder','Neumann','Schwarz','Zimmermann','Braun','Krüger','Hoffmann'],
      fr:['Martin','Bernard','Dubois','Thomas','Robert','Richard','Petit','Durand','Leroy','Moreau','Simon','Laurent','Lefebvre','Michel','Garcia','David','Bertrand','Roux','Vincent','Fournier'],
      in:['Sharma','Verma','Gupta','Singh','Kumar','Das','Mehta','Joshi','Reddy','Nair','Iyer','Mukherjee','Chatterjee','Ghosh','Bose','Banerjee','Sen','Dutta','Roy','Bhatt'],
      br:['Silva','Santos','Souza','Oliveira','Ferreira','Pereira','Almeida','Costa','Rodrigues','Nascimento','Lima','Araújo','Barbosa','Ribeiro','Carvalho','Martins','Rocha','Correia','Gomes','Martins'],
      jp:['Satō','Suzuki','Takahashi','Tanaka','Watanabe','Itō','Yamamoto','Nakamura','Kobayashi','Kato','Yoshida','Yamada','Sasaki','Yamaguchi','Matsumoto','Inoue','Kimura','Hayashi','Shimizu','Hashimoto']
    };
    var last=pick(lastNames[region]||lastNames.us);

    var streetList=STREETS[region]||STREETS.us;
    var streetNum=randInt(1,9999);
    var street=pick(streetList);

    var cityList=CITIES[region]||CITIES.us;
    var city=pick(cityList);

    var stateList=STATES[region]||STATES.us;
    var state=pick(stateList);

    var zip='';
    if(region==='us') zip=''+randInt(10000,99999);
    else if(region==='uk') zip=String.fromCharCode(65+randInt(0,25))+randInt(1,9)+''+randInt(0,9)+' '+''+randInt(0,9)+String.fromCharCode(65+randInt(0,25))+String.fromCharCode(65+randInt(0,25));
    else if(region==='ca') zip=String.fromCharCode(65+randInt(0,25))+''+randInt(0,9)+' '+''+randInt(0,9)+String.fromCharCode(65+randInt(0,25))+''+randInt(0,9);
    else if(region==='au') zip=''+randInt(1000,9999);
    else if(region==='de') zip=''+randInt(10000,99999);
    else if(region==='fr') zip=''+randInt(10000,99999);
    else if(region==='in') zip=''+randInt(100000,999999);
    else if(region==='br') zip=''+randInt(10000,99999)+'-'+''+randInt(100,999);
    else if(region==='jp') zip=''+randInt(100,999)+'-'+''+randInt(1000,9999);

    var phone='';
    if(region==='us') phone='('+randInt(200,999)+') '+randInt(200,999)+'-'+randInt(1000,9999);
    else if(region==='uk') phone='0'+randInt(7000,7999)+' '+randInt(100000,999999);
    else if(region==='ca') phone='('+randInt(200,999)+') '+randInt(200,999)+'-'+randInt(1000,9999);
    else if(region==='au') phone='04'+randInt(10,99)+' '+randInt(100,999)+' '+randInt(100,999);
    else if(region==='de') phone='+49 '+randInt(150,899)+' '+randInt(1000000,9999999);
    else if(region==='fr') phone='06 '+padZero(randInt(10,99))+' '+padZero(randInt(10,99))+' '+padZero(randInt(10,99))+' '+padZero(randInt(10,99));
    else if(region==='in') phone='+91 '+randInt(6000,9999)+' '+randInt(100000,999999);
    else if(region==='br') phone='('+randInt(11,99)+') 9'+randInt(1000,9999)+'-'+randInt(1000,9999);
    else if(region==='jp') phone='0'+randInt(70,90)+'-'+randInt(1000,9999)+'-'+randInt(1000,9999);

    var email=(first.toLowerCase()+'.'+last.toLowerCase()+randInt(1,999)+'@'+pick(DOMAINS));

    var domains=['gmail.com','yahoo.com','outlook.com','hotmail.com','protonmail.com','icloud.com','zoho.com','yandex.com'];

    var male_titles=['Mr.','Dr.','Prof.'];
    var female_titles=['Ms.','Mrs.','Dr.','Prof.'];
    var title=g==='male'?pick(male_titles):pick(female_titles);

    var birthYear=randInt(1960,2005);
    var birthMonth=padZero(randInt(1,12));
    var birthDay=padZero(randInt(1,28));
    var age=2026-birthYear;

    var ssn=''+randInt(100,999)+'-'+randInt(10,99)+'-'+randInt(1000,9999);

    var country={us:'United States',uk:'United Kingdom',ca:'Canada',au:'Australia',de:'Germany',fr:'France',in:'India',br:'Brazil',jp:'Japan'}[region]||'United States';

    var user=(first.toLowerCase()+last.toLowerCase()+randInt(1,999));

    var pwdChars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    var pwd='';
    for(var p=0;p<randInt(12,18);p++) pwd+=pwdChars[randInt(0,pwdChars.length-1)];

    return {
      title:title, first:first, last:last, gender:g,
      email:email, phone:phone,
      street:streetNum+' '+street, city:city, state:state, zip:zip, country:country,
      birthdate:birthYear+'-'+birthMonth+'-'+birthDay, age:age,
      ssn:ssn, username:user, password:pwd
    };
  }

  var identities=[];

  genBtn.addEventListener('click', function(){
    var region=document.getElementById('fng-region').value;
    var gender=document.getElementById('fng-gender').value;
    var count=parseInt(document.getElementById('fng-count').value)||5;

    window.TCTP.showProgress();
    genBtn.disabled=true;

    setTimeout(function(){
      identities=[];
      for(var i=0;i<count;i++) identities.push(generateOne(region,gender));

      renderCards();
      renderTable();
      renderJSON();

      document.getElementById('fng-result').style.display='';
      window.TCTP.hideProgress();
      genBtn.disabled=false;
      window.TCTP.switchToResultTab();
    },100);
  });

  function renderCards(){
    var html='';
    identities.forEach(function(id,idx){
      html+='<div class="tctp-fng-card">'+
        '<div class="tctp-fng-card-header">'+
          '<div class="tctp-fng-avatar">'+id.first[0]+id.last[0]+'</div>'+
          '<div><strong>'+id.title+' '+id.first+' '+id.last+'</strong><br><span style="font-size:12px;color:var(--muted,#64748b);">'+id.gender.charAt(0).toUpperCase()+id.gender.slice(1)+' &middot; Age '+id.age+'</span></div>'+
        '</div>'+
        '<div class="tctp-fng-card-body">'+
          '<div class="tctp-fng-field"><i class="fa-solid fa-envelope"></i><span>'+id.email+'</span></div>'+
          '<div class="tctp-fng-field"><i class="fa-solid fa-phone"></i><span>'+id.phone+'</span></div>'+
          '<div class="tctp-fng-field"><i class="fa-solid fa-location-dot"></i><span>'+id.street+', '+id.city+', '+id.state+' '+id.zip+'</span></div>'+
          '<div class="tctp-fng-field"><i class="fa-solid fa-globe"></i><span>'+id.country+'</span></div>'+
          '<div class="tctp-fng-field"><i class="fa-solid fa-cake-candles"></i><span>'+id.birthdate+'</span></div>'+
          '<div class="tctp-fng-field"><i class="fa-solid fa-fingerprint"></i><span>SSN: '+id.ssn+'</span></div>'+
          '<div class="tctp-fng-field"><i class="fa-solid fa-user"></i><span>'+id.username+'</span></div>'+
        '</div>'+
      '</div>';
    });
    document.getElementById('fng-cards').innerHTML='<div class="tctp-fng-grid">'+html+'</div>';
  }

  function renderTable(){
    var rows='';
    identities.forEach(function(id){
      rows+='<tr><td>'+id.title+' '+id.first+' '+id.last+'</td><td>'+id.gender+'</td><td>'+id.email+'</td><td>'+id.phone+'</td><td>'+id.city+', '+id.state+'</td><td>'+id.country+'</td></tr>';
    });
    document.getElementById('fng-table').innerHTML='<div style="overflow-x:auto;"><table class="tctp-table"><thead><tr><th>Name</th><th>Gender</th><th>Email</th><th>Phone</th><th>City, State</th><th>Country</th></tr></thead><tbody>'+rows+'</tbody></table></div>';
  }

  function renderJSON(){
    document.getElementById('fng-json-pre').textContent=JSON.stringify(identities,null,2);
  }

  document.getElementById('fng-copy-all').addEventListener('click',function(){
    var text=identities.map(function(id){
      return id.title+' '+id.first+' '+id.last+'\n'+id.email+'\n'+id.phone+'\n'+id.street+', '+id.city+', '+id.state+' '+id.zip+'\n'+id.country+'\nDOB: '+id.birthdate+'\nSSN: '+id.ssn+'\nUsername: '+id.username;
    }).join('\n\n---\n\n');
    window.TCTP.copyText(text);
  });

  document.getElementById('fng-copy-json').addEventListener('click',function(){
    window.TCTP.copyText(JSON.stringify(identities,null,2));
  });

  document.getElementById('fng-copy-csv').addEventListener('click',function(){
    var csv='Title,First Name,Last Name,Gender,Email,Phone,Street,City,State,Zip,Country,Birthdate,Age,SSN,Username\n';
    identities.forEach(function(id){
      csv+='"'+id.title+'","'+id.first+'","'+id.last+'","'+id.gender+'","'+id.email+'","'+id.phone+'","'+id.street+'","'+id.city+'","'+id.state+'","'+id.zip+'","'+id.country+'","'+id.birthdate+'","'+id.age+'","'+id.ssn+'","'+id.username+'"\n';
    });
    window.TCTP.copyText(csv);
  });

  // Tabs
  document.querySelectorAll('.tctp-rsz-tab[data-tab]').forEach(function(tab){
    tab.addEventListener('click',function(){
      document.querySelectorAll('.tctp-rsz-tab').forEach(function(t){t.classList.remove('sel')});
      tab.classList.add('sel');
      var id=tab.getAttribute('data-tab');
      document.querySelectorAll('.tctp-rsz-tab-panel').forEach(function(p){p.style.display='none'});
      var map={cards:'fng-cards',table:'fng-table',json:'fng-json'};
      var panel=document.getElementById(map[id]);
      if(panel) panel.style.display='';
    });
  });
})();