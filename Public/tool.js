var utily = {};
utily.manage_url = 'http://manage.com';
/**
 * 存储localStorage
 */
utily.setStore = function(name, content) {
    if (!name) return;
    if (typeof content !== 'string') {
        content = JSON.stringify(content);
    }
    window.localStorage.setItem(name, content);
}

/**
 * 获取localStorage
 */
utily.getStore = function(name) {
    if (!name) return;
    return window.localStorage.getItem(name);
}

/**
 * 删除localStorage
 */
utily.removeStore = function(name) {
    if (!name) return;
    window.localStorage.removeItem(name);
}

utily.logout = function(refresh) {
    utily.removeStore('xy_nickname')
    utily.removeStore('xy_headpic')
    if (refresh) {
        location.reload()
    }
}
/**
 * 随机生成指定范围数字 不重复
 * @param  {[type]} 长度   [description]
 * @param  {[type]} 开始下标 [description]
 * @param  {[type]} 结束下标   [description]
 * @return {[type]}       [description]
 */
utily.random = function createRandom(num ,from ,to )
{
    var arr=[];
    for(var i=from;i<=to;i++)
        arr.push(i);
    arr.sort(function(){
        return 0.5-Math.random();
    });
    arr.length=num;
    return arr;
}