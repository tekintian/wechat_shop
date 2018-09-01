// app.js

App ({
  d: {
    hostUrl: 'https://scwhbs.com/api/',
    hostImg: 'https://scwhbs.com/api/',
    hostVideo: 'https://scwhbs.com/api/',
    userId: 1,
    appId:"wx5afa288e60d81f0e",
    appKey:"",
    apiUrl:'https://scwhbs.com/api/',
  },

  globalData: {
    userInfo: null,
    loginCode: null
  },

  onLaunch: function () {
    // 调用API从本地缓存中获取数据
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs);

    // login
    this.getUserInfo();
  },

  getUserInfo: function (cb) {
    var that = this

    if (this.globalData.userInfo) {
      typeof cb == "function" && cb(this.globalData.userInfo)
    } else {
      // 调用登录接口
      wx.login({
        success: function (res) {
          var code = res.code;
          that.globalData.loginCode = code;

          wx.navigateTo({
            url: '/pages/login/login',
          });
        }
      });
    }
  },

  getUserSessionKey: function () {
    // 用户的订单状态
    var that = this;

    wx.request({
      url: that.d.apiUrl + 'Login/getsessionkey',
      method: 'post',
      data: {
        code: that.globalData.loginCode
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },

      success: function (res) {
        // init data
        var data = res.data;

        if (data.status==0) {
          wx.showToast({
            title   : data.err,
            duration: 2000
          });

          return false;
        }

        that.globalData.userInfo['sessionId'] = data.session_key;
        that.globalData.userInfo['openid'] = data.openid;
        that.onLoginUser();
      },

      fail: function (e) {
        wx.showToast({
          title: '网络异常！err:getsessionkeys',
          duration: 2000
        });
      },
    });
  },

  onLoginUser:function () {
    var that = this;
    var user = that.globalData.userInfo;

    wx.request({
      url: that.d.apiUrl + 'Login/authlogin',
      method:'post',
      data: {
      SessionId: user.sessionId,
      gender:user.gender,
      NickName: user.nickName,
      HeadUrl: user.avatarUrl,
      openid:user.openid
      },
      header: {
      'Content-Type':  'application/x-www-form-urlencoded'
      },

      success: function (res) {
        // init data
        var data = res.data.arr;
        var status = res.data.status;

        if (status!=1) {
          wx.showToast({
            title: res.data.err,
            duration: 3000
          });

          return false;
        }

        that.globalData.userInfo['id'] = data.ID;
        that.globalData.userInfo['NickName'] = data.NickName;
        that.globalData.userInfo['HeadUrl'] = data.HeadUrl;
        var userId = data.ID;

        if (!userId) {
          wx.showToast({
            title: '登录失败！',
            duration: 3000
          });

          return false;
        }

        that.d.userId = userId;
      },

      fail: function (e) {
        wx.showToast({
          title: '网络异常！err:authlogin',
          duration: 2000
        });
      },
    });
  },

  getOrBindTelPhone: function (returnUrl) {
    var user = this.globalData.userInfo;

    if (!user.tel) {
      wx.navigateTo({
        url: 'pages/binding/binding'
      });
    }
  },

  onPullDownRefresh: function () {
    wx.stopPullDownRefresh();
  }
});
