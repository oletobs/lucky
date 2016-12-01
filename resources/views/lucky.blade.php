<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>u lucky? @yield('title')</title>

    @section('stylesheets')
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @show

</head>

<body>

@section('header')

@show

@section('content')
    <table class="table table-inverse">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Class</th>
            <th>Level</th>
        </tr>
        </thead>
        <tbody>
        @foreach($members as $key=>$member)
            <tr>
                <td>{{ ++$key }}</td>
                <td>{{ $member->character->name }}</td>
                <td>{{ $member->character->class }}</td>
                <td>{{ $member->character->level }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@show

@section('footer')

@show

@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
@show

</body>

</html>