// JavaScript Document
function chk_info(chk_id,chk_name){
	var checkbox = $("input[name='attribute[]']:checked").length;
    if (checkbox>4) {
    	alert('最多只能选四个产品属性.');
    	document.getElementById(chk_id).checked=false;
    	return false;
    };
   
	if (document.getElementById(chk_id).checked) {
		var info = '<div style="clear:both;margin-top:5px;" id=div_'+chk_id+'><p>'+chk_name+'：<input class="inp_1 inp_6 " name="guige_name['+chk_id+'][]" value=""/> &nbsp;&nbsp;&nbsp;<input style="margin:5px;width:50px" type="button" onclick="$(this).parent().parent().remove()" style="cursor:pointer" value="删除"><input style="margin:5px;width:80px;" type="button" value="添加'+chk_name+'" style="margin-left:15px;" onclick="guige_append('+chk_id+')"></p></div>';
		$('#guige').append(info);
	}else{
		document.getElementById("div_"+chk_id).remove();
	}
}

function guige_append(chk_id){
	var info = '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="inp_1 inp_6 " name="guige_name['+chk_id+'][]" value=""/> &nbsp;&nbsp;&nbsp; <input style="margin:5px;width:50px" type="button" onclick="$(this).parent().remove()" style="cursor:pointer" value="删除"></p>';
	$("#div_"+chk_id).append(info);
}

function attr_append(){
  document.getElementById('add_attr').style.display='none';
  $('#attrs2').append('<p style="margin-left:70px"><br /><input class="inp_1 inp_6" id="attrs_name" name="attrs_name" value=""/><br /><input style="margin:5px;width:50px" type="button" onclick="addAttrs(this)" style="cursor:pointer" value="完成"> <input style="margin:5px;width:50px" type="button" onclick="clear_button(this)" style="cursor:pointer" value="取消"></p>');
}


function clear_button(obj){
  $(obj).parent().remove();
  document.getElementById('add_attr').style.display='block';
}

