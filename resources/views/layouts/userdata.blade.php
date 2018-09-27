@if(Auth::check())
<ul class="nav pull-right">
	<li><a>{{Auth::user()->name}}</a></li>
	<li><a href="{{url('/logout')}}">Выход</a></li>
</ul>
@endif