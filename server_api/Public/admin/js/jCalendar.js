var g_isMozilla = (typeof document.implementation != 'undefined') && (typeof document.implementation.createDocument != 'undefined') && (typeof HTMLDocument != 'undefined')
document.write("<div id='meizzCalendarLayer' style='position:absolute; z-index: 9999; width: 144px; height: 193px; display: none'>");
document.write("<iframe name='meizzCalendarIframe' scrolling='no' frameborder='0' width='100%' height='100%'></iframe></div>");

function writeIframe() {
    var strIframe =
    "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><style>"
    + "*{font-size: 12px; font-family: 宋体}"
    + ".bg{  color: " + WebCalendar.lightColor
    + "; cursor: default; background-color: " + WebCalendar.darkColor + ";}"
    + "table#tableMain{ width: 142; height: 180;}"
    + "table#tableWeek td{ border:solid 1px #fff;color: " + WebCalendar.lightColor + ";}"
    + "table#tableDay  td{ font-weight: bold;}"
    + "td#meizzYearHead, td#meizzYearMonth{color: " + WebCalendar.wordColor + "}"
    + ".out { text-align: center; border-top: 1px solid " + WebCalendar.DarkBorder + "; border-left: 1px solid " + WebCalendar.DarkBorder + ";"
    + "border-right: 1px solid " + WebCalendar.lightColor + "; border-bottom: 1px solid " + WebCalendar.lightColor + ";}"
    + ".over{ text-align: center; border-top: 1px solid #FFFFFF; border-left: 1px solid #FFFFFF;"
    + "border-bottom: 1px solid " + WebCalendar.DarkBorder + "; border-right: 1px solid " + WebCalendar.DarkBorder + "}"
    + "input{ border: 1px solid " + WebCalendar.darkColor + "; padding-top: 1px; height: 18; cursor: pointer;"
    + "       color:" + WebCalendar.wordColor + "; background-color: " + WebCalendar.btnBgColor + "}"
    + "</style></head><body onselectstart='return false' style='margin: 0px' oncontextmenu='return false'><form name=meizz>";

    strIframe += "<select name='tmpYearSelect'  onblur='parent.hiddenSelect(this)' style='z-index:1;position:absolute;top:3;left:18;display:none'"
    + " onchange='parent.WebCalendar.thisYear=this.value; parent.hiddenSelect(this); parent.writeCalendar();'></select>"
    + "<select name=tmpMonthSelect onblur='parent.hiddenSelect(this)' style='z-index:1; position:absolute;top:3;left:74;display:none'"
    + " onchange='parent.WebCalendar.thisMonth=this.value; parent.hiddenSelect(this); parent.writeCalendar();'></select>"
    + "<table id=tableMain class=bg border=0 cellspacing=2 cellpadding=0>"
    + "<tr><td width=140 height=19 bgcolor='" + WebCalendar.lightColor + "'>"
    + "    <table width=140 id=tableHead border=0 cellspacing=1 cellpadding=0><tr align=center>"
    + "    <td width=15 height=19 class=bg title='向前翻 1 月&#13;快捷键：←' style='cursor: pointer' onclick='parent.prevM()'><b>&lt;</b></td>"
    + "    <td width=60 id=meizzYearHead  title='点击此处选择年份' onclick='parent.funYearSelect(parseInt(this.innerText, 10))'"
    + "        onmouseover='this.bgColor=parent.WebCalendar.darkColor; this.style.color=parent.WebCalendar.lightColor'"
    + "        onmouseout='this.bgColor=parent.WebCalendar.lightColor; this.style.color=parent.WebCalendar.wordColor'></td>"
    + "    <td width=50 id=meizzYearMonth title='点击此处选择月份' onclick='parent.funMonthSelect(parseInt(this.innerText, 10))'"
    + "        onmouseover='this.bgColor=parent.WebCalendar.darkColor; this.style.color=parent.WebCalendar.lightColor'"
    + "        onmouseout='this.bgColor=parent.WebCalendar.lightColor; this.style.color=parent.WebCalendar.wordColor'></td>"
    + "    <td width=15 class=bg title='向后翻 1 月&#13;快捷键：→' onclick='parent.nextM()' style='cursor: pointer'><b>&gt;</b></td></tr></table>"
    + "</td></tr><tr><td height=20><table id=tableWeek border=1 width=140 cellpadding=0 cellspacing=0 ";

    strIframe += " borderColorLight='" + WebCalendar.darkColor + "' borderColorDark='" + WebCalendar.lightColor + "'>"
    + "    <tr align=center><td height=20>日</td><td>一</td><td>二</td><td>三</td><td>四</td><td>五</td><td>六</td></tr></table>"
    + "</td></tr><tr><td valign=top width=140 bgcolor='" + WebCalendar.lightColor + "'>"
    + "    <table id=tableDay height=120 width=140 border=0 cellspacing=0 cellpadding=0>";
    for (var x = 0; x < 5; x++) {
        strIframe += "<tr>";
        for (var y = 0; y < 7; y++)
            strIframe += "<td class='out' id='meizzDay" + (x * 7 + y) + "'></td>";
        strIframe += "</tr>";
    }
    strIframe += "<tr>";
    for (var x = 35; x < 38; x++)
        strIframe += "<td class='out' id='meizzDay" + x + "'></td>";

    strIframe += "<td colspan=4 class='out' title='" + WebCalendar.regInfo + "'><input type='button' onclick='parent.clearCalendar();' value='取消' style='cursor:pointer;width:49%; height:100%'/><input style=' background-color: " + WebCalendar.btnBgColor
   	+ ";cursor: pointer; width: 49%; height: 100%;' onfocus='this.blur()'"
   	+ " type=button value='关闭' onclick='parent.hiddenCalendar()'/></td></tr></table>"
   	+ "</td></tr><tr><td height=20 width=140 bgcolor='" + WebCalendar.lightColor + "'>"
   	+ "    <table border=0 cellpadding=1 cellspacing=0 width=140><tr>"
   	+ "    <td><input style='width: 23' name=prevYear title='向前翻 1 年&#13;快捷键：↑' onclick='parent.prevY()' type=button value='&lt;&lt;' onfocus='this.blur()'></td>"
   	+ "    <td><input style='width: 17' onfocus='this.blur()' name=prevMonth title='向前翻 1 月&#13;快捷键：←' onclick='parent.prevM()' type=button value='&lt;'></td>"
   	+ "    <td align=center><input name=today type=button value='今天' onfocus='this.blur()' style='width: 50' title='当前日期&#13;快捷键：T' onclick=\"parent.returnToDay(new Date().getDate() +'/'+ (new Date().getMonth() +1) +'/'+ new Date().getFullYear())\"></td>"
   	+ "    <td align=right><input style='width: 17' title='向后翻 1 月&#13;快捷键：→' name=nextMonth onclick='parent.nextM()' type=button value='&gt;' onfocus='this.blur()'></td>"
   	+ "    <td align=right><input style='width: 23' name=nextYear title='向后翻 1 年&#13;快捷键：↓' onclick='parent.nextY()' type=button value='&gt;&gt;' onfocus='this.blur()'></td>"
   	+ "    </tr></table>"
   	+ "</td></tr><table></form></body></html>";
    with (WebCalendar.iframe) {
        document.writeln(strIframe);
        document.close();
        for (var i = 0; i < 38; i++) {
            WebCalendar.dayObj[i] = document.getElementById("meizzDay" + i);
            WebCalendar.dayObj[i].onmouseover = dayMouseOver;
            WebCalendar.dayObj[i].onmouseout = dayMouseOut;
            WebCalendar.dayObj[i].onclick = returnDate;
        }
    }
}
function WebCalendar() //初始化日历的设置
{
    this.regInfo = "选择日期";
    this.daysMonth = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    this.day = new Array(39);                 //定义日历展示用的数组
    this.dayObj = new Array(39);               //定义日期展示控件数组
    this.dateStyle = null;                     //保存格式化后日期数组
    this.objExport = null;                     //日历回传的显示控件
    this.objAge = null;                        // 年龄【hp 新增】
    this.callbackFunc = null;                  // 回调函数
    this.eventSrc = null;                      //日历显示的触发控件
    this.inputDate = null;                     //转化外的输入的日期(d/m/yyyy)
    this.thisYear = new Date().getFullYear(); //定义年的变量的初始值
    this.thisMonth = new Date().getMonth() + 1; //定义月的变量的初始值
    this.thisDay = new Date().getDate();     //定义日的变量的初始值
    this.today = this.thisDay + "/" + this.thisMonth + "/" + this.thisYear;   //今天(d/m/yyyy)
    this.iframe = window.frames["meizzCalendarIframe"]; //日历的 iframe 载体
    this.calendar = getObjectById("meizzCalendarLayer");  //日历的层
    this.dateReg = "";           //日历格式验证的正则式
    
	this.yearFall   = 50;           //定义年下拉框的年差值
    this.format     = "yyyy-mm-dd"; //回传日期的格式
    this.timeShow   = false;        //是否返回时间
    this.drag       = true;         //是否允许拖动
    this.darkColor  = "#97CBFE";    //控件的暗色
    this.lightColor = "#FFFFFF";    //控件的亮色
    this.btnBgColor = "#FFF5A0";    //控件的按钮背景色
    this.wordColor  = "#000040";    //控件的文字颜色
    this.wordDark   = "#DCDCDC";    //控件的暗文字颜色
    this.dayBgColor = "#FFFACD";    //日期数字背景色
    this.todayColor = "#F2BE8D";    //今天在日历上的标示背景色
    this.DarkBorder = "#FFE4C4";    //日期显示的立体表达色
}

var WebCalendar = new WebCalendar();
function getPos(o) {
    var left = o.offsetLeft;
    var top = o.offsetTop;
    while (o = o.offsetParent) {
        left += o.offsetLeft;
        top += o.offsetTop
    }
    return {
        left: left,
        top: top
    }
}
function clearCalendar() {
    WebCalendar.objExport.value = "";
    hiddenCalendar();
}
function setday(e, setEle, ageId, callback) //主调函数
{
    if (setEle == null) setEle = e;

    writeIframe();
    var o = WebCalendar.calendar.style;
    WebCalendar.eventSrc = e;
    WebCalendar.callbackFunc = callback;
    WebCalendar.objAge = document.getElementById(ageId);
    if (arguments.length == 0)
        WebCalendar.objExport = e;
    else
        WebCalendar.objExport = setEle;

    WebCalendar.iframe.document.getElementById('tableWeek').style.cursor = "pointer";
    var h = setEle.clientHeight, p = e.type;
    var pos = getPos(setEle);
    var t = pos.top;
    var l = pos.left;
    o.display = "";
    o.left = l + "px";
    o.top = (t + h) + "px";
    if (!WebCalendar.timeShow)
        WebCalendar.dateReg = /^(\d{1,4})(-|\/|.)(\d{1,2})\2(\d{1,2})$/;
    else
        WebCalendar.dateReg = /^(\d{1,4})(-|\/|.)(\d{1,2})\2(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/;
    try {

        if (WebCalendar.objExport.value.trim() != "") {
            WebCalendar.dateStyle = WebCalendar.objExport.value.trim().match(WebCalendar.dateReg);
            if (WebCalendar.dateStyle == null) {
                WebCalendar.thisYear = new Date().getFullYear();
                WebCalendar.thisMonth = new Date().getMonth() + 1;
                WebCalendar.thisDay = new Date().getDate();
                alert("原文本框里的日期有错误！\n可能与你定义的显示时分秒有冲突！");
                WebCalendar.objExport.value = "";
                writeCalendar();
                return false;
            }
            else {
                WebCalendar.thisYear = parseInt(WebCalendar.dateStyle[1], 10);
                WebCalendar.thisMonth = parseInt(WebCalendar.dateStyle[3], 10);
                WebCalendar.thisDay = parseInt(WebCalendar.dateStyle[4], 10);
                WebCalendar.inputDate = parseInt(WebCalendar.thisDay, 10) + "/" + parseInt(WebCalendar.thisMonth, 10) + "/"
            + parseInt(WebCalendar.thisYear, 10); writeCalendar();
            }
        }
        else {
            WebCalendar.thisYear = new Date().getFullYear();
            WebCalendar.thisMonth = new Date().getMonth() + 1;
            WebCalendar.thisDay = new Date().getDate();
            writeCalendar();
        }
    }
    catch (e) {
        writeCalendar();
    }
}
function funMonthSelect() //月份的下拉框
{
    var m = isNaN(parseInt(WebCalendar.thisMonth, 10)) ? new Date().getMonth() + 1 : parseInt(WebCalendar.thisMonth);
    var e = WebCalendar.iframe.document.forms[0].tmpMonthSelect;
    for (var i = 1; i < 13; i++) e.options.add(new Option(i + "月", i));
    e.style.display = ""; e.value = m; e.focus(); window.status = e.style.top;
}
function funYearSelect() //年份的下拉框
{
    var n = WebCalendar.yearFall;
    var e = WebCalendar.iframe.document.forms[0].tmpYearSelect;
    var y = isNaN(parseInt(WebCalendar.thisYear, 10)) ? new Date().getFullYear() : parseInt(WebCalendar.thisYear);
    y = (y <= 1000) ? 1000 : ((y >= 9999) ? 9999 : y);
    var min = (y - n >= 1000) ? y - n : 1000;
    var max = (y + n <= 9999) ? y + n : 9999;
    min = (max == 9999) ? max - n * 2 : min;
    max = (min == 1000) ? min + n * 2 : max;
    for (var i = min; i <= max; i++) e.options.add(new Option(i + "年", i));
    e.style.display = ""; e.value = y; e.focus();
}
function prevM()  //往前翻月份
{
    WebCalendar.thisDay = 1;
    if (WebCalendar.thisMonth == 1) {
        WebCalendar.thisYear--;
        WebCalendar.thisMonth = 13;
    }
    WebCalendar.thisMonth--;
    writeCalendar();
}
function nextM()  //往后翻月份
{
    WebCalendar.thisDay = 1;
    if (WebCalendar.thisMonth == 12) {
        WebCalendar.thisYear++;
        WebCalendar.thisMonth = 0;
    }
    WebCalendar.thisMonth++;
    writeCalendar();
}
function prevY() { WebCalendar.thisDay = 1; WebCalendar.thisYear--; writeCalendar(); } //往前翻 Year
function nextY() { WebCalendar.thisDay = 1; WebCalendar.thisYear++; writeCalendar(); } //往后翻 Year
function hiddenSelect(e) {
    for (var i = e.options.length; i > -1; i--)
        e.remove(i);
    e.style.display = "none";
}
function getObjectById(id) { return document.getElementById(id); }
function hiddenCalendar() { getObjectById("meizzCalendarLayer").style.display = "none"; };
function appendZero(n) { return (("00" + n).substr(("00" + n).length - 2)); } //日期自动补零程序
String.prototype.trim = function() { return this.replace(/(^\s*)|(\s*$)/g, ""); }
function dayMouseOver() {
    this.className = "over";
    this.bgColor = WebCalendar.darkColor;
    if (WebCalendar.day[this.id.substr(8)].split("/")[1] == WebCalendar.thisMonth)
        this.style.color = WebCalendar.lightColor;
}
function dayMouseOut() {
    this.className = "out";
    var d = WebCalendar.day[this.id.substr(8)], a = d.split("/");
    this.bgColor = WebCalendar.dayBgColor;
    if (a[1] == WebCalendar.thisMonth && d != WebCalendar.today) {
        if (WebCalendar.dateStyle && a[0] == parseInt(WebCalendar.dateStyle[4], 10))
            this.style.color = WebCalendar.lightColor;
        this.style.color = WebCalendar.wordColor;
    }
    if (WebCalendar.inputDate == d) {
        this.bgColor = WebCalendar.darkColor;
        this.style.color = WebCalendar.lightColor;
    }
    if (d == WebCalendar.today) {
        this.bgColor = WebCalendar.todayColor;
        this.style.color = WebCalendar.lightColor;
    }
}
function writeCalendar() //对日历显示的数据的处理程序
{
    var y = WebCalendar.thisYear;
    var m = WebCalendar.thisMonth;
    var d = WebCalendar.thisDay;
    WebCalendar.daysMonth[1] = (0 == y % 4 && (y % 100 != 0 || y % 400 == 0)) ? 29 : 28;
    if (!(y <= 9999 && y >= 1000 && parseInt(m, 10) > 0 && parseInt(m, 10) < 13 && parseInt(d, 10) > 0)) {
        alert("对不起，你输入了错误的日期！");
        WebCalendar.objExport.value = "";
        WebCalendar.thisYear = new Date().getFullYear();
        WebCalendar.thisMonth = new Date().getMonth() + 1;
        WebCalendar.thisDay = new Date().getDate();
    }
    y = WebCalendar.thisYear;
    m = WebCalendar.thisMonth;
    d = WebCalendar.thisDay;

    if (g_isMozilla) {
        WebCalendar.iframe.document.getElementById('meizzYearHead').textContent = y + " 年";
        WebCalendar.iframe.document.getElementById('meizzYearMonth').textContent = parseInt(m, 10) + " 月";
    }
    else {
        WebCalendar.iframe.document.getElementById('meizzYearHead').innerText = y + " 年";
        WebCalendar.iframe.document.getElementById('meizzYearMonth').innerText = parseInt(m, 10) + " 月";
    }


    WebCalendar.daysMonth[1] = (0 == y % 4 && (y % 100 != 0 || y % 400 == 0)) ? 29 : 28; //闰年二月为29天
    var w = new Date(y, m - 1, 1).getDay();
    var prevDays = m == 1 ? WebCalendar.daysMonth[11] : WebCalendar.daysMonth[m - 2];
    for (var i = (w - 1); i >= 0; i--) //这三个 for 循环为日历赋数据源（数组 WebCalendar.day）格式是 d/m/yyyy
    {
        WebCalendar.day[i] = prevDays + "/" + (parseInt(m, 10) - 1) + "/" + y;
        if (m == 1)
            WebCalendar.day[i] = prevDays + "/" + 12 + "/" + (parseInt(y, 10) - 1);
        prevDays--;
    }

    for (var i = 1; i <= WebCalendar.daysMonth[m - 1]; i++)
        WebCalendar.day[i + w - 1] = i + "/" + m + "/" + y;

    for (var i = 1; i < 38 - w - WebCalendar.daysMonth[m - 1] + 1; i++) {
        WebCalendar.day[WebCalendar.daysMonth[m - 1] + w - 1 + i] = i + "/" + (parseInt(m, 10) + 1) + "/" + y;
        if (m == 12)
            WebCalendar.day[WebCalendar.daysMonth[m - 1] + w - 1 + i] = i + "/" + 1 + "/" + (parseInt(y, 10) + 1);
    }

    for (var i = 0; i < 38; i++)    //这个循环是根据源数组写到日历里显示
    {
        var a = WebCalendar.day[i].split("/");
        if (g_isMozilla)
            WebCalendar.dayObj[i].textContent = a[0];
        else
            WebCalendar.dayObj[i].innerText = a[0];
        WebCalendar.dayObj[i].title = a[2] + "-" + appendZero(a[1]) + "-" + appendZero(a[0]);
        WebCalendar.dayObj[i].bgColor = WebCalendar.dayBgColor;
        WebCalendar.dayObj[i].style.color = WebCalendar.wordColor;
        if ((i < 10 && parseInt(WebCalendar.day[i], 10) > 20) || (i > 27 && parseInt(WebCalendar.day[i], 10) < 12))
            WebCalendar.dayObj[i].style.color = WebCalendar.wordDark;
        if (WebCalendar.inputDate == WebCalendar.day[i])    //设置输入框里的日期在日历上的颜色
        {
            WebCalendar.dayObj[i].bgColor = WebCalendar.darkColor;
            WebCalendar.dayObj[i].style.color = WebCalendar.lightColor;
        }
        if (WebCalendar.day[i] == WebCalendar.today)      //设置今天在日历上反应出来的颜色
        {
            WebCalendar.dayObj[i].bgColor = WebCalendar.todayColor;
            WebCalendar.dayObj[i].style.color = WebCalendar.lightColor;
        }
    }
}

function returnToDay(today) {
    if (WebCalendar.objExport) {
        var returnValue;
        var a = today.split("/");
        var d = WebCalendar.format.match(/^(\w{4})(-|\/|.|)(\w{1,2})\2(\w{1,2})$/);
        if (d == null) { alert("你设定的日期输出格式不对！\r\n\r\n请重新定义 WebCalendar.format ！"); return false; }
        var flag = d[3].length == 2 || d[4].length == 2; //判断返回的日期格式是否要补零
        returnValue = flag ? a[2] + d[2] + appendZero(a[1]) + d[2] + appendZero(a[0]) : a[2] + d[2] + a[1] + d[2] + a[0];
        if (WebCalendar.timeShow) {
            var h = new Date().getHours(), m = new Date().getMinutes(), s = new Date().getSeconds();
            returnValue += flag ? " " + appendZero(h) + ":" + appendZero(m) + ":" + appendZero(s) : " " + h + ":" + m + ":" + s;
        }
        WebCalendar.objExport.value = returnValue;
        try {
            if (WebCalendar.objAge) WebCalendar.objAge.value = new Date().getFullYear() - a[2];
            if (WebCalendar.callbackFunc) WebCalendar.callbackFunc();
        } catch (e) { }
        hiddenCalendar();
    }
}

function returnDate() //根据日期格式等返回用户选定的日期
{
    if (WebCalendar.objExport) {
        var returnValue;
        var a = WebCalendar.day[this.id.substr(8)].split("/");
        var d = WebCalendar.format.match(/^(\w{4})(-|\/|.|)(\w{1,2})\2(\w{1,2})$/);
        if (d == null) { alert("你设定的日期输出格式不对！\r\n\r\n请重新定义 WebCalendar.format ！"); return false; }
        var flag = d[3].length == 2 || d[4].length == 2; //判断返回的日期格式是否要补零
        returnValue = flag ? a[2] + d[2] + appendZero(a[1]) + d[2] + appendZero(a[0]) : a[2] + d[2] + a[1] + d[2] + a[0];
        if (WebCalendar.timeShow) {
            var h = new Date().getHours(), m = new Date().getMinutes(), s = new Date().getSeconds();
            returnValue += flag ? " " + appendZero(h) + ":" + appendZero(m) + ":" + appendZero(s) : " " + h + ":" + m + ":" + s;
        }
        WebCalendar.objExport.value = returnValue;
        try {
            if (WebCalendar.objAge) WebCalendar.objAge.value = new Date().getFullYear() - a[2];
            if (WebCalendar.callbackFunc) WebCalendar.callbackFunc();
        } catch (e) { }
        hiddenCalendar();
    }
}

function __onclick2() {
    if (WebCalendar.eventSrc != window.event.srcElement)
        hiddenCalendar();
}

function __onclick(evt) {
    window.event = evt;
    if (WebCalendar.eventSrc != window.event.srcElement)
        hiddenCalendar();
}

if (g_isMozilla) {
    window.document.addEventListener('click', __onclick, true);
}
else {
    document.onclick = __onclick2;
}