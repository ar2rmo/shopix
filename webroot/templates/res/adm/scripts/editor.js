//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var editmode='text';	//text-html
var ea = new Array;
var floating='none';
var _lang='';
var first_load_flag; // для загрузки файлов

var htmlbuttonsarray = [
	'text','Режим текста','text',
	'Save','Сохранить','save',
	'Bold','Полужирный','b',
	'Italic','Курсив','i',
	'Underline','Подчеркнутый','u',
	'StrikeThrough','Перечеркнутый','s',
	'Superscript','Верхний индекс','sup',
	'Subscript','Нижний индекс','sub',
	'JustifyLeft','По левому краю','l',
	'JustifyCenter','По центру','c',
	'JustifyRight','По правому краю','r',
	'JustifyFull','По ширине','j',
	'InsertOrderedList','Нумерованый список','ol',
	'InsertUnorderedList','Маркерный список','ul',
	'Indent','Увеличить отступ','in',
	'Outdent','Уменьшить отступ','out',
	'CreateLink','Гиперссылка','a',
	'InsertImage','Вставить изображение','img',
	'InsertFile','Загрузить файл','file'
];

var textbuttonsarray = [
	'p','Параграф','p',
	'Bold','Полужирный','b',
	'Italic','Курсив','i',
	'Underline','Подчеркнутый','u',
	'h1','Заголовок 1','h1',
	'h2','Заголовок 2','h2',
	'h3','Заголовок 3','h3',
	'ul','ul','ul',
	'li','li','li',
	'ol','ol','ol',
	'br','br','br',
	'Superscript','Верхний индекс','sup',
	'Subscript','Нижний индекс','sub',
	'JustifyLeft','По левому краю','l',
	'JustifyCenter','По центру','c',
	'JustifyRight','По правому краю','r',
	'JustifyFull','По ширине','j',
	'CreateLink','Гиперссылка','a',
	'CreateLink2','Гиперссылка в новом окне','a',
	'InsertImage','Вставить изображение','img',
	'InsertFile','Загрузить файл','file'
];

function mess(text, mode){
	$("#message").remove();
	var buttons;
	if(mode==1){
		buttons="<a class='button_cancel' onclick='$(\"#message\").remove();'>Закрыть сообщение</a>";
	}else if(mode==2){
		buttons="<a class='button_cancel' onclick='$(\"#message\").remove();'>Закрыть сообщение</a>&nbsp;&nbsp;&nbsp;<a class='button_cancel' onclick='window.close();'>Закрыть окно</a>";
	}else if(mode==5){
		buttons="<a class='button_cancel' onclick='$(\"#message\").remove();'>Закрыть сообщение</a>&nbsp;&nbsp;&nbsp;<a class='button_cancel' onclick='$(parent.document.getElementById(\"editor_frame\")).remove();'>Закрыть редактор</a>";
	}else{
		buttons="";
	}
	
	//var h=parseInt(document.body.scrollHeight);
	var msg="<div id='message' class='form'><table class='form'><tr><td style='background-color:#eeeeee; padding:20px;'><table align='center' border='0'><tr><td>"+text+"</td></tr><tr><td align='right' style='padding:10px 0;'>"+buttons+"</td></tr></table></td></tr></table></div>";
	$(msg).appendTo("body");
}

function getCookieData(labelName) {
    var labelLen = labelName.length
    var cookieData = document.cookie
    var cLen = cookieData.length
    var i = 0
    var cEnd
    while ( i < cLen) {
        var j = i+labelLen
        if ( cookieData.substring( i , j ) == labelName) {
            cEnd = cookieData.indexOf( ";" , j )
            if (cEnd == -1) {
                cEnd = cookieData.length
            }
            return unescape(cookieData.substring( j+1 , cEnd ))
		}
		i++
    }
    return ""
}

function getCookieVal (offset) {
    var endstr = document.cookie.indexOf(";", offset);
    if (endstr == -1) endstr = document.cookie.length;
    return unescape(document.cookie.substring(offset, endstr));
}
function get_cookie(name) {
    var arg = name + "=";
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;
    while (i < clen) {
        var j = i + alen;
        if (document.cookie.substring( i , j ) == arg) return getCookieVal(j);
        i = document.cookie.indexOf(" ", 1 ) + 1;
        if (i==0) break;
    }
    return null;
}

function initeditor(lang){
	var text="<html><head><link rel='stylesheet' type='text/css' href='/templates/css.css'><style>body{background-color:#D6EAFF;}#xhid{display:none;}img.fleft{float:left;margin:5px;}img.fright{float:right;margin:5px;}</style></head><body></body></html>";//opener.document.getElementsByTagName("textarea")[opener.tarea].value;

	//editarea=eval('html_'+lang);

	editarea=document.getElementById('html_'+lang).contentWindow;

	editarea.document.designMode='on';
	editarea.document.open();
	editarea.document.write(text);
	editarea.document.close();

	ea[lang]=editarea.document;

}

function showtextbuttons(lang){
	//var buttontable="<table width='10px' border='1' cellpadding='0' cellspacing='0'><tr>";
	var buttontable="";
	for(i=0;i<textbuttonsarray.length;i+=3){
		buttontable+="<button title='"+textbuttonsarray[i+1]+"' onclick='settextstyle(\""+textbuttonsarray[i]+"\",\""+lang+"\");return false;'><img src='/resources/adm/buttons/"+textbuttonsarray[i+2]+".gif'></button>";
	}
	/* buttontable+="<td><input type='text' size='5' id='othertag_"+lang+"' value='p'></td>"+
		"<td><button onclick='settextstyle(\"othertag\",\""+lang+"\")'>insTag</button></td>"; */
	//buttontable+="</tr></table>";
	document.writeln(buttontable);
}

function showhtmlbuttons(lang){
	var buttontable="<table width='10px' border='1' cellpadding='0' cellspacing='0'><tr>";
	for(i=0;i<htmlbuttonsarray.length;i+=3){
		buttontable+="<td><button title='"+htmlbuttonsarray[i+1]+"' onclick='sethtmlstyle(\""+htmlbuttonsarray[i]+"\",\""+lang+"\")'><img src='/resources/adm/buttons/"+htmlbuttonsarray[i+2]+".gif'></button></td>";
	}
	buttontable+="</tr></table>";
	document.writeln(buttontable);
}

function instag(text1,text2,lang){
	if(text1=='link' || text1=='link2' || text1=='img'){
		var href;
		if(!(href=prompt("Введите адрес ссылки:",''))){
			return;
		}
	}

	var tarea=document.getElementById(lang);
		if ((document.selection)){
			tarea.focus();
			text=document.selection.createRange().text;
			if(text1=='link'){
				//alert(1);
				document.selection.createRange().text = '<a href="'+href+'">'+text+'</a>';
			}else if(text1=='link2'){
				//alert(2);
				document.selection.createRange().text = '<a href="'+href+'" target="_blank">'+text+'</a>';
			}else if(text1=='img'){
				//alert(3);
				document.selection.createRange().text = '<img src="'+href+'">';
			}else if(text1=='li'){
				text=text.replace(/\n/g,"</li>\n<li>");
				text="<li>"+text+"</li>";
				document.selection.createRange().text = text;
			}else{
				document.selection.createRange().text = text1+text+text2;
			}
		}else if(tarea.selectionStart != undefined){
			var str = tarea.value;
			var start = tarea.selectionStart;
			var end = tarea.selectionEnd;
			var length = end - start;
			seltext=str.substr(start, length);
			if(text1=='link'){
				tarea.value = str.substr(0, start) + '<a href="'+href+'">' + seltext + '</a>' + str.substr(start + length);
				tarea.selectionStart=end+href.length+15;
			}else if(text1=='link2'){
				tarea.value = str.substr(0, start) + '<a href="'+href+'" target="_blank">' + seltext + '</a>' + str.substr(start + length);
				tarea.selectionStart=end+href.length+31;
			}else if(text1=='img'){
				tarea.value = str.substr(0, start) + '<img src="'+href+'" alt="'+seltext+'" title="'+seltext+'">' + str.substr(start + length);
				tarea.selectionStart=end+href.length+31;
			}else if(text1=='li'){
				//alert(2);
				seltext=seltext.replace(/\n/g,"</li>\n<li>");
				seltext="<li>"+seltext+"</li>";
				var seltextlen=seltext.length;
				tarea.value=str.substr(0,start) + seltext + str.substr(start+length);
				tarea.selectionStart=start+seltextlen;
			}else{
				tarea.value = str.substr(0, start) + text1 + seltext + text2 + str.substr(start + length);
				tarea.selectionStart=end+text1.length+text2.length;
			}
			tarea.selectionEnd=tarea.selectionStart;
		}else{
			tarea.value += ('<a href="'+href+'"></a>');
		}
	tarea.focus();
}

function settextstyle(buttonname, lang){
	_lang=lang;
	switch(buttonname){
		case 'html':
			$("#"+lang+" tr:eq(1) td:eq(0) div:eq(0)").hide();
			$("#"+lang+" tr:eq(1) td:eq(0) div:eq(1)").show();

			var textobj=document.getElementById(lang);
			var htmlobj = document.getElementById('html_'+lang);

			htmlobj.contentWindow.document.body.innerHTML=textobj.value;

			textobj.style.position='absolute';
			textobj.style.visibility='hidden';

			htmlobj.style.height='500px';
			htmlobj.style.width='95%';
			htmlobj.style.position='relative';
			htmlobj.style.visibility='visible';

			editmode='html';
			break;
		case 'p':
			instag('<p>','</p>',lang);
			break;
		case 'Bold':
			instag('<b>','</b>',lang);
			break;
		case 'Italic':
			instag('<i>','</i>',lang);
			break;
		case 'Underline':
			instag('<u>','</u>',lang);
			break;
		case 'StrikeThrough':
			instag('<span style="text-decoration: line-through;">','</span>',lang);
			break;
		case 'Superscript':
			instag('<sup>','</sup>',lang);
			break;
		case 'Subscript':
			instag('<sub>','</sub>',lang);
			break;
		case 'h1':
			instag('<h1>','</h1>',lang);
			break;
		case 'h2':
			instag('<h2>','</h2>',lang);
			break;
		case 'h3':
			instag('<h3>','</h3>',lang);
			break;
		case 'ul':
			instag('<ul>','</ul>',lang);
			break;
		case 'li':
			instag('li','li',lang);
			break;
		case 'ol':
			instag('<ol>','</ol>',lang);
			break;
		case 'br':
			instag('','<br>',lang);
			break;
		case 'JustifyLeft':
			instag('<div align="left">','</div>',lang);
			break;
		case 'JustifyCenter':
			instag('<div align="center">','</div>',lang);
			break;
		case 'JustifyRight':
			instag('<div align="right">','</div>',lang);
			break;
		case 'JustifyFull':
			instag('<div align="justify">','</div>',lang);
			break;
		case 'CreateLink':
			instag('link','',lang);
			break;
		case 'CreateLink2':
			instag('link2','',lang);
			break;
		case 'InsertImage':
			//instag('img','',lang);
			instag('[img]','',lang);
			show_img_panel();
			//instag('<sub>','</sub>',lang);
			break;
		case 'InsertFile':
			instag('[file]','',lang);
			show_file_panel();
			break;
		case 'RemoveFormat':
			//instag('<sub>','</sub>',lang);
			break;
		case 'Save':
			save_fun();
			break;
		case 'othertag':
			var tag=document.getElementById('othertag_'+lang).value;
			instag('<'+tag+'>','</'+tag+'>',lang);
			break;
		case 'preview':
			var textobj=document.getElementById('text_'+lang);
			var htmlobj=document.getElementById('html_'+lang);

			$("#"+lang+" tr:eq(1) td:eq(0) div:eq(0)").hide();

			var cancelpreview="<div id='cancelpreview_"+lang+"'><table width='10px' border='1' cellpadding='0' cellspacing='0'><tr><td><button onclick='canceltextpreview(\""+lang+"\")'>Закрыть просмотр</button></td></tr></table></div>";
			$(cancelpreview).appendTo($("#"+lang+" tr:eq(1) td:eq(0)"));


			var text="<html><head><link rel='stylesheet' type='text/css' href='css.css'></head><body>"+textobj.value+"</body></html>";
			editarea=document.getElementById('html_'+lang).contentWindow;

			editarea.document.designMode='on';
			editarea.document.open();
			editarea.document.write(text);
			editarea.document.close();
			editarea.document.designMode='off';

			textobj.style.visibility='hidden';
			textobj.style.position='absolute';
			htmlobj.style.visibility='visible';
			htmlobj.style.position='relative';
			htmlobj.style.height='500px';
			htmlobj.style.width='95%';

			break;
	}
}

function canceltextpreview(lang){
	$("#cancelpreview_"+lang).remove();
	$("#"+lang+" tr:eq(1) td:eq(0) div:eq(0)").show();

	var textobj=document.getElementById('text_'+lang);
	var htmlobj=document.getElementById('html_'+lang);

	htmlobj.contentWindow.document.designMode='on';
	htmlobj.style.visibility='hidden';
	htmlobj.style.position='absolute';
	htmlobj.style.height='0px';
	htmlobj.style.width='0px';

	textobj.style.visibility='visible';
	textobj.style.position='relative';
}

function save_fun(){
	if(document.getElementById('editor_mode').value=='page'){
		var page_id=document.getElementById('page_id').value;

		var content_ar = new Array();
		var keywords_ar = new Array();
		var description_ar = new Array();
		var blocks = new Array();

		for(i=1;i<=3;i++){
			keywords_ar[i]=document.getElementById('keywords_'+i).value;
			description_ar[i]=document.getElementById('description_'+i).value;
			if(document.getElementById('text_lang_'+i).style.visibility=='hidden'){
				content_ar[i]=document.getElementById('html_lang_'+i).contentWindow.document.body.innerHTML;
			}else{
				content_ar[i]=document.getElementById('text_lang_'+i).value;
			}
		}

		for(i=1;i<=2;i++){
			if(document.getElementById('text_block_'+i).style.visibility=='hidden'){
				blocks[i]=document.getElementById('html_block_'+i).contentWindow.document.body.innerHTML;
			}else{
				blocks[i]=document.getElementById('text_block_'+i).value;
			}
		}

		mess("Сохранение страницы...",1);
		$.post("/modules/editor/controlset.php",
			{mode:'savepage', page_id:page_id,
				keywords_1:keywords_ar[1], keywords_2:keywords_ar[2], keywords_3:keywords_ar[3],
				description_1:description_ar[1], description_2:description_ar[2], description_3:description_ar[3],
				content_1:content_ar[1], content_2:content_ar[2], content_3:content_ar[3],
				block_1:blocks[1], block_2:blocks[2]
			},
			function(data){
				mess(data,5);
			}
		);
	}
}

function sethtmlstyle(buttonname, lang){
	_lang=lang;
	switch(buttonname){
		case 'Save':
			save_fun();

			break;
		case 'text':
			//var textblock=document.getElementById('text_'+lang).parentNode;
			$("#"+lang+" tr:eq(1) td:eq(0) div:eq(1)").hide();
			$("#"+lang+" tr:eq(1) td:eq(0) div:eq(0)").show();

			var textobj=document.getElementById('text_'+lang);
			var htmlobj = document.getElementById('html_'+lang);

			textobj.value=htmlobj.contentWindow.document.body.innerHTML;

			htmlobj.style.position='absolute';
			htmlobj.style.visibility='hidden';
			htmlobj.style.height='0px';
			htmlobj.style.width='0px';


			textobj.style.position='relative';
			textobj.style.visibility='visible';

			editmode='text';
			break;

		case 'CreateLink':
			if(ea[lang].selection){
				linktext=ea[lang].selection.createRange().text;
				seln=ea[lang].selection.createRange();
			}else{
				linktext=ea[lang].getSelection();
			}

			if(linktext==''){
				alert("Необходимо выделить текст для создания ссылки.");
				break;
			}else{
				if(linkhref=prompt("Введите адрес ссылки:",''))
					ea[lang].execCommand('CreateLink',false,linkhref);
			}
			break;
		case 'InsertImage':
			ea[lang].execCommand('InsertImage',false,'xxx');
			show_img_panel();

			break;
		case 'InsertFile':
			ea[lang].execCommand('InsertImage',false,'fff');
			show_file_panel();
			break;
		default:
			ea[lang].execCommand(buttonname,false,null);
	}
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////            Работа с рисунками                ////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function show_img_panel(){
	floating='none';
	var img_panel="<div id='img_panel' class='form'><table class='form' align='center' border='1' cellpadding='0' cellspacing='2' style='margin-top:10px;'>"+
		"<tr><td colspan='2' align='right' height='30px'><a class='button_cancel' onclick='close_img_panel()'>Закрыть</a></td></tr>" +
		"<tr>"+
			"<td height='30px' style='width:190px;'>"+
				"<a href='javascript:void(0);' onclick='add_dir(\"img\")'><img src='/resources/adm/img/add.png' alt='Создать каталог' title='Создать каталог'></a>&nbsp;&nbsp;&nbsp;"+
				"<a href='javascript:void(0);' onclick='remove_dir(\"img\")'><img src='/resources/adm/img/delete.png' alt='Удалить каталог' title='Удалить каталог'></a>&nbsp;&nbsp;&nbsp;"+
				"<a href='javascript:void(0);' onclick='rename_dir()'><img src='/resources/adm/img/edit.png' alt='Переименовать каталог' title='Переименовать каталог'></a>&nbsp;&nbsp;&nbsp;"+
			"</td>"+
			"<td valign='top' style='width:810px;'>"+
				"<div style='position:relative; width:100%;' align='left'>&nbsp;&nbsp;&nbsp;"+
					"<a class='button' onclick='select_img_panel()'><img src='/resources/adm/img/add.png' alt='Загрузить рисунок' title='Загрузить рисунок'></a>&nbsp;&nbsp;&nbsp;"+
					"<a class='button' onclick='remove_file()'><img src='/resources/adm/img/delete.png' alt='Удалить рисунок' title='Удалить рисунок'></a>&nbsp;&nbsp;&nbsp;"+
					"<a class='button' onclick='rename_file_panel()'><img src='/resources/adm/img/edit.png' alt='Переименовать рисунок' title='Переименовать рисунок'></a>&nbsp;&nbsp;&nbsp;"+

					"<div style='position:absolute;top:0px;right:0px;' id='floating_buttons'>"+
						"<img src='/resources/adm/buttons/floatleft.gif' onclick='floating=\"left\"; curfloat(this);' alt='left' title='Выравнивание слева'> "+
						"<img src='/resources/adm/buttons/floatright.gif' onclick='floating=\"right\"; curfloat(this);' alt='right' title='Выравнивание справа'> "+
						"<img src='/resources/adm/buttons/floatnone.gif' onclick='floating=\"none\"; curfloat(this);' alt='none' title='По умолчанию' class='currentfloat'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+
					"</div>"+
				"</div>"+
			"</td>"+
		"</tr>"+
		"<tr>"+
			"<td valign='top' style='height:530px; width:175px;'>"+
				"<div id='dir_list' style='height:100%; width:100%; overflow:scroll;'></div>"+
			"</td>"+
			"<td valign='top' style='height:530px; width:600px;'>"+
				"<div id='files_content' style='height:100%; width:100%; overflow:scroll;'></div>"+
			"</td>"+
		"</tr>"+
		"</table></div>";
		$(img_panel).appendTo("body");

		mess('Чтение каталогов, подождите...',0);
		get_dir_list('img','');
		//read_dir('img','');
}

function curfloat(th){
	$("#floating_buttons img").removeClass("currentfloat");
	$(th).addClass("currentfloat");
}

function close_img_panel(){
	if(document.getElementById('text_'+_lang).style.visibility!='hidden'){
		var tarea=document.getElementById('text_'+_lang);
		tarea.value=tarea.value.replace('[img]','');
	}else{
		imgs=ea[_lang].getElementsByTagName("IMG");
		for(i=0;i<imgs.length;i++){
			if(imgs[i].src.match(/xxx$/)){
				ximg=imgs[i];
				break;
			}
		}
		$(ximg).remove();
	}
	$("#img_panel").remove();
}

function select_img_panel(){
	first_load_flag=1;
	var width=getCookieData('rw');
	var height=getCookieData('rh');
	if(!width){
		width='';
	}
	if(!height){
		height='';
	}

	var dir=$("#dir_list li.cur_dir a").attr("id");
	var select_img_panel="<div id='select_img_panel' class='form'><iframe id='upload_iframe' name='upload_iframe' style='visibility:hidden;' onload='after_load_file(\"img\")'></iframe><table class='form' align='center' style='width:500px; margin-top:0px;'>"+
		"<tr>"+
			"<td>"+
			"</td>"+
			"<td>"+
				"<form id='send_form' method='post' enctype='multipart/form-data' target='upload_iframe' action='/modules/editor/controlset.php' onsubmit='return store_resize()'>"+
					"<input type='hidden' name='mode' value='upload_img'>"+
					"<input type='hidden' name='dir' value='"+dir+"'>"+
					"<table border='0' align='center'>"+
						"<tr><td colspan='2'><br></td></tr>"+
						"<tr><td colspan='2'><input id='file' type='file' name='file'></td></tr>"+
						"<tr><td colspan='2'><label><input type='checkbox' name='allow_resize' value='on' onchange='allow_resize_fun()'> измененить размер</label></td></tr>"+
						"<tr><td>Ширина: </td><td><input type='text' name='img_width' style='width:100px' disabled value='"+width+"'> px</td></tr>"+
						"<tr><td>Высота: </td><td><input type='text' name='img_height' style='width:100px' disabled value='"+height+"'> px</td></tr>"+
						"<tr><td colspan='2'>"+
							"<label><input type='radio' name='proportions' value='on_width' checked disabled> пропорции по ширине</label><br>"+
							"<label><input type='radio' name='proportions' value='on_height' disabled> пропорции по высоте</label><br>"+
							"<label><input type='radio' name='proportions' value='none' disabled> без пропорций</label>"+
						"</td></tr>"+
						"<input type='submit' value='Загрузить' name='send_button'>"+
						"<input type='hidden' value='no' name='replace'>"+
					"</table>"+
				"</form>"+
			"</td>"+
		"</tr>"+
		"<tr><td align='right' colspan='2'><a class='button_cancel' onclick='$(\"#select_img_panel\").remove();'>Закрыть</a></td></tr>"+
		"<tr><td colspan='2'><br></td></tr>"+
		"</table></div>";
		$(select_img_panel).appendTo("body");
}

function store_resize(){
	if(document.getElementById('send_form').elements['img_width'].disabled==false){
		var width=document.getElementById('send_form').elements['img_width'].value;
		var height=document.getElementById('send_form').elements['img_height'].value;

		if(width=='' || height=='' || width.match(/[^0-9]/) || height.match(/[^0-9]/)){
			mess('<b class="error">Неверные параметры!</b>',1);
			return false;
		}

		var ndate = new Date();
		var tenYearFromNow = ndate.getTime() + (365*24*60*60*1000*10);
		ndate.setTime (tenYearFromNow);
		document.cookie="rw="+width+"; expires="+ndate.toGMTString()+"; path=/";
		document.cookie="rh="+height+"; expires="+ndate.toGMTString()+"; path=/";
	}else{
		return true;
	}
}

function allow_resize_fun(){
	if(document.getElementById('send_form').elements['img_width'].disabled){
		document.getElementById('send_form').elements['img_width'].disabled=false;
		document.getElementById('send_form').elements['img_height'].disabled=false;
		document.getElementById('send_form').elements[6].disabled=false;
		document.getElementById('send_form').elements[7].disabled=false;
		document.getElementById('send_form').elements[8].disabled=false;

	}else{
		document.getElementById('send_form').elements['img_width'].disabled=true;
		document.getElementById('send_form').elements['img_height'].disabled=true;
		document.getElementById('send_form').elements[6].disabled=true;
		document.getElementById('send_form').elements[7].disabled=true;
		document.getElementById('send_form').elements[8].disabled=true;

	}
	//alert('as');
}

function after_load_file(param){//param = file || img
	if(first_load_flag){
		first_load_flag=0;
		return false;
	}
	var data=document.getElementById('upload_iframe').contentWindow.document.body.innerHTML;
	if(data.match(/class=.error/i)){
		mess(data,1);
	}else{
		//window.stop();
		$("#dir_list li.cur_dir a").click();
		$("#select_"+param+"_panel").remove();
		//mess(data,1);
	}
}

function replace_file(){
	document.getElementById('send_form').elements['replace'].value='ok';
	document.getElementById('send_form').submit();
}

function remove_file(){
	var file_name=$("#files_content img.cur_file").parent().attr("id");
	if(!file_name){
		mess("Ничего не выбрано!",1);
		return false;
	}
	if(!confirm("Вы уверены что хотите удалить "+file_name+"?")){
		return false;
	}
	mess('Идет удаление...',1);
	$.post("/modules/editor/controlset.php",{mode:'remove_file',file_name:file_name},resfun);
	function resfun(data){
		if(data!=''){
			mess(data,1);
		}else{
			$("#dir_list li.cur_dir a").click();
		}
	}
}

function rename_file_panel(){
	var dir=$("#dir_list li.cur_dir a").attr("id");
	var old_name=$("#files_content img.cur_file").parent().attr("id");

	if(!old_name){
		mess("Ничего не выбрано!",1);
		return false;
	}

	var pattern=new RegExp(dir+"/","i");
	var old_name=old_name.replace(pattern,'');

	var new_file_panel="<div id='new_file_panel' class='form'><style>table.form tr td{padding:5px 20px;}</style><table class='form' align='center' style='margin-top:160px;'>"+
		"<tr><td><br></td></tr>"+
		"<tr><td>Введите новое название файла:</td></tr>"+
		"<tr>"+
			"<td>"+
				"<input type='text' style='width:200px;' id='new_file_name' value='"+old_name+"'>"+
			"</td>"+
		"</tr>"+
		"<tr>"+
			"<td align='right'>"+
				"<a class='save' onclick='save_renamed_file(0)'>Сохранить</a>&nbsp;&nbsp;&nbsp;<a class='button_cancel' onclick='$(\"#new_file_panel\").remove();'>Отмена</a>"+
			"</td>"+
		"</tr>"+
		"<tr><td><br></td></tr>"+
		"</table></div>";
	$(new_file_panel).appendTo("body");
}

function save_renamed_file(r){
	var dir=$("#dir_list li.cur_dir a").attr("id");
	var old_name=$("#files_content img.cur_file").parent().attr("id");
	var new_name=document.getElementById('new_file_name').value;
	new_name=dir+'/'+new_name;
	mess("Переименование...",0);
	// $.post("/modules/editor/controlset.php",{mode:'save_renamed_file',old_name:old_name,new_name:new_name},resfun);
	// function resfun(data){
		// mess(data,1);
	// }

	$.post('/modules/editor/controlset.php',{mode:'save_renamed_dir',old_name:old_name,new_name:new_name,replace:r},resfun);
	function resfun(data){
		if(data.match(/class=.error/i)){
			mess(data,1);
		}else{
			$("#new_file_panel").remove();
			$("#dir_list li.cur_dir a").click();

			//document.getElementById(old_id).id=new_id;
			//mess("<b class='ok'>Переименование успешно завершено.</b>",1);
		}
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////            файлы                ////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function show_file_panel(){
	var file_panel="<div id='file_panel' class='form'><table class='form' align='center' border='1' cellpadding='0' cellspacing='2' style='margin-top:10px;'>"+
		"<tr><td colspan='2' align='right' height='30px'><a class='button_cancel' onclick='close_file_panel()'>Закрыть</a></td></tr>"+
		"<tr>"+
			"<td height='30px' style='width:190px;'>"+
				"<a href='javascript:void(0);' onclick='add_dir(\"file\")'><img src='/resources/adm/img/add.png' alt='Создать каталог' title='Создать каталог'></a>&nbsp;&nbsp;&nbsp;"+
				"<a href='javascript:void(0);' onclick='remove_dir(\"file\")'><img src='/resources/adm/img/delete.png' alt='Удалить каталог' title='Удалить каталог'></a>&nbsp;&nbsp;&nbsp;"+
				"<a href='javascript:void(0);' onclick='rename_dir()'><img src='/resources/adm/img/edit.png' alt='Переименовать каталог' title='Переименовать каталог'></a>&nbsp;&nbsp;&nbsp;"+
			"</td>"+
			"<td valign='top' style='width:810px;'>"+
				"<div style='position:relative; width:100%;' align='left'>&nbsp;&nbsp;&nbsp;"+
					"<a class='button' onclick='select_file_panel()'><img src='/resources/adm/img/add.png' alt='Загрузить файл' title='Загрузить файл'></a>&nbsp;&nbsp;&nbsp;"+
					"<a class='button' onclick='remove_file()'><img src='/resources/adm/img/delete.png' alt='Удалить файл' title='Удалить файл'></a>&nbsp;&nbsp;&nbsp;"+
					"<a class='button' onclick='rename_file_panel()'><img src='/resources/adm/img/edit.png' alt='Переименовать файл' title='Переименовать файл'></a>&nbsp;&nbsp;&nbsp;"+

					"<div style='position:absolute;top:0px;right:0px;' id='floating_buttons'>"+
						//"<img src='/resources/adm/buttons/floatleft.gif' onclick='floating=\"fleft\"; curfloat(this);' alt='left' title='Обтекание слева' class='currentfloat'> "+
						//"<img src='/resources/adm/buttons/floatright.gif' onclick='floating=\"fright\"; curfloat(this);' alt='right' title='Обтекание справа'> "+
						//"<img src='/resources/adm/buttons/floatnone.gif' onclick='floating=\"fnone\"; curfloat(this);' alt='none' title='Без обтекания'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+
					"</div>"+
				"</div>"+
			"</td>"+
		"</tr>"+
		"<tr>"+
			"<td valign='top' style='height:530px; width:175px;'>"+
				"<div id='dir_list' style='height:100%; width:100%; overflow:scroll;'></div>"+
			"</td>"+
			"<td valign='top' style='height:530px; width:600px;'>"+
				"<div id='files_content' style='height:100%; width:100%; overflow:scroll;'></div>"+
			"</td>"+
		"</tr>"+
		"</table></div>";
		$(file_panel).appendTo("body");

		mess('Чтение каталогов, подождите...',0);
		get_dir_list('file','');
		//read_dir('img','');
}

function close_file_panel(){
	if(document.getElementById('text_'+_lang).style.visibility!='hidden'){
		var tarea=document.getElementById('text_'+_lang);
		tarea.value=tarea.value.replace('[file]','');
	}else{
		imgs=ea[_lang].getElementsByTagName("IMG");
		for(i=0;i<imgs.length;i++){
			if(imgs[i].src.match(/fff$/)){
				ximg=imgs[i];
				break;
			}
		}
		$(ximg).remove();
	}
	$("#file_panel").remove();
}

function select_file_panel(){
	first_load_flag=1;

	var dir=$("#dir_list li.cur_dir a").attr("id");
	var select_img_panel="<div id='select_file_panel' class='form'><iframe id='upload_iframe' name='upload_iframe' style='visibility:hidden;' onload='after_load_file(\"file\")'></iframe>"+
		"<table class='form' align='center' style='width:300px; margin-top:0px;'>"+
		"<tr><td colspan='2'><br></td></tr>"+
		"<tr>"+
			"<td>"+
			"</td>"+
			"<td>"+
				"<form id='send_form' method='post' enctype='multipart/form-data' target='upload_iframe' action='/modules/editor/controlset.php'>"+
					"<input type='hidden' name='mode' value='upload_file'>"+
					"<input type='hidden' name='dir' value='"+dir+"'>"+
					"<table border='0' align='center'>"+
						"<tr><td colspan='2'><input id='file' type='file' name='file' style='width:200px;'></td></tr>"+
						"<input type='submit' value='Загрузить' name='send_button'>"+
						"<input type='hidden' value='no' name='replace'>"+
					"</table>"+
				"</form>"+
			"</td>"+
		"</tr>"+
		"<tr><td align='right' colspan='2'><a class='button_cancel' onclick='$(\"#select_file_panel\").remove();'>Закрыть</a></td></tr>"+
		"<tr><td colspan='2'><br></td></tr>"+
		"</table></div>";
		$(select_img_panel).appendTo("body");
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////            работа с каталогами                ////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_dir_list(param,start_dir){//param = dir || file
	$.post('/modules/editor/controlset.php',{mode:'get_dir_list',param:param},resfun);
	function resfun(data){
		$("#dir_list").html(data);
		$("#message").remove();
		$("#dir_list a").each(function(){
			this.onclick=function(){
				$("#dir_list li").removeClass("cur_dir");
				$(this).parent().addClass("cur_dir");
				read_dir(param,this.id);
			}
		});

		if(start_dir==''){
			$("#dir_list a:first").click();
		}else{
			$(document.getElementById(start_dir)).parent().addClass("cur_dir");
		}
	}

}

function read_dir(param,dir){//param = file || img
	param=='file' ? mode='read_file_dir' : mode='read_img_dir';

	mess("Чтение каталога, подождите...",0);
	$.post('/modules/editor/controlset.php',{mode:mode,dir:dir},resfun);
	function resfun(data){
		function ins_cur_file(){
			//alert('ok');
			//return;
			if(param=='img'){
				if(floating=='none'){
					cl='';
					align='';
				}else{
					cl=" class=\""+floating+"\"";

					if(floating=='left'){
						align=" align=\"left\"";
					}else if(floating=='right'){
						align=" align=\"right\"";
					}
				}

				if(document.getElementById('text_'+_lang).style.visibility=='hidden'){
					var text="<img src=\""+this.id+"\""+cl+align+">";
					$("#img_panel").remove();
					imgs=ea[_lang].getElementsByTagName("IMG");
					for(i=0;i<imgs.length;i++){
						if(imgs[i].src.match(/xxx$/)){
							ximg=imgs[i];
							break;
						}
					}
					$(ximg).replaceWith(text);
				}else{
					var text="<img src=\""+this.id+"\""+cl+align+">";
					$("#img_panel").remove();
					tarea=document.getElementById('text_'+_lang);
					tarea.value=tarea.value.replace('[img]',text);
					tarea.focus();
				}
			}else if(param=='file'){
				if(document.getElementById('text_'+_lang).style.visibility=='hidden'){
					var text="<a href=\""+this.id+"\">Скачать</a>";
					$("#file_panel").remove();
					imgs=ea[_lang].getElementsByTagName("IMG");
					for(i=0;i<imgs.length;i++){
						if(imgs[i].src.match(/fff$/)){
							ximg=imgs[i];
							break;
						}
					}
					$(ximg).replaceWith(text);
				}else{
					var text="<a href=\""+this.id+"\">Скачать</a>";
					$("#file_panel").remove();
					tarea=document.getElementById('text_'+_lang);
					tarea.value=tarea.value.replace('[file]',text);
					tarea.focus();
				}
			}
		}

		$("#files_content").html(data);
		$("#message").remove();
		$("#files_content a").click(function(){
			$("#files_content img").removeClass();
			//$("#files_content a").removeClass();
			//$(this).addClass("cur_file");
			$(this).children().addClass("cur_file");
		});
		$("#files_content a").dblclick(ins_cur_file);
	}
}

function add_dir(param){//img || file
	var new_dir_panel="<div id='new_dir' class='form'><style>table.form tr td{padding:5px 20px;}</style><table class='form' align='center' style='margin-top:160px;'>"+
		"<tr><td><br></td></tr>"+
		"<tr><td>Введите название каталога:</td></tr>"+
		"<tr>"+
			"<td>"+
				"<input type='text' style='width:200px;' id='new_dir_name'>"+
			"</td>"+
		"</tr>"+
		"<tr>"+
			"<td align='right'>"+
				"<a class='save' onclick='save_new_dir(\""+param+"\")'>Сохранить</a>&nbsp;&nbsp;&nbsp;<a class='button_cancel' onclick='$(\"#new_dir\").remove();'>Отмена</a>"+
			"</td>"+
		"</tr>"+
		"<tr><td><br></td></tr>"+
		"</table></div>";
	$(new_dir_panel).appendTo("body");
}

function save_new_dir(param){//img || file
	var parent_dir=$("#dir_list li.cur_dir a").attr('id');
	var new_dir=document.getElementById('new_dir_name').value;


	mess("Сохранение...",0);
	$.post('/modules/editor/controlset.php',{mode:'save_new_dir',parent_dir:parent_dir,new_dir:new_dir},resfun);
	function resfun(data){
		if(data.match(/class=.error/i)){
			mess(data,1);
		}else{
			$("#new_dir").remove();
			mess("Чтение списка каталогов...",0);
			get_dir_list(param,parent_dir);
		}
	}
}

function remove_dir(param){//param = file || img
	var dirname=$("li.cur_dir").text();
	if(!confirm("Удалить каталог "+dirname+"?")){
		return false;
	}
	var dir=$("#dir_list li.cur_dir a").attr('id');

	$.post('/modules/editor/controlset.php',{mode:'remove_dir',dir:dir},resfun);
	function resfun(data){
		if(data.match(/class=.error/i)){
			mess(data,1);
		}else{
			mess("Чтение списка каталогов...",0);
			get_dir_list(param,'');
		}
	}
}

function rename_dir(){
	var old_name=$("#dir_list li.cur_dir a").text();
	var new_dir_panel="<div id='new_dir' class='form'><style>table.form tr td{padding:5px 20px;}</style><table class='form' align='center' style='margin-top:160px;'>"+
		"<tr><td><br></td></tr>"+
		"<tr><td>Введите новое название каталога:</td></tr>"+
		"<tr>"+
			"<td>"+
				"<input type='text' style='width:200px;' id='new_dir_name' value='"+old_name+"'>"+
			"</td>"+
		"</tr>"+
		"<tr>"+
			"<td align='right'>"+
				"<a class='save' onclick='save_renamed_dir()'>Сохранить</a>&nbsp;&nbsp;&nbsp;<a class='button_cancel' onclick='$(\"#new_dir\").remove();'>Отмена</a>"+
			"</td>"+
		"</tr>"+
		"<tr><td><br></td></tr>"+
		"</table></div>";
	$(new_dir_panel).appendTo("body");
}

function save_renamed_dir(){

	var old_name=$("#dir_list li.cur_dir a").text();
	var new_name=document.getElementById('new_dir_name').value;

	var old_id=$("#dir_list li.cur_dir a").attr('id');
	var pattern=new RegExp(old_name+"$","i");
	var new_id=old_id.replace(pattern,new_name);

	mess("Сохранение...",0);
	$.post('/modules/editor/controlset.php',{mode:'save_renamed_dir',old_name:old_id,new_name:new_id},resfun);
	function resfun(data){
		if(data.match(/class=.error/i)){
			mess(data,1);
		}else{
			$("#new_dir").remove();
			$("#dir_list li.cur_dir a").text(new_name);
			//mess(new_id,1);
			document.getElementById(old_id).id=new_id;
			mess("<b class='ok'>Переименование успешно завершено.</b>",0);
			var h=setTimeout("$(\"#message\").remove();",500);
		}
	}
}

function close_editor(){
	var editor_frame = parent.document.getElementById('editor_frame');
	$(editor_frame).remove();
}