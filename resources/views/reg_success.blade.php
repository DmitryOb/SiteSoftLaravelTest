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
	<div class="span2"></div>
	<div class="span8">
		<h3>Ура!</h3>
		<p>Поздравляем! Вы успешно зарегистрировались.</p>
		<p>
			Воспользуйтесь <a href="{{url('/login')}}">формой авторизации</a> чтобы войти на сайт под своей учетной записью
		</p>
	</div>
</div>
@endsection