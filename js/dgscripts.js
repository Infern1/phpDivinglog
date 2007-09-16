/*
Copyright (c) 2007, Gurú Sistemas and/or Gustavo Adolfo Arcila Trujillo
All rights reserved.
www.gurusitemas.com

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer
	  in the documentation and/or other materials provided with the distribution.
    * Neither the name of the Gurú Sistemas Intl nor Gustavo Adolfo Arcila Trujillo nor the names of its contributors may be used to
	  endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS  "AS IS"  AND ANY EXPRESS  OR  IMPLIED WARRANTIES, INCLUDING, 
BUT NOT LIMITED TO,  THE IMPLIED WARRANTIES  OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT
SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,  INDIRECT,  INCIDENTAL, SPECIAL, EXEMPLARY,  OR CONSEQUENTIAL 
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF  USE, DATA, OR PROFITS;  OR BUSINESS 
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. 

phpMyDataGrid is Open Source released under the BSD License, and we need your help if you like this script and think to use it 
please make a donation. Our goal is To buy a house for 2 little childrens, so we need to collect USD 20.000 by receiving 4.000 
donations of USD 5 each, (If you think you can do a higher donation, don't think twice, just do it ;-) if you compare, you can 
find commercial versions with less features than phpMyDataGrid with prices higher than USD499.  So, just to make a donation is 
a cheap. 

Please remember donating is one way to show your support, copy and paste in your internet browser the following link to make your donation
https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=tavoarcila%40gmail%2ecom&item_name=phpMyDataGrid%202007&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8

For more info, samples, tips, screenshots, help, contact, forum, please visit phpMyDataGrid site  
http://www.gurusistemas.com/indexdatagrid.php

For contact author: tavoarcila at gmail dot com or info at gurusistemas dot com
*/

var DG_esNS = document.getElementById&&!document.all; var DG_capa = null; var difX = 0; var difY=0;

function DG_centrar(DivName,DivWidth,DivHeight) { 
	if (window.innerHeight){
		posY = window.pageYOffset; posX = window.pageXOffset
	}else if (document.documentElement && document.documentElement.scrollTop){
		posY = document.documentElement.scrollTop; posX = document.documentElement.scrollLeft
	}else if (document.body){
		posY = document.body.scrollTop; posX = document.body.scrollLeft
	}
	PixX = (screen.availWidth - DivWidth)/2; PixY = (screen.availHeight - DivHeight)/2;
	if (DG_esNS){
		document.getElementById(DivName).style.left = parseInt(posX + PixX) +'px'
		document.getElementById(DivName).style.top  = parseInt(posY + PixY) +'px'
	}else{
		document.getElementById(DivName).style.pixelLeft = parseInt(posX + PixX) 
		document.getElementById(DivName).style.pixelTop = parseInt(posY + PixY) 
	}
}

function DG_liberaCapa() { DG_capa = null; }
	
function DG_clickCapa(e, obj) {
	if (!DG_esNS) { DG_capa = event.srcElement.parentElement.style; difX = e.offsetX; difY = e.offsetY; } else { DG_capa = obj.parentNode; difX = e.layerX; difY = e.layerY;}
}
	
function DG_mueveCapa(e) {
	if (DG_capa != null) {
		if (DG_esNS) { 
			DG_capa.style.top = (e.clientY-difY)+"px"; DG_capa.style.left = (e.clientX-difX)+"px";
		} else {
			DG_capa.pixelLeft = event.clientX-difX + document.body.scrollLeft; DG_capa.pixelTop = event.clientY-difY + document.body.scrollTop;
		} return false;
	}
}

function DG_chgpg(pgNumber) { DG_Do ("chgPage", pgNumber); }
function DG_orderby(field,order)	{ DG_Do ("orderby",field, order) }

function DG_addrow(){ DG_Do ("add"); }

function DG_deleterow( intRow, code){
	if (confirm(txtDelete)) DG_Do("delete",intRow, code);
}

function DG_editrow( intRow, code){
	DG_Do("edit",intRow, code);
}

function DG_viewrow( intRow, code){
	DG_Do("view",intRow, code);
}

function DG_showSearchBox(){
	DG_hss("DG_srchDIV","inline");
}

function DG_doSearch(){
	DG_centrar("DG_srchDIV",300,250);
	DG_hss("DG_srchDIV","none");
	DG_Do ("search");
}

function DG_doSave(fields,recno){
	DG_Do ("save",fields,recno);
}

function DG_resetSearch(){
	DG_hss("DG_srchDIV","none");
	DG_hss("rstsearch","none");
	DG_Do ("resetsearch");
}

function DG_setsearch(campo,fldvalue){
	var tmp = new Date();
	var tmpajax = tmp.getYear() + tmp.getMonth() + tmp.getDay() + tmp.getMinutes()
	eval ("results = camposearch.search(/"+campo+":sel/gi)");
	if(results == "-1"){
		DG_sii("searchBox","<input type='text' id='dg_schrstr' class='input' size='35' value='"+fldvalue+"' onkeypress='return DG_bl_enter(event)' /><input type='hidden' id='boxshr' name='boxshr' value='0' />");
	}else{
		DG_hss("imgsearch","none");
		DG_ajaxLoader(scrName, "ajaxDHTMLDiv", "2&fs="+campo+"&tAjax="+Math.random(), "searchBox", "<input type='hidden' id='boxshr' name='boxshr' value='1'>"+txtLoading );
		DG_checkAjaxSearch("boxshr", fldvalue);
	}
}

function DG_checkAjaxSearch(id, fldvalue){
	dato = DG_gvv(id);
	if (dato != '0'){
		DG_svv(id) = dato+1;
		if (dato == 40){
			DG_sii("searchBox") = "<input type='text' id='dg_schrstr' class='input' size='35' value='"+fldvalue+"' onkeypress='return DG_bl_enter(event)' /><input type='hidden' id='boxshr' name='boxshr' value='0' />";
			DG_hss("imgsearch","inline");
		}else{
			setTimeout("DG_checkAjaxSearch('"+id+"','"+fldvalue+"');",1000);
		}
	}else{
		document.getElementById("imgsearch").style.display='inline';
	}
}

function DG_Do(action, p1, p2, p3){
	var theDiv   = "dgDiv"
	var dgvcode  = "";
	var DG_ajaxid   = 1;
	var dgrtd    = '';
	var pgNumber = DG_gvv("dg_r");
	var vOrder   = DG_gvv("dg_order");
	var oe       = DG_gvv("dg_oe");
	var ss       = DG_gvv("dg_ss");
	var schrstr  = DG_gvv("dg_schrstr");
	var selected_checkboxes = selected_checks();
	switch (action){
		case "chgPage": pgNumber = p1; break;
		case "orderby": vOrder = p1; oe = p2; break;
		case "search" : pgNumber=0; if (schrstr=="") DG_hss("rstsearch","none"); else DG_hss("rstsearch","inline"); break;
		case "resetsearch" : pgNumber=0; schrstr=""; DG_svv("dg_schrstr",""); break;
		case "delete": dgrtd=p1; DG_ajaxid=3; dgvcode=p2; break;
		case "add"   : DG_ajaxid=5; theDiv = "addDiv"; break;
		case "edit"  : DG_ajaxid=5; theDiv = "addDiv"; dgrtd = p1; dgvcode=p2; break;
		case "view"  : DG_ajaxid=5; theDiv = "addDiv"; dgrtd = p1; dgvcode="view"+p2; break;
		case "save"  : DG_ajaxid=6; dgrtd = p2; 
			for ( field in p1 ) {
				if (field!='inArray'){
					var fldName = p1[field].split(":");
					if (DG_isdefined(fldName[1]) && fldName[1]=="check"){
						fldValue= DG_gcc(fldName[0]);
					}else{
						fldValue= DG_gvv(fldName[0]);
					}
					dgvcode = dgvcode+"&"+fldName[0]+"="+fldValue;
				}
			}
			DG_sii("addDiv","");
			break;
		default: DG_ajaxid=action; dgrtd = p1; if(DG_isdefined(p2)) theDiv=p2; break;
	}
	parametersAjax = DG_ajaxid+"&dg_r="+pgNumber+"&dg_order="+vOrder+"&dg_oe="+oe+"&dg_ss="+ss+"&dg_schrstr="+schrstr+"&dgrtd="+dgrtd+"&dgvcode="+dgvcode+"&chksel="+selected_checkboxes+params+"&x="+screen.width+"&y="+screen.height+"&dg_tAjax="+Math.random();
	if (DG_isdefined(p3))
		location.href=scrName+"?DG_ajaxid="+parametersAjax;
	else
		DG_ajaxLoader(scrName, "ajaxDHTMLDiv", parametersAjax, theDiv);
}

function DG_hss(objToProcess, status){
	document.getElementById(objToProcess).style.display=status;
}

function DG_gvv(objToProcess){
	return document.getElementById(objToProcess).value;
}

function DG_gcc(objToProcess){
	if (document.getElementById(objToProcess).checked) 
		return 1; 
	else 
		return 0;
}

function DG_svv(objToProcess, strValue){
	document.getElementById(objToProcess).value = strValue;
}

function DG_sii(objToProcess, strValue){
	document.getElementById(objToProcess).innerHTML = strValue;
}

function DG_isdefined(objToTest) {
	if (null == objToTest) {
		return false;
	}
	if ("undefined" == typeof(objToTest) ) {
		return false;
	}
	return true;
}

function DG_ajaxLoader(programa, id, parametros, displayid, text) {
    var DG_esNS = document.getElementById&&!document.all
	if (!DG_isdefined(text)) text = txtLoading;
	if (window.innerHeight){ posY = window.pageYOffset
	}else if (document.documentElement && document.documentElement.scrollTop){
		posY = document.documentElement.scrollTop
	}else if (document.body){ posY = document.body.scrollTop }
	elemento=document.getElementById(id); 
	if (DG_esNS){ elemento.style.top  = parseInt(posY) +'px'; }else{ elemento.style.pixelTop = parseInt(posY); }
	if (DG_esNS){ elemento.style.left  = '0px'; }else{ elemento.style.pixelLeft = 0; }
	elemento.innerHTML="<div class='dgAjax'><img border='0' width='16' height='16' src='"+imgpath+imgAjax+"'>&nbsp;&nbsp;"+text+"&nbsp;&nbsp;&nbsp;<\/div>";
	if (methodForm=='POST'){	
		x = false;
		if (window.XMLHttpRequest) { // Mozilla, Safari,...
			 x = new XMLHttpRequest();
			 if (x.overrideMimeType) {
				x.overrideMimeType('text/html');
			 }
		  } else if (window.ActiveXObject) { // IE
			 try {
				x = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
				try {
				   x = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {}
			 }
		  }
		  if (!x) {
			 alert('Cannot create XMLHTTP instance');
			 return false;
		}
		if (document.getElementById) { var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest(); }
		if (x) {
			x.onreadystatechange = function() {
				if (x.readyState == 4 && x.status == 200) {
					document.getElementById(displayid).innerHTML = x.responseText;
					document.getElementById(id).innerHTML = '';
				}
			}
			if(parametros!='') parameters='DG_ajaxid=' + parametros; else parameters='';
			url=programa;
			x.open('POST', url, true);
			x.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			x.setRequestHeader("Content-length", parameters.length);
			x.setRequestHeader("Connection", "close");
			x.send(parameters);
		}
	}else{
		if (document.getElementById) { var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest(); }
		if (x) { 
			x.onreadystatechange = function() { 
				if (x.readyState == 4 && x.status == 200) { 
					document.getElementById(displayid).innerHTML = x.responseText;
					document.getElementById(id).innerHTML = '';
				}
			}
			if(parametros!='') url=programa+'?DG_ajaxid=' + parametros; else url=programa;
			x.open("GET", url, true); x.send(null);
		}
	}
}

function DG_bl_enter(e) {
	var charCode; charCode = e.keyCode ;
	if (charCode == 13) return false; else return true;
}

function DG_D_edit (objField,dgvcode){
	idField = objField.id;
	arrFldData = idField.split(".-.");
	column = arrFldData[0];
	if (DG_gvv("ajaxDHTMLediting")=='0'){
		var aColumn	= aColumns[column];
		var lenmax	= (aColumn['maxlength']=="0")?'': ' maxlength="'+aColumn['maxlength']+'" ';
		var rows	= (aColumn['maxlength']=="0")?'': ' rows="'+aColumn['maxlength']+'" ';
		var inputtext = objField.innerHTML.toString();
		var new_id = objField.id + "_AjaxDhtml";
		inputtext = inputtext.replace(/\n/,"");
		if(new_id=="_AjaxDhtml"){
			return;
		}else{
			var bRes;
			var txt1 = inputtext.toUpperCase();
			if( txt1.indexOf( "<INPUT" ) < 0 && txt1.indexOf( "<SELECT" ) < 0 && txt1.indexOf( "<TEXTAREA" ) < 0 ){
				var thename= ' id="'+new_id+'" ';
				var savefield='DG_save_field(\''+new_id+'\',\''+escape(inputtext)+'\',\''+dgvcode+'\')';
				var cancelfield='DG_cancel_field(\''+new_id+escape(inputtext)+'\')';
				var keypress, events;
				if (ajaxStyle=="silent"){
					keypress='return DG_silent_enter(event,\''+new_id+'\',\''+ escape(inputtext) +'\',\''+dgvcode+'\')';
					events=' onDblClick="'+savefield+'" onChange="'+savefield+'" onBlur="'+cancelfield+'" onKeyPress="'+keypress+'" ' ;
				}else{	
					keypress=' return DG_bl_enter(event)';
					events=' onKeyPress="'+keypress+'" ';
				}
				var selectall=false;
				var  frm   = '';
				switch (aColumn["datatype"]){ 	
					case "select":
						frm+= '<select class="dgSelectpage" style="width:95%"'+thename+events+'>';
						for ( therow in aColumn["select"] ){
							selected=(inputtext==aColumn["select"][therow]) ? "selected":"";
							frm+= '<option value="' +therow+ '"' +selected+'>'+aColumn["select"][therow]+'<\/option>';
						}
						frm+= '<\/select>';	
					break;
					case "check":
						var checked=(aColumn["select"][1]==inputtext) ? "checked":"";
						frm  +=	'<span class="dgBold">'+aColumn["select"][0]+'/'+aColumn["select"][1]+'<\/span><input class="dgCheck" type="checkbox" '+thename+checked+events+lenmax+'>';
					break;
					case "textarea":
						frm += '<textarea class="input" style="width:95%"'+thename+events+rows+'>'+inputtext+'</textarea>';
					break;
					default:
						frm  +=	'<input '+thename+' type="text" class="input" style="width:95%" value="'+inputtext+'" '+events+lenmax+'>';
						selectall=true;
				}
				if (ajaxStyle!="silent"){
					frm +='<div style="width:95%" align="center"><img src="'+imgpath+imgSave+'" alt="'+txtSave+'" onClick="'+savefield+'" class="dgImgLink">';
					frm +='<img src="'+imgpath+imgCancel+'" alt="'+txtCancel+'" onClick="'+cancelfield+'" class="dgImgLink"></div>';
				}	
				objField.innerHTML=frm;
				document.getElementById(new_id).focus();
				if (aColumn["datatype"]!="check" && aColumn["datatype"]!="select" ){
					document.getElementById(new_id).select();
				}
				DG_svv("ajaxDHTMLediting",1);
			}
		} 
	}
}

function DG_silent_enter(e,new_id,oldtext,dgvcode) {
	var charCode;
	charCode = e.keyCode ;
	if (charCode == 13) DG_save_field(new_id,oldtext,dgvcode); 
	if (charCode == 27) DG_cancel_field(new_id+oldtext);
	return true;
}

function DG_cancel_field(idfield){
	var mydummy_array=idfield.split("_AjaxDhtml");
	DG_svv("ajaxDHTMLediting",0);
	DG_sii(mydummy_array[0],unescape(mydummy_array[1]));
}

function DG_save_field(idfield, oldtext, dgvcode){
	arrFldData = idfield.split(".-.");
	column = arrFldData[0];
	txt = document.getElementById(idfield);
	var texto = txt.value;
	var dbvalue= txt.value.toString();
	if (txt.type.indexOf('select')==0) texto=aColumns[column]["select"][texto];
	if (txt.type.indexOf('checkbox')==0){ 
		dbvalue= (txt.checked==false) ? '0':'1';
		texto= (txt.checked==false) ? aColumns[column]["select"][0]:aColumns[column]["select"][1];
	}
	var mydummy_array=idfield.split("_AjaxDhtml");
	var txt = document.getElementById(mydummy_array[0]);
	if (DG_trim(texto)==""){ texto="&nbsp;";}
	txt.innerHTML = texto;
	mask=aColumns[column]["mask"];
	DG_svv("ajaxDHTMLediting",0);
	if (dbvalue == unescape(oldtext)) return;
	txt.style.color= dgAjaxChanged;
	DG_ajaxLoader(scrName, "ajaxDHTMLDiv", "4&dgrtd="+idfield+"&nt="+dbvalue+"&dgvcode="+dgvcode+params+"&tAjax="+Math.random(), "ajaxDHTMLDiv", txtSaving);
	if (decimalPoint==".") sepMiles = "\\,"; else sepMiles = ".";
	if (thereisCalc){
		for (actualitem in aColumns){ 
			strLeft = aColumns[actualitem]["mask"];
			if (strLeft.substring(0,5)=='calc:'){
				var parts=strLeft.split(":");
				var expresionToCalc=parts[1];
				var expresion = parts[1];
				var ShowCalc = false;
				expresion = expresion.replace(/\+/g," "); expresion = expresion.replace(/-/g," ");
				expresion = expresion.replace(/\//g," "); expresion = expresion.replace(/\*/g," ");
				expresion = expresion.replace(/\(/g," "); expresion = expresion.replace(/\)/g," ");
				expresionToCalc = expresionToCalc.replace(/\+/g," + "); expresionToCalc = expresionToCalc.replace(/-/g," - ");
				expresionToCalc = expresionToCalc.replace(/\//g," / "); expresionToCalc = expresionToCalc.replace(/\*/g," * ");
				expresionToCalc = expresionToCalc.replace(/\(/g," ( "); expresionToCalc = expresionToCalc.replace(/\)/g," ) ");
				var varExpresion = expresion.split(" ")
				var indice = idfield.split(".-.");
				indice[1] = indice[1].replace(/_AjaxDhtml/gi,'');
				for(var ind=0; ind<varExpresion.length; ind++){
					var expresion = varExpresion[ind]
					if (column == expresion) ShowCalc = true;
					expresion = expresion.replace(/ /g,"");
					if (expresion!=''){
						var puede= 0
						for(var ni in aColumns){
							if(aColumns[ni]["strfieldName"] == expresion) puede=1
						}
						if (puede==1){
							var expresiones = document.getElementById(expresion+".-."+indice[1]).innerHTML;
							if (decimalPoint=='.'){
								eval ("expresiones = expresiones.replace(/\,/g,'');");
								expresiones = expresiones.replace(/\./g,'.');
							}else{
								expresiones = expresiones.replace(/\./g,'');
								eval ("expresiones = expresiones.replace(/\,/g,'.');");
							}
							expresiones = expresiones.replace(/ /g,"");
							if (expresiones == "" || expresiones == " ") expresiones = 0;
							expresion=" "+expresion+" ";
							expresionToCalc=expresionToCalc.replace(expresion, expresiones);
						}
					}
				}
				if (ShowCalc){
					dato = eval(expresionToCalc);
					switch (decimals){
						case 1 : vr=10; break;
						case 3 : vr=1000; break;
						case 4 : vr=10000; break;
						default: vr=100; break;
					}
					dato=Math.round(dato* vr) / vr; 
					document.getElementById(aColumns[actualitem]["strfieldName"]+".-."+indice[1]).style.color = dgAjaxChanged;
					document.getElementById(aColumns[actualitem]["strfieldName"]+".-."+indice[1]).innerHTML = dato.toString();
				}
			}
		}
	}
}

function DG_setCheckboxes(the_form, do_check) {
	var elts      = (typeof(document.forms[the_form].elements['chksel[]']) != 'undefined')
	? document.forms[the_form].elements['chksel[]']
	: (typeof(document.forms[the_form].elements['chksel[]']) != 'undefined')
	? document.forms[the_form].elements['chksel[]']
	: document.forms[the_form].elements['chksel[]'];
	var elts_cnt  = (typeof(elts.length) != 'undefined')
	? elts.length
	: 0;
	if (elts_cnt) {
		for (var i = 0; i < elts_cnt; i++) {
			if ( elts[i].checked)
			elts[i].checked = false;
			else
			elts[i].checked = true;
		} // end for
	} else {
		elts.checked        = do_check;
	} // end if... else
	return true;
} // end of the 'setCheckboxes()' function


function DG_ltrim( value ) { var re = /\s*((\S+\s*)*)/; return value.replace(re, "$1"); }
function DG_rtrim( value ) { var re = /((\s*\S+)*)\s*/; return value.replace(re, "$1"); }

function DG_trim( value ) { return DG_ltrim(DG_rtrim(value));}
document.onmousemove = DG_mueveCapa;
document.onmouseup = DG_liberaCapa;