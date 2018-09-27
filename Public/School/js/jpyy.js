$(function() {
    var result = new Vue({
        el: '#jpyy',
        data: {
            area: [],
            city: [],
            request:{
                getjapschool:"",
                page:1,
                limit:10,
                school_type:1,
                xingzhi_name:"",
                rank:"",
                nowcid:"",
                schoolname:""
            },
            count:"",
            lists:[],
            tuition: [{
                name: "60万円以下",
                id: "1"
            }, {
                name: "60-70万円",
                id: "2"
            }, {
                name: "70-80万円",
                id: "3"
            }, {
                name: "80万円以上",
                id: "4"
            }]
        },
        methods: {
            getarea: function(_id) {
                var _data = {};
                if (_id) {
                    _data = {
                        id : _id
                    }
                }
                var _this = this;
                $.ajax({
                    url:"http://manage.com/getarea",
                    type:"post",
                    data:_data,
                    success:function(res){
                        if (res.status) {
                            if (!_id) {
                                _this.area = res.data;
                            }else{
                                _this.city = res.data;
                            }
                        }
                    }
                })
                console.log("xxx", utily.manage_url);
            },
            change: function(key,val){
                console.log(key,val);
            },
            getdata: function(){
                var _this = this;
                $.ajax({
                    url:"http://manage.com/getjapschool",
                    type:"get",
                    data: _this.request,
                    success:function(res){
                        if (res.code == 0) {
                            _this.lists = res.data;
                            _this.count = res.count
                            $("body, html").animate({
                                scrollTop: $(".resultPart").offset().top - 130
                            }, 200)
                            $("#jqPaginator").html("");
                            if (res.count > 0) {
                                $('#jqPaginator').jqPaginator({
                                    totalCounts: parseInt(res.count),
                                    pageSize: _this.request.limit,
                                    visiblePages: 7,
                                    currentPage: _this.request.page,
                                    first: "<a>首页</a>",
                                    last: "<a>末页</a>",
                                    prev: "<a>上一页</a>",
                                    page: "<a class='page'>{{page}}</a>",
                                    next: "<a>下一页</a>",
                                    onPageChange: function(num, type) {
                                        if (type == "change") {
                                            _this.request.page = num;
                                            _this.getdata();
                                        }
                                    }
                                })
                            }
                        }
                        console.log("xxx",res);
                    }
                })
            }
        },
        mounted: function() {
            var _this = this;
            this.getarea();
            this.getdata();
            $("#area,#city").select2({
                width: "300px",
            })
            $(".selectcity .city_item").eq(0).addClass("active")

            $("#area").on("select2:select",function(e){
                _this.getarea($("#area").val());
            });
        }
    });
    var config = {
        reset: true,
        delay: 'always',
        mobile: true,
    }
    window.sr = new scrollReveal(config);
})