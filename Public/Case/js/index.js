$(function() {
    var app = new Vue({
        el: '#caseIndex',
        data: {
            lock: true,
            choose: [
                {
                    name: "全部",
                    type: []
                }, {
                    name: "学生学历",
                    type: [{
                        name: "初中"
                    }, {
                        name: "高中"
                    }, {
                        name: "大学/大专在读"
                    }, {
                        name: "SGU英语项目"
                    }, {
                        name: "毕业工作"
                    }]
                }, {
                    name: "服务项目",
                    type: []
                }, {
                    name: "日语水平",
                    type: [{
                        name: "N1"
                    }, {
                        name: "N2"
                    }, {
                        name: "N3"
                    }, {
                        name: "N4"
                    }, {
                        name: "N5"
                    }, {
                        name: "0基础"
                    }]
                }, {
                    name: "录取年份",
                    type: [{
                        name: "2000-2012"
                    }, {
                        name: "2013"
                    }, {
                        name: "2014"
                    }, {
                        name: "2015"
                    }, {
                        name: "2016"
                    }, {
                        name: "2017"
                    }]
                }, {
                    name: "名校报考经验",
                    type: [{
                        name: "东京大学"
                    }, {
                        name: "京都大学"
                    }, {
                        name: "大阪大学"
                    }, {
                        name: "庆应大学"
                    }, {
                        name: "早稻田大学"
                    }
                    // , {
                    //     name: "更多"
                    // }
                    ]
                }
            ],
            s_choose: [],
            // requet: {
            //     PageIndex: 1,
            //     rows: 10
            // },
            lists: [],
            query:{
                education: '',
                programcate: '',
                japan_language: '',
                receive_year: '',
                receive_college: '',
                page: 1,
                rows: 10,
                c: ''
            },
            lock: true,
            r_pic: ''
            // lotwhere: ''
        },
        methods: {
            tabChoose: function(item,event) {
                var _this = this;
                var el = event.target;
                // this.s_choose = item.type;
                this.s_choose = item;

                $('.in_ul li').removeClass('active');
                $(el).addClass("active").siblings().removeClass("active");

                _this.query.c = $(el).text();

                if(item.name == "全部") {
                     window.location.href = "/case"
                }
                //已选中的二级条件高亮
                var receiveYear = _this.getQueryString("receive_year",true) == '2010'?'2000-2012':_this.getQueryString("receive_year",true);
                _this.$nextTick(function() {
                    $('.in_ul li').each(function () {
                        if ($(this).text() == _this.getQueryString("education",true)) {
                            $(this).addClass("active").siblings().removeClass("active");
                        }
                        if ($(this).text() == _this.getQueryString("programcate",true)) {
                            $(this).addClass("active").siblings().removeClass("active");
                        }
                        if ($(this).text() == _this.getQueryString("japan_language",true)) {
                            $(this).addClass("active").siblings().removeClass("active");
                        }
                        if ($(this).text() == receiveYear) {
                            $(this).addClass("active").siblings().removeClass("active");
                        }
                        if ($(this).text() == _this.getQueryString("receive_college",true)) {
                            $(this).addClass("active").siblings().removeClass("active");
                        }
                    })
                })
            },
            search: function (item,event) {
                var _this = this;
                var el = event.target;
                
                if (item.name == '2000-2012') {
                    item.name = '2010'
                }
                //记录选中的二级条件
                if(!$(el).hasClass('active')) {
                    $(el).addClass("active").siblings().removeClass("active");
                
                    if ($(el).data('type') == "学生学历") {
                        _this.query.education= item.name;
                    }else if ($(el).data('type') == "服务项目") {
                        _this.query.programcate= item.name;
                    }else if ($(el).data('type') == "日语水平") {
                        _this.query.japan_language= item.name;
                    }else if ($(el).data('type') == "录取年份") {
                        _this.query.receive_year= item.name;
                    }else if ($(el).data('type') == "名校报考经验") {
                        _this.query.receive_college= item.name;
                    }
                } else {
                    $(el).removeClass("active");
                    if ($(el).data('type') == "学生学历") {
                        _this.query.education= '';
                    }else if ($(el).data('type') == "服务项目") {
                        _this.query.programcate= '';
                    }else if ($(el).data('type') == "日语水平") {
                        _this.query.japan_language= '';
                    }else if ($(el).data('type') == "录取年份") {
                        _this.query.receive_year= '';
                    }else if ($(el).data('type') == "名校报考经验") {
                        _this.query.receive_college= '';
                    }
                }
                window.location.href = "/case?education="+_this.query.education+'&programcate='+_this.query.programcate+'&japan_language='+_this.query.japan_language+'&receive_year='+_this.query.receive_year+'&receive_college='+_this.query.receive_college+'&cat='+_this.query.c;
                
            },
            getallprogramcate: function () {
                var _this = this;
                //获取服务项目
                $.ajax({
                    url:"http://crm.xiaoying.net?m=case&a=getallprogramcate",
                    type:"POST",
                    dataType:"json",
                    success:function(res){
                        var obj = res;
                        // console.log('getallprogramcate',res);
                        var array = [];
                        for (var i = 0; i < _this.choose.length; i++) {
                            if(_this.choose[i].name == '服务项目') {
                                for(let i in obj) {
                                    var o = {}
                                    o.name = obj[i].name;
                                    
                                    array.push(o);
                                }
                                _this.choose[i].type = array;
                                return false
                            }   
                            
                        }
                    }
                })
            },
            getData: function(){
                var _this = this;
                $("#loader").show();
                $.ajax({
                    url:"http://crm.xiaoying.net?m=case&a=frontsearch",
                    type:"POST",
                    data: {
                        //education  学历
                        //programcate  服务项目
                        //japan_language  日语水平
                        //receive_year  录取年份
                        //receive_college  名校报考
                        //page: 当前页
                        //rows: 每页多少条
                        education: _this.query.education,
                        programcate: _this.query.programcate,
                        japan_language: _this.query.japan_language,
                        receive_year: _this.query.receive_year,
                        receive_college: _this.query.receive_college,
                        page: _this.query.page,
                        rows: _this.query.rows
                    },
                    dataType:"json",
                    success:function(res){
                        if (res.status && res.data != null) {
                           
                           //处理图片
                            var array = res.data.list;
                            for (var i = 0; i < res.data.list.length; i++) {
                                if(res.data.list[i].pic) {
                                    array[i].pic = 'http://crm.xiaoying.net/' + res.data.list[i].pic;
                                } else {
                                    var ramand = _this.randomNum(1,10);
                                    array[i].r_pic = ramand;
                                    array[i].pic = '/Public/Case/img/ramandImg/'+array[i].r_pic+'.jpg';
                                }
                            }
                            _this.lists = array;
                            _this.query.page = _this.query.page + 1;

                            _this.$nextTick(function() {
                                //瀑布流初始化
                                if (_this.query.page == 2) {
                                    $grid = $('.list_Ul').masonry({
                                        // columnWidth: 330,
                                        itemSelector: '.item_li',
                                        // gutter: 30,
                                        transitionDuration: 0
                                    });
                                    $grid.masonry('reloadItems');
                                    $grid.masonry('layout');
                                }

                                $('.list_Ul').append($("#model_list").html());
                                $('.list_Ul li').addClass('show');
                                $grid.masonry('reloadItems');
                                $grid.masonry('layout');

                            })


                            _this.lock = true;
                            $("#loader").fadeOut(450);
                        } else {
                            _this.lock = false;
                            $("#loader").fadeOut(450);
                        }
                    }
                })
            },
            randomNum: function (minNum,maxNum) {
                switch(arguments.length){ 
                    case 1: 
                        return parseInt(Math.random()*minNum+1,10); 
                    break; 
                    case 2: 
                        return parseInt(Math.random()*(maxNum-minNum+1)+minNum,10); 
                    break; 
                        default: 
                            return 0; 
                        break; 
                } 
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
            }
        },

        mounted: function() {
            $("#loader").show();
            var _this = this;
            _this.query.c =  _this.getQueryString("cat",true);

            _this.query.education = _this.getQueryString("education",true)?_this.getQueryString("education",true):'';
            _this.query.programcate = _this.getQueryString("programcate",true)?_this.getQueryString("programcate",true):'';
            _this.query.japan_language = _this.getQueryString("japan_language",true)? _this.getQueryString("japan_language",true):'';
            _this.query.receive_year = _this.getQueryString("receive_year",true)?_this.getQueryString("receive_year",true):'';
            _this.query.receive_college = _this.getQueryString("receive_college",true)?_this.getQueryString("receive_college",true):'';

            // 多行溢出兼容IE
            var body=document.getElementsByTagName("body")[0];
            if(typeof body.style.WebkitAnimation!="undefined") {
                // alert("当前浏览器是Webkit内核！");
                // $('.item_li a h2').removeClass('ie');
            }else {
                // alert("当前浏览器不是Webkit内核！");
                $('.item_li a h2').addClass('ie');
            }
            _this.getallprogramcate();

            
            //初始化选择分类

            $('.c_ul li').each(function () {
                if ($(this).text() == _this.getQueryString("cat",true)) {
                    $(this).addClass("active").siblings().removeClass("active");
                    _this.s_choose = _this.choose[$(this).index()];

                    // console.log('_this.s_choose',_this.s_choose)
                    if ($(this).text() == '服务项目') {

                        $.ajax({
                            url:"http://crm.xiaoying.net?m=case&a=getallprogramcate",
                            type:"POST",
                            dataType:"json",
                            success:function(res){
                                var obj = res;
                                // console.log('getallprogramcate',res);
                                var array = [];
                                for (var i = 0; i < _this.choose.length; i++) {
                                    if(_this.choose[i].name == '服务项目') {
                                        for(let i in obj) {
                                            var o = {}
                                            o.name = obj[i].name;
                                            
                                            array.push(o);
                                        }
                                        _this.choose[i].type = array;

                                         _this.$nextTick(function() {
                                            // console.log($('.in_ul li').text())
                                             $('.in_ul li').each(function () {
                                                if ($(this).text() == _this.getQueryString("programcate",true)) {
                                                    $(this).addClass("active").siblings().removeClass("active");
                                                }
                                            })
                                        })
                                        return false
                                    }   
                                    
                                }
                            }
                        })
                            
                        
                    } else {
                        var receiveYear = _this.getQueryString("receive_year",true) == '2010'?'2000-2012':_this.getQueryString("receive_year",true);
                         _this.$nextTick(function() {
                            $('.in_ul li').each(function () {

                                if ($(this).text() == _this.getQueryString("education",true)) {
                                    $(this).addClass("active").siblings().removeClass("active");
                                }
                                if ($(this).text() == _this.getQueryString("japan_language",true)) {
                                    $(this).addClass("active").siblings().removeClass("active");
                                }

                                if ($(this).text() == receiveYear) {
                                    $(this).addClass("active").siblings().removeClass("active");
                                }
                                if ($(this).text() == _this.getQueryString("receive_college",true)) {
                                    $(this).addClass("active").siblings().removeClass("active");
                                }

                            })

                        })
                    }
                }
            })
            //初始化案例列表

            _this.getData();

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
    });
})