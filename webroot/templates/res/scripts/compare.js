// #CMP=

function compUpdateCmp(resp){
	document.getElementById('cmp').innerHTML=resp;
}
function errUpdateCmp(){

}
function compSendCmp(resp){
	get('/ajax_compare/info',compUpdateCmp,errUpdateCmp);
}
function errSendCmp(){

}
function AddToCmp(pid){
	get('/ajax_compare/add?product='+pid,compSendCmp,errSendCmp);
	launchWindow('#dgaddcompare');
	return false;
}
function DelFromCmp(pid){
	get('/ajax_compare/del?product='+pid,compSendCmp,errSendCmp);
	return false;
}