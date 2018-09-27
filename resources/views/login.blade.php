@extends('layouts.layout')

@section('content')
<div class="navbar">
	<div class="navbar-inner">
		<a class="brand" href="{{url('/')}}">Сайтсофт</a>
		<ul class="nav">
			<li><a href="{{url('/')}}">Главная</a></li>
			@if(!Auth::check())
			<li class="active"><a href="{{url('/login')}}">Авторизация</a></li>
			@endif
			<li><a href="{{url('/reg')}}">Регистрация</a></li>
		</ul>
		@include('layouts.userdata')
	</div>
</div>

<div class="row-fluid">
	<div class="span4"></div>
	<div class="span3">
		@if(!empty($error))
		<div class="alert alert-error">Вход в систему с указанными данными невозможен</div>
		@endif
		<form action="{{url('/login')}}" method="post" class="form-horizontal">
			{{csrf_field()}}
			<div class="control-group">
				<b>Авторизация</b>
			</div>
			<div class="control-group">
				<input type="text" id="inputLogin" name="username" placeholder="Логин" data-cip-id="inputLogin" autocomplete="off">
			</div>
			<div class="control-group">
				<input type="password" id="inputPassword" name="password" placeholder="Пароль" data-cip-id="inputPassword">
			</div>
			<div class="control-group">
				<label class="checkbox">
					<input type="checkbox" name="remember" value="1"> Запомнить меня
				</label>
				<button type="submit" class="btn btn-primary">Вход</button>
			</div>
		</form>
	</div>
</div>
@endsection