<div class="mt-5 px-5 py-5 bg-white shadow-md rounded w-full max-w-lg">
    <form action="{{ URL::to('filter') }}" method="post">
        @csrf

        <div class="flex items-center justify-center space-x-1">
            <div class="block text-gray-800 text-sm font-bold w-1/6 text-right">
                Până la:
            </div>
            <input type="date" name="end_date" class="w-2/6 shadow appearance-none border rounded py-2 px-3 text-gray-900 focus:outline-green-500 w-1/3 text-xs"
                value="{{isset($enddatetime) ? date('Y-m-d', strtotime($enddatetime)) : date('Y-m-d')}}"
            >
            <div class="pl-2 fill-gray-800">
                <box-icon type="solid" name="time" size="sm"></box-icon>
            </div>
            <input type="number" name="end_hour" min="0" max="24" class="w-1/6 shadow appearance-none border rounded py-2 px-3 text-gray-900 focus:outline-green-500 w-1/3"
                value="{{isset($enddatetime) ? date('H', strtotime($enddatetime)) : date('H')}}"
            >
            <div>
                :
            </div>
            <input type="number" name="end_minute" min="0" max="59" class="w-1/6 shadow appearance-none border rounded py-2 px-3 text-gray-900 focus:outline-green-500 w-1/3"
                   value="{{isset($enddatetime) ? date('i', strtotime($enddatetime)) : date('i')}}"
            >
        </div>
        <div class="flex items-center justify-center space-x-1 mt-4">
            <div class="block text-gray-800 text-sm font-bold w-1/6 text-right">
                De la:
            </div>
            <input type="date" name="start_date" class="w-2/6 shadow appearance-none border rounded py-2 px-3 text-gray-900 focus:outline-green-500 text-xs"
               value="{{isset($startdatetime) ? date('Y-m-d', strtotime($startdatetime)) : date('Y-m-d', strtotime("-1 week"))}}"
            >
            <div class="pl-2 fill-gray-800">
                <box-icon type="solid" name="time" size="sm"></box-icon>
            </div>
            <input type="number" name="start_hour" min="0" max="23" class="w-1/6 shadow appearance-none border rounded py-2 px-3 text-gray-900 focus:outline-green-500 w-1/3"
                   value="{{isset($startdatetime) ? date('H', strtotime($startdatetime)) : '00'}}"
            >
            <div>
                :
            </div>
            <input type="number" name="start_minute" min="0" max="59" class="w-1/6 shadow appearance-none border rounded py-2 px-3 text-gray-900 focus:outline-green-500 w-1/3"
                   value="{{isset($startdatetime) ? date('i', strtotime($startdatetime)) : '00'}}"
            >
        </div>
        <div class="text-right mt-4">
            <button class="hover:bg-green-500 text-green-500 hover:text-white px-5 py-2 rounded-md text-sm font-bold transition">
                Filtrează
            </button>
        </div>
    </form>
</div>
