$(function() {
    var mallindex = new Vue({
        el: '#onlinemall',
        data: {},
        filters: {},
        methods: {

        },
        mounted: function() {

            $("#area,#city").select2({
                width: "300px",
            })
            
            $(".selectcity .city_item").eq(0).addClass("active")

		    var config = {
		        reset: true,
		        delay: 'always',
		        mobile: true,
		    }
		    window.sr = new scrollReveal(config);
        }
    })


})