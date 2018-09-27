@extends('layouts.layout')

@section('content')
<div id="decrypted"></div>
<div class="navbar">
	<div class="navbar-inner">
		<a class="brand" href="{{url('/')}}">Сайтсофт</a>
		<ul class="nav">
			<li class="active"><a href="{{url('/')}}">Главная</a></li>
			@if(!Auth::check())
				<li><a href="{{url('/login')}}">Авторизация</a></li>
			@endif
			<li><a href="{{url('/reg')}}">Регистрация</a></li>
		</ul>
		@include('layouts.userdata')
	</div>
</div>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
<div class="row-fluid">
	<div class="span2"></div>
	<div class="span8">
		@if(Auth::check())
			<form action="" method="post" class="form-horizontal" id="SendMsgForm">
				<div class="alert alert-error" hidden></div>
				<div class="control-group">
					<textarea type="password" id="inputText" placeholder="Ваше сообщение..." data-cip-id="inputText"></textarea>
				</div>
				<div class="control-group">
					<button type="submit" class="btn btn-primary">Отправить сообщение</button>
					<span class="encBlock">
						<label for="isEncrypt">Зашифровать сообщение</label>
						<input type="checkbox" name="isEncrypt">
					</span>
				</div>
			</form>
		@endif
		<div class="chat-container"></div>
	</div>
</div>
<script>
	var server = "ws://localhost:4236/";
	var socket = new WebSocket(server);
	socket.onclose = function(event) {
		if (event.wasClean) {
			console.log('Соединение закрыто чисто');
		} else {
			console.log('Обрыв соединения');
		}
		console.log('Код: ' + event.code + ' причина: ' + event.reason);
	};
	socket.onerror = function(error) {
		console.log("Ошибка " + error.message);
	};
	socket.onopen = function() {
		console.log("Соединение установлено.");
		socket.send('give me all messages');
	};
	socket.onmessage = function(event) {
		function IsJsonString(str) {
			try {
				JSON.parse(str);
			} catch (e) {
				return false;
			}
			return true;
		}
		if (IsJsonString(event.data)){
			$('.chat-container').html('');
			var data = JSON.parse(event.data);
			@if(Auth::check())
				var currenUserId = Number('{{Auth::user()->id}}');
			@endif
			if (typeof(currenUserId)=='undefined'){
				//not auth user
				data.forEach(function(e){
					var html='<div class="well">';
					html+='<span class="header-of-msg">'+e.Time+'</span>';
					html+='<h5>'+e.Name+':</h5>';
					if (e.isEnc=="1"){
						html+='<p class="textMsgPrivat">'+'Это приватное сообщение'+'</p>';
					} else {
						html+='<p class="textMsg">'+e.Msg+'</p>';
					}
					html+='</div>';
					var newEl = jQuery.parseHTML(html);
					$('.chat-container').append(newEl);
				});
			} else {
				//auth user
				data.forEach(function(e){
					var currenUserId = this.valueOf();
					var html='<div class="well" '+'is-enc='+e.isEnc+' msg-id='+e.id+'>';
					html+='<span class="header-of-msg">'+e.Time+'</span>';
					var checkhtml = '';
					var ckecked=Number(e.isEnc)?'checked':'';
					if (Number(e.userID)==currenUserId){
						html+='<span class="header-of-edit" onclick=editMsg(this)><i class="fas fa-edit"></i></span>';
						html+='<span class="header-of-delete" onclick=deleteMsg(this)><i class="far fa-trash-alt"></i></span>';
						checkhtml = '<span class="msg-check-status" hidden><label for="isEncrypt'+e.id+'">Зашифровать сообщение</label><input type="checkbox" '+ckecked+' name="isEncrypt'+e.id+'"><span>';
					}
					html+='<h5>'+e.Name+':</h5>';
					if (e.isEnc=="1"){
						@if(Auth::check())
						var userID = '{{Auth::user()->id}}';
						if (e.userID==userID){
							html+='<a class="textMsg" encrypted="'+e.Msg+'" onclick=decrypt(this)>'+'Расшифровать сообщение'+'</a>';
							html+=checkhtml;
						} else {
							html+='<p class="textMsgPrivat">'+'Это приватное сообщение'+'</p>';
						}
						@endif
					} else {
						html+='<p class="textMsg">'+e.Msg+'</p>';
						html+=checkhtml;
					}
					html+='</div>';
					var newEl = jQuery.parseHTML(html);
					$('.chat-container').append(newEl);
				}, currenUserId);
			}
		} else {
			console.log(event.data)
		}
	};
	$('#SendMsgForm').submit(function(e){
		$('.alert-error').hide();
		e.preventDefault();
		var msg = $('#SendMsgForm textarea').val();
		if (!msg || !(/\S/.test(msg))){
			$('.alert-error').show();
			$('.alert-error').text('Сообщение не может быть пустым или состоять из пробелов');
		} else {
			// send msg
			var time = new Date().toJSON().slice(0, 19).replace('T', ' ');
			@if(Auth::check())
			var userID = Number('{{Auth::user()->id}}');
			var name = '{{Auth::user()->name}}';
			var pass = '{{Auth::user()->password}}';
			var isEnc = 0;
			@endif
			if ($('input[name="isEncrypt"]').is(':checked')){
				isEnc = 1;
				var encrypted = CryptoJS.AES.encrypt(msg, pass);
				msg = encrypted.toString();
			}
			var data = { Time: time, id : userID, Name: name, Msg: msg, isEnc: isEnc };
			var JsonStringData = JSON.stringify(data);
			socket.send(JsonStringData);
			$('#SendMsgForm').trigger('reset');
		}
	})
	function editMsg(e){
		$(e).siblings('span.msg-check-status').show();
		if ($(e).parent().attr('is-enc')=='1'){
			var el = $(e).siblings('a.textMsg');
			$(el).replaceWith( "<input class='editMsgEnc' style='width:500px' placeholder='Новый текст'>");
		} else {
			var el = $(e).siblings('p.textMsg');
			$(el).replaceWith( "<input class='editMsg' value='"+$(el).text()+"'>");
		}
	}
	$(document).on("keyup", "input.editMsg", function(e) {
		if (e.keyCode==13){
			$(this).siblings('span.msg-check-status').hide();
			var el = $(e.target);
			var msgID = el.parent().attr('msg-id');
			var newText = e.target.value;
			if (!newText || !(/\S/.test(newText))){
				alert('Сообщение не может быть пустым или состоять из пробелов');
			} else {
				var msgID = el.parent().attr('msg-id');
				@if(Auth::check())
				var pass = '{{Auth::user()->password}}';
				@endif
				var encrypted = CryptoJS.AES.encrypt(newText, pass);
				var newestText = encrypted.toString();
				if ($(this).siblings('span.msg-check-status').find('input').is(':checked')){
					var dataToserv = msgID+'~'+newestText+'~'+'Y';
					socket.send(dataToserv);
				} else {
					var dataToserv = msgID+'~'+newText;
					socket.send(dataToserv);
				}
			}
		}
	});
	$(document).on("keyup", "input.editMsgEnc", function(e) {
		if (e.keyCode==13){
			$(this).siblings('span.msg-check-status').hide();
			var el = $(e.target);
			if (!e.target.value || !(/\S/.test(e.target.value))){
				alert('Сообщение не может быть пустым или состоять из пробелов');
			} else {
				var msgID = el.parent().attr('msg-id');
				@if(Auth::check())
				var pass = '{{Auth::user()->password}}';
				@endif
				var encrypted = CryptoJS.AES.encrypt(e.target.value, pass);
				var newText = encrypted.toString();
				if (!$(this).siblings('span.msg-check-status').find('input').is(':checked')){
					var dataToserv = msgID+'~'+e.target.value+'~'+'N';
					socket.send(dataToserv);
				} else {
					var dataToserv = msgID+'~'+newText;
					socket.send(dataToserv);
				}
			}
		}
	});
	function deleteMsg(e){
		var msgID = $(e).parent().attr('msg-id');
		var dataToserv = 'd'+'~'+msgID;
		socket.send(dataToserv);
	}
	function decrypt(e){
		@if(Auth::check())
			var userID = Number('{{Auth::user()->id}}');
			var enc = $(e).attr('encrypted');
			var inputPass = prompt('Введите ваш пароль');
			var data = {inputPass:inputPass, userID:userID, _token:'<?php echo csrf_token() ?>'};
			$.ajax({
				url: "{{url('/ajaxcheckPrivate')}}",
				type: 'post',
				data: data,
				context: e,
				success: function (resp) {
					if (resp=='0'){
						alert('Неверный пароль!');
					} else {
						var decrypted = CryptoJS.AES.decrypt(enc, resp).toString(CryptoJS.enc.Utf8);
						$(this).replaceWith( '<p class="textMsg">'+decrypted+'</p>');
					}
				}
			});
		@endif
	}
</script>
@endsection