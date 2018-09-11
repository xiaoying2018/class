$(function() {
    var mallindex = new Vue({
        el: '#malldetail',
        data: {
            detail: {}
        },
        filters: {
            filtertime: function(val) {
                if (val) {
                    val = val.substring(0, 10)
                    return val;
                } else {
                    return '2018-09-01';
                }
            },
        },
        methods: {
            caseData: function(_id) {
                var _this = this;
                $.ajax({
                    url: "/api/banji_detail",
                    type: "post",
                    data: { id: _id },
                    success: function(res) {
                        if (res.result) {
                        	if (res.data.detail) {
                        		res.data.detail = escapeStringHTML(res.data.detail);
                        	}
                            _this.detail = res.data;
                        } else {
                            alert(res.msg)
                        }
                    }
                })
            }
        },
        mounted: function() {
            this.caseData(getQueryString('id'));
        }
    })

    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }


    function escapeStringHTML(str) {
        str = str.replace(/&lt;/g, '<');
        str = str.replace(/&gt;/g, '>');
        str = str.replace(/&amp;/g, '"');
        str = str.replace(/&quot;/g, '"');
        str = str.replace(/&#039;/g, "'");
        return str;
    }
});