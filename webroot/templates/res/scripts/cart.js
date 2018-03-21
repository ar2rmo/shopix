function compUpdateCart(resp){
	document.getElementById('cart').innerHTML=resp;
}
function errUpdate(){

}
function compSendCart(resp){
	get('/ajax_cart/info',compUpdateCart,errUpdate);
}
function errSend(){

}
function AddToCart(pid,qty){
	if (qty === undefined) {
		get('/ajax_cart/add?product='+pid,compSendCart,errSend);
	} else {
		get('/ajax_cart/add?product='+pid+'&count='+qty,compSendCart,errSend);
	}
	launchWindow('#dgaddcart');
	return false;
}
function get(href,comp,err){
	var xhr=getXmlHttp();
   	xhr.open("GET", href, true);
   	xhr.setRequestHeader("ajax","1");
    xhr.onreadystatechange = function() {
	    if (xhr.readyState!=4) return
	        if (xhr.status==200) {
	        	comp(xhr.responseText);
	        } else {
	            err(xhr.status)
	        }

    }
    xhr.send(null);
}
function getXmlHttp(){
	  var xmlhttp;
	  try {
	    	xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	  }
	  catch (e) {
	    try {
	      	xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    catch (E) {
	      	xmlhttp = false;
	    }
	  }
	  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
	    xmlhttp = new XMLHttpRequest();
	  }
	  return xmlhttp;
}

function onChangeVariant(){
	var selectedIndex = this.selectedIndex;
	var cls = $(this[selectedIndex]).attr('class');
	if(cls==="forsale"){
		$('#cart_num').show();
		$('#cart_add').show();
	} else {
		$('#cart_num').hide();
		$('#cart_add').hide();
	}
}