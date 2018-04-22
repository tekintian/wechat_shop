$(function() {
	var baseUrl = "http://web.shangyongapp.com/admin/index.php";
	var newArry = [{}, {}, {}];
	for(var i=0; i<3; i++) {
		newArry[i].value = [];
	}
	var arryThis = [];
	var arry = [];
	var arryTime = 0;

	/************************Grass数据回填**************************/
	var skuHead = eval($('#king-add-specs').attr('data-sku'));
	var skuContent = eval($('#king-table').attr('data-sku'));
	if(skuContent){
		// console.log(skuHead);
		// console.log(skuContent);
		newArry = skuHead;
		for (var i = 0; i < 3; i++) {
			if(!newArry[i]){
				newArry[i] = {"name":"","value":[]};
			}
		};
		creatTableNew();
		$('#king-add-table').show();
		$('.king-listM').each(function(k,v){
			arry.push($(v).html());
		});

		$('.king-invents-num,.code,.grass_price').attr('readonly', 'readonly').css('background','#eee');
	}
	/************************Grass数据回填**************************/
	/*判断总库存是否输入正确*/
	$('.king-invents-num').on('blur', function() {
		var _val = $(this).val();
		var reg = /^[0-9]+$/;
		if(!reg.test(_val)) {
			$('.king-warm-red').show();
		} else {
			$('.king-warm-red').hide();
		}
	});
	/*判断商家编码是否输入正确*/
	$('#king-write-coding input').on('blur', function() {
		var _val = $(this).val();
		var reg = /^[0-9a-zA-Z]+$/;
		if(!reg.test(_val)) {
			$('.king-warm-red1').show();
		} else {
			$('.king-warm-red1').hide();
		}
	});
	/*实现点击添加规格项目：最多添加三项*/
	var times = 0;
	$('.king-but').on('click', function() {
		times = $('.king_add_list[ischeck=check]').length;
		$('.king_add_list').eq(times).show().attr('isCheck', 'check');
		var cheLen = $('.king_add_list[isCheck]').length;
		times++;
		if(times == 3) {
			times = 0;
		}
		if(cheLen == 3) {
			$('.king-but').parent().hide();
		}

		if(skuContent){
			$('.input-mini').val('');
			skuContent = {};
		}
	});
	/*实现鼠标移入移除删除图标显示与隐藏*/
	$('.king_add_list').on('mouseover', function() {
		$(this).find('.king-click-close').show();
	});
	$('.king_add_list').on('mouseout', function() {
		$(this).find('.king-click-close').hide();
	});
	/*实现点击删除图标删除规格项目*/
	var time = 0;
	$('.king-click-close').on('click', function() {
		$(this).parents('.king_add_list').hide().removeAttr('isCheck');
		$(this).siblings('.king-checkbox').remove();
		$(this).siblings('.king-nowrite').val('');
		$(this).parent().siblings('.king-add-title').hide();
		var cheLen = $('.king_add_list[isCheck]').length;
		time++;
		if(time == 3) {
			time = 0;
		}
		if(cheLen < 3) {
			$('.king-but').parent().show();
		}
		$(this).parent().next().empty();
		var parentId = $(this).parents('.king_add_list').attr('firstId');
		deleteArrayAll(newArry[parseInt(parentId)-1].value);
		deleteArrayAll(arry);
		creatTableNew();
		var len1 = newArry[0].value.length;
		var len2 = newArry[1].value.length;
		var len3 = newArry[2].value.length;
		if(len1==0&&len2==0&&len3==0) {
			deleteAllAfter();
		}
		$(this).parents('.king-click-list').siblings('.king-add-listM').empty();

		if(skuContent){
			//编辑状态下, 删除组,需要清空价格,库存等值
			$('.input-mini').val('');
			skuContent = {};
		}
	});
	/*获取规格项名称*/
	function getData() {
		$.ajax({
			type: "get",
			url: baseUrl+"?m=Product&a=getFormat&shopid=0",
			dataType: "json",
			success: function(d) {
				var lens = d.data.length;
				var len = $('.king-listB').length;
				for(var i=0; i<len; i++) {
					for(var j=0; j<lens; j++) {
						var cont = d.data[j].name;
						var _id = d.data[j].id;
						$('.king-listB').eq(i).find('ul').append('<li class="king-addAndshow" isId="' + _id +'">'+ cont +'</li>');
					}
				}
			}
		});
	}
	/*点击切换规格项目名称列表的展开与收起*/
	$('.king-upOrdown').on('click', function() {
		$(this).parent().find('.king-listB').toggle();
		var isClick = $(this).attr('isClick');
		if(isClick == 'false') {
			$(this).attr('isClick', 'true');
			$(this).find('.king-toggle').removeClass('king-tog-down').addClass('king-tog-up');
			$(this).removeClass('king-upOrdown-changeB').addClass('king-upOrdown-change');
			$(this).siblings('.king-nowrite').removeClass('king-nowrite-changeB').addClass('king-nowrite-change');
			getData();
		} else {
			$(this).attr('isClick', 'false');
			$(this).find('.king-toggle').removeClass('king-tog-up').addClass('king-tog-down');
			$(this).removeClass('king-upOrdown-change').addClass('king-upOrdown-changeB');
			$(this).siblings('.king-nowrite').removeClass('king-nowrite-change').addClass('king-nowrite-changeB');
			$('.king-listB ul li').remove('.king-addAndshow');
		}
	});
	/*选择规格项：1.直接从一级请求列表中选择
				  2.自己在输入框中输入选择
	*/
	$('.king-listB ul').on('click', 'li', function() {
		var text = $(this).html();
		var val1 = $('.king-nowrite').eq(0).val();
		var val2 = $('.king-nowrite').eq(1).val();
		var val3 = $('.king-nowrite').eq(2).val();
		if(text == val1|| text == val2 || text == val3) {
				$('#king-warm').text('规格名不能相同').fadeIn();
				setTimeout(function() {
					$('#king-warm').fadeOut().text('');
				}, 2500);
				return false;
		} else {
			var _id = $(this).attr('isId');
			var parentId = $(this).parents('.king_add_list').attr('firstId');
			var valNo = $(this).parents('.king-click-list').find('.king-nowrite').val();

			if(valNo!=='' && text!==valNo) {
				$(this).parents('.king-click-list').siblings('.king-add-listM').empty();
			}
			$(this).parents('.king-click-list').find('.king-nowrite').val(text).attr('isId', _id);
			$(this).parents('.king-click-list').find('.king-nowrite').val(text).next().val(_id);
			/*thVal===>th标题*/
			var thVal = $(this).parents('.king_add_list').find('.king-nowrite').val();
			deleteArrayAll(newArry[parseInt(parentId)-1].value);
			if(parentId == '1') {
				newArry[0].name = thVal;
			}
			if(parentId == '2') {
				newArry[1].name = thVal;
			}
			if(parentId == '3') {
				newArry[2].name = thVal;
			}
			deleteArrayAll(arry);
			creatTableNew();
			$(this).parents('.king-click-list').find('.king-upOrdown').attr('isClick', 'false');
			$(this).parents('.king-click-list').find('.king-listB').hide();
			$(this).parents('.king-click-list').find('.king-toggle').removeClass('king-tog-up').addClass('king-tog-down');
			$(this).parents('.king-click-list').find('.king-upOrdown').removeClass('king-upOrdown-change').addClass('king-upOrdown-changeB');
			$(this).parents('.king-click-list').find('.king-nowrite').removeClass('king-nowrite-change').addClass('king-nowrite-changeB');
			$(this).parents('.king_add_list').find('.king-add-title').show();
			$('.king-listB ul').empty();
			$('.king-autoAdd').val('');
		}
	});
	$('.king-autoAdd').keydown(function() {
		$(this).next().empty();
		// $(this).next().append('<li class="king-input" isId="0"></li>');
	});
	$('.king-autoAdd').keyup(function() {
		var _val = $(this).val();
		if(_val == '') {
			$(this).next().empty();
			getData();
		} else {
			$(this).next().empty();
			// $(this).next().append('<li class="king-input" isId="0">'+ _val +'</li>');
			get('shopid=0', _val);
		}
	});
	/*获取与规格相对应的参数*/
	function getDatas(id) {
		$.ajax({
			type: "get",
			url: baseUrl+"?m=Product&a=getFormatContent&fid="+ id,
			dataType: "json",
			success: function(d) {
				var lens = d.data.length;
				var len = $('.king-absolute').length;
				if(lens == 0) {
					for(var i=0; i<len; i++) {
						// $('.king-absolute').eq(i).append('<li class="king-addAndshowA">没有找到匹配项</li>');
						$('.king-absolute').eq(i).append('<a href='+baseUrl+'?m=FormatContent&a=add&fid='+id+' class="king-addAndshowA">没有找到匹配项</a>');
					}
				} else {
					for(var i=0; i<len; i++) {
						for(var j=0; j<lens; j++) {
							var cont = d.data[j].name;
							$('.king-absolute').eq(i).attr('isId', id);
							$('.king-absolute').eq(i).next().val(id);
							$('.king-absolute').eq(i).append('<li class="king-addAndshowA">'+ cont +'</li>');
						}
					}
				}
			}
		});
	}
	/*显示相应的数据*/
	$('.king-add-guigez ul').on('click', 'li', function() {
		/*thVal===>th标题*/
		var thVal = $(this).parents('.king_add_list').find('.king-nowrite').val();
		var firstId = $(this).parents('.king_add_list').attr('firstId');
		arryThis.push(firstId);
		var text = $(this).text();
		var _len = arry.length;
		// console.log(arry);
		for(var i=0; i<_len; i++) {
			if(text == arry[i]) {
				$('#king-warm').text('已经添加了相同的规格值').fadeIn();
				setTimeout(function() {
					$('#king-warm').fadeOut().text('');
				}, 2500);
				return false;
			}
		}
		arryTime++;
		arry.push(text);


		if(firstId == '1') {
			newArry[0].value.push(text);
		}
		if(firstId == '2') {
			newArry[1].value.push(text);
			/*******grass******/
			if(!$.isEmptyObject(skuContent)){
				var ii = 0;
				// console.log(skuContent)
				if(newArry[0].value.length>0&&newArry[1].value.length>0&&newArry[2].value.length==0){
					//只有两组, 添加在第二组的情况
					for (var i = 0; i < newArry[0].value.length; i++) {
						for (var j = 0; j < newArry[1].value.length; j++) {
							if(ii%newArry[1].value.length==newArry[1].value.length-1){
								skuContent.splice(ii,0,{"fmt_price":"","fmt_stock":"","importcode":"","fmt_value":'|'+newArry[0].value[i]+'|'+newArry[1].value[j]+'|'});
							}
							ii++;
						};
					};
				}else{
					//有三组, 添加在第二组的情况
					var ii = 0;
					for (var i = 0; i < newArry[0].value.length; i++) {
						for (var j = 0; j < newArry[1].value.length; j++) {
							for (var k = 0; k < newArry[2].value.length; k++) {
								if(ii%((newArry[1].value.length)*newArry[2].value.length)-((newArry[1].value.length-1)*newArry[2].value.length)>=0){
									skuContent.splice(ii,0,{"fmt_price":"","fmt_stock":"","importcode":"","fmt_value":'|'+newArry[0].value[i]+'|'+newArry[1].value[j]+'|'+newArry[2].value[k]+'|'});
								}
								ii++;
							};
						};
					};
				}
			}
			/*******grass******/
		}
		if(firstId == '3') {
			newArry[2].value.push(text);

			/*******grass******/
			if(!$.isEmptyObject(skuContent)){
				if(newArry[0].value.length>0&&newArry[1].value.length>0&&newArry[2].value.length>0){
					//有三组,添加在第三组的情况
					var ii = 0;
					for (var i = 0; i < newArry[0].value.length; i++) {
						for (var j = 0; j < newArry[1].value.length; j++) {
							for (var k = 0; k < newArry[2].value.length; k++) {
								if(ii%newArry[2].value.length==newArry[2].value.length-1){
									skuContent.splice(ii,0,{"fmt_price":"","fmt_stock":"","importcode":"","fmt_value":'|'+newArry[0].value[i]+'|'+newArry[1].value[j]+'|'+newArry[2].value[k]+'|'});
								}
								ii++;
							};
						};
					};
				}
			}
			/*******grass******/
		}


		creatTableNew();
		$(this).parent().hide();
		$('#king-add-table').show();
		var formatHead = $(this).parents('.king_add_list').find('.king-nowrite').val();
		$('.king-invents-num,.code,.grass_price').attr('readonly', 'readonly').css('background','#eee');
		$(this).parents('.king-add-guigez').hide();
		$(this).parents('.king-add-guigez').siblings('.arrow').hide();
		$(this).parents('.king_add_list').find('.king-add-listM').append('<li><span class="king-listM">'+text+'</span><i class="king-close-list" id="'+arryTime+'"></i><input type="hidden" name="format[format]['+formatHead+'][]" value="'+text+'" /></li>');
	});
	/*确认/关闭按钮*/
	$('.king-add-s').on('click', function() {
		$(this).siblings('.king-add-guigez').find('.king-absolute').empty();
		var id = $(this).parents('.king_add_list').find('.king-nowrite').attr('isId');
		getDatas(id);
		$(this).siblings('.king-add-guigez').find('.king-absolute').show();
		$(this).siblings('.king-add-guigez').show();
		$(this).siblings('.arrow').show();
		$(this).siblings('.king-add-guigez').find('.king-input-addGui').val('');
	});
	$('.king-nosure').on('click', function() {
		$(this).parent().hide();
		$(this).siblings('.king-absolute').empty();
		$(this).parent().siblings('.arrow').hide();
	});

	/*搜寻符合要求的数据*/
	$('.king-input-addGui').keydown(function(event) {
		$(this).siblings('.king-absolute').empty();
		// $(this).siblings('.king-absolute').append('<li class="king-inputs"></li>');
	});
	$('.king-input-addGui').keyup(function(event) {
		var id = $(this).next().attr('isid');
		var _val = $('.king-input-addGui').val();
		if(_val == '') {
			$(this).next().empty();
			getDatas(id);
		} else {
			$(this).next().empty();
			// $(this).siblings('.king-absolute').append('<li class="king-inputs">'+ _val +'</li>');
			get('fid='+id, _val);
		}
	});
	/*删除显示的数据块*/
	$('.king-add-listM').on('click', '.king-close-list',function() {
		var i = parseInt($(this).parents('.king_add_list').attr('firstId'))-1;
		var len = newArry[i].value.length;
		var content = $(this).parent().find('.king-listM').text();
		deleteArray(newArry[i].value, content);
		deleteArray(arry, content);
		if(len == 0) {
			newArry[i].name = '';
		}
		$(this).parent().remove();

		/****grass****/
		if(skuContent){
			for (var i = skuContent.length-1; i >= 0 ; i--) {
				if(skuContent[i].fmt_value.indexOf('|'+content+'|') !=-1){
					skuContent.splice(i,1);
				}
			};
		}
		/****grass****/


		creatTableNew();
		var len1 = newArry[0].value.length;
		var len2 = newArry[1].value.length;
		var len3 = newArry[2].value.length;
		if(len1==0&&len2==0&&len3==0) {
			deleteAllAfter();
		}


	});
	/*获取查询数据*/
	function get(eles, val) {
		var url = baseUrl+"?m=Product&a=getFormat&"+eles+"&name="+val;
		$.ajax({
			type: "get",
			url: url,
			dataType: "json",
			success:function(d) {
				if(!(d.data == undefined)) {
					var _len = d.data.length;
					var _value;
					var _id;
					for(var i=0; i<_len; i++) {
						_value = d.data[i].name;
						_id = d.data[i].id;
						if(eles == 'shopid=0') {
							var ele = $('<li class="king-addli" isId="'+ _id +'">'+ _value +'</li>');
							$('.king-listB ul').append(ele);
							/*if(val == _value) {
								$('.king-input').remove();
							}*/
						} else {
							var ele = $('<li class="king-addlis">'+ _value +'</li>');
							$('.king-absolute').append(ele);
							/*if(val == _value) {
								$('.king-inputs').remove();
							}*/
						}
					}
				}
			}
		});
	}
	/*表格的显示与隐藏*/
	function deleteAllAfter() {
		$('#king-add-table').hide();
		$('.king-invents-num,.code,.grass_price').removeAttr('readonly').css('background','white');
		$('.king-invents-num').val('0');
	}
	/*从数组中删除数据*/
	function deleteArrayAll(array) {
		var len = array.length;
		array.splice(0, len);
	}
	function deleteArray(array, cont) {
		var len = array.length;
		for(var i=0; i<len; i++) {
			if(cont == array[i]) {
				array.splice(i, 1);
			}
		}
	}
	/*判断价格与库存的状态来显示提示*/
	var word = ['价格最小为0.01', '请输入一个数字'];
	var word1 = ['库存不能为空', '请输入一个数字'];
	showWarm('.price', word);
	showWarm('.kucun', word1);
	function showWarm(goal, words) {
		$('tbody').on('blur', goal, function() {
			var value = $(this).val();
			// var reg = /^[0-9]+$/;
			var reg = /^\d+(\.\d{1,2})?$/;
			if(value == '') {
				$('#king-warm2').text('').fadeIn()
				$('#king-warm2').text(words[0]).fadeIn();
				$(this).val('');
				setTimeout(function() {
					$('#king-warm2').fadeOut().text('');
				}, 1000);

			} else {
				if(!reg.test(value)) {
					$('#king-warm2').text('').fadeIn()
					$('#king-warm2').text(words[1]).fadeIn();
					$(this).val('');
					setTimeout(function() {
						$('#king-warm2').fadeOut().text('');
					}, 1000);
				}
			}
		});
	}
	all();
	function all() {
		$('tbody').on('blur', '.kucun', function() {
			var len = $('.kucun').length;
			var number = 0;
			for(var i=0; i<len; i++) {
				var value = $('.kucun').eq(i).val();
				var num;
				if(value == '') {
					num = 0;
				} else {
					num = parseInt(value);
				}
				number += num;
				$('.king-invents-num').val(number);
			}

		})
	}

/*创建表格*/
function creatTableNew() {
	var len1, len2, len3;
	len1 = newArry[0]?newArry[0].value.length:0;
	len2 = newArry[1]?newArry[1].value.length:0;
	len3 = newArry[2]?newArry[2].value.length:0;
	if(len2==0&&len3==0||len1==0&&len2==0||len1==0&&len3==0) {
 		var row = 1;
 		if(len2==0&&len3==0) {
 			creatOnlyData(0, len1, row);
 		} else if(len1==0&&len2==0) {
 			creatOnlyData(2, len3, row);
 		} else if(len1==0&&len3==0) {
 			creatOnlyData(1, len2, row);
 		}
 	}else if(len1==0||len2==0||len3==0) {
 		$('.table-sku-stock tbody').empty();
 		$('.text-center').remove();
 		if(len1 == 0) {
 			var row = len3;
 			$('.th-price').before('<th class="text-center">'+newArry[1].name+'</th>');
 			$('.th-price').before('<th class="text-center">'+newArry[2].name+'</th>');
 			creatSecond(1, 2, row, len2, len3);
 		}
 		if(len2 == 0) {
 			var row = len3;
 			$('.th-price').before('<th class="text-center">'+newArry[0].name+'</th>');
 			$('.th-price').before('<th class="text-center">'+newArry[2].name+'</th>');
 			creatSecond(0, 2, row, len1, len3);
 		}
 		if(len3 == 0) {
 			var row = len2;
 			$('.th-price').before('<th class="text-center">'+newArry[0].name+'</th>');
 			$('.th-price').before('<th class="text-center">'+newArry[1].name+'</th>');
 			creatSecond(0, 1, row, len1, len2);
 		}
 	}else if(len1!==0&&len2!==0||len1!==0&&len3!==0||len2!==0&&len3!==0) {
 		$('.table-sku-stock tbody').empty();
 		$('.text-center').remove();
 		var rowT = len3;
 		var row = len2*len3;
 		for(var i=0; i<3; i++) {
 			$('.th-price').before('<th class="text-center">'+newArry[i].name+'</th>');
 		}
 		creatThere(len1, len2, len3, rowT, row);
 	}
}
	/*页面中不存在表格时添加*/
	function creatOnlyData(i, len, row) {
		$('.text-center').remove();
 		$('.table-sku-stock tbody').empty();
		$('.th-price').before('<th class="text-center">'+newArry[i].name+'</th>');
		var ii = 0;
		for(var j=0; j<len; j++) {
			format = {"fmt_price":"","fmt_stock":"","importcode":"",};
			if(skuContent){
				format.fmt_price  = skuContent[ii]?skuContent[ii].fmt_price:'';
				format.fmt_stock  = skuContent[ii]?skuContent[ii].fmt_stock:'';
				format.importcode = skuContent[ii]?skuContent[ii++].importcode:'';
			}
			$('.table-sku-stock tbody').append('<tr><td rowspan="'+row+'">'+newArry[i].value[j]+'</td><td><input name="format[price][]" value="'+format.fmt_price+'" type="text" class="input-mini input_width_74 king_ie_box price"/></td><td><input name="format[stock][]" value="'+format.fmt_stock+'" type="text" class="input-mini input_width_74 king_ie_box kucun"/></td><td><input name="format[code][]" value="'+format.importcode+'" type="text" class="input-mini input_width_104 king_ie_box" /></td></tr>');
		}
	}
	/*页面中已经存在一条数据表格*/
	function creatSecond(a, b, row, len1, len2) {
		var ii = 0;
		for(var i=0; i<len1; i++) {
			for(var j=0; j<len2; j++) {
				format = {"fmt_price":"","fmt_stock":"","importcode":"",};
				if(skuContent){
					format.fmt_price  = skuContent[ii]?skuContent[ii].fmt_price:'';
					format.fmt_stock  = skuContent[ii]?skuContent[ii].fmt_stock:'';
					format.importcode = skuContent[ii]?skuContent[ii++].importcode:'';
				}
				if(j==0) {
					$('.table-sku-stock tbody').append('<tr><td rowspan="'+row+'">'+newArry[a].value[i]+'</td><td>'+newArry[b].value[j]+'</td><td><input name="format[price][]" value="'+format.fmt_price+'" type="text" class="input-mini input_width_74 king_ie_box price"/></td><td><input  name="format[stock][]" value="'+format.fmt_stock+'" type="text" class="input-mini input_width_74 king_ie_box kucun"/></td><td><input name="format[code][]" value="'+format.importcode+'" type="text" class="input-mini input_width_104 king_ie_box" /></td></tr>');
				} else {
					$('.table-sku-stock tbody').append('<tr><td>'+newArry[b].value[j]+'</td><td><input  name="format[price][]" value="'+format.fmt_price+'" type="text" class="input-mini input_width_74 king_ie_box price"/></td><td><input name="format[stock][]" value="'+format.fmt_stock+'" type="text" class="input-mini input_width_74 king_ie_box kucun"/></td><td><input  name="format[code][]" value="'+format.importcode+'" type="text" class="input-mini input_width_104 king_ie_box" /></td></tr>');
				}
			}
		}
	}
	/*页面中已经存在两条数据表格了*/
	function creatThere(len1, len2, len3, rowT, row) {
		var ii = 0;
		for(var i=0; i<len1; i++) {
			for(var j=0; j<len2; j++) {
				for(var z=0; z<len3; z++) {

					format = {"fmt_price":"","fmt_stock":"","importcode":"",};
					if(skuContent){
						format.fmt_price  = skuContent[ii]?skuContent[ii].fmt_price:'';
						format.fmt_stock  = skuContent[ii]?skuContent[ii].fmt_stock:'';
						format.importcode = skuContent[ii]?skuContent[ii++].importcode:'';
					}
					if(j==0&&z==0) {
						$('.table-sku-stock tbody').append('<tr><td rowspan="'+row+'">'+newArry[0].value[i]+'</td><td rowspan="'+rowT+'">'+newArry[1].value[j]+'</td><td>'+newArry[2].value[z]+'</td><td><input  name="format[price][]" value="'+format.fmt_price+'" type="text" class="input-mini input_width_74 king_ie_box price"/></td><td><input  name="format[stock][]" value="'+format.fmt_stock+'" type="text" class="input-mini input_width_74 king_ie_box kucun"/></td><td><input  name="format[code][]" value="'+format.importcode+'" type="text" class="input-mini input_width_104 king_ie_box" /></td></tr>');
					} else {
						if(z==0) {
							$('.table-sku-stock tbody').append('<tr><td rowspan="'+rowT+'">'+newArry[1].value[j]+'</td><td>'+newArry[2].value[z]+'</td><td><input name="format[price][]" value="'+format.fmt_price+'"  type="text" class="input-mini input_width_74 king_ie_box price"/></td><td><input  name="format[stock][]" value="'+format.fmt_stock+'" type="text" class="input-mini input_width_74 king_ie_box kucun" /></td><td><input  name="format[code][]" value="'+format.importcode+'" type="text" class="input-mini input_width_104 king_ie_box" /></td></tr>');
						} else {
							$('.table-sku-stock tbody').append('<tr><td>'+newArry[2].value[z]+'</td><td><input name="format[price][]" value="'+format.fmt_price+'" type="text" class="input-mini input_width_74 king_ie_box price"/></td><td><input  name="format[stock][]" value="'+format.fmt_stock+'" type="text" class="input-mini input_width_74 king_ie_box kucun"/></td><td><input  name="format[code][]" value="'+format.importcode+'" type="text" class="input-mini input_width_104 king_ie_box" /></td></tr>');
						}
					}
				}
			}
		}
	}
});
