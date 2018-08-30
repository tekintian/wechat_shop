var app = getApp();

Page({
  data: {
    address: '',
    focus: [],
    indicatorDots: true,
    autoplay: true,
    interval: 5000,
    duration: 1000,
    circular: true,
    productData: [],
    proCat:[],
    page: 2,
    index: 2,
    brand:[],
    // 滑动
    imgUrl: [],
    kbs:[],
    lastcat:[]
  },
//跳转商品列表页
listdetail:function(e){
    console.log(e.currentTarget.dataset.title)
    wx.navigateTo({
      url: '../listdetail/listdetail?title='+e.currentTarget.dataset.title,
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

//品牌街跳转商家详情页
jj:function(e){
    var id = e.currentTarget.dataset.id;
    wx.navigateTo({
      url: '../listdetail/listdetail?brandId='+id,
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
    url: '../works/works',
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
//点击加载更多
getMore:function(e){
  var that = this;
  var page = that.data.page;
  wx.request({
      url: app.d.apiUrl + 'Index/getlist',
      method:'post',
      data: {page:page},
      header: {
        'Content-Type':  'application/x-www-form-urlencoded'
      },
      success: function (res) {
        var prolist = res.data.prolist;
        if(prolist==''){
          wx.showToast({
            title: '没有更多数据！',
            duration: 2000
          });
          return false;
        }
        //that.initProductData(data);
        that.setData({
          page: page+1,
          productData:that.data.productData.concat(prolist)
        });
        //endInitData
      },
      fail:function(e){
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
        //that.initProductData(data);
        that.setData({
          focus:focus,
          proCat:procat,
          productData:prolist,
          brand: brand
        });

        that.initAddress();
        //endInitData
      },
      fail:function(e){
        wx.showToast({
          title: '网络异常！',
          duration: 2000
        });
      },
    })

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

  initAddress: function() {
    var that = this;

    wx.getLocation({
      type: 'wgs84', // 默认为 wgs84 返回 gps 坐标，gcj02 返回可用于 wx.openLocation 的坐标
      success: function(res){
        // success
        var longitude=res.longitude
        var latitude=res.latitude

        wx.request({
          url: 'https://api.map.baidu.com/geocoder/v2/?ak=gvKRWUeglHk8WKM6moMOkKeABeFwiK0v&location='+latitude+','+longitude+'&output=json',
          data: {},
          header:{
            'Content-Type':'application/json'
          },
          success: function(res){
            // success
            console.log(res);
            var address=res.data.result.formatted_address;
            that.setData({address:address});
          },
          fail: function() {
            // fail
          },
          complete: function() {
            // complete
          }
        })
      },
      fail: function() {
        // fail
      },
      complete: function() {
        // complete
      }
    })
  },

  doSearch:function(){
    var searchKey = this.data.searchValue;
    wx.navigateTo({
      url: '/pages/search/search',
    })
  }
});
