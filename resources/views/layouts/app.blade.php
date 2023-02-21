<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panorama</title>
    @vite('resources/css/app.css')
    {{-- Text Rich Editor Assets --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
</head>
<body>
    <nav class="w-full bg-slate-800 py-4 px-16 flex justify-between items-center">
        <div class="text-white inline-flex items-end">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                <path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zM12 3a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0v-.75a.75.75 0 011.5 0v.75a4.5 4.5 0 11-9 0v-.75a.75.75 0 011.5 0v.75z" clip-rule="evenodd" />
              </svg>              
            <p class="ml-2">Shopify Administrator</p>
        </div>
        <ul class="inline-flex text-sm">
            <li>
                <a class="text-white hover:bg-slate-700 px-2 py-1 rounded-md" href="{{ route('home') }}">
                    Home
                </a>
            </li>
        </ul>
    </nav>
    <div class="w-full h-full bg-gray-200 py-7">
        <div class="max-w-5xl bg-white mx-auto rounded-md pb-4">
            @yield('content')
        </div>
    </div>
    @yield('scripts')
</body>
</html>