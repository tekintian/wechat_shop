// pages/order/detail.js

var app = getApp();

Page ({
  data: {
    orderId   : 0,
    orderData : {},
    proData   : [],
  },

  onLoad: function(options) {
    this.setData({
      orderId: options.orderId,
    })

    this.loadProductDetail();
  },

  loadProductDetail: function() {
    var that = this;

    wx.request({
      url   : app.d.apiUrl + 'Order/order_details',
      method: 'post',
      data  : {
        order_id: that.data.orderId,
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },

      success: function (res) {
        var status = res.data.status;

        if (status==1) {
          var pro = res.data.pro;
          var ord = res.data.ord;

          that.setData({
            orderData : ord,
            proData   : pro
          });
        } else {
          wx.showToast({
            title   : res.data.err,
            duration: 2000
          });
        }
      },

      fail: function () {
        // fail
        wx.showToast({
          title   : '网络异常！',
          duration: 2000
        });
      }
    });
  },

  searchValueInput: function (e) {
    var value = e.detail.value;

    this.setData({
      searchValue: value,
    });

    if (!value && this.data.productData.length == 0) {
      this.setData({
        hotKeyShow: false,
        historyKeyShow: false,
      });
    }
  },

})