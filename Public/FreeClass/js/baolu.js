$(function() {

    var baolu = new Vue({
        el: '#baolu',
        data: {
            request:{
                category:"",
                sort_by_people_num:"",
            },
            lists:[],
            lock: true

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
                    url:"/api/baoluban",
                    data:_this.request,
                    type:"post",
                    success:function(_res){
                        if (_res.result) {
                            _this.lists = _res.data.banji;

                            
                             _this.$nextTick(function() {

                                $('.list_Ul').append($("#ullist").html());
                               
                            })

                            _this.lock = true;
                        } else {
                            _this.lock = false;
                        }

                    }
                })
            }
        },
        mounted: function() {
            var _this = this;
            _this.getdata();
            $(".s_nav li").eq(6).addClass('active');

            $(".topcondit li,.paixu li").click(function(){
                $(this).addClass("active").siblings().removeClass("active");
            })

            $(window).scroll(function() {
                var scrollTop = $(this).scrollTop();
                var scrollHeight = $(document).height();
                var windowHeight = $(this).height();
                if (parseInt(scrollTop + windowHeight) == scrollHeight && _this.lock) {
                    _this.lock = false;
                    // console.log('scroll',_this.query.page);
                    _this.getData();
                }
            });

        }
    })
});