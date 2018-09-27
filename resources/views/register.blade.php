@extends('layouts.layout')

@section('content')
<div class="navbar">
	<div class="navbar-inner">
		<a class="brand" href="{{url('/')}}">Сайтсофт</a>
		<ul class="nav">
			<li><a href="{{url('/')}}">Главная</a></li>
			@if(!Auth::check())
			<li><a href="{{url('/login')}}">Авторизация</a></li>
			@endif
			<li class="active"><a href="{{url('/reg')}}">Регистрация</a></li>
		</ul>
		@include('layouts.userdata')
	</div>
</div>

<div class="row-fluid">
	<div class="span4"></div>
	<div class="span8">
		<form action="{{URL::to('/store')}}" method="post" class="form-horizontal">
			{{csrf_field()}}
			<div class="control-group">
				<b>Регистрация</b>
			</div>
			<div class="control-group">
				<input type="text" id="inputLogin" name="username" placeholder="Логин" data-cip-id="inputLogin" autocomplete="off">
				<span class="help-inline"></span>
			</div>
			<div class="control-group">
				<input type="password" id="inputPassword" name="password" placeholder="Пароль" data-cip-id="inputPassword" minlength="1">
				<span class="help-inline"></span>
			</div>
			<div class="control-group">
				<input type="password" id="inputPassword2" name="password" placeholder="Повторите пароль" data-cip-id="inputPassword2" minlength="1">
				<span class="help-inline"></span>
			</div>
			<div class="control-group">
				<button type="submit" class="btn btn-primary">Отправить</button>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	$('form').submit(function (e) {
		$('.control-group').removeClass('error')
		$('.help-inline').text('');
		var name = $('input[name="username"]');
		var pass1 = $('input[data-cip-id="inputPassword"]');
		var pass2 = $('input[data-cip-id="inputPassword2"]');
		var errorText;
		if (!name.val()) {
			errorText = 'Поле "логин" не может быть пустыми';
			name.closest('.control-group').addClass('error');
			name.next().text(errorText);
			e.preventDefault();
		} else if (!pass1.val()) {
			errorText = 'Поле "пароль" не может быть пустыми';
			pass1.closest('.control-group').addClass('error');
			pass1.next().text(errorText);;
			e.preventDefault();
		} else if (!pass2.val()) {
			errorText = 'Поле "повторите пароль" не может быть пустыми';
			pass2.closest('.control-group').addClass('error');
			pass2.next().text(errorText);
			e.preventDefault();
		} else if (pass1.val()!=pass2.val()) {
			errorText = 'Пароли не совпадают';
			pass1.closest('.control-group').addClass('error');
			pass2.closest('.control-group').addClass('error');
			pass1.next().text(errorText);
			pass2.next().text(errorText);
			e.preventDefault();
		} else {
			$('form').trigger('submit')
		}
	});
</script>
@endsection