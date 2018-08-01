$(function () {
	var baseUrl = "http://web.shangyongapp.com/admin/index.php"
	var arryAddress = [];
	var arrySelete = [];
	var arrySaddress = [];
	var arryShowId = [];
	var arryShowText = [];
	var num = [];
	var arryId = [];
	var arryisid = [];
	required();
	addAddress();
	addAddress2();
	recall();
	getSuer();

	/***********************编辑运费模板数据回填*************************/
	var city = $('#king_address').attr('data-city');
	if(city){
		var oCity = eval('('+city+')');
		console.log(oCity);
		for(i in oCity){
			for(j in oCity[i]){
				arryShowId.push(oCity[i][j].id);
				arryShowText.push(oCity[i][j].area);
			}
			creatTableEdit(oCity[i][0]);
		}
		getShowORhide();
	}
	/***********************编辑运费模板数据回填*************************/

	/*模板名称不能为空，不能没有指定配送区域就保存*/
	function required() {
		$('#king_btn').on('click', function() {
			var _val = $('#king_mubanN input').val();
			var _len = $('tbody tr').length;
			if(_val == ''&& _len == 0) {
				$('#king_mubanN p').show();
				$('#king_warm').html('至少要有一个配送区域').fadeIn();
				setTimeout(function() {
					$('#king_warm').fadeOut();
				}, 1000);
				return false;
			} else {
				if(_val == '') {
					$('#king_mubanN p').show();
					return false;
				} else {
					$('#king_mubanN p').hide();
				}
				if(_len == 0) {
					$('#king_warm').html('至少要有一个配送区域').fadeIn();
					setTimeout(function() {
						$('#king_warm').fadeOut();
					}, 1000);
					return false;
				}
			}

			/*****************必须选择了所有的地区**********************/
			var len;
			var _cont;
			var _id = error = '';
			var _len = $('tbody tr').length;
			for(var i=0; i<_len; i++) {
				len = $('tbody tr').eq(i).find('.text-depth').length;
				for(var j=0; j<len; j++) {
					_cont = $('tbody tr').eq(i).find('.text-depth').eq(j).attr('id');
					_id += '&id[]='+_cont;
				}
			}

			var url = baseUrl+"?m=PostTpl&a=getArea"+_id;
			$.ajax({
				url:url,
				dataType:"json",
				async:false,
				success:function(d){
					if(d.data!=''){
						error = '地区必须全部选择';
					}
				}
			});
			if(error){
				$('#king_warm').html(error).fadeIn();
				setTimeout(function() {
					$('#king_warm').fadeOut();
				}, 1000);
				error = '';
				return false;
			}
			/*****************必须选择了所有的地区**********************/
		});
	}
	/*地区选择框的弹出：获取地址数据；显示地址选择框*/
	$('#king_area_check').on('click', function() {
		var len;
		var _cont;
		var _len = $('tbody tr').length;
		for(var i=0; i<_len; i++) {
			len = $('tbody tr').eq(i).find('.text-depth').length;
			for(var j=0; j<len; j++) {
				_cont = $('tbody tr').eq(i).find('.text-depth').eq(j).attr('id');
				arryAddress.push(_cont);
			}
		}
		getData(arryAddress);
		$('#boxWrap').show();
		$('#king_sure').show();
		$('#king_sure2').hide();
		deleteArrayAll(arryAddress);
		return false;
	});
	/*地区选择框的隐藏: 隐藏地址选择框；清除可选择省份和已选择省份的内容*/
	$('#king_cancel').on('click', function() {
		hiddenK();
	});
	function hiddenK() {
		$('#boxWrap').hide();
		$('#allow_address').empty();
		$('#alert_address').empty();
	}
	/*获取数据*/
	function getData(arry) {
		var _len = arry.length;
		var _id = '', url, address, id;
		if(_len == 0) {
			url = baseUrl+"?m=PostTpl&a=getArea";
		} else {
			for(var i=0; i<_len; i++) {
				_id += ('&id[]='+ arry[i]);
				url = baseUrl+"?m=PostTpl&a=getArea"+_id;
			}
		}
		$.ajax({
			type: "get",
			url: url,
			dataType: "json",
			success: function(d) {
				$('#allow_address').empty();
				var len = (d.data).length;
				num.push(len);
				if(len == 0) {
					return false;
				} else {
					for(var i=0; i<len; i++) {
						address = d.data[i].name;
						id = d.data[i].id;
						$('#allow_address').append('<li id="'+id+'" class="king_ie_box" isClick="false">'+address+'</li>');
					}
				}
			}
		});
	}
	/*添加省份：可选省份的选择与取消样式改变，数据存清操作*/
	function addAddress() {
		$('#allow_address').on('click', 'li', function() {
			var isClick = $(this).attr('isClick');
			var id = $(this).attr('id');
			var cont = $(this).text();
			if(isClick == 'false') {
				$(this).attr('isClick', 'ture');
				$(this).addClass('area-editor-selete');
				arrySelete.push(id);
				arrySaddress.push(cont);
			} else {
				$(this).attr('isClick', 'false');
				$(this).removeClass('area-editor-selete');
				deleteOnly(arrySelete, id);
				deleteOnly(arrySaddress, cont);
			}
		});
	}
	/*点击添加按钮：从可选省份中删除选中省份；将选中省份添加到已选择省份中；清空arrySelete，arrySaddress，防止可多次点击添加*/
	function addAddress2() {
		$('#king_add').on('click', function() {
			var len = arrySelete.length;
			for(var i=0; i<len; i++) {
				$('#allow_address li[id="'+arrySelete[i]+'"]').remove();
				$('#alert_address').append('<li id="'+arrySelete[i]+'"><span class="king_word">'+arrySaddress[i]+'</span><span class="remove-btn">×</span></li>');
			}
			deleteArrayAll(arrySelete);
			deleteArrayAll(arrySaddress);
		});
	}
	/*单个删除已选省份：删除已选省份添加回可选省份*/
	function recall() {
		$('#alert_address').on('click', '.remove-btn', function() {
			var id = $(this).parent().attr('id');
			var text = $(this).parent().find('.king_word').text();
			$(this).parent().remove();
			$('#allow_address').append('<li id="'+id+'" class="king_ie_box">'+text+'</li>');
		});
	}
	/*点击确认按钮，在页面中创建表格*/
	function getSuer() {
		$('#king_sure').on('click', function() {
			var len = $('#alert_address li').length;
			var text, id;
			for(var i=0; i<len; i++) {
				id = $('#alert_address li').eq(i).attr('id');
				text = $('#alert_address li').eq(i).find('.king_word').text();
				arryShowId.push(id);
				arryShowText.push(text);
			}
			hiddenK();
			creatTable();
			getShowORhide();
		});
	}
	function creatTable() {
		var len = arryShowId.length;
		var lenn =  arryShowText.length;
		if(len!==0 && lenn!==0) {
			$('tbody').append('<tr isId="'+arryShowId[0]+'"><td><div class="king_address"></div><div class="pull-right"><a href="javascript:void(0);" class="king_redact">编辑</a><a href="javascript:void(0);" class="king_in_delete">删除</a></div><div class="king_clear"></div></td><td><input name="tpl[first_amount]['+arryShowId[0]+']" type="text" value="" class="cost-input firstNum"></td><td><input name="tpl[first_fee]['+arryShowId[0]+']" type="text" value="" class="cost-input secondNum"></td><td><input name="tpl[additional_amount]['+arryShowId[0]+']" type="text" value="" class="cost-input firstNum"></td><td><input name="tpl[additional_fee]['+arryShowId[0]+']" type="text" value="" class="cost-input secondNum"></td></tr>');
			for(var i=0; i<len; i++) {
				if(i == (len-1)) {
					$('tr[isId="'+arryShowId[0]+'"] .king_address').append('<span class="text-depth" id="'+arryShowId[i]+'">'+arryShowText[i]+'</span><input type="hidden" name="tpl[city]['+arryShowId[0]+']['+arryShowId[i]+']" value="'+arryShowText[i]+'" />');
				} else {
					$('tr[isId="'+arryShowId[0]+'"] .king_address').append('<span class="text-depth" id="'+arryShowId[i]+'">'+arryShowText[i]+'</span>、<input type="hidden" name="tpl[city]['+arryShowId[0]+']['+arryShowId[i]+']" value="'+arryShowText[i]+'" />');

				}
			}
		}
		deleteArrayAll(arryShowId);
		deleteArrayAll(arryShowText);
	}

	//编辑模板的时候回填数据
	function creatTableEdit(o) {
		var len = arryShowId.length;
		var lenn =  arryShowText.length;
		if(len!==0 && lenn!==0) {
			$('tbody').append('<tr isId="'+arryShowId[0]+'"><td><div class="king_address"></div><div class="pull-right"><a href="javascript:void(0);" class="king_redact">编辑</a><a href="javascript:void(0);" class="king_in_delete">删除</a></div><div class="king_clear"></div></td><td><input name="tpl[first_amount]['+arryShowId[0]+']" value="'+o.first_amount+'" type="text" value="" class="cost-input firstNum"></td><td><input name="tpl[first_fee]['+arryShowId[0]+']" value="'+o.first_fee+'" type="text" value="" class="cost-input secondNum"></td><td><input name="tpl[additional_amount]['+arryShowId[0]+']" value="'+o.additional_amount+'" type="text" value="" class="cost-input firstNum"></td><td><input name="tpl[additional_fee]['+arryShowId[0]+']" value="'+o.additional_fee+'" type="text" value="" class="cost-input secondNum"></td></tr>');
			for(var i=0; i<len; i++) {
				if(i == (len-1)) {
					$('tr[isId="'+arryShowId[0]+'"] .king_address').append('<span class="text-depth" id="'+arryShowId[i]+'">'+arryShowText[i]+'</span><input type="hidden" name="tpl[city]['+arryShowId[0]+']['+arryShowId[i]+']" value="'+arryShowText[i]+'" />');
				} else {
					$('tr[isId="'+arryShowId[0]+'"] .king_address').append('<span class="text-depth" id="'+arryShowId[i]+'">'+arryShowText[i]+'</span>、<input type="hidden" name="tpl[city]['+arryShowId[0]+']['+arryShowId[i]+']" value="'+arryShowText[i]+'" />');

				}
			}
		}
		deleteArrayAll(arryShowId);
		deleteArrayAll(arryShowText);
	}
	/*删除功能*/
	$('tbody').on('click', '.king_in_delete', function() {
		var deleteId = $(this).parents('tr').attr('isid');
		$('#king_delete_warm').show();
		sureDelete(deleteId);
	});
	/*编辑功能*/
	$('tbody').on('click', '.king_redact', function() {
		var lenAll = $('tbody tr .text-depth').length;
		var len = $(this).parents('tr').find('.text-depth').length;
		var isid = $(this).parents('tr').attr('isid');
		for(var i=0; i<lenAll; i++) {
			var id = $('tbody tr .text-depth').eq(i).attr('id');
			arryAddress.push(id);
		}
		for(var i=0; i<len; i++) {
			var idd = $(this).parents('tr').find('.text-depth').eq(i).attr('id');
			var text = $(this).parents('tr').find('.text-depth').eq(i).text();
			arrySelete.push(idd);
			arrySaddress.push(text);
		}
		var arrylen = arrySelete.length;
		for(var i=0; i<arrylen; i++) {
			$('#alert_address').append('<li id="'+arrySelete[i]+'"><span class="king_word">'+arrySaddress[i]+'</span><span class="remove-btn">×</span></li>');
		}
		getData(arryAddress);
		$('#boxWrap').show();
		$('#king_sure').hide();
		$('#king_sure2').show();
		getnumber(isid);
		deleteArrayAll(arryAddress);
		deleteArrayAll(arrySelete);
		deleteArrayAll(arrySaddress);
	});

	function getnumber(isid) {
		arryisid.push(isid);
	}
	$('#king_sure2').on('click', function() {
		var len = $('#alert_address li').length;
		var text, id;
		for(var i=0; i<len; i++) {
			id = $('#alert_address li').eq(i).attr('id');
			text = $('#alert_address li').eq(i).find('.king_word').text();
			arryShowId.push(id);
			arryShowText.push(text);
		}
		hiddenK();
		clickRemove(arryisid[0]);
		getShowORhide();
	});
	/*编辑后清除重写已选地址*/
	function clickRemove(isid) {
		$('tbody tr[isid="'+isid+'"]').find('.king_address').empty();
		var lenn = arryShowId.length;
		if(lenn == 0) {
			$('tbody tr[isid="'+isid+'"]').remove();
		}
		for(var i=0; i<lenn; i++) {
			if(i == (lenn-1)) {
				$('tbody tr[isid="'+isid+'"]').find('.king_address').append('<span class="text-depth" id="'+arryShowId[i]+'">'+arryShowText[i]+'</span><input type="hidden" name="tpl[city]['+arryShowId[0]+']['+arryShowId[i]+']" value="'+arryShowText[i]+'" />');
			} else {
				$('tbody tr[isid="'+isid+'"]').find('.king_address').append('<span class="text-depth" id="'+arryShowId[i]+'">'+arryShowText[i]+'</span>、<input type="hidden" name="tpl[city]['+arryShowId[0]+']['+arryShowId[i]+']" value="'+arryShowText[i]+'" />');
			}
		}
		$('tbody tr[isid="'+isid+'"]').attr('isid', arryShowId[0]);
		deleteArrayAll(arryShowId);
		deleteArrayAll(arryShowText);
		deleteArrayAll(arryisid);
	}
	/*删除确认框===确认*/
	function sureDelete(deleteId) {
		arryId.push(deleteId);
	}
	$('#king_sure_delete').on('click', function() {
			$(this).parents('#king_delete_warm').hide();
			$('tr[isid="'+arryId[0]+'"]').remove();
			getShowORhide();
			deleteArrayAll(arryId);
	});
	/*删除确认框===取消*/
	$('#king_sure_cancel').on('click', function() {
		$(this).parents('#king_delete_warm').hide();
	});
	/*判断tfoot的显示与隐藏*/
	function getShowORhide() {
		var lenIdNum = $('tbody tr .text-depth').length;
		if(lenIdNum == num[0]) {
			$('tfoot').hide();
		} else {
			$('tfoot').show();
		}
	}
	/*判断输入框输入的正确性*/
	$('tbody').on('blur', '.firstNum', function() {
		var reg = /^[0-9]+$/;
		var val = $(this).val();
		if(!reg.test(val)) {
			$(this).val(0);
		}
	});
	$('tbody').on('blur', '.secondNum', function() {
		var reg = /^[0-9]+$/;
		var reg2 = /^\d*(.\d{2})?$/;
		var val = $(this).val();
		if(!reg.test(val)&&!reg2.test(val)) {
			$(this).val('0.00');
		} else {
			if(reg.test(val)) {
				$(this).val(val+'.00');
			} else {

			}
		}
	});

	 /* 实现功能: 清空数组的操作*/
	function deleteArrayAll(array) {
		var len = array.length;
		array.splice(0, len);
	}
	/*删除数组中的一个数字*/
	function deleteOnly(array, cont) {
		var len = array.length;
		for(var i=0; i<len; i++) {
			if(array[i] == cont) {
				array.splice(i, 1);
			}
		}
	}
});