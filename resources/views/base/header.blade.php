<nav class="bg-white fixed w-full z-20 top-0 start-0 border-b border-gray-200">
    <div class="w-full flex flex-wrap justify-center items-center gap-48 mx-auto p-4">
        <a href="/landing" class="flex items-center space-x-3">
            <img src="{{ asset('img/cvgrandislogo.png') }}" class="h-8" alt="Logo">
        </a>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
            <ul
                class="flex flex-col md:flex-row justify-between p-4 md:p-0 mt-4 gap-16 md:mt-0 font-medium border border-gray-100 md:border-0 rounded-lg bg-gray-50 md:bg-white">
                @guest
                @else
                    <li>
                        <a href="/dashboard"
                            class="block py-2 px-3 text-gray-800 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-900 md:p-0">Dashboard</a>
                    </li>
                    @if (Auth::user()->hasRole('Administrator'))
                        <li>
                            <a href="{{ route('order.list') }}"
                                class="block py-2 px-3 text-gray-800 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-900 md:p-0">Order
                                List</a>
                        </li>
                        <li>
                            <a href="{{ route('forecast') }}"
                                class="block py-2 px-3 text-gray-800 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-900 md:p-0">Forecast</a>
                        </li>
                    @endif
                @endguest
            </ul>
        </div>
        <div class="flex md:order-2 space-x-3 md:space-x-0 gap-8">

            <div class="flex md:order-2 space-x-3 md:space-x-0">
                @guest
                    <a href="{{ route('login') }}"
                        class="text-white ml-2 bg-blue-900 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Login</a>
                @else
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-white ml-2 bg-blue-900 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</nav>
