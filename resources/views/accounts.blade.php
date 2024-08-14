<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Conturi</title>

    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>

<body class="antialiased h-auto bg-emerald-100">
    <div class="items-center py-5 h-full flex flex-col">
        <x-navbar current="accounts"/>

        <div class="mt-5 py-5 bg-white shadow-md rounded w-full max-w-lg flex justify-center flex-col">
            <table class="table-auto">
                <thead>
                    <tr class="border-b-4 border-green-300">
                        <th class="p-4 font-normal text-slate-800">Nume</th>
                        <th class="p-4 font-normal text-slate-800">Număr de telefon</th>
                        <th class="p-4 font-normal text-slate-800">Admin</th>
                        @if ($user_is_admin)
                            <th class="p-4"></th>
                            <th class="p-4"></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($users_list as $unique_user)
                        <tr class="border-b-2 border-slate-200">
                            <th class="p-4 font-light text-slate-800 text-xs">
                                {{$unique_user['name']}}
                            </th>
                            <th class="p-4 font-light text-slate-800 text-xs">
                                {{$unique_user['phone']}}
                            </th>
                            <th class="p-4 font-light text-slate-800 text-xs">
                                @if ($unique_user['is_admin'])
                                    Da
                                @else
                                    Nu
                                @endif
                            </th>
                            @if ($user_is_admin)
                                <th class="">
                                    @if ($unique_user['id'] != $user_id)
                                        <a href="{{URL::to('edituser\/').strval($unique_user['id'])}}">
                                            <div class="rounded-md transition hover:bg-yellow-500 fill-yellow-500 hover:fill-white">
                                                <box-icon type='solid' name='edit' class="mx-2 my-2" size="sm"></box-icon>
                                            </div>
                                        </a>
                                    @endif
                                </th>
                                <th class="">
                                    @if ($unique_user['id'] != $user_id)
                                        <form action="{{ URL::to('deleteaccount') }}" method="post" class="m-0">
                                            @csrf
                                            <input type="hidden" name="id_to_delete" value="{{ $unique_user['id'] }}">
                                            <button onclick="return confirm('Urmează să ștergeți utilizatorul!')"
                                                class="rounded-md transition hover:bg-red-500 fill-red-500 hover:fill-white hover:cursor-pointer">
                                                <box-icon type='solid' name='trash' class="mx-2 my-2" size="sm"></box-icon>
                                            </button>
                                        </form>
                                    @endif
                                </th>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>


            @if ($user_is_admin)
                <div class="flex items-center justify-center mt-6">
                    <a href="{{ URL::to('newaccount') }}">
                        <div class="rounded-md transition text-green-500 hover:text-white hover:bg-green-500 fill-green-500 hover:fill-white flex items-center justify-center px-2">
                            <box-icon name='plus-medical' class="mx-2 my-2" size="sm"></box-icon> Adaugă utilizator nou
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
