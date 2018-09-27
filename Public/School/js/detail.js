$(function() {
    var result = new Vue({
        el: '#jpyy_detail',
        data: {
        	tempimg:"",
        	detail:{}
        },
        filters:{
            filtercontent: function(val){
            	if (val) {
            		return this.escapeStringHTML(val)
            	}else{
            		return val
            	}
            }
        },
        methods: {
            getdata: function(_id){
                var _this = this;
                $.ajax({
                    url:"http://manage.com/getschooldetail",
                    type:"get",
                    data: {id:_id},
                    success:function(res){
                    	if (res.status) {
                    		_this.detail = res.data;
                    	}
                        console.log("xxx",res);
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
            }
        },
        mounted: function() {
            var _this = this;
            var _s = Math.round(Math.random()*5+1);
            this.tempimg = '../../Public/Common/img/school/random'+_s+'.jpg';
		    $(".cover_banner_part").backgroundBlur({
			    imageURL : '../../Public/Common/img/school/random'+_s+'.jpg',
			    blurAmount : 10,
			    duration: 1000,
			    endOpacity : 1
			});
			this.getdata(this.getQueryString('id'));
        }
    });
})