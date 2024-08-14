<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Authenticate</title>

    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>

<body class="antialiased h-auto">
    <div class="bg-emerald-100 flex justify-center px-3 py-10 h-full items-center">

        <div class="bg-white shadow-md rounded w-full max-w-sm p-5">
            <div class="mt-10 text-xl text-lime-950 fill-lime-950 font-bold flex justify-center text-center">
                Sistem de supraveghere pentru automobil
            </div>
            <div class="mt-6 text-gray-800 text-lg italic font-semibold flex justify-center">
                Autentificare
            </div>

            <form class="px-8 py-6 pb-8 mb-4" action="{{ URL::to('login') }}" method="post">
                @csrf

                @if ( isset($error_message) )
                    <x-error message="{{ $error_message }}"/>
                @endif

                <div class="mb-4">
                    <label class="block text-gray-800 text-sm font-bold mb-2">
                        Utilizator
                    </label>
                    <input name="name" type="text" placeholder="Username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 focus:outline-green-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-800 text-sm font-bold mb-2">
                        Parola
                    </label>
                    <input name="password" type="password" placeholder="Password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 focus:outline-green-500">
                </div>
                <div class="mt-12 text-right">
                    <button class="bg-emerald-100 hover:bg-green-500 text-gray-900 hover:text-white px-5 py-2 rounded-md text-sm font-bold transition">
                        Login
                    </button>
                </div>
            </form>
        </div>

    </div>
</body>

</html>
