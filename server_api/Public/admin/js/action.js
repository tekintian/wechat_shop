// JavaScript Document
function left_open(e,id){
  var vt=e;
  var vts=document.getElementById(id);
  if(vts.style.display==''){
	  vt.className='aaa_pts_left_1';
	  vts.style.display='none';
	  }else{
	  vt.className='aaa_pts_left_1 aaa_pts_left_1_2';
	  vts.style.display='';  
	}
}

function new_url(url){
   location='#'
}

function del_id_url(id){
   if(confirm("确认删除吗？"))
   { 
	  window.location.href='?type=del&id='+id
   }
}

function win_open(url,width,height){   height==null ? height=600 : height;
   width==null ?  width=800 : width;
   var myDate=new Date()
   window.open(url,'newwindow'+myDate.getSeconds(),'height='+height+',width='+width);
}

function changeClock(){
	var d = new Date();
	var M=(d.getMonth()+1)>9?d.getMonth().toString():'0' + (d.getMonth()+1); 
	var I=d.getMinutes()>9?d.getMinutes().toString():'0' + d.getMinutes(); 
	var S=d.getSeconds()>9?d.getSeconds().toString():'0' + d.getSeconds();
	document.getElementById("addtime").value = d.getFullYear() + "-" + M + "-" + d.getDate() + " " + d.getHours() + ":" + I + ":" + S;
}
