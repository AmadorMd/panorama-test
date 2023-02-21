<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panorama</title>
    {{-- Text Rich Editor Assets --}}
   
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
</head>
<body>
    <head>
        <ul>
            <li><a href="{{ route('home') }}">Collections</a></li>
            <li><a href="#">Products</a></li>
        </ul>
    </head>
    @yield('content')
    @yield('scripts')
</body>
</html>