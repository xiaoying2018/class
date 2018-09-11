$(function() {

    var result = new Vue({
        el: '#result',
        data: {
            lists :[],
            request:{},
            type:""
        },
        filters: {
            filtertime: function(val){
                if (val) {
                    val = val.substring(0,10)
                    return val;
                }else{
                    return '2018-09-01';
                }
            },
            filtername: function(val){
                if (val) {
                    var s = val.split('@')[0];
                    return s;
                }else{
                    return '小莺出国官方教务';
                }
            },
            filterprice: function(val) {
                var s = val.split('.')[0];
                return s;
            },
            filterFace: function(val) {
                if (val != null) {
                    return val
                } else {
                    return '/Public/random/student/' + Math.floor(Math.random() * 10 + 1) + '.jpg'
                }
            }
        },
        methods: {
            condit: function(key,val){
                this.request[key] = val;
                this.getdata();
            },  
            getdata: function(){
                var _this = this;
                $.ajax({
                    url:"/api/search_all",
                    type:"post",
                    data:_this.request,
                    success:function(_res){
                        if (_res.result) {
                            _this.lists = _res.data
                        }
                    }
                })
            }
        },
        mounted: function() {
            this.request['type'] = getQueryString('type','need')
            this.request['name'] = getQueryString('keywords','need')
            window.setTimeout(function(){
                $("#ssinput").val(getQueryString('keywords','need'))
            },500)
            this.getdata();
            $(".navGroup").addClass("scrollDown");
        }
    })

    function getQueryString(name, needdecoed) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var lh = window.location.search;
        if (needdecoed) {
            lh = decodeURI(window.location.search)
        }
        var r = lh.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }
});