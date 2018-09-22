var app = getApp()

Page ({
  data: {
    current : 0,
    shopList: [],
    ptype   : '',
    title   : '送菜娃商城',
    page    : 2,
    catId   : 0,
    brandId : 0
  },

  showModal: function () {
    // 显示遮罩层
    var animation = wx.createAnimation({
      duration      : 200,
      timingFunction: "linear",
      delay         : 0
    })

    this.animation = animation
    animation.translateY(300).step()

    this.setData({
      animationData   : animation.export(),
      showModalStatus : true
    })

    setTimeout(function () {
      animation.translateY(0).step()

      this.setData({
        animationData : animation.export()
      })
    }.bind(this), 200)
  },

  hideModal: function () {
    // 隐藏遮罩层
    var animation = wx.createAnimation({
      duration      : 200,
      timingFunction: "linear",
      delay         : 0
    })

    this.animation = animation
    animation.translateY(300).step()

    this.setData({
      animationData : animation.export(),
    })

    setTimeout(function () {
     animation.translateY(0).step()

     this.setData({
        animationData   : animation.export(),
        showModalStatus : false
     })
    }.bind(this), 200)
  },

  // 点击加载更多
  getMore: function(e) {
    var that = this;

    var page = that.data.page;

    wx.request({
      url   : app.d.apiUrl + 'Product/get_more',
      method: 'post',
      data  : {
        page    : page,
        ptype   : that.data.ptype,
        cat_id  : that.data.catId,
        brand_id: that.data.brandId
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },

      success: function (res) {
        var prolist = res.data.pro;

        if (prolist=='') {
          wx.showToast({
            title: '没有更多数据！',
            duration: 2000
          });

          return false;
        }

        that.setData({
          page: page+1,
          shopList:that.data.shopList.concat(prolist)
        });
      },

      fail: function(e) {
        wx.showToast({
          title: '网络异常！',
          duration: 2000
        });
      }
    })
  },

  onLoad: function (options) {
    var objectId = options.title;

    // 更改头部标题
    wx.setNavigationBarTitle({
      title : objectId,

      success: function() {
      },
    });

    // 页面初始化 options为页面跳转所带来的参数
    var cat_id = options.cat_id;
    var ptype = options.ptype;
    var brandId = options.brandId;
    var that = this;

    if (!ptype) {
      ptype = that.data.ptype;
    }

    if (!brandId) {
      brandId = that.data.brandId;
    }

    that.setData({
      ptype: ptype,
      catId: cat_id,
      brandId: brandId
    })

    // ajax请求数据
    wx.request({
      url   : app.d.apiUrl + 'Product/lists',
      method: 'post',
      data  : {
        cat_id  : cat_id,
        ptype   : ptype,
        brand_id: brandId
      },
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },

      success: function(res) {
        var shoplist = res.data.pro;

        that.setData({
          shopList: shoplist
        })
      },

      error: function(e) {
        wx.showToast({
          title: '网络异常！',
          duration: 2000
        });
      }
    })
  },

  // 详情页跳转
  lookdetail: function (e) {
    console.log(e)
    var lookid = e.currentTarget.dataset;
    console.log(e.currentTarget.dataset);

    wx.navigateTo({
      url: "/pages/index/detail?id=" + lookid.id
    })
  },

  switchSlider: function (e) {
    this.setData({
      current: e.target.dataset.index
    })
  },

  changeSlider: function (e) {
    this.setData({
      current: e.detail.current
    })
  },

  onReady: function () {
    // 页面渲染完成
  },

  onShow: function () {
    // 页面显示
  },

  onHide: function () {
    // 页面隐藏
  },

  onUnload: function () {
    // 页面关闭
  }
})
