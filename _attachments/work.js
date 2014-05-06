
  $.fn.toggleName = function(name1, name2) {
    this.val(
      (this.val()==name1)? name2 : name1
    );
  }
  
  $.fn.toggleText = function(text1, text2) {
    this.text(
      (this.text()==text1)? text2 : text1
    );
  }

  function find(version) {
    return $(".pleat.open[data-version='"+version+"']");
  }

  function findPleat(version) {
    return $(".pleat.close[data-version='"+version+"']");
  }

  $.fn.getHeight = function() {
    var fake=$("<div>").css({"position":"fixed","left":"-1000px"}).append(this.clone());
    $(document.body).append(fake);
    var height=fake.outerHeight();
    fake.remove();
    return height;
  }

  $.fn.rotate = function () {
    return $("<div>").addClass("rotated-text__wrapper").append(
      $("<div>").addClass("rotated-text").append(this)
    );
  }

  function addPleat(version) {
    var header=find(version).filter("th").first();
    var pleat=$("<td/>").addClass("pleat").addClass("close").attr("rowspan",$("tbody tr").length).attr("data-version",version);
    var language=header.find(".language").clone(true,true);
    language.attr("title",language.html()).html(language.data("id")).removeClass("expand");
    pleat.append(language.rotate());
    pleat.append(header.find(".creator").clone(true,true).rotate());
    find(version).filter("td").first().after(pleat);
    var pleatHead=$("<th/>").addClass("pleat").addClass("close").append(
       $("<div>").addClass("relative-wrapper").append(
            $("<span>").addClass("button show").html("Montrer")
       )
    ).attr("data-version",version);
    header.after(pleatHead.clone());
    var pleatFoot=$("<th/>").addClass("pleat").addClass("close").attr("data-version",version);
    find(version).last().after(pleatHead);
  }

  function findUnits(version) {
    return find(version).find(".unit");
  }

  function getVersions() {
    var versions=[];
    $("#hexapla .header tr:first-child .pleat.open").each(function() {
      versions.push($(this).data("version"));
    });
    return versions;
  }

  function getSize(unit) {
    var rowspan=unit.closest("td").attr("rowspan");
    if (rowspan) return parseInt(rowspan); 
    else return 1;
  }

  function positionSplits(context) {
    $("span.split",context).each(function() {
      var currTd=$(this).closest("td");
      var line=$(this).data("line");
      var position={};
      var tableLine=$("tr[data-line="+line+"]");
      if (tableLine.find("td:visible").length>0) {
	position=tableLine.find("td:visible").position();
        $(this).removeClass("dynamic");
      } else {
        $(this).addClass("dynamic");
      }
      var currPos=$(this).closest("td").position();
      $(this).css("top",(position.top-currPos.top-24)+"px");
    });
    positionDynamicSplits(context);
  }
  
  function positionDynamicSplits(context) {
    $("span.split.dynamic",context).each(function() {
      var unit=$(this).closest(".unit");
      var currTop=unit.position().top;
      var currLine=$(this).data("line");
      var startTop,endTop,startLine,endLine;
      var prev=$(this).prev(".split:not(.dynamic)");
      if (prev.length==1) {
        startTop=prev.position().top-currTop;
        startLine=prev.data("line");
      } else {
        startTop=0;
        startLine=unit.getLine();
      }
      var next=$(this).next(".split:not(.dynamic)");
      if (next.length==1) {
        endTop=next.position().top-currTop;
        endLine=next.data("line");
      } else {
        endTop=unit.height();
        endLine=unit.getLine()+getSize(unit);
      }
      var lineDiff=(currLine-startLine)/(endLine-startLine);
      var top=lineDiff*(endTop-startTop);
      $(this).css("top",(top-24)+"px");
    });
  }

  function positionPleats() {
    var closedPleats=$(".pleat.close:visible");
    //pleats positioning is done automatically with FF23 and Chromium 28
    //chromium has a bug, which requires to redraw the fixed elements
    closedPleats.children(":visible").redraw();
    return;
  }

  //http://forrst.com/posts/jQuery_redraw-BGv
  $.fn.redraw = function() {
    return this.hide(0, function(){$(this).show()});
  };

  function toggleShow(version) {
    find(version).toggle();
    findPleat(version).toggle();
    //when one version is edited, and we show a non edited one, pagination is ugly
    //so we toggle edited versions twice to get back to correct pagination
    //applying to both top and bottom buttons, so we do it twice
    find($(".unit.edit").getVersion("td.open")).find("input.edit").each(toggleEdit); 
    positionSplits();
  }

  $.fn.isEdited = function() {
    return this.find("textarea").length>0;
  }

  function htmlToString(unit) {
    return unit.html()
      .replace(/<br\/?>/g, "\n").replace("&lt;","<").replace("&gt;",">");
  }

  function stringToHtml(formattedString) {
     return formattedString.replace("<","&lt").replace(">","&gt;").replace(/\n$/,"\n ").replace(/\n/g, "<br>");
  }

  $.fn.getVersion = function(ancestor) {
    return this.closest(ancestor).data("version");
    return $(ancestor,$(this).closest("tr")).index($(this).closest(ancestor)) +1 ;
  }

  $.fn.getReference = function() {
    return {
      version: this.closest(".unit").data("version"),
      line: this.closest("tr").data("line")
    }
  }
  
  $.fn.getLanguage = function() {
    return find(this.getVersion("td.open")).find(".language").data("id");
  }
  
  $.fn.getLine = function() {
    return this.closest("tr").data("line");
  }

  function autoSize() {
    // Copy textarea contents; browser will calculate correct height of copy,
    // which will make overall container taller, which will make textarea taller.
    var text = stringToHtml($(this).val());
    $(this).parent().find("div.text").html(text);
    $(this).css({'width':'100%','height':'100%'});
  }
  
  function modified() {
    $(this).addClass("dirty");
    autoSize.apply(this);
    positionSplits($(this).closest(".unit"));
  }

  function toggleEdit () {
    var version=$(this).getVersion("th.open");
	var doc = find(version);
    var units = findUnits(version);
	var top = doc.first();
	var edited = units.isEdited();
    doc.find("input.edit").toggleName("Lire", "Editer");
    if (edited) {
      top.css("width","auto");
      doc.removeClass("edit");
	  top.find("textArea").remove();
	  top.find(".delete").remove();
    } else {
      doc.addClass("edit");
      top.css("width",doc.first().outerWidth()+"px");
	  if(version != "original") {
		top.prepend('<span class="button delete"></span>');
		top.find(".delete").on("click", clickDeleteVersion);
	  }
    }
	setEditState(edited, top, "title");
	setEditState(edited, top, "work-creator");
	if(version != "original")
	  setEditState(edited, top, "creator");
	setLangEditState(edited, top);
	setEditState(edited, top, "date");
    units.each(function() {
      var unit=$(this);
      if ($(this).isEdited()) {
		var self=this;
		saveUnit.apply($(this).find('textarea'),[function () {
		  $(self).find(".text").html(stringToHtml($(self).find("textarea").val()));
		  unit.find(".split").remove();
		  unit.find(".join").remove();
		  unit.find("textarea").remove();
		  unit.removeClass("edit");
		}]);
      } else {
		$(this).addClass("edit").find("span").remove();
		var textarea=$("<textarea/>");
		textarea.val(htmlToString($(".text",this)));
		$(this).prepend(textarea);
		$(this).find(".text").css("min-height",(getSize(unit)*32)+"px");
		autoSize.apply(textarea);
		if (getVersions().indexOf(version)>0) {
		  createJoins(unit);
		  createSplits(unit);
		}
      }
    });
  }
  
  function setLangEditState(isEdited, container) {
	var target = container.find(".language");
	if(isEdited) {
	  container.find("select").remove();
	  target.removeClass("edit").show();
	} else {
	  var language = $(getLanguageSelector());
	  language.val(target.data("id"));
	  language.addClass("editedMeta").css("width", "50%");
	  language.on("change", function() {
		var id = $("#hexapla").data("id");
		var ref = $(this).closest("th").data("version");
		$.ajax({
		  type: "PUT",
		  url: "work/"+id+"/"+ref,
		  contentType: 'text/plain',
		  data: JSON.stringify({"key":"language", "value": language.val()})
		}).done(function() {
		  var lang = language.find("option:selected");
		  var lang_id = lang.val();
		  var lang_text = lang.text().split("-")[0];
		  target.data("id", lang_id).attr("data-id", lang_id);
		  target.text(lang_text);
		  $("#hexapla").find(".close[data-version='" + ref + "']").find(".language")
		    .attr("data-id", lang_id).data("id", lang_id).attr("title", lang_text).html(lang_id);
		}).fail(function() { alert("fail!"); });
	  });
	  target.addClass("edit");
	  target.before(language).hide();
	}
  }
  
  function setEditState(isEdited, container, name) {
	setEditStateForComponent(isEdited, container, name, "focusout", '<textarea class="editedMeta ' + name + '" />');
  }
  
  function setEditStateForComponent(isEdited, container, name, event, textComponent) {
	var target = container.find("." + name);
	if(isEdited) {
	  target.removeClass("edit").show();
	} else {
	  target.addClass("edit");
	  var component=$(textComponent);
	  component.attr("placeholder", name);
	  component.on(event, function() {
		if(component.hasClass("dirty")) {
		  var id = $("#hexapla").data("id");
		  var ref = $(this).closest("th").data("version");
		  $.ajax({
		    type: "PUT",
		    url: "work/"+id+"/"+ref,
		    contentType: 'text/plain',
		    data: JSON.stringify({"key": name, "value": component.val()})
		  }).done(function(data) {
			if(name == "creator") {
			  changeVersion(ref, data);
			}
			component.val(data);
			target.text(data)
			component.removeClass("dirty");
		  }).fail(function() { alert("fail!"); });
		}
	  });
	  component.val($(target).text());
	  target.before(component);
	  target.hide();
	}
  }
  
  function changeVersion(oldVersion, newVersion) {
	$("#hexapla").find("*[data-version='" + oldVersion + "']").attr("data-version", newVersion).data("version", newVersion).find(".creator").html(newVersion);
  }
  
  function toggleAddVersion() {
	$("#addPanel").slideToggle(200);
  }
  
  function addVersion() {
	var id = $("#hexapla").data("id");
	var ref = $("#addPanel").find("input[type='text']").val();
	if(ref != "") {
	  $.ajax({
		type: "PUT",
		url: "work/"+id+"/"+ref,
		contentType: 'text/plain',
		data: JSON.stringify({"key": "creator", "value": ref})
	  }).done(function() {
		window.location.href = id + "?edit=" + ref;
	  }).fail(function() { alert("fail!"); });
	}
	return false;
  }
  
  function getLanguageSelector() {
	return '<select>'+ '<option value="﻿aa">Afar - Afaraf - Afar</option>'
	+ '<option value="ab">Abkhaze - Аҧсуа - Abkhazian</option>'
	+ '<option value="ae">Avestique - Avesta - Avestan</option>'
	+ '<option value="af">Afrikaans - Afrikaans - Afrikaans</option>'
	+ '<option value="ak">Akan - Akan - Akan</option>'
	+ '<option value="am">Amharique - አማርኛ - Amharic</option>'
	+ '<option value="an">Aragonais - Aragonés - Aragonese</option>'
	+ '<option value="ar">Arabe - ‫العربية - Arabic</option>'
	+ '<option value="as">Assamais - অসমীয়া - Assamese</option>'
	+ '<option value="av">Avar - авар мацӀ - Avaric</option>'
	+ '<option value="ay">Aymara - Aymar aru - Aymara</option>'
	+ '<option value="az">Azéri - Azərbaycan dili - Azerbaijani</option>'
	+ '<option value="ba">Bachkir - башҡорт теле - Bashkir</option>'
	+ '<option value="be">Biélorusse - Беларуская - Belarusian</option>'
	+ '<option value="bg">Bulgare - български език - Bulgarian</option>'
	+ '<option value="bh">Bihari - भोजपुरी - Bihari</option>'
	+ '<option value="bi">Bichelamar - Bislama - Bislama</option>'
	+ '<option value="bm">Bambara - Bamanankan - Bambara</option>'
	+ '<option value="bn">Bengalî - বাংলা - Bengali</option>'
	+ '<option value="bo">Tibétain - བོད་ཡིག - Tibetan</option>'
	+ '<option value="br">Breton - Brezhoneg - Breton</option>'
	+ '<option value="bs">Bosnien - Bosanski jezik - Bosnian</option>'
	+ '<option value="ca">Catalan - Català - Catalan</option>'
	+ '<option value="ce">Tchétchène - нохчийн мотт - Chechen</option>'
	+ '<option value="ch">Chamorro - Chamoru - Chamorro</option>'
	+ '<option value="co">Corse - Corsu - Corsican</option>'
	+ '<option value="cr">Cri - ᓀᐦᐃᔭᐍᐏᐣ - Cree</option>'
	+ '<option value="cs">Tchèque - Česky - Czech</option>'
	+ '<option value="cu">Vieux slave - Словѣньскъ - Old Church Slavonic</option>'
	+ '<option value="cv">Tchouvache - чӑваш чӗлхи - Chuvash</option>'
	+ '<option value="cy">Gallois - Cymraeg - Welsh</option>'
	+ '<option value="da">Danois - Dansk - Danish</option>'
	+ '<option value="de">Allemand - Deutsch - German</option>'
	+ '<option value="dv">Dhivehi - ‫ދިވެހި - Divehi</option>'
	+ '<option value="dz">Dzongkha - རྫོང་ཁ - Dzongkha</option>'
	+ '<option value="ee">Ewe - Ɛʋɛgbɛ - Ewe</option>'
	+ '<option value="el">Grec moderne - Ελληνικά - Greek</option>'
	+ '<option value="en">Anglais - English - English</option>'
	+ '<option value="eo">Espéranto - Esperanto - Esperanto</option>'
	+ '<option value="es">Espagnol - "Español; castellano" - Spanish</option>'
	+ '<option value="et">Estonien - Eesti keel - Estonian</option>'
	+ '<option value="eu">Basque - Euskara - Basque</option>'
	+ '<option value="fa">Persan - ‫فارسی - Persian</option>'
	+ '<option value="ff">Peul - Fulfulde - Fulah</option>'
	+ '<option value="fi">Finnois - Suomen kieli - Finnish</option>'
	+ '<option value="fj">Fidjien - Vosa Vakaviti - Fijian</option>'
	+ '<option value="fo">Féringien - Føroyskt - Faroese</option>'
	+ '<option value="fr">Français - French</option>'
	+ '<option value="fy">Frison - Frysk - Western Frisian</option>'
	+ '<option value="ga">Irlandais - Gaeilge - Irish</option>'
	+ '<option value="gd">Écossais - Gàidhlig - Scottish Gaelic</option>'
	+ '<option value="gl">Galicien - Galego - Galician</option>'
	+ '<option value="gn">Guarani - Avañe\'ẽ - Guarani</option>'
	+ '<option value="gu">Gujarâtî - ગુજરાતી - Gujarati</option>'
	+ '<option value="gv">Mannois - Ghaelg - Manx</option>'
	+ '<option value="ha">Haoussa - ‫هَوُسَ - Hausa</option>'
	+ '<option value="he">Hébreu - ‫עברית - Hebrew</option>'
	+ '<option value="hi">Hindî - "हिन्दी ; हिंदी" - Hindi</option>'
	+ '<option value="ho">Hiri motu - Hiri Motu - Hiri Motu</option>'
	+ '<option value="hr">Croate - Hrvatski - Croatian</option>'
	+ '<option value="ht">Créole haïtien - Kreyòl ayisyen - Haitian</option>'
	+ '<option value="hu">Hongrois - magyar - Hungarian</option>'
	+ '<option value="hy">Arménien - Հայերեն - Armenian</option>'
	+ '<option value="hz">Herero - Otjiherero - Herero</option>'
	+ '<option value="ia">Interlingua - Interlingua - Interlingua</option>'
	+ '<option value="id">Indonésien - Bahasa Indonesia - Indonesian</option>'
	+ '<option value="ie">Occidental - Interlingue - Interlingue</option>'
	+ '<option value="ig">Igbo - Igbo - Igbo</option>'
	+ '<option value="ii">Yi - ꆇꉙ - Sichuan Yi</option>'
	+ '<option value="ik">Inupiaq - Iñupiaq - Inupiaq</option>'
	+ '<option value="io">Ido - Ido - Ido</option>'
	+ '<option value="is">Islandais - Íslenska - Icelandic</option>'
	+ '<option value="it">Italien - Italiano - Italian</option>'
	+ '<option value="iu">Inuktitut - ᐃᓄᒃᑎᑐᑦ - Inuktitut</option>'
	+ '<option value="ja">Japonais - 日本語 (にほんご) - Japanese</option>'
	+ '<option value="jv">Javanais - Basa Jawa - Javanese</option>'
	+ '<option value="ka">Géorgien - ქართული - Georgian</option>'
	+ '<option value="kg">Kikongo - KiKongo - Kongo</option>'
	+ '<option value="ki">Kikuyu - Gĩkũyũ - Kikuyu</option>'
	+ '<option value="kj">Kuanyama - Kuanyama - Kwanyama</option>'
	+ '<option value="kk">Kazakh - Қазақ тілі - Kazakh</option>'
	+ '<option value="kl">Kalaallisut - Kalaallisut - Kalaallisut</option>'
	+ '<option value="km">Khmer - ភាសាខ្មែរ - Khmer</option>'
	+ '<option value="kn">Kannara - ಕನ್ನಡ - Kannada</option>'
	+ '<option value="ko">Coréen - 한국어 (韓國語) - Korean</option>'
	+ '<option value="kr">Kanouri - Kanuri - Kanuri</option>'
	+ '<option value="ks">Kashmiri - "कश्मीरी ; كشميري" - Kashmiri</option>'
	+ '<option value="ku">Kurde - كوردی - Kurdish</option>'
	+ '<option value="kv">Komi - коми кыв - Komi</option>'
	+ '<option value="kw">Cornique - Kernewek - Cornish</option>'
	+ '<option value="ky">Kirghiz - кыргыз тили - Kirghiz</option>'
	+ '<option value="la">Latin - "Latine ; lingua latina" - Latin</option>'
	+ '<option value="lb">Luxembourgeois - Lëtzebuergesch - Luxembourgish</option>'
	+ '<option value="lg">Ganda - Luganda - Ganda</option>'
	+ '<option value="li">Limbourgeois - Limburgs - Limburgish</option>'
	+ '<option value="ln">Lingala - Lingála - Lingala</option>'
	+ '<option value="lo">Lao - ພາສາລາວ - Lao</option>'
	+ '<option value="lt">Lituanien - Lietuvių kalba - Lithuanian</option>'
	+ '<option value="lu">Luba-katanga - kiluba - Luba-Katanga</option>'
	+ '<option value="lv">Letton - Latviešu valoda - Latvian</option>'
	+ '<option value="mg">Malgache - Fiteny malagasy - Malagasy</option>'
	+ '<option value="mh">Marshallais - Kajin M̧ajeļ - Marshallese</option>'
	+ '<option value="mi">Māori de Nouvelle-Zélande - Te reo Māori - Māori</option>'
	+ '<option value="mk">Macédonien - македонски јазик - Macedonian</option>'
	+ '<option value="ml">Malayalam - മലയാളം - Malayalam</option>'
	+ '<option value="mn">Mongol - Монгол - Mongolian</option>'
	+ '<option value="mo">Moldave - лимба молдовеняскэ - Moldavian</option>'
	+ '<option value="mr">Marâthî - मराठी - Marathi</option>'
	+ '<option value="ms">Malais - بهاس ملايو - Malay</option>'
	+ '<option value="mt">Maltais - Malti - Maltese</option>'
	+ '<option value="my">Birman - ဗမာစာ - Burmese</option>'
	+ '<option value="na">Nauruan - Ekakairũ Naoero - Nauru</option>'
	+ '<option value="nb">Norvégien Bokmål - Norsk bokmål - Norwegian Bokmål</option>'
	+ '<option value="nd">Ndébélé du Nord - isiNdebele - North Ndebele</option>'
	+ '<option value="ne">Népalais - नेपाली - Nepali</option>'
	+ '<option value="ng">Ndonga - Owambo - Ndonga</option>'
	+ '<option value="nl">Néerlandais - Nederlands - Dutch</option>'
	+ '<option value="nn">Norvégien Nynorsk - Norsk nynorsk - Norwegian Nynorsk</option>'
	+ '<option value="no">Norvégien - Norsk - Norwegian</option>'
	+ '<option value="nr">Ndébélé du Sud - Ndébélé - South Ndebele</option>'
	+ '<option value="nv">Navajo - "Diné bizaad ; Dinékʼehǰí" - Navajo</option>'
	+ '<option value="ny">Chichewa - "ChiCheŵa ; chinyanja" - Chichewa</option>'
	+ '<option value="oc">Occitan - Occitan - Occitan</option>'
	+ '<option value="oj">Ojibwé - ᐊᓂᔑᓈᐯᒧᐎᓐ - Ojibwa</option>'
	+ '<option value="om">Oromo - Afaan Oromoo - Oromo</option>'
	+ '<option value="or">Oriya - ଓଡ଼ିଆ - Oriya</option>'
	+ '<option value="os">Ossète - Ирон æвзаг - Ossetian</option>'
	+ '<option value="pa">Panjâbî - "ਪੰਜਾਬੀ ; پنجابی" - Panjabi</option>'
	+ '<option value="pi">Pâli - पािऴ - Pāli</option>'
	+ '<option value="pl">Polonais - Polski - Polish</option>'
	+ '<option value="ps">Pachto - ‫پښتو - Pashto</option>'
	+ '<option value="pt">Portugais - Português - Portuguese</option>'
	+ '<option value="qu">Quechua - "Runa Simi ; Kichwa" - Quechua</option>'
	+ '<option value="rm">Romanche - Rumantsch grischun - Romansh</option>'
	+ '<option value="rn">Kirundi - kiRundi - Kirundi</option>'
	+ '<option value="ro">Roumain - Română - Romanian</option>'
	+ '<option value="ru">Russe - русский язык - Russian</option>'
	+ '<option value="rw">Kinyarwanda - Kinyarwanda - Kinyarwanda</option>'
	+ '<option value="sa">Sanskrit - संस्कृतम् - Sanskrit</option>'
	+ '<option value="sc">Sarde - sardu - Sardinian</option>'
	+ '<option value="sd">Sindhi - "सिन्धी ; ‫سنڌي، سندھی" - Sindhi</option>'
	+ '<option value="se">Same du Nord - Davvisámegiella - Northern Sami</option>'
	+ '<option value="sg">Sango - Yângâ tî sängö - Sango</option>'
	+ '<option value="si">Cingalais - සිංහල - Sinhalese</option>'
	+ '<option value="sk">Slovaque - Slovenčina - Slovak</option>'
	+ '<option value="sl">Slovène - Slovenščina - Slovene</option>'
	+ '<option value="sm">Samoan - Gagana fa\'a Samoa - Samoan</option>'
	+ '<option value="sn">Shona - chiShona - Shona</option>'
	+ '<option value="so">Somali - Soomaaliga - Somali</option>'
	+ '<option value="sq">Albanais - Shqip - Albanian</option>'
	+ '<option value="sr">Serbe - српски језик - Serbian</option>'
	+ '<option value="ss">Siswati - SiSwati - Swati</option>'
	+ '<option value="st">Sotho du Sud - seSotho - Sotho</option>'
	+ '<option value="su">Soundanais - Basa Sunda - Sundanese</option>'
	+ '<option value="sv">Suédois - Svenska - Swedish</option>'
	+ '<option value="sw">Swahili - Kiswahili - Swahili</option>'
	+ '<option value="ta">Tamoul - தமிழ் - Tamil</option>'
	+ '<option value="te">Télougou - తెలుగు - Telugu</option>'
	+ '<option value="tg">Tadjik - "тоҷикӣ ; toğikī ; ‫تاجیکی" - Tajik</option>'
	+ '<option value="th">Thaï - ไทย - Thai</option>'
	+ '<option value="ti">Tigrinya - ትግርኛ - Tigrinya</option>'
	+ '<option value="tk">Turkmène - "Türkmen ; Түркмен" - Turkmen</option>'
	+ '<option value="tl">Tagalog - Tagalog - Tagalog</option>'
	+ '<option value="tn">Tswana - seTswana - Tswana</option>'
	+ '<option value="to">Tongien - faka Tonga - Tonga</option>'
	+ '<option value="tr">Turc - Türkçe - Turkish</option>'
	+ '<option value="ts">Tsonga - xiTsonga - Tsonga</option>'
	+ '<option value="tt">Tatar - "татарча ; tatarça ; ‫تاتارچا" - Tatar</option>'
	+ '<option value="tw">Twi - Twi - Twi</option>'
	+ '<option value="ty">Tahitien - Reo Mā`ohi - Tahitian</option>'
	+ '<option value="ug">Ouïghour - "Uyƣurqə ; ‫ئۇيغۇرچ" - Uighur</option>'
	+ '<option value="uk">Ukrainien - українська мова - Ukrainian</option>'
	+ '<option value="ur">Ourdou - ‫اردو - Urdu</option>'
	+ '<option value="uz">Ouzbek - Ўзбек - Uzbek</option>'
	+ '<option value="ve">Venda - tshiVenḓa - Venda</option>'
	+ '<option value="vi">Vietnamien - Tiếng Việt - Viêt Namese</option>'
	+ '<option value="vo">Volapük - Volapük - Volapük</option>'
	+ '<option value="wa">Wallon - Walon - Walloon</option>'
	+ '<option value="wo">Wolof - Wollof - Wolof</option>'
	+ '<option value="xh">Xhosa - isiXhosa - Xhosa</option>'
	+ '<option value="yi">Yiddish - ‫ייִדיש - Yiddish</option>'
	+ '<option value="yo">Yoruba - Yorùbá - Yoruba</option>'
	+ '<option value="za">Zhuang - Saɯ cueŋƅ - Zhuang</option>'
	+ '<option value="zh">Chinois - 中文, 汉语, 漢語 - Chinese</option>'
	+ '<option value="zu">Zoulou - isiZulu - Zulu</option>'
	+ '</select>';
  }
  
  function clickDeleteVersion() {
	var ref = $(this).closest("th").data("version");
	if(confirm('Supprimer la traduction "' + ref + '" ?')) {
	  deleteVersion(ref);
	}
  }
  
  function deleteVersion(version) {
	var id = $("#hexapla").data("id");
	$.ajax({
	  type: "PUT",
	  url: "work/"+id+"/"+version,
	  contentType: 'text/plain',
	  data: JSON.stringify({"key": "delete"})
	}).done(function() {
	  window.location.reload(true);
	}).fail(function() { alert("fail!"); });
  }
  
  function openEditedVersions() {
	var version = $("#hexapla").find(".edited").last();
	var ref = version.closest("th").data("version");
	find(ref).show();
    findPleat(ref).hide();
    find($(".unit.edit").getVersion("td.open")).find("input.edit").each(toggleEdit);
    positionSplits();
	version.find(".edit").click();
	version.removeClass("edited");
  }

  function getEndLine (units,index) {
    var nextIndex=index+1;
    var lastLine=0;
    if (nextIndex<units.length) {
      var nextUnit=units.eq(nextIndex);
      lastLine=nextUnit.getReference().line - 1 ;
    } else {
      lastLine=$("#hexapla").data("lines") - 1;
    }
    return lastLine;
    
  }
 
  function createJoin(unit1,unit2) {
      var p=($(unit2).offset().top-$(unit1).offset().top-$(unit1).outerHeight()+32)/(-2);
      var join=$("<span/>").addClass("join").attr("title","merge with previous").css("top",p+"px");
      unit2.prepend(join);
  }


  function createJoins(unit) {
    unit.find(".join").remove();
    var version=unit.getVersion("td.open");
    var units=findUnits(version);
    var currIndex=units.index(unit);
    if (currIndex>0) {
      var prevUnit=units.eq(currIndex-1);
      createJoin(prevUnit,unit);
    }
  }
  function createSplits(unit) {
    unit.find(".split").remove();
    var reference=unit.getReference();
    var version=reference.version;
    var currLine=reference.line;
    var units=findUnits(version);
    var currIndex=units.index(unit);
    var size=getSize(unit);
    var lastLine=currLine+size-1;
    var maxLines=$("#hexapla").data("lines");
    var currPos=unit.position();
    if (currLine<lastLine && currLine<maxLines) {
      for (var i=currLine+1; i<=lastLine; ++i) {
	var split=$("<span/>").addClass("split").attr("title","split line "+i).data("line",i);
	unit.append(split);
      }
      positionSplits();
    }
  }

  function unedit() {
    var self=this;
    saveUnit.apply(this,[function() {
       var unit=$(self).closest(".unit");
       unit.html(stringToHtml($(self).val())).removeClass("edit");
    }]);
  }

  function saveUnit(callback) {
    var self=this;
    if ($(this).hasClass("dirty")) {
      $(this).prop("disabled",true);
      var content=$(this).closest(".unit").find("textarea").val();
      editOnServer(content, $(this).getReference()).done(function(message,result) {
		if (result == "success") {
		  $(self).removeClass("dirty"); 
		  $(self).prop("disabled",false);
			if (callback && typeof(callback) == "function") {
			  callback();
			}
		} else {
		  alert(result+":"+message);
		}
      });
    } else {
      if (callback && typeof(callback) == "function") {
		callback();
      }
    } 
  }

  function getPreviousUnit(unit) {
    var version=unit.getVersion("td.open");
    var units=findUnits(version);
    return $(units.eq(units.index(unit)-1));
  }

  var editOnServer = function(content, reference) {
    var id=$("#hexapla").data("id");
    return $.ajax({
      type: "PUT",
      url: "version/"+id+"?"+ $.param(reference),
      contentType: "text/plain",
      data: content
    });
  }

  $(document).ready(function() {

    $("#hexapla").on("click", ".button.hide", function() {
      //if ($("thead.header th.pleat.open:visible").length > 1) {
        toggleShow($(this).getVersion("th.open"));
      //}
    });

    $("#hexapla").on("click", ".button.show", function() {
      toggleShow($(this).getVersion("th.close"));
    });

    $("#hexapla").on("click", ".button.edit-license", function() {
      window.location=getPrefix()+"/license/"+$("#hexapla").data("id")+'/'+$(this).getVersion("th");
    });

    $("input.edit").on("click",toggleEdit);

    $("tr").on("select mouseup keyup",".unit", function (e) {
      //requires jquery.selection plugin
      var txt=$.selection();
      if (txt) {
	$("form.concordance #query").val(txt);
	var language=$(this).getLanguage();
	$("form.concordance #language").val(language);
      }
    });

    $("tr").on("click", ".join", function(e) {
      e.stopPropagation();
      var unit=$(this).closest(".unit");
      var version=unit.getVersion("td.open");
      var units=findUnits(version);
      var previousUnit=units.eq(units.index(unit)-1);
      if (previousUnit) {
	editOnServer("null", $(this).closest(".unit").getReference())
	  .done(function() {
            var previousContent=previousUnit.find("textarea").val();
            var thisContent=unit.find("textarea").val();
            previousUnit.find("textarea").val(previousContent+"\n"+thisContent);
            var thisLine=unit.getLine();
            var prevLine=previousUnit.getLine();
            var size=getSize(unit);
            var newSpan=thisLine-prevLine+size;
            previousUnit.closest("td").attr("rowspan",newSpan);
            previousUnit.find(".text").css("min-height",(newSpan*32)+"px");
            unit.closest("td").remove();
            createJoins(previousUnit);
            createSplits(previousUnit);
        });
      }
    });

    $.fn.setSize = function (size) {
      this.closest("td").attr("rowspan",size).find(".text").css("min-height",size*32+"px");
    }

    $("tr").on("click", ".split", function(e) {
      e.stopPropagation();
      var unit=$(this).closest(".unit");
      var line=$(this).data("line");
      var version=unit.data("version");
      editOnServer("", {
        version:version,
        line: line
      }).done(function() {
        var size=getSize(unit);
        var initialLine=unit.getLine();
        var newUnit=$("<div/>").append("<textarea>");
        var text=$("<div>").addClass("text");
        newUnit.append(text);
        autoSize.apply($("textarea",newUnit));
        newUnit.addClass("unit edit").attr("data-version",version);
        $(this).remove();
        var newTd=$("<td>").addClass("pleat open").attr("data-version",version).append(newUnit);
        newUnit.setSize(size-(line-initialLine));
        unit.setSize(line-initialLine);
        var versions=getVersions();
        var versionIndex=versions.indexOf(version);
        if (versionIndex==0) {
	  $("tr[data-line="+line+"]").prepend(newTd);
        } else {
	  var ok=false;
	  $("tr[data-line="+line+"] .unit").each(function() {
            var currVersion=$(this).data("version");
	    if (versions.indexOf(currVersion) > versions.indexOf(version)) {
	      $(this).closest("td").before(newTd);
	      ok=true;
	      return false;
	    }
	    if (versions.indexOf($(this).data("version")) +1 == versions.length) {
	      $(this).closest("td").before(newTd);
	    }
	  });
          if (!ok) {
	    $("tr[data-line="+line+"]").append(newTd);
          }
        }
        createJoins(unit);
        createSplits(unit);
        createJoins(newUnit);
        createSplits(newUnit);
        $(".tosplit").removeClass("tosplit");
      });
    });

    $("#hexapla").on('change input cut paste','textarea',modified);

    $("tr").on("focusout", ".unit.edit textarea", saveUnit);
	
	$(".top").on("click", ".addVersion", toggleAddVersion);
	
	$("#addPanel").on("submit", addVersion);
    
    var versions=getVersions();
    const N = versions.length;
    for (var i = N-1; i>=0; i--) {
      addPleat(versions[i]);
    }
    for (var i = 2; i<N; i++) {
      toggleShow(versions[i]);
    }
	
	openEditedVersions();

  });


