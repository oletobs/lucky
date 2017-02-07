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
        <link rel="stylesheet" href="{{ asset('css/app.074f298959736542c110.css') }}">
    @show
    <script type="text/javascript" src="//wow.zamimg.com/widgets/power.js"></script>
    <script>var wowhead_tooltips = { "colorlinks": false, "iconizelinks": false, "renamelinks": false }</script>
</head>


<body>
<div class="container-fluid">

    <div class="row justify-content-center bottom-gutter">
        <div class="col col-auto"><h1 class="logo"><span>L</span>egendary<small class="text-muted">.rip</small></h1></div>
    </div>


@yield('content')

</div>

@section('scripts')
    <script src="{{ asset('js/app.0a06648a40dc67fd1446.js') }}"></script>
@show

</body>
</html>