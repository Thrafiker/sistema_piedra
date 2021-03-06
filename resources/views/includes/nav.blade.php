
<nav class="navbar navbar-default">

	<div class="container-fluid">
	 
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">
				{{ trans('messages.welcome', ["user" => Auth::check() ? Auth::user()->name : '' ]) }}
			</a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-right">
			 
				@if (Auth::check())
					<li><a href="{{ url('users/logout') }}">@lang('messages.logout')</a></li>
					@else
					<li><a href="{{ url('users/login') }}">@lang('messages.login')</a></li>
					<li><a href="{{ url('users/register') }}">@lang('messages.register')</a></li>
				@endif

				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">@lang('messages.language') <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="{{ url('language/es')}}">@lang('messages.spanish')</a></li>
						<li><a href="{{ url('language/en')}}">@lang('messages.english')</a></li>
					</ul>
				</li>
			</ul>
		</div><!-- /.navbar-collapse -->

	</div><!-- /.container-fluid -->

</nav>
