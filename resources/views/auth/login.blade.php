@extends('layout.master')

@section('title', 'Login')

@section('content')

    <body class="bg-gray-100 text-gray-800 font-sans">
        <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <a href="{{ route('landing') }}" class="inline-block">
                    <h1 class="text-4xl font-bold text-blue-900 hover:text-blue-700 transition-colors">CV Grandis Nusantara
                    </h1>
                </a>
                <p class="mt-2 text-md text-gray-600">Silakan login untuk mengakses akun Anda.</p>
            </div>

            <div class="max-w-md w-full bg-white shadow-xl rounded-xl p-8 space-y-6">
                <h2 class="text-2xl font-bold text-center text-gray-900">
                    Login Akun Anda
                </h2>

                <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Alamat Email
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email') }}"
                            class="appearance-none block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="anda@domain.com">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="appearance-none block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Password Anda">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-900 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-700 transition duration-150 ease-in-out">
                            Masuk
                        </button>
                    </div>
                </form>
                <p class="mt-2 text-center text-sm text-gray-600">
                    <a href="{{ route('landing') }}" class="font-medium text-blue-600 hover:text-blue-500 hover:underline">
                        &larr; Kembali ke Beranda
                    </a>
                </p>
            </div>

            <p class="mt-8 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} CV Grandis Nusantara. Hak Cipta Dilindungi.
            </p>
        </div>
    </body>
@endsection
