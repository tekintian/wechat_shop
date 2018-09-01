var app = getApp();

Page ({
  data: {
    canIUse: wx.canIUse('button.open-type.getUserInfo')
  },

  onLoad: function() {
    // 查看是否授权
    wx.getSetting({
      success: function(res){
        if (res.authSetting['scope.userInfo']) {
          // 已经授权，可以直接调用 getUserInfo 获取头像昵称
          wx.getUserInfo({
            success: function(res) {
              console.log(res.userInfo)
            }
          })
        }
      }
    })
  },

  bindGetUserInfo: function(e) {
    console.log(e.detail.userInfo)

    app.globalData.userInfo = e.detail.userInfo;
    typeof cb == "function" && cb(app.globalData.userInfo);

    // get user sessionKey
    // get sessionKey
    app.getUserSessionKey();

    wx.switchTab({
      url: '../index/index'
    });
  }
})
