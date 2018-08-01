/**
 * 城市处理对象
 * @author Grass <14712905@qq.com>
 */
var Area = {};
Area.data = '';//所有数据
Area.province = 0;//省份id

/**
 * 获取option
 * data的格式:
 * [
 * {
 *     "id":1,
 *     "name":"四川省",
 *     "list":[
 *         {...},
 *         {...},
 *         ...
 *     ]
 * },
 * {...},
 * ]
 * @param  {array} data 输入的数据
 * @param  {string} val  默认选择的值
 * @return {string}      返回所有的option
 */
Area.getOption = function (data,val){
        var str = '<option>请选择...</option>';
        for (var i = 0; i < data.length; i++) {
            str += '<option '+(data[i].id==val?'selected':'')+' value="'+data[i].id+'">'+data[i].name+'</option>';
        };
        return str;
    }

/**
 * 获取数据
 * 可以输入省份id,获取城市列表
 * 可以输入城市id,获取县/区列表
 * @param  {array} data 所有数据
 * @param  {number} pid  省份id
 * @param  {number} cid  城市id
 * @return {array}      返回的列表
 */
Area.getData = function (data,pid,cid){
        if(pid===undefined && cid===undefined){
            this.data = data;
            return data;
        }
        for (var i = 0; i < data.length; i++) {
            if(data[i].id==pid){
                if(cid===undefined){
                    this.province = pid;
                    return data[i].list;
                }
                for (var j = 0; j < data[i].list.length; j++) {
                    if(data[i].list[j].id==cid){
                        return data[i].list[j].list;
                    }
                }
            }
        }
        return '';
    }