// pages/search/search.js

var app = getApp();
var QQMapWX = require('../../utils/qqmap-wx-jssdk.js');
var qqmapwx;

Page ({
  data: {
    focus     : true,
    addrValue : '',
    searchData: []
  },

  onLoad: function(options) {
    var that = this;

    qqmapwx = new QQMapWX({
      key: 'KSSBZ-LL66X-7LV4Z-77M4Z-USSIS-H6FXT'
    });
  },

  doSearch: function() {
    var that = this;
    that.data.searchData.length = 0;

    qqmapwx.getSuggestion({
        keyword   : this.data.addrValue,
        region    : app.globalData.province + app.globalData.city,
        region_fix: 1,

        success: function(res) {
          that.setData({
            searchData : res.data,
          });

          console.log(that.data.searchData);
        },

        fail: function(res) {
          console.log(res);
        },

        complete: function(res) {
          console.log(res);
        }
    });
  },

  searchValueInput: function(e) {
    var value = e.detail.value;

    this.setData({
      addrValue : value,
    });
  }
});