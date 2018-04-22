
(function($, undefined) {

    $.uedWidget = function(name, base, prototype) {
        var namespace = name.split( "." )[0], fullName;
        name = name.split( "." )[1];
        fullName = namespace + "-" + name;
        // 例如参数name='ued.tabs'，变成namespace='ued',name='tabs',fullName='ued-tabs'
        // base默认为Widget类，组件默认会继承base类的所有方法
        if (!prototype) {
            prototype = base;
            base = $.UEDWidget;
        }
        // create selector for plugin
        $.expr[ ":" ][fullName] = function(elem) {
            return !!$.data(elem, name);
        };
        // 创建命名空间$.ued.tabs
        $[namespace] = $[namespace] || {};
        // 组件的构造函数
        $[ namespace ][name] = function(options, element) {
            // allow instantiation without initializing for simple inheritance
            if (arguments.length) {
                this._createWidget(options, element);
            }
        };
        // 初始化父类，一般调用了$.Widget
        var basePrototype = new base();
        // we need to make the options hash a property directly on the new instance
        // otherwise we'll modify the options hash on the prototype that we're
        // inheriting from
        //		$.each( basePrototype, function( key, val ) {
        //			if ( $.isPlainObject(val) ) {
        //				basePrototype[ key ] = $.extend( {}, val );
        //			}
        //		});
        basePrototype.options = $.extend(true, {}, basePrototype.options);
        // 给om.tabs继承父类的所有原型方法和参数
        $[ namespace ][name].prototype = $.extend(true, basePrototype, {
            namespace : namespace,
            widgetName : name,
            // 组件的事件名前缀，调用_trigger的时候会默认给trigger的事件加上前缀
            // 例如_trigger('create')实际会触发'tabscreate'事件
            widgetEventPrefix : $[ namespace ][name].prototype.widgetEventPrefix || name,
            widgetBaseClass : fullName
        }, prototype);
        // 把tabs方法挂到jquery对象上，也就是$('#tab1').tabs();
        var temp = $.uedWidget.bridge(name, $[ namespace ][name]);
    };

    $.uedWidget.bridge = function(name, object) {
        $.fn[name] = function(options) {
            // 如果tabs方法第一个参数是string类型，则认为是调用组件的方法，否则调用options方法
            var isMethodCall = typeof options === "string", args = Array.prototype.slice.call(arguments, 1), returnValue = this;
            // allow multiple hashes to be passed on init
            options = !isMethodCall && args.length ? $.extend.apply(null, [true, options].concat(args)) : options;
            // '_'开头的方法被认为是内部方法，不会被执行，如$('#tab1').tabs('_init')
            if (isMethodCall && options.charAt(0) === "_") {
                return returnValue;
            }
            if (isMethodCall) {
                this.each(function() {
                    // 执行组件方法
                    var instance = $.data(this, name);
                    if (options == 'options') {
                        returnValue = instance && instance.options;
                        return false;
                    } else {
                        var methodValue = instance && $.isFunction(instance[options]) ? instance[options].apply(instance, args) : instance;
                        if (methodValue !== instance && methodValue !== undefined) {
                            returnValue = methodValue;
                            return false;
                        }
                    }
                });
            } else {
                // 调用组件的options方法
                this.each(function() {
                    var instance = $.data(this, name);
                    if (instance) {
                        // 设置options后再次调用_init方法，第一次调用是在_createWidget方法里面。这个方法需要开发者去实现。
                        // 主要是当改变组件中某些参数后可能需要对组件进行重画
                        instance._setOptions(options || {});
                        $.extend(instance.options, options);
                        $(instance.beforeInitListeners).each(function() {
                            this.call(instance);
                        });
                        instance._init();
                        $(instance.initListeners).each(function() {
                            this.call(instance);
                        });
                    } else {
                        // 没有实例的话，在这里调用组件类的构造函数，并把构造后的示例保存在dom的data里面。注意这里的this是dom，object是模块类
                        $.data(this, name, new object(options, this));
                    }
                });
            }
            return returnValue;
        };
    };
    $.uedWidget.addCreateListener = function(name, fn) {
        var temp = name.split(".");
        $[ temp[0] ][temp[1]].prototype.createListeners.push(fn);
    };
    $.uedWidget.addInitListener = function(name, fn) {
        var temp = name.split(".");
        $[ temp[0] ][temp[1]].prototype.initListeners.push(fn);
    };
    $.uedWidget.addBeforeInitListener = function(name, fn) {
        var temp = name.split(".");
        $[ temp[0] ][temp[1]].prototype.beforeInitListeners.push(fn);
    };
    $.UEDWidget = function(options, element) {
        this.createListeners = [];
        this.initListeners = [];
        this.beforeInitListeners = [];
        // allow instantiation without initializing for simple inheritance
        if (arguments.length) {
            this._createWidget(options, element);
        }
    };
   
    $.UEDWidget.prototype = {
        widgetName : "widget",
        widgetEventPrefix : "",
        options : {
            disabled : false
        },
        _createWidget : function(options, element) {
            // $.widget.bridge stores the plugin instance, but we do it anyway
            // so that it's stored even before the _create function runs
            $.data(element, this.widgetName, this);
            this.element = $(element);
            this.options = $.extend(true, {}, this.options, this._getCreateOptions(), options);
            var self = this;
            //注意，不要少了前边的 "ued-"，不然会与jquery-ui冲突
            this.element.bind("ued-remove._" + this.widgetName, function() {
                self.destroy();
            });
            // 开发者实现
            this._create();
            $(this.createListeners).each(function() {
                this.call(self);
            });
            // 如果绑定了初始化的回调函数，会在这里触发。注意绑定的事件名是需要加上前缀的，如$('#tab1').bind('tabscreate',function(){});
            this._trigger("create");
            // 开发者实现
            $(this.beforeInitListeners).each(function() {
                this.call(self);
            });
            this._init();
            $(this.initListeners).each(function() {
                this.call(self);
            });
        },
        _getCreateOptions : function() {
            return $.metadata && $.metadata.get( this.element[0] )[this.widgetName];
        },
        _create : function() {
        },
        _init : function() {
        },
        destroy : function() {
            this.element.unbind("." + this.widgetName).removeData(this.widgetName);
            this.widget().unbind("." + this.widgetName);
        },
        widget : function() {
            return this.element;
        },

        option : function(key, value) {
            var options = key;
            if (arguments.length === 0) {
                // don't return a reference to the internal hash
                return $.extend({}, this.options);
            }
            if ( typeof key === "string") {
                if (value === undefined) {
                    return this.options[key];
                    // 获取值
                }
                options = {};
                options[key] = value;
            }
            this._setOptions(options);
            // 设置值
            return this;
        },
        _setOptions : function(options) {
            var self = this;
            $.each(options, function(key, value) {
                self._setOption(key, value);
            });
            return this;
        },
        _setOption : function(key, value) {
            this.options[key] = value;
            return this;
        },

        // $.widget中优化过的trigger方法。type是回调事件的名称，如"onRowClick"，event是触发回调的事件（通常没有这个事件的时候传null）
        // 这个方法只声明了两个参数，如有其他参数可以直接写在event参数后面
        _trigger : function(type, event) {
            // 获取初始化配置config中的回调方法
            var callback = this.options[type];
            // 封装js标准event对象为jquery的Event对象
            event = $.Event(event);
            event.type = type;
            // copy original event properties over to the new event
            // this would happen if we could call $.event.fix instead of $.Event
            // but we don't have a way to force an event to be fixed multiple times
            if (event.originalEvent) {
                for (var i = $.event.props.length, prop; i; ) {
                    prop = $.event.props[--i];
                    event[prop] = event.originalEvent[prop];
                }
            }
            // 构造传给回调函数的参数，event放置在最后
            var newArgs = [], argLength = arguments.length;
            for (var i = 2; i < argLength; i++) {
                newArgs[i - 2] = arguments[i];
            }
            if (argLength > 1) {
                newArgs[argLength - 2] = arguments[1];
            }
            return !($.isFunction(callback) && callback.apply(this.element, newArgs) === false || event.isDefaultPrevented() );
        }
    };
})(jQuery);
﻿/*
 * uedBottomBar
 * author xzjiang
 * Date: 2013/1/8
 */




(function($) {
    /**
     * @name uedSelect
     * @class 下拉列表框
     */
    $.uedWidget('ued.uedSelect', {
        options : {
			//··默认宽度
			width:300
		},
        _create : function() {
            var $el = this.element, options = this.options, self = this;
            this._createSelect($el);
        },
		
        _init : function() {
            var self = this, options = this.options, $el = this.element;
        },
		
        _createSelect : function($el) {
			var $el = this.element, options = this.options, self = this;
			//··设置文本主体
			var $container = $('<div class="uew-select"></div>')
			var $stateDefault = $('<div class="uew-select-value ue-state-default"></div>')
			var $text = $('<em class="uew-select-text"></em>')
			$stateDefault.append($text).append('<em class="uew-icon uew-icon-triangle-1-s"></em>')
			$el.wrap($container)
			$el.before($stateDefault)
			
			//··设置组件宽度
			var width = parseInt($el.attr("width")) ? parseInt($el.attr("width")) : options.width;
			$el.width(width);
			
		   //··设置组件的样式和大小（在有单位和无单位情况）
		   if($el.attr("unit")){
				var $unit =$('<span class="uew-unit">'+$el.attr("unit")+'</span>')
				$stateDefault.addClass("select-hasUnit").append($unit)
				$stateDefault.width(width - 47);
		   }
		   else{
				$stateDefault.width(width -27);
			}
			
			//判断边框样式
			if($el.hasClass('uew-no-border')){
				$el.parent().find('.uew-select-value').addClass('uew-no-border');
				// 边框的hover状态
				$el.hover(function(){
					$el.parent().find(".uew-select-value").addClass("uew-border-flag");
				},function(){
					$el.parent().find(".uew-select-value").removeClass("uew-border-flag");
				});
			}
		
			//··默认文本
			$text.text($el.find(":selected").text())
			
			//··添加焦点操作
			$el.focus(function(){
				$el.parent().addClass("ue-state-focus");
			});
			$el.blur(function(){
				$el.parent().removeClass("ue-state-focus");
			});
			$el.parent().hover(function(){
				$el.parent().addClass("ue-state-hover");
			},function(){
				$el.parent().removeClass("ue-state-hover");
			});
			
			
			
			
			$el.change(function(){
				$el.parent().find(".uew-select-text").text($(this).find(":selected").text())
			});
        },
		// 重置option选项
		setOption : function()
		{
			var self = this, options = this.options, $el = this.element;
			$el.parent().find(".uew-select-text").text($el.find(":selected").text());
		},
        destroy : function() {
            var $uedSelect = this.element;
            $uedSelect.remove();
        }
    });
})(jQuery);



