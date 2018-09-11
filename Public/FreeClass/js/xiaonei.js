$(function() {

    var xiaonei = new Vue({
        el: '#xiaonei',
        data: {
            lists:[],
            request:{}
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
                    url:"/api/xiaoneikaocourse",
                    type:"post",
                    data:_this.request,
                    success:function(_res){
                        if (_res.result) {
                            _this.lists = _res.data.open_course
                        }
                    }
                })
            }
        },
        mounted: function() {
            this.getdata();
            $(".s_nav li").eq(5).addClass('active');
            $(".topcondit li,.paixu li").click(function(){
                $(this).addClass("active").siblings().removeClass("active");
            })
        }
    })
});