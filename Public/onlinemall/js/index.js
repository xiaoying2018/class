$(function() {
    var mallindex = new Vue({
        el: '#onlinemall',
        data: {
            lists:[],
            count:"",
            requset:{
                page_size: 12,
                page:1,
                cate:"",
                name:""
            }
        },
        filters: {
             filterdesc: function(val){
                var _desc = val;
                if (val == '') {
                    _desc = '小莺出国自2010年开始为小莺学员提供全面的留学产品以及服务。'
                }
                return _desc;
             }
        },
        methods: {
            change(key,val){
                this.lists = [];
                this.requset.page = 1;
                this.requset[key] = val;
                this.getdata();
            },
            getdata(){
                var _this = this;
                $.ajax({
                    url:"/api/getgoods",
                    type:"post",
                    data: this.requset,
                    success:function(res){
                        if (res.status) {
                            _this.lists = res.data;
                            _this.count = res.count;
                            $("#jqPaginator").html("");
                            if (res.count > 0) {
                                $('#jqPaginator').jqPaginator({
                                    totalCounts: parseInt(res.count),
                                    pageSize: _this.requset.page_size,
                                    visiblePages: 7,
                                    currentPage: _this.requset.page,
                                    first: "<a>首页</a>",
                                    last: "<a>末页</a>",
                                    prev: "<a>上一页</a>",
                                    page: "<a class='page'>{{page}}</a>",
                                    next: "<a>下一页</a>",
                                    onPageChange: function(num, type) {
                                        if (type == "change") {
                                            _this.requset.page = num;
                                            _this.getdata();
                                        }
                                    }
                                })
                            }
                        }
                    }
                })
            }
        },
        mounted: function() {
            this.getdata();

            $("#area,#city").select2({
                width: "300px",
            })

            $(".selectcity .city_item").eq(0).addClass("active")

            $(document).on('click', '.listul .item', function() {
                $(this).addClass("active").siblings().removeClass("active");
            });

		    var config = {
		        reset: true,
		        delay: 'always',
		        mobile: true,
		    }
		    window.sr = new scrollReveal(config);
        }
    })


})