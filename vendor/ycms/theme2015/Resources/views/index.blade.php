@extends('theme2015::layouts.master')

@section('content')
	
	<h1>Hello World</h1>
	
	<p>
		This view is loaded from module: {!! config('theme2015.name') !!}
	</p>

@stop