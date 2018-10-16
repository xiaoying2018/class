$(function() {

    var freeclasss = new Vue({
        el: '#freeclasss',
        data: {
            lists:[],
            request:{
                type: 'free'
            }
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
                    url:"/api/opencourse",
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
            $(".s_nav li").eq(3).addClass('active');
            $(".topcondit li,.paixu li,.typeul li").click(function(){
                $(this).addClass("active").siblings().removeClass("active");
            })
        }
    })
});