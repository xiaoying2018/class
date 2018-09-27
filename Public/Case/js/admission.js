$(function() {
    var app = new Vue({
        el: '#caseAdmission',
        data: {
            lock: true,
            choose: [{
                name: "全部",
                item: []
            }, {
                name: "录取大学",
                item: [
                    // {
                    //     name:"全部",
                    //     value:"",
                    //     classname:"active"
                    // },
                    {
                        name: "东京大学",
                        value: ""
                    },
                    {
                        name: "京都大学",
                        value: ""
                    },
                    {
                        name: "北海道大学",
                        value: ""
                    },
                    {
                        name: "大阪大学",
                        value: ""
                    },
                    {
                        name: "一桥大学",
                        value: ""
                    },
                    {
                        name: "广岛大学",
                        value: ""
                    },
                    {
                        name: "名古屋大学",
                        value: ""
                    }
                ]
            }, {
                name: "录取年份",
                item: [
                    // {
                    //     name:"全部",
                    //     value:"",
                    //     classname:"active"
                    // },
                    {
                        name: "2000-2012",
                        value: ""
                    },
                    {
                        name: "2013",
                        value: ""
                    },
                    {
                        name: "2014",
                        value: ""
                    },
                    {
                        name: "2015",
                        value: ""
                    },
                    {
                        name: "2016",
                        value: ""
                    },
                    {
                        name: "2017",
                        value: ""
                    },
                    {
                        name: "2018",
                        value: ""
                    }
                ]
            }, {
                name: "录取类型",
                item: [
                    // {
                    //     name:"全部",
                    //     value:"",
                    //     classname:"active"
                    // },
                    {
                        name: "语言学校",
                        value: ""
                    },
                    {
                        name: "高中/专门学校",
                        value: ""
                    },
                    {
                        name: "本科",
                        value: ""
                    },
                    {
                        name: "大学别科/预科",
                        value: ""
                    },
                    {
                        name: "大学院修士",
                        value: ""
                    }
                ]
            }, {
                name: "热门标签",
                item: [
                    // {
                    //     name:"全部",
                    //     value:"",
                    //     classname:"active"
                    // },
                    {
                        name: "跨专业申请",
                        value: ""
                    },
                    {
                        name: "零中介",
                        value: ""
                    },
                    {
                        name: "专升本硕",
                        value: ""
                    },
                    {
                        name: "SGU/G30英文项目",
                        value: ""
                    }
                ]
            }, {
                name: "专业分类",
                item: [
                    // {
                    //     name:"全部",
                    //     value:"",
                    //     classname:"active"
                    // },
                    {
                        name: "日本语",
                        value: ""
                    },
                    {
                        name: "经济金融",
                        value: ""
                    },
                    {
                        name: "经营商科",
                        value: ""
                    },
                    {
                        name: "社会学 ",
                        value: ""
                    },
                    {
                        name: "机械工程 ",
                        value: ""
                    },
                    {
                        name: "建筑设计",
                        value: ""
                    },
                    {
                        name: "视觉传达",
                        value: ""
                    },
                    {
                        name: "理工类",
                        value: ""
                    }
                ]
            }],
            s_choose: [],
            // haschoose:[],
            lists: [],
            query: {
                lotwhere: '',
                year: '',
                xueli: '',
                hottag: '',
                college: '',
                page: 1,
                rows: 19,
                c: '',
            },
            lock: true,
            pageCount: 0
        },
        methods: {
            tabChoose: function(item, event) {
                var _this = this;
                var el = event.target;

                $('.in_ul li').removeClass('active');
                $(el).addClass("active").siblings().removeClass("active");
                _this.s_choose = item;
                _this.query.c = $(el).text();

                if (item.name == "全部") {
                    window.location.href = "/case/admission"
                }
                var receiveYear = _this.getQueryString("year", true) == '2010' ? '2000-2012' : _this.getQueryString("year", true);
                _this.$nextTick(function() {
                    $('.in_ul li').each(function() {
                        if ($(this).text() == _this.getQueryString("lotwhere", true)) {
                            $(this).addClass("active").siblings().removeClass("active");
                        }
                        if ($(this).text() == receiveYear) {
                            $(this).addClass("active").siblings().removeClass("active");
                        }
                        if ($(this).text() == _this.getQueryString("xueli", true)) {
                            $(this).addClass("active").siblings().removeClass("active");
                        }
                        if ($(this).text() == _this.getQueryString("hottag", true)) {
                            $(this).addClass("active").siblings().removeClass("active");
                        }
                        if ($(this).text() == _this.getQueryString("college", true)) {
                            $(this).addClass("active").siblings().removeClass("active");
                        }
                    })
                })
            },
            search: function(item, el) {
                var _this = this;
                var el = event.target;

                if (item.name == '2000-2012') {
                    item.name = '2010'
                }
                //记录选中的二级条件
                if (!$(el).hasClass('active')) {
                    $(el).addClass("active").siblings().removeClass("active");

                    if ($(el).data('type') == "录取大学") {
                        _this.query.lotwhere = item.name;
                    } else if ($(el).data('type') == "录取年份") {
                        _this.query.year = item.name;
                    } else if ($(el).data('type') == "录取类型") {
                        _this.query.xueli = item.name;
                    } else if ($(el).data('type') == "热门标签") {
                        _this.query.hottag = item.name;
                    } else if ($(el).data('type') == "专业分类") {
                        _this.query.college = item.name;
                    }
                } else {
                    $(el).removeClass("active");
                    if ($(el).data('type') == "录取大学") {
                        _this.query.lotwhere = '';
                    } else if ($(el).data('type') == "录取年份") {
                        _this.query.year = '';
                    } else if ($(el).data('type') == "录取类型") {
                        _this.query.xueli = '';
                    } else if ($(el).data('type') == "热门标签") {
                        _this.query.hottag = '';
                    } else if ($(el).data('type') == "专业分类") {
                        _this.query.college = '';
                    }
                }
                window.location.href = "/case/admission?lotwhere=" + _this.query.lotwhere + '&year=' + _this.query.year + '&xueli=' + _this.query.xueli + '&hottag=' + _this.query.hottag + '&college=' + _this.query.college + '&cat=' + _this.query.c;
            },
            // ,
            // deltag1: function(item) {
            //     var _this = $(item.target);
            //     _this.addClass("disappear");
            //     setTimeout(function(){
            //         _this.remove();
            //     },400)
            // },
            getData: function() {
                var _this = this;
                $("#loader").show();
                $.ajax({
                    url: "http://crm.xiaoying.net?m=casedata&a=search",
                    type: "POST",
                    data: {
                        lotwhere: _this.query.lotwhere,
                        year: _this.query.year,
                        xueli: _this.query.xueli,
                        hottag: _this.query.hottag,
                        college: _this.query.college,
                        page: _this.query.page,
                        rows: _this.query.rows
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status && res.data != null) {

                            /**
                                lotwhere  录取大学
                                year  录取年份
                                xueli 录取类型
                                hottag  热门标签
                                college  专业
                            **/
                            _this.pageCount = res.data.total;
                            // console.log(_this.pageCount);

                            _this.lists = res.data.list;
                            $('.pageBox').pagination({
                                pageCount: _this.pageCount,
                                showData: _this.query.rows,
                                current: _this.query.page,
                                homePage: '首页',
                                endPage: '末页',
                                isHide: true,
                                count: 2,
                                activeCls: 'active',
                                coping: true,
                                callback: function(api) {

                                    if (_this.query.page >= 2) {
                                        _this.query.page = api.getCurrent();
                                        _this.getData();
                                    }
                                }
                            });
                            _this.query.page = _this.query.page + 1;
                            _this.$nextTick(function() {

                                //瀑布流初始化
                                // if (_this.query.page == 2) {


                                //     $grid = $('.list_body').masonry({
                                //         // columnWidth: 330,
                                //         itemSelector: '.list_item',
                                //         // gutter: 30,
                                //         transitionDuration: 0
                                //     });
                                //     $grid.masonry('reloadItems');
                                //     $grid.masonry('layout');
                                // }

                                $('.list_body').html($(".list_body_m").html());
                                $('.list_body li').addClass('show');
                                // $grid.masonry('reloadItems');
                                // $grid.masonry('layout');
                            });
                            _this.lock = true;
                            $("#loader").fadeOut(450);
                        } else {
                            _this.lock = false;
                            $("#loader").fadeOut(450);
                        }
                    }
                })
            },
            getQueryString: function(name, needdecoed) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
                var lh = window.location.search;
                if (needdecoed) {
                    lh = decodeURI(window.location.search)
                }
                var r = lh.substr(1).match(reg);
                if (r != null) return unescape(r[2]);
                return null;
            },
            // addtag:function(item,el){
            //     item.classname='active';
            //     console.log("xxx",item);
            //     this.haschoose.push(item)
            // },
            // deltag: function(item) {
            //     var _this = $(item.target);
            //     _this.addClass("disappear");
            //     setTimeout(function(){
            //         _this.remove();
            //     },400)
            // }
        },
        mounted: function() {
            var _this = this;
            _this.query.c = _this.getQueryString("cat", true);

            _this.query.lotwhere = _this.getQueryString("lotwhere", true) ? _this.getQueryString("lotwhere", true) : '';
            _this.query.year = _this.getQueryString("year", true) ? _this.getQueryString("year", true) : '';
            _this.query.xueli = _this.getQueryString("xueli", true) ? _this.getQueryString("xueli", true) : '';
            _this.query.hottag = _this.getQueryString("hottag", true) ? _this.getQueryString("hottag", true) : '';
            _this.query.college = _this.getQueryString("college", true) ? _this.getQueryString("college", true) : '';
            _this.query.page = _this.getQueryString("page", true) ? _this.getQueryString("page", true) : 1;
            //初始化选择分类

            $('.c_ul li').each(function() {
                if ($(this).text() == _this.getQueryString("cat", true)) {
                    $(this).addClass("active").siblings().removeClass("active");
                    _this.s_choose = _this.choose[$(this).index()];

                    // console.log('_this.s_choose',_this.s_choose)
                    var receiveYear = _this.getQueryString("year", true) == '2010' ? '2000-2012' : _this.getQueryString("year", true);
                    _this.$nextTick(function() {
                        $('.in_ul li').each(function() {
                            if ($(this).text() == _this.getQueryString("lotwhere", true)) {
                                $(this).addClass("active").siblings().removeClass("active");
                            }
                            if ($(this).text() == receiveYear) {
                                $(this).addClass("active").siblings().removeClass("active");
                            }
                            if ($(this).text() == _this.getQueryString("xueli", true)) {
                                $(this).addClass("active").siblings().removeClass("active");
                            }
                            if ($(this).text() == _this.getQueryString("hottag", true)) {
                                $(this).addClass("active").siblings().removeClass("active");
                            }
                            if ($(this).text() == _this.getQueryString("college", true)) {
                                $(this).addClass("active").siblings().removeClass("active");
                            }
                        })

                    })
                }
            })

            _this.getData();

            //热门活动轮播初始化
            var mySwiper = new Swiper('.swiper-container', {
                pagination: '.pagination',
                loop: true,
                // autoplay: 3000,
                autoplayDisableOnInteraction: false,
                grabCursor: true,
                paginationClickable: true
            })
            $('.arrow-left').on('click', function(e) {
                e.preventDefault()
                mySwiper.swipePrev()
            });
            $('.arrow-right').on('click', function(e) {
                e.preventDefault()
                mySwiper.swipeNext()
            });

        }
    });
})