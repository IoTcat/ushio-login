$(function(){
	var tab = 'account_number';
	var usr = '';
	var code = '';
	var hash = '';
	var timer;
	// 选项卡切换
	$(".account_number").click(function () {
		$('.tel-warn').addClass('hide');
		tab = $(this).attr('class').split(' ')[0];
		checkBtn();
        $(this).addClass("on");
        $(".message").removeClass("on");
        $("#num2").addClass("hide");
        $("#num").removeClass("hide");
        $("#lab_num").html('邮箱');
    });
	// 选项卡切换
	$(".message").click(function () {
		$('.tel-warn').addClass('hide');
		tab = $(this).attr('class').split(' ')[0];
		checkBtn();
		$(this).addClass("on");
        $(".account_number").removeClass("on");
        $("#num").addClass("hide");
        $("#num2").removeClass("hide");
        $("#lab_num").html('手机号');
		
    });

	if(page.params.hasOwnProperty('require')){
	    if(page.params.require == 'tel'){
	        $('.tel-warn').addClass('hide');
	        tab = $(".message").attr('class').split(' ')[0];
	        checkBtn();
	        $(".message").addClass("on");
	        $(".account_number").removeClass("on");
	        $("#num").addClass("hide");
	        $("#num2").removeClass("hide");
	        $("#lab_num").html('手机号');
	        $('.change-login').hide();
	    }
	    session.onload(function(){
	    	if(!session.get('hash')){
	    		window.location.href = '/login.html?require='+page.params.require;
	    	}
	    });
	}else{
	    session.onload(function(){
	    	if(!session.get('hash')){
	    		window.location.href = '/login.html';
	    	}
	    });
	}
	$('.change-login').hide();

	$('#num').keyup(function(event) {
		$('.tel-warn').addClass('hide');
		checkBtn();
	});

	$('#pass').keyup(function(event) {
		$('.tel-warn').addClass('hide');
		checkBtn();
	});

	$('#veri').keyup(function(event) {
		$('.tel-warn').addClass('hide');
		checkBtn();
	});

	$('#num2').keyup(function(event) {
		$('.tel-warn').addClass('hide');
		checkBtn();
	});

	$('#veri-code').keyup(function(event) {
		$('.tel-warn').addClass('hide');
        checkCode($('#veri-code').val());
	});

	// 按钮是否可点击
	function checkBtn()
	{

        $(".log-btn").off('click').addClass("off");
	}

    $('.z').click(function(){

        if(($('#agree').val() == "1") && hash.length){
            sendBtn();
        }else{
            checkBtn();
        }
    });


	function checkTo(phone){
		if(tab == 'account_number'){

			var status = true;
			if (phone == '') {
				$('.num-err').removeClass('hide').find("em").text('请输入邮箱');
				return false;
			}
			var param = /^([A-Za-z0-9_\-\.\u4e00-\u9fa5])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,8})$/;
			if (!param.test(phone)) {
				// globalTip({'msg':'手机号不合法，请重新输入','setTime':3});
				$('.num-err').removeClass('hide');
				$('.num-err').text('邮箱不合法，请重新输入');
				return false;
			}
		}else{


			var status = true;
			if (phone == '') {
				$('.num-err').removeClass('hide').find("em").text('请输入手机号');
				return false;
			}
			var param = /^1[34578]\d{9}$/;
			if (!param.test(phone)) {
				// globalTip({'msg':'手机号不合法，请重新输入','setTime':3});
				$('.num-err').removeClass('hide');
				$('.num-err').text('手机号不合法，请重新输入');
				return false;
			}

		}

        usr = phone;
		return status;
	}

	function checkCode(pCode){
		checkBtn();
		if (pCode == '') {
			$('.error').removeClass('hide').text('请输入验证码');
			return false;
		} else if(pCode.length ==6){
			$('.error').addClass('hide');

            code = pCode;
			$.get('/api/verCode.php?type='+((tab == 'account_number')?'email':'tel')+'&usr='+usr+'&code='+code, function(data){
                data = JSON.parse(data);
				if(data.code == 200){

					hash = data.hash;
					var oTime = $(".form-data .time"),
					oSend = $(".form-data .send"),
					oEm = $(".form-data .time em");
	                clearInterval(timer);
	                oSend.text("验证成功");
	                oSend.css("color", 'green');
				    oSend.show();
	                oEm.text("120");
	                oTime.addClass("hide");
                    sendBtn();
	                $('.error').addClass('hide')
				}else{
					$('.error').removeClass('hide').text('验证码错误！');
                    checkBtn();
				}

			});

            return true;
		}else{
            return false;
        }
	}



	// 登录点击事件
	function sendBtn(){


        if(!(($('#agree').val() == "1") && hash.length)) return;

    $(".log-btn").removeClass("off");
    $('.log-btn').click(function(){
    	session.onload(function(){
			$.get('/api/checkUpdate.php?thash='+hash+'&hash='session.get('hash'), function(data){
	            data = JSON.parse(data);
				if(data.code == 200){
					tips.success({message: '更新成功！'});
	                var to = 'https://www.eee.dog/';
	                if(cookie.get('_from')){
	                    to = decodeURI(cookie.get('_from'));
	                    cookie.del('_from');
	                }
					window.location.href=to;
				}else{
					tips.warning({message: data.message});
	                setTimeout(()=>{window.location.reload()}, 2000);
				}
			});
		});
    });
	}

	// 登录的回车事件
	$(window).keydown(function(event) {
    	if (event.keyCode == 13) {
    		$('.log-btn').trigger('click');
    	}
    });


	$(".form-data").delegate(".send","click",function () {

		if(tab == 'account_number'){
			var to = $.trim($('#num').val());
		}else{
			var to = $.trim($('#num2').val());
		}
		

		if (checkTo(to)) {
	       	var oTime = $(".form-data .time"),
			oSend = $(".form-data .send"),
			num = parseInt(oTime.text()),
			oEm = $(".form-data .time em");
		    $(this).hide();
		    oTime.removeClass("hide");
		    timer = setInterval(function () {
		   	var num2 = num-=1;
	            oEm.text(num2);
	            if(num2==0){
	                clearInterval(timer);
	                oSend.css("color", 'red');
				    oSend.show();
	                oEm.text("120");
	                oTime.addClass("hide");
	            }
	        },1000);

				$.ajax({
		            url: '/api/'+((tab == 'account_number')?'email':'sms')+'Code.php',
		            type: 'get',
		            dataType: 'json',
		            async: true,
		            data: {email: to, tel: to},
		            success:function(data){
                    data = JSON.parse(data);
		                if (data.code == '200') {

		                } else {
			                clearInterval(timer);
			                oSend.text("重新发送验证码");
			                oSend.css("color", 'red');
						    oSend.show();
			                oEm.text("120");
			                oTime.addClass("hide");
		                }
		            },
		            error:function(){
		                clearInterval(timer);
		                oSend.text("重新发送验证码");
		                oSend.css("color", 'red');
					    oSend.show();
		                oEm.text("120");
		                oTime.addClass("hide");
		            }
		        });

		}
    });



});
