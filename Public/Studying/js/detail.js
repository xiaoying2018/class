$(function(){
	
	var result = new Vue({
        el: '#studying',
        data: {
        }
    });
    var config = {
        reset: true,
        delay: 'always',
        mobile: true,
    }
    window.sr = new scrollReveal(config);
})