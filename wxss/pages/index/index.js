var app = getApp();
var QQMapWX = require('../../utils/qqmap-wx-jssdk.js');
var qqmapwx;

Page ({
  data: {
    address       : '',
    latitude      : null,
    longitude     : null,
    focus         : [],
    indicatorDots : true,
    autoplay      : true,
    interval      : 5000,
    duration      : 1000,
    distance      : true,
    productData   : [],
    proCat        : [],
    page          : 2,
    index         : 2,
    brand         : [],
    // 滑动
    imgUrl        : [],
    kbs           : [],
    lastcat       : []
  },

  // 跳转商品列表页
  listdetail: function (e) {
    console.log(e.currentTarget.dataset.title)

    wx.navigateTo({
      url: '/pages/listdetail/listdetail?title='+e.currentTarget.dataset.title,

      success: function(res){
        // success
      },

      fail: function() {
        // fail
      },

      complete: function() {
        // complete
      }
    })
  },

  // 品牌街跳转商家详情页
  jj: function (e) {
    var id = e.currentTarget.dataset.id;

    wx.navigateTo({
      url: '/pages/listdetail/listdetail?brandId='+id,

      success: function(res){
        // success
      },

      fail: function() {
        // fail
      },

      complete: function() {
        // complete
      }
    })
  },

  tian: function (e) {
    var id = e.currentTarget.dataset.id;

    wx.navigateTo({
      url: '/pages/works/works',

      success: function (res) {
        // success
      },

      fail: function () {
        // fail
      },

      complete: function () {
        // complete
      }
    })
  },

  // 点击加载更多
  getMore: function (e) {
    var that = this;
    var page = that.data.page;

    wx.request({
      url: app.d.apiUrl + 'Index/getlist',
      method:'post',
      data: {
        page: page
      },
      header: {
        'Content-Type':  'application/x-www-form-urlencoded'
      },

      success: function (res) {
        var prolist = res.data.prolist;

        if (prolist=='') {
          wx.showToast({
            title: '没有更多数据！',
            duration: 2000
          });

          return false;
        }

        //that.initProductData(data);
        that.setData({
          page        : page+1,
          productData : that.data.productData.concat(prolist)
        });

        //endInitData
      },

      fail: function (e) {
        wx.showToast({
          title: '网络异常！',
          duration: 2000
        });
      }
    })
  },

  changeIndicatorDots: function (e) {
    this.setData({
      indicatorDots: !this.data.indicatorDots
    })
  },

  changeAutoplay: function (e) {
    this.setData({
      autoplay: !this.data.autoplay
    })
  },

  intervalChange: function (e) {
    this.setData({
      interval: e.detail.value
    })
  },

  durationChange: function (e) {
    this.setData({
      duration: e.detail.value
    })
  },

  onLoad: function (options) {
    var that = this;

    qqmapwx = new QQMapWX({
      key: 'KSSBZ-LL66X-7LV4Z-77M4Z-USSIS-H6FXT'
    });

    if (!that.data.latitude || !that.data.longitude) {
      wx.getLocation({
        type: 'gcj02',

        success: function(res) {
          that.setData({
            latitude  : res.latitude,
            longitude : res.longitude
          });

          console.log(res);

          // 杨陵区政府 {lat: 34.27221, lng: 108.08455}

          qqmapwx.calculateDistance({
            to  : [
              {
                latitude  : 34.27221,
                longitude : 108.08455
              }
            ],

            success: function(res) {
              console.log(res);

              if (res.result.elements.distance > 5000) {
                that.setData({
                  distance  : false
                });
              } else {
                that.setData({
                  distance  : true
                });
              }

              console.log(that.data);
            },

            fail: function(res) {
              console.log(res);
            },

            complete: function(res) {
              console.log(res);
            }
          });

          that.initAddr();
        },

        fail: function() {
        },

        complete: function() {
        }
      });
    } else {
      that.initAddr();
    }

    wx.request({
      url: app.d.apiUrl + 'Index/index',
      method:'post',
      data: {},
      header: {
        'Content-Type':  'application/x-www-form-urlencoded'
      },

      success: function (res) {
        var focus = res.data.focus;
        var procat = res.data.procat;
        var prolist = res.data.prolist;
        var brand = res.data.brand;

        that.setData({
          focus:focus,
          proCat:procat,
          productData:prolist,
          brand: brand
        });
      },

      fail:function(e){
        wx.showToast({
          title: '网络异常！',
          duration: 2000
        });
      },
    })
  },

  initAddr: function() {
    var that = this;

    if (that.data.latitude != null && that.data.longitude != null) {
      qqmapwx.reverseGeocoder({
        location: {
          latitude  : that.data.latitude,
          longitude : that.data.longitude
        },

        success: function(res) {
          console.log(res);

          app.globalData.province = res.result.address_component.province;
          app.globalData.city = res.result.address_component.city;

          that.setData({address: res.result.address_reference.landmark_l2.title});
        },

        fail: function(res) {
          console.log(res);
        },

        complete: function(res) {
          console.log(res);
        }
      });
    }
  },

  onShareAppMessage: function () {
    return {
      title: '送菜娃商城',
      path: '/pages/index/index',

      success: function(res) {
        // 分享成功
      },

      fail: function(res) {
        // 分享失败
      }
    }
  },

  doSearch: function() {
    wx.navigateTo({
      url: '/pages/search/search',
    })
  },

  doAddress: function() {
    wx.navigateTo({
      url: '/pages/addr_search/addr_search',
    })
  }
});
