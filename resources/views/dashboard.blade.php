<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Acasă</title>

    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>

<body class="antialiased h-auto bg-emerald-100 ">
    <div class="items-center py-5 h-auto flex flex-col">

        <x-navbar current="dashboard"/>

        <x-filter startdatetime="{{$start_datetime}}" enddatetime="{{$end_datetime}}"/>

        <!--
        <div class="mt-5 px-5 py-5 bg-white shadow-md rounded w-full max-w-sm">
            <a href="{{ url('videos/mimimi.mp4') }}">a</a>
            <video width="320" height="240" controls>
                <source src="{{ url('videos/mimimi.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        -->

        @isset($events)
            @foreach($events as $event)
                <a href="{{ URL::to('event/'.$event['video_id']) }}" class="mt-5 px-5 py-5 bg-white hover:bg-green-500 text-gray-900 hover:text-white shadow-md rounded w-full max-w-lg transition">
                    Evenimentul de la {{$event['created_at']}}
                </a>
            @endforeach
        @endif
    </div>
</body>

</html>
