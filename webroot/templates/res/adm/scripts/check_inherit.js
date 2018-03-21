Array.prototype.equals = function (array) {
	//http://stackoverflow.com/questions/7837456/comparing-two-arrays-in-javascript
    // if the other array is a falsy value, return
    if (!array)
        return false;

    // compare lengths - can save a lot of time 
    if (this.length != array.length)
        return false;

    for (var i = 0, l=this.length; i < l; i++) {
        // Check if we have nested arrays
        if (this[i] instanceof Array && array[i] instanceof Array) {
            // recurse into the nested arrays
            if (!this[i].equals(array[i]))
                return false;       
        }           
        else if (this[i] != array[i]) { 
            // Warning - two different object instances will never be equal: {x:20} != {x:20}
            return false;   
        }           
    }       
    return true;
}   

function check_inherit(_prefix,_obj){
	var me = {};
	me.prefix=_prefix;
	me.editBegin=function(){};
	me.editParentSet=function(){};
	me.editRestore=function(){};
	var fields={};
	me.fields=fields;
	for(var o in _obj){
		var obj={};
		var name=o;
		obj.edit = $(_obj[o].edit)[0];
		obj.check = $(_obj[o].check)[0];
		if(obj.edit&&obj.check){
			obj.edit.getValue=function(){	
				if ($(this).attr('type') &&$(this).attr('type')=="checkbox"){
					return this.checked==true?'on':'off';

				}else{
					return $(this).val();
				}
			};
			obj.check.getValue=function(){
				var pVal=$(this).attr(me.prefix+"-prnt-value");
				
				if(pVal.trim()[0]=="["){
					return $.parseJSON(pVal);
				}else{
					return pVal;
				}
			}
			var cmpr=function(_co){
				var v1=this.getValue();
				var v2=_co.getValue();
				

				if(v1 instanceof Array && v2 instanceof Array){
					v1.sort();v2.sort();
					return (v1.equals(v2));
				}
				else if(v1==v2){
					return true
				}
				return false;
			}
			obj.check.compare=cmpr;
			obj.edit.compare=cmpr;
			obj.check.mEdit=obj.edit;
			obj.edit.mCheck=obj.check;
			$(obj.edit).on('input',function(){
				this.mCheck.checked=true;
				me.editBegin.call(this);
				/*
				if(this.compare(this.mCheck)){
					//nothing
				}else{
					this.mCheck.checked=false;
				}
				*/
			})
			$(obj.edit).on('change',function(){
				this.mCheck.checked=true;
				me.editBegin.call(this);
				/*
				if(this.compare(this.mCheck)){
					//nothing
				}else{
					this.mCheck.checked=false;
				}
				*/
			})			
			$(obj.check).on('change',function(){
				if(this.checked==true){
					if(this.mEdit.myValue!=undefined){
						if ($(this.mEdit).attr('type')=="checkbox"){
							this.mEdit.checked=this.mEdit.myValue=='on'?true:false;
						}else{
							$(this.mEdit).val(this.mEdit.myValue);
						}
					}
					me.editRestore.call(this.mEdit);
				}else{		
					this.mEdit.myValue=this.mEdit.getValue();
					if ($(this.mEdit).attr('type')=="checkbox"){
						this.mEdit.checked=this.getValue()=='on'?true:false;
					}else{
						$(this.mEdit).val(this.getValue());
					}					
					
					me.editParentSet.call(this.mEdit);
				}
			})
			fields[name]=obj;
		}
	}
	return me;
};