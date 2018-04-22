var app = getApp();
// pages/order/downline.js
Page({
  data:{
    orderId:0,
    reason:'',
    remark:'',
    imgUrl:'',
  },
  onLoad:function(options){
    this.setData({
      orderId: options.orderId,
    });
  },
  submitReturnData:function(){
    //console.log(this.data);
    //数据验证
    if(!this.data.remark){
      wx.showToast({
        title: '请填写退款原因',
        icon: 'success',
        duration: 2000
      });
      return;
    }
    // if(!this.data.remark){
    //   wx.showToast({
    //     title: '请填写退货描述',
    //     icon: 'success',
    //     duration: 2000
    //   });
    //   return;
    // }
    var that = this;
    wx.request({
      url: app.d.ceshiUrl + '/Api/Order/orders_edit',
      method:'post',
      data: {
        id: that.data.orderId,
        type:'refund',
        back_remark:that.data.remark,
        //imgUrl:that.data.imgUrl,
      },
      header: {
        'Content-Type':  'application/x-www-form-urlencoded'
      },
      success: function (res) {
        //--init data        
        var status = res.data.status;
        if(status == 1){
          wx.showToast({
            title: '您的申请已提交审核！',
            duration: 2000
          });
          // wx.navigateTo({
          //   url: '/pages/user/dingdan?currentTab=4',
          // });
        }else{
          wx.showToast({
            title: res.data.err,
            duration: 2000
          });
        }
      },
    });

  },
  reasonInput:function(e){
    this.setData({
      reason: e.detail.value,
    });
  },
  remarkInput:function(e){
    this.setData({
      remark: e.detail.value,
    });
  },
  uploadImgs:function(){

    wx.chooseImage({
      success: function(res) {
        console.log(res);
        var tempFilePaths = res.tempFilePaths
        wx.uploadFile({
          url: 'http://example.weixin.qq.com/upload', //仅为示例，非真实的接口地址
          filePath: tempFilePaths[0],
          name: 'file',
          formData:{
            'user': 'test'
          },
          success: function(res){
            var data = res.data
            //do something
          }
        })
      }
    });
  },
})