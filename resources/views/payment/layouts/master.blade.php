<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ 'Payment' }}</title>
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/libs/bootstrap-5/bootstrap.min.css') }}">

    @stack('script')
</head>

<body
    style="font-family: 'DM Sans', sans-serif; color: #0d1117; background-color: #fdfdfd; margin: 0; padding: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh;">
    @yield('content')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/libs/bootstrap-5/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
