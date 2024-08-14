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

        <div class="mt-5 px-10 py-10 bg-white shadow-md rounded w-full max-w-lg h-auto flex flex-col items-center">
            <div class="mb-10 font-bold text-center text-gray-800 text-xl">
                Evenimentul de la {{$video_details->created_at}}
            </div>

            <video controls class="rounded mb-10">
                <source src="{{ URL::to('videos/'.$video_details->video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>

            @if ($user_is_admin)
                <div class="w-full justify-end flex">
                    <a href="{{URL::to('deleteevent/'.$video_details->video_id)}}"  onclick="return confirm('Urmează să ștergeți evenimentul!')">
                        <div class="rounded-md transition hover:bg-red-500 fill-red-500 hover:fill-white hover:cursor-pointer px-5 py-2 flex items-center justify-center text-red-500 hover:text-white">
                            <box-icon type='solid' name='trash' class="mr-2" size="sm"></box-icon> Șterge evenimentul
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
