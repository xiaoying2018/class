$(function(){
	$(".select2").select2({
		width:"450px"
	});
    var config = {
        reset: true,
        delay: 'always',
        mobile: true,
    }
    window.sr = new scrollReveal(config);
})