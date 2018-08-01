/*
 * jQuery XYTipsWindow Plus @requires jQuery v1.3.2
 * Dual licensed under the MIT and GPL licenses.
 *
 * Copyright (c) xinyour (http://www.xinyour.com/)
 *
 * Autor: Await
 * webSite: http://leotheme.cn/
 * Date: 星期四 2011年05月15日
 * Version: 2.8.0
 **********************************************************************
 * @example
 * $("#example").XYTipsWindow();
 **********************************************************************
 * XYTipsWindow o参数可配置项：
 *		    ___title : 窗口标题文字;
 *	  	    ___boxID : 弹出层ID(默认随机);
 *	 	  ___content : 内容(可选内容为){ text | id | img | swf | url | iframe};
 *	 	    ___width : 窗口宽度(默认宽度为300px);
 *	 	   ___height : 窗口离度(默认高度为200px);
 *	   ___titleClass : 窗口标题样式名称;
 *	 	  ___closeID : 关闭窗口ID;
 *	    ___triggerID : 相对于这个ID定位;[暂时取消此功能]
 *	   ___boxBdColor : 弹出层外层边框颜色(默认值:#E9F3FD);
 *   ___boxBdOpacity : 弹出层外层边框透明度(默认值:1,不透明);
 * ___boxWrapBdColor : 弹出层内部边框颜色(默认值:#A6C9E1);
 *  ___windowBgColor : 遮罩层背景颜色(默认值:#000000);
 *___windowBgOpacity : 遮罩层背景透明度(默认值:0.5);
 *		     ___time : 自动关闭等待时间;(单位毫秒);
 *		     ___drag : 拖动手柄ID[当指定___triggerID的时候禁止拖动];
 * ___dragBoxOpacity : 设置窗口拖动时窗口透明度(默认值:1,不透明);
 *	    ___showTitle : 是否显示标题(布尔值 默认为true);
 *	    ___showBoxbg : 是否显示弹出层背景(布尔值 默认为true);
 *		   ___showbg : 是否显示遮罩层(布尔值 默认为false);
 *	  	   ___button : 数组，要显示按钮的文字;
 *		 ___callback : 回调函数，默认返回所选按钮显示的文 ;
 *		  ___offsets : 设定弹出层位置,默认居中;内置固定位置浮动:left-top(左上角);right-top(右上角);left-bottom(左下角);right-bottom(右下角);middle-top(居中置顶);middle-bottom(居中置低);left-middle(靠左居中);right-middle(靠右居中);
 *		      ___fns : 弹出窗口后执行的函数;
 **********************************************************************/
;(function(){
	$.XYTipsWindow=function(o){
		defaults = $.extend({
			___title:"Hello World",
			___boxID:boxID(10),
			___content:"text:内容",
			___width: "300",
			___height: "200",
			___titleClass: "boxTitle",
			___closeID:"",
			___triggerID:"",
			___boxBdColor:"#E9F3FD",
			___boxBdOpacity:"1",
			___boxWrapBdColor:"#A6C9E1",
			___windowBgColor:"#000000",
			___windowBgOpacity:"0.5",
			___time:"",
			___drag:"",
			___dragBoxOpacity:"1",
			___showTitle:true,
			___showBoxbg:true,
			___showbg:false,
			___offsets:"",
			___button:"",
			___callback:function(){},
			___fns:function(){}
		},o);
		$.XYTipsWindow.init(defaults);
	};
	var BOXID,isIE6 = !-[1,] && !window.XMLHttpRequest;
	var $XYTipsWindowarr = new Array();
	var boxID = function (n){
		var Str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for (var i = 0, r =""; i < n; i++){
			r += Str.charAt(Math.floor(Math.random() * 62));
		};
		return r;
	};
	$.extend($.XYTipsWindow,{
		//初始化
		init: function (o){
			BOXID = o;
			if ($("#"+o.___boxID).length>0){
				alert("对不起，创建弹出层失败！窗口“"+o.___boxID+"”已存在！");
				return false;
			};
			var $box = $("#"+o.___boxID);
			$.XYTipsWindow.showBox(o);
			$(".___closeBox",$box).die().live("click",function(){
				$.XYTipsWindow.removeBox();
			}).css({zIndex:"870618"});
			if(o.___closeID != ""){
				$("#"+o.___closeID,$box).die().live("click",function(){
					$.XYTipsWindow.removeBox();
				});
			};
			if(o.___time != "") {
				setTimeout($.XYTipsWindow.removeBox,o.___time);
			};
			if(o.___showbg != "" && o.___showbg == true){
				var $boxBgDom = "<div id=\"XYTipsWindowBg\" style=\"position:absolute;background:"+o.___windowBgColor+";filter:alpha(opacity=0);opacity:0;width:100%;left:0;top:0;z-index:870618\"><iframe src=\"about:blank\" style=\"width=100%;height:"+$(document).height()+"px;filter:alpha(opacity=0);opacity:0;scrolling=no;z-index:870610\"></iframe></div>";
				$($boxBgDom).appendTo("body").animate({opacity:o.___windowBgOpacity},200);
			};
			if(o.___drag != "") {
				$.XYTipsWindow.dragBox(o);
			};
			if(o.___fns != "" && $.isFunction(o.___fns)){
				o.___fns.call(this);
			};
			$.XYTipsWindow.contentBox(o);
			if (o.___button!=""){
				$.XYTipsWindow.ask(o);
			};
			$.XYTipsWindow.keyDown(o);
			$.XYTipsWindow.setBoxzIndex(o);
			if(o.___showbg != true){
				$("#"+o.___boxID).addClass("shadow");
			};
			$("#"+o.___boxID).die().live("mouseenter",function(){
				BOXID = o;
			});
		},
		getID: function(){
			return thisID = BOXID.___boxID;
		},
		//构造弹出层
		showBox: function(o) {
			var $titleHeight = o.___showTitle!=true ? 1 : 33,
				$borderHeight = o.___showTitle!=true ? 0 : 10;
				$boxDialogHeight = o.___button!="" ? 45 : 0;
				$boxDialogBorder = $boxDialogHeight == "0" ? "0" : "1";
			var $width = parseInt(o.___width) > 1000 ? 1000 : parseInt(o.___width),
				$height = parseInt(o.___height) > 850 ? 850 : parseInt(o.___height);
			var $boxDom = "<div id=\""+o.___boxID+"\" class=\"XYTipsWindow\">";
				$boxDom += "<div class=\"___boxWrap\">";
				$boxDom += "<div class=\"___boxTitle\"><h3></h3><span class=\"___closeBox\">关闭</span></div>";
				$boxDom += "<div class=\"___boxContent\"></div>";
				$boxDom += "<div class=\"___boxDialog\"></div>";
				$boxDom += "</div>";
				$boxDom += "<div class=\"___boxBd\"></div>";
				$boxDom += "<iframe src=\"about:blank\" style=\"position:absolute;left:0;top:0;filter:alpha(opacity=0);opacity:0;scrolling=no;z-index:10714\"></iframe>";
				$boxDom += "</div>";
				$($boxDom).appendTo("body");
			var $box = $("#"+o.___boxID);
				$box.css({
					position:"relative",
					width:$width+12+"px",
					height:$height+$titleHeight+$borderHeight+$boxDialogHeight+1+"px",
					zIndex: "891208"
				});
			var $iframe = $("iframe",$box);
				$iframe.css({
					width:$width+12+"px",
					height:$height+$titleHeight+$borderHeight+$boxDialogHeight+1+"px"
				});
			var $boxWrap = $(".___boxWrap",$box);
				$boxWrap.css({
					position:"relative",
					top:"5px",
					margin:"0 auto",
					width:$width+2+"px",
					height:$height+$titleHeight+$boxDialogHeight+1+"px",
					overflow:"auto",
					zIndex: "20590"
				});
			var $boxContent = $(".___boxContent",$box);
				$boxContent.css({
					position: "relative",
					width:$width+"px",
					height:$height+"px",
					padding:"0",
					borderWidth:"1px",
					borderStyle:"solid",
					borderColor:o.___boxWrapBdColor,
					overflow: "auto",
					backgroundColor:"#fff"
				});
			var $boxDialog =  $(".___boxDialog",$box);
				$boxDialog.css({
					width:$width+"px",
					height: $boxDialogHeight+"px",
					borderWidth:$boxDialogBorder+"px",
					borderStyle:"solid",
					borderColor:o.___boxWrapBdColor,
					borderTop:"none",
					textAlign:"right"
				});
			var $boxBg = $(".___boxBd",$box);
				$boxBg.css({
					position: "absolute",
					width:$width+12+"px",
					height:$height+$titleHeight+$borderHeight+$boxDialogHeight+1+"px",
					left: "0",
					top: "0",
					opacity: o.___boxBdOpacity,
					background:o.___boxBdColor,
					zIndex: "10715"
				});
			var $title = $(".___boxTitle>h3",$box);
				$title.html(o.___title);
				$title.parent().css({
					position: "relative",
					width:$width+"px",
					borderColor:o.___boxWrapBdColor
				});
			if(o.___titleClass != ""){
				$title.parent().addClass(o.___titleClass);
				$title.parent().find("span").hover(function(){
					$(this).addClass("hover");
				},function(){
					$(this).removeClass("hover");
				});
			};
			if(o.___showTitle!=true){$(".___boxTitle",$box).remove();}
			if(o.___showBoxbg!=true){
				$(".___boxBd",$box).remove();
				$box.css({
					width:$width+2+"px",
					height:$height+$titleHeight+$boxDialogHeight+1+"px"
				});
				$boxWrap.css({left:"0",top:"0"});
			};
			//定位弹出层
			var TOP = -1;
				$.XYTipsWindow.getDomPosition(o);
			var $location = o.___offsets;
			var $wrap = $("<div id=\""+o.___boxID+"parent\"></div>");
			var est = isIE6 ? (o.___triggerID!="" ? 0 : document.documentElement.scrollTop) : "";
			if(o.___offsets=="" || o.___offsets.constructor == String){
				switch($location){
					case("left-top")://左上角
						$location={left:"0px",top:"0px"+est};
						TOP=0;
						break;
					case("left-bottom")://左下角
						$location={left:"0px",bottom:"0px"};
						break;
					case("right-top")://右上角
						$location={right:"0px",top:"0px"+est};
						TOP=0;
						break;
					case("right-bottom")://右下角
						$location={right:"0px",bottom:"0px"};
						break;
					case("middle-top")://居中置顶
						$location={left:"50%",marginLeft:-parseInt($box.width()/2)+"px",top:"0px"+est};
						TOP=0;
						break;
					case("middle-bottom")://居中置低
						$location={left:"50%",marginLeft:-parseInt($box.width()/2)+"px",bottom:"0px"};
						break;
					case("left-middle")://左边居中
						$location={left:"0px",top:"50%"+est,marginTop:-parseInt($box.height()/2)+"px"+est};
						TOP=$getPageSize[1]/2-$box.height()/2;
						break;
					case("right-middle")://右边居中
						$location={right:"0px",top:"50%"+est,marginTop:-parseInt($box.height()/2)+"px"+est};
						TOP=$getPageSize[1]/2-$box.height()/2;
						break;
					default://默认为居中
						$location={left:"50%",marginLeft:-parseInt($box.width()/2)+"px",top:"50%"+est,marginTop:-parseInt($box.height()/2)+"px"+est};
						TOP=$getPageSize[1]/2-$box.height()/2;
						break;
				};
			}else{
				var str=$location.top;
					$location.top = $location.top+est;
				if (typeof(str)!= 'undefined'){
					str=str.replace("px","");
					TOP=str;
				};
			};
			if (o.___triggerID!="") {
				var $offset = $("#"+o.___triggerID).offset();
				var triggerID_W = $("#"+o.___triggerID).outerWidth(),triggerID_H = $("#"+o.___triggerID).outerHeight();
				var triggerID_Left = $offset.left,triggerID_Top = $offset.top;
				var vL = $location.left,vT = $location.top;
				if (typeof(vL)!= 'undefined' || typeof(vT)!= 'undefined' ){
					vL =  parseInt(vL.replace("px",""));
					vT =  parseInt(vT.replace("px",""));
				};
				var ___left = vL >= 0 ? parseInt(vL)+triggerID_Left : parseInt(vL)+triggerID_Left-$getPageSize[2];
				var ___top = vT >= 0 ? parseInt(vT)+triggerID_Top : parseInt(vT)+triggerID_Top-$getPageSize[3];
				$location = {left:___left+"px",top:___top+"px"};
			};
			if (isIE6){
				if (o.___triggerID=="") {
					if (TOP>=0){
						$.XYTipsWindow.addStyle(".ui_fixed_"+o.___boxID+"{width:100%;height:100%;position:absolute;left:expression(documentElement.scrollLeft+documentElement.clientWidth-this.offsetWidth);top:expression(documentElement.scrollTop+"+TOP+")}");
						$wrap=$("<div class=\""+o.___boxID+"IE6FIXED\" id=\""+o.___boxID+"parent\"></div>");
						$box.appendTo($wrap);
						$("body").append($wrap);
						$("."+o.___boxID+"IE6FIXED").css($location).css({
							position:"absolute",
							width:$width+12+"px",
							height:$height+$titleHeight+$borderHeight+$boxDialogHeight+1+"px",
							zIndex: "891208"
						}).addClass("ui_fixed_"+o.___boxID);
					}else{
						$.XYTipsWindow.addStyle(".ui_fixed2_"+o.___boxID+"{width:100%;height:100%;position:absolute;left:expression(documentElement.scrollLeft+documentElement.clientWidth-this.offsetWidth);top:expression(documentElement.scrollTop+documentElement.clientHeight-this.offsetHeight)}");
						$wrap=$("<div class=\""+o.___boxID+"IE6FIXED\"  id=\""+o.___boxID+"parent\"></div>");
						$box.appendTo($wrap);
						$("body").append($wrap);
						$("."+o.___boxID+"IE6FIXED").css($location).css({
							position:"absolute",
							width:$width+12+"px",
							height:$height+$titleHeight+$borderHeight+$boxDialogHeight+1+"px",
							zIndex: "891208"
						}).addClass("ui_fixed2_"+o.___boxID);
					};
					$("body").css("background-attachment","fixed").css("background-image","url(n1othing.txt)");
				}else{
					$wrap.css({
						position:"absolute",
						left:___left+"px",
						top:___top+"px",
						width:$width+12+"px",
						height:$height+$titleHeight+$borderHeight+$boxDialogHeight+1+"px",
						zIndex: "891208"
					});
				};
			}else{
				$wrap.css($location).css({
					position:"fixed",
					width:$width+12+"px",
					height:$height+$titleHeight+$borderHeight+$boxDialogHeight+1+"px",
					zIndex: "891208"
				});
				if (o.___triggerID!="") {$wrap.css({position:"absolute"})};
				$("body").append($wrap);
				$box.appendTo($wrap);
			};
		},
		//装载弹出层内容
		contentBox: function (o) {
			var $box = $("#"+o.___boxID);
			var $width = parseInt(o.___width) > 1000 ? 1000 : parseInt(o.___width),
				$height = parseInt(o.___height) > 850 ? 850 : parseInt(o.___height);
			var $contentID = $(".___boxContent",$box);
				$contentType = o.___content.substring(0,o.___content.indexOf(":"));
				$content = o.___content.substring(o.___content.indexOf(":")+1,o.___content.length);
				$.ajaxSetup({global: false});
			switch($contentType) {
				case "text":
					$contentID.html($content);
				break;
				case "id":
					$("#"+$content).children().appendTo($contentID);
				break;
				case "img":
				$.ajax({
					beforeSend:function() {
						$contentID.html("<p class='boxLoading'>loading...</p>");
					},
					error:function(){
						$contentID.html("<p class='boxError'>加载数据出错...</p>");
					},
					success:function(html){
						$contentID.html("<img src="+$content+" alt='' />");
					}
				});
				break;
				case "swf":
					$.ajax({
						beforeSend:function() {
							$contentID.html("<p class='boxLoading'>loading...</p>");
						},
						error:function(){
							$contentID.html("<p class='boxError'>加载数据出错...</p>");
						},
						success:function(html){
							$contentID.html("<div id='"+o.___boxID+"swf'><h1>Alternative content</h1><p><a href=\"http://www.adobe.com/go/getflashplayer\"><img src=\"http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif\" alt=\"Get Adobe Flash player\" /></a></p></div><script type=\"text/javascript\" src=\"swfobject.js\" ></script><script type=\"text/javascript\">swfobject.embedSWF('"+$content+"', '"+o.___boxID+"swf', '"+$width+"', '"+$height+"', '9.0.0', 'expressInstall.swf');</script>");
							$("#"+o.___boxID+"swf").css({
								position:"absolute",
								left:"0",
								top:"0",
								textAlign:"center"
							});
						}
				});
				break;
				case "url":
				var contentDate=$content.split("?");
				$.ajax({
					beforeSend:function() {
						$contentID.html("<p class='boxLoading'>loading...</p>");
					},
					type:contentDate[0],
					url:contentDate[1],
					data:contentDate[2],
					error:function(){
						$contentID.html("<p class='boxError'><em></em><span>加载数据出错...</span></p>");
					},
					success:function(html){
						$contentID.html(html);
					}
				});
				break;
				case "iframe":
				$contentID.css({overflowY:"hidden"});
				$.ajax({
					beforeSend:function() {
						$contentID.html("<p class='boxLoading'>loading...</p>");
					},
					error:function(){
						$contentID.html("<p class='boxError'>加载数据出错...</p>");
					},
					success:function(html){
						$contentID.html("<iframe src=\""+$content+"\" width=\"100%\" height=\""+parseInt(o.___height)+"px\" scrolling=\"auto\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\"></iframe>");
					}
				});
			};
		},
		//对话模式
		ask:function(o){
			var $box = $("#"+o.___boxID);
				$boxDialog = $(".___boxDialog",$box);
			if (o.___button!=""){
				var map = {}, answerStrings = [];
				if (o.___button instanceof Array) {
					for (var i = 0; i < o.___button.length; i++) {
						map[o.___button[i]] = o.___button[i];
						answerStrings.push(o.___button[i]);
					};
				} else {
					for (var k in o.___button) {
						map[o.___button[k]] = k;
						answerStrings.push(o.___button[k]);
					};
				};
				$boxDialog.html($.map(answerStrings, function(v) {
					return "<input class='dialogBtn' type='button'  value='" + v + "' />";
				}).join(' '));
				$(".dialogBtn", $boxDialog).hover(function(){
					$(this).addClass("hover");
				},function(){
					$(this).removeClass("hover");
				}).click(function() {
					var $this = this;
					if(o.___callback != "" && $.isFunction(o.___callback)) {
						//设置回调函数返回值很简单，就是回调函数名后加括号括住的返回值就可以了。
						o.___callback(map[$this.value]);
					};
					$.XYTipsWindow.removeBox(o);
				});
			};
		},
		//获取要吸附的ID的left和top值并重新计算弹出层left和top值
		getDomPosition: function (o) {
			var $box = $("#"+o.___boxID);
			var	cw=document.documentElement.clientWidth,ch=document.documentElement.clientHeight;
			var sw = $box.outerWidth(),sh = $box.outerHeight();
			var $soffset = $box.offset(),sl = $soffset.left,st = $soffset.top;
			$getPageSize = new Array();
			$getPageSize.push(cw,ch,sw,sh,sl,st);
		},
		//设置窗口的zIndex
		setBoxzIndex: function (o) {
			$XYTipsWindowarr.push(document.getElementById(o.___boxID+"parent"));//存储窗口到数组
			var ___event = "mousedown" || "click";
			var $box = $("#"+o.___boxID+"parent");
			$box.die().live("click",function(){
				for(var i=0; i < $XYTipsWindowarr.length; i++){
					$XYTipsWindowarr[i].style.zIndex = 870618;
				};
				this.style.zIndex = 891208;
			});
		},
		//写入CSS样式
		addStyle : function(s) {
			var T = this.style;
			if(!T){
				T = this.style = document.createElement('style');
				T.setAttribute('type', 'text/css');
				document.getElementsByTagName('head')[0].appendChild(T);
			};
			T.styleSheet && (T.styleSheet.cssText += s) || T.appendChild(document.createTextNode(s));
		},
		//绑定拖拽
		dragBox: function (o){
			var $moveX = 0,$moveY = 0,
				drag = false;
			var $ID = $("#"+o.___boxID);
				$Handle = $("."+o.___drag,$ID);
			$Handle.mouseover(function() {
				if(o.___triggerID!=""){
					$(this).css("cursor","default");
				}else{
					$(this).css("cursor","move");
				};
			});
			$Handle.mousedown(function(e) {
				drag = o.___triggerID!="" ? false : true;
				if (o.___dragBoxOpacity) {
					if (o.___boxBdOpacity!="1") {
						$ID.children("div").css("opacity",o.___dragBoxOpacity);
						$ID.children("div.___boxBd").css("opacity",o.___boxBdOpacity);
					}else{
						$ID.children("div").css("opacity",o.___dragBoxOpacity);
					};
				};
				e = window.event?window.event:e;
				var ___ID = document.getElementById(o.___boxID);
				$moveX = e.clientX - ___ID.offsetLeft;
				$moveY = e.clientY - ___ID.offsetTop;
				$(document).mousemove(function(e) {
					if (drag) {
						e = window.event?window.event:e;
						window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty();
						var ___x = e.clientX - $moveX;
						var ___y = e.clientY - $moveY;
						$(___ID).css({
							left : ___x,
							top : ___y
						});
					};
				});
				$(document).mouseup(function(){
					drag = false;
					if (o.___dragBoxOpacity) {
						if (o.___boxBdOpacity!="1") {
							$ID.children("div").css("opacity","1");
							$ID.children("div.___boxBd").css("opacity",o.___boxBdOpacity);
						}else{
							$ID.children("div").css("opacity","1");
						};
					};
				});
			});
		},
		//关闭弹出层
		removeBox: function (){
			var $box = $("#"+BOXID.___boxID);
			var $boxbg = $("#XYTipsWindowBg");
			if($box != null || $boxbg != null){
				var $contentID = $(".___boxContent",$box);
					$contentType = BOXID.___content.substring(0,BOXID.___content.indexOf(":"));
					$content = BOXID.___content.substring(BOXID.___content.indexOf(":")+1,BOXID.___content.length);
				if ($contentType == "id") {
					$contentID.children().appendTo($("#"+$content));
					$box.parent().removeAttr("style").remove();
					$boxbg.animate({opacity:"0"},500,function(){$(this).remove();});
					$("body").css("background","#fff");
				}else{
					$box.parent().removeAttr("style").remove();
					$boxbg.animate({opacity:"0"},500,function(){$(this).remove();});
					$("body").css("background","#fff");
				};
			};
		},
		//健盘事件，当按Esc的时候关闭弹出层
		keyDown: function(o) {
			document.onkeydown = function(e) {
				e = e || event;
				if(e.keyCode == 27){
					$.XYTipsWindow.removeBox();
				};
			};
		}
	});
})(jQuery);