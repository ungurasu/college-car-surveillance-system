<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Acasă</title>

    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>

<body class="antialiased h-auto bg-emerald-100">
    <div class="items-center py-5 h-auto flex flex-col">

        <x-navbar/>

        <div class="mt-5 px-10 py-10 bg-white shadow-md rounded w-full max-w-lg h-auto">
            <div class="mb-10 font-bold text-center text-gray-800 text-2xl">
                @isset($is_edit)
                    Editează utilizatorul
                @else
                    Creează un nou utilizator
                @endisset
            </div>
            <form action="{{ isset($is_edit) ? URL::to('submitaccountedit') : URL::to('submitnewaccount') }}" method="post">
                @csrf

                @if ( isset($error_messages) )
                    @foreach($error_messages as $error_message)
                        <x-error message="{{ $error_message }}"/>
                    @endforeach
                @endif

                @isset($is_edit)
                    <input type="hidden" name="edited_user_id" value="{{ $edited_user_id }}">
                @endisset

                <div class="block text-gray-800 text-sm font-bold text-left">
                    Nume utilizator
                </div>
                <input type="text" name="new_username" class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-900 focus:outline-green-500 w-1/3"
                       value="{{isset($new_username) ? $new_username : ''}}"
                >

                <div class="block text-gray-800 text-sm font-bold text-left mt-4">
                    Număr de telefon
                </div>
                <input type="text" name="new_phone_number" class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-900 focus:outline-green-500 w-1/3"
                       value="{{isset($new_phone_number) ? $new_phone_number : ''}}"
                >

                <div class="block text-gray-800 text-sm font-bold text-left mt-4">
                    Parolă
                </div>
                <input type="password" name="new_password" class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-900 focus:outline-green-500 w-1/3"
                       value="{{isset($new_password) ? $new_password : ''}}"
                >

                <div class="block text-gray-800 text-sm font-bold text-left mt-4">
                    Confirmă parola
                </div>
                <input type="password" name="confirm_password" class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-900 focus:outline-green-500 w-1/3"
                       value=""
                >

                <div class="flex items-center justify-left space-x-2 mt-4">
                    <input type="checkbox" name="new_is_admin" {{isset($new_is_admin) && $new_is_admin == 1 ? 'checked' : ''}}>
                    <label class="font-bold text-sm text-gray-800">
                        Acest utilizator este administrator.
                    </label>
                </div>

                @isset($is_edit)
                    <div class="flex items-center justify-left space-x-2 mt-4">
                        <input type="checkbox" name="keep_old_password" {{isset($keep_old_password) && $keep_old_password == 1 ? 'checked' : ''}}>
                        <label class="font-bold text-sm text-gray-800">
                            Păstrează parola curentă.
                        </label>
                    </div>
                @endisset

                <div class="flex items-center justify-center mt-10">
                    <button class="hover:bg-green-500 fill-green-500 hover:fill-white text-green-500 hover:text-white px-5 py-2 rounded-md text-sm font-bold transition flex items-center justify-center">
                        @isset($is_edit)
                            <box-icon name='edit' class="mr-2"></box-icon> Editează
                        @else
                            <box-icon name='user-plus' class="mr-2"></box-icon> Creează
                        @endisset
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
