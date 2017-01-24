<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:;base64,=">
    <title>u lucky? @yield('title')</title>

    @section('stylesheets')
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{ asset('css/app.9d2b9789625e19773958.css') }}">
    @show

</head>


<body>
<div class="container-fluid">

    <header>
        <div class="logo-container"><h1 id="logo"><span>L</span>egendary<small>.rip</small></h1></div>
    </header>


@yield('content')

</div>

@section('scripts')
    <script src="{{ asset('js/app.c1afa1c3c836d704c982.js') }}"></script>
@show

</body>
</html>