$(function(){
	var result = new Vue({
        el: '#jpyy',
        data: {
            area:[]
        },
        methods:{
            getarea:function(){
               console.log("xxx",utily.manage_url);
            }

        },
        mounted:function(){
            this.getarea();

            $(".selectcity .city_item").eq(0).addClass("active")
        }
    });
    var config = {
        reset: true,
        delay: 'always',
        mobile: true,
    }
    window.sr = new scrollReveal(config);
})