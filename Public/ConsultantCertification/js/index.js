$(function () {
	//搜索
	function search(condition) {

		var resMobile = /^1[34578]\d{9}$/;  //手机号
		var resQQ = /^[1-9]\d{4,9}$/;	//QQ号
		var resWechat = /^[a-zA-Z]([-_a-zA-Z0-9]{5,19})+$/;	//weixin


		if (resMobile.test(condition) || resQQ.test(condition) || resMobile.test(condition) ||resWechat.test(condition)) {
			$('.loadingPart').show().siblings().hide();

			$.ajax({
	            url:"http://crm.xiaoying.net/index.php?m=signIn&a=getUserInfo",
	            type:"POST",
	            data: {
		            condition: condition
		        },
		        dataType:"json",
	            success:function(res){
	                if (res.status) {
	                	var info = res.info;
	                	console.log(res.info);
	                	$('.successPart').show().siblings().hide();
	                	$('.address').text(info.address);
	                	$('.department').text(info.department_name);
	                	$('.full_name').text(info.nick_name);

	                }else {
	                	console.log(res.info);
						$('.errorPart').show().siblings().hide();
	                }
	            },
	            error: function(res){
	            	console.log('error',res.info);
	            	// $('.errorPart').show().siblings().hide();
	            }
	        })
        }else {
			$('.loading').show().siblings().hide();
			alert('请输入小莺顾问手机号/QQ号/微信');
		}
	}
	$('#searchBtn').click(function () {
		search($('#seaechInput').val());
	})
	$('#seaechInput').on('keypress',function(event){ 
        if(event.keyCode == 13){  
            search($('#seaechInput').val());
        }  

    });

		//placeholder  兼容IE9
	var hasPlaceholderSupport = function() {
	    var attr = "placeholder";
	    var input = document.createElement("input");
	    return attr in input;
	}
	var support = hasPlaceholderSupport();
	if (!support) {
	    $('[placeholder]').focus(function() {
	        var input = $(this);
	        if (input.val() == input.attr('placeholder')) {
	            input.val('');
	            input.removeClass('placeholder');
	        }
	    }).blur(function() {
	        var input = $(this);
	        if (input.val() == '' || input.val() == input.attr('placeholder')) {
	            input.addClass('placeholder');
	            input.val(input.attr('placeholder'));
	        }
	    }).blur().parents('form').submit(function() {
	        $(this).find('[placeholder]').each(function() {
	            var input = $(this);
	            if (input.val() == input.attr('placeholder')) {
	                input.val('');
	            }
	        })
	    });
	}
})
