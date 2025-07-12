@extends('layouts.app')

@section('content')
<h1>User Profile</h1>
<p>{{ $user->name }}</p>
<p>{{ $user->email }}</p>
<p>{{ $user->profile->first_name }}</p>
<p>{{ $user->profile->last_name }}</p>
<p>{{ $user->profile->phone }}</p>
<p>{{ $user->profile->avatar }}</p>
<p>{{ $user->profile->birth_date }}</p>

@endsection