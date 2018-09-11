$(function() {

    var download = new Vue({
        el: '#download',
        data: {
            cate: [],
            lists: [],
            count: "",
            requset: {
                limit_num: 10,
                page: 1,
                category: '',
                name:''
            },
            name_input:""
        },
        filters: {},
        methods: {
            condit: function(key, val) {
                this.requset.page = 1
                if (key == 'name') {
                    this.requset[key] = this.name_input;
                }else{
                    this.requset[key] = val;
                }
                this.getdata();
            },
            getcate: function() {
                var _this = this;
                $.ajax({
                    url: "/api/matecate",
                    type: "post",
                    success: function(res) {
                        if (res.result) {
                            _this.cate = res.data;
                        }
                    }

                })
            },
            getdata: function() {
                var _this = this;
                $.ajax({
                    url: "/api/mate",
                    type: "post",
                    data: _this.requset,
                    success: function(res) {
                        if (res.result) {
                            $("#jqPaginator").hide();
                            _this.lists = [];
                            _this.count = 0;

                            if (res.data.mates && res.data.mates.length > 0) {
                                $("#jqPaginator").show();
                                _this.lists = res.data.mates;
                                _this.count = parseInt(res.data.count);
                                $('#jqPaginator').jqPaginator({
                                    totalCounts: parseInt(res.data.count),
                                    pageSize: _this.requset.limit_num,
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
            },
            downloadfile: function(n) {}
        },
        mounted: function() {
            if (getQueryString('keywords')) {
                this.name_input = getQueryString('keywords','need')
                this.condit('name',getQueryString('keywords','need'))
            }
            this.getcate();
            this.getdata();
            $(document).on('click', '.listDv .item', function() {
                $(".listDv .item").removeClass("active");
                $(this).addClass("active");
            })
            $(".s_nav li").eq(7).addClass('active');

        }
    })

    $(document).on('click', '.btn_dw', function() {
        if (utily.getStore('xy_nickname')) {
            var _path = $(this).attr("data-file");
            downloadFile(_path);
        }else{
            utily.setStore('xy_logined_href',location.href)
            window.location.href = '/user/#/login?a=login';
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

    window.downloadFile = function(sUrl) {
        //iOS devices do not support downloading. We have to inform user about this.
        if (/(iP)/g.test(navigator.userAgent)) {
            alert('Your device does not support files downloading. Please try again in desktop browser.');
            return false;
        }

        //If in Chrome or Safari - download via virtual link click
        if (window.downloadFile.isChrome || window.downloadFile.isSafari) {
            //Creating new link node.
            var link = document.createElement('a');
            link.href = sUrl;

            if (link.download !== undefined) {
                //Set HTML5 download attribute. This will prevent file from opening if supported.
                var fileName = sUrl.substring(sUrl.lastIndexOf('/') + 1, sUrl.length);
                link.download = fileName;
            }

            //Dispatching click event.
            if (document.createEvent) {
                var e = document.createEvent('MouseEvents');
                e.initEvent('click', true, true);
                link.dispatchEvent(e);
                return true;
            }
        }

        // Force file download (whether supported by server).
        if (sUrl.indexOf('?') === -1) {
            sUrl += '?download';
        }

        window.open(sUrl, '_self');
        return true;
    }

    window.downloadFile.isChrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
    window.downloadFile.isSafari = navigator.userAgent.toLowerCase().indexOf('safari') > -1;
});