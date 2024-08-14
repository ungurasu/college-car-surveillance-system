<nav class="flex items-center justify-between flex-wrap bg-white px-6 py-3 h-fit w-full shadow-md">
    <div class="fill-green-500 transition hover:rotate-180 hover:fill-lime-950">
        <box-icon type='solid' name='car-crash' class="mx-2 my-2" size="md"></box-icon>
    </div>
    <div class="block lg:hidden fill-green-500 hover:fill-lime-950">
        <button id="menu-btn">
            <box-icon name='menu' size="md"></box-icon>
        </button>
    </div>
    <div id="menu-items" class="lg:flex flex-grow lg:block hidden lg:w-auto w-full lg:items-center">
        <ul class="list-reset lg:flex justify-end flex-1 items-center">
            <li class="mr-3 lg:mt-0 mt-3">
                <a class="inline-block flex items-center lg:w-max w-full py-2 px-4 transition rounded text-lime-950 hover:text-white hover:bg-green-500 fill-lime-950 hover:fill-white"
                   href="{{ URL::to('dashboard') }}"
                >
                    @if ( isset($current) && $current == 'dashboard')
                        <box-icon name='chevron-right'></box-icon>
                    @endif
                    Acasă
                </a>
            </li>
            <li class="mr-3 lg:mt-0 mt-3">
                <a class="inline-block flex items-center lg:w-max w-full py-2 px-4 transition rounded text-lime-950 hover:text-white hover:bg-green-500 fill-lime-950 hover:fill-white"
                   href="{{ URL::to('accounts') }}"
                >
                    @if ( isset($current) && $current == 'accounts')
                        <box-icon name='chevron-right'></box-icon>
                    @endif
                    Conturi
                </a>
            </li>
            <li class="mr-3 lg:mt-0 mt-3">
                <a class="inline-block flex items-center lg:w-max w-full py-2 px-4 transition rounded text-lime-950 hover:text-white hover:bg-green-500 fill-lime-950 hover:fill-white"
                   href="{{ URL::to('logout') }}"
                >
                    Deconectare
                </a>
            </li>
        </ul>
    </div>
</nav>

<script>
    document.getElementById('menu-btn').onclick = function() {
        document.getElementById("menu-items").classList.toggle("hidden");
    }
</script>
