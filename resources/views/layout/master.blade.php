<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <title>Grandis - @yield('title')</title>
    <link rel="icon" href="{{ asset('img/cvgrandis.png') }}">
    <style>
        .chart-container {
            position: relative;
            height: 60vh;
            width: 100%;
            max-width: 900px;
            margin: auto;
        }

        .toast-transition {
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }

        .toast-hidden {
            opacity: 0;
            transform: translateY(-20px);
        }

        .layanan-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 0.75rem;
            transition: transform 0.3s ease;
        }

        .card:hover .layanan-img {
            transform: scale(1.03);
        }
    </style>
</head>

<body class="min-h-screen w-full">
    @include('base.header')

    <div class="fixed top-5 right-5 z-[100] w-full max-w-xs space-y-3">
        @if (session('success'))
            <div id="toast-success-global"
                class="flex items-center w-full p-4 text-gray-600 bg-green-100 rounded-lg shadow dark:text-gray-300 dark:bg-green-800 toast-transition"
                role="alert">
                <div
                    class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-200 rounded-lg dark:bg-green-700 dark:text-green-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                    </svg>
                    <span class="sr-only">Success icon</span>
                </div>
                <div class="ms-3 text-sm font-normal">{{ session('success') }}</div>
                <button type="button"
                    class="ms-auto -mx-1.5 -my-1.5 bg-green-100 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-green-800 dark:text-green-300 dark:hover:bg-green-700"
                    data-dismiss-target="#toast-success-global" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div id="toast-error-global"
                class="flex items-center w-full p-4 text-gray-600 bg-red-100 rounded-lg shadow dark:text-gray-300 dark:bg-red-800 toast-transition"
                role="alert">
                <div
                    class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-200 rounded-lg dark:bg-red-700 dark:text-red-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z" />
                    </svg>
                    <span class="sr-only">Error icon</span>
                </div>
                <div class="ms-3 text-sm font-normal">{{ session('error') }}</div>
                <button type="button"
                    class="ms-auto -mx-1.5 -my-1.5 bg-red-100 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-red-800 dark:text-red-300 dark:hover:bg-red-700"
                    data-dismiss-target="#toast-error-global" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('info'))
            <div id="toast-info-global"
                class="flex items-center w-full p-4 text-gray-600 bg-blue-100 rounded-lg shadow dark:text-gray-300 dark:bg-blue-800 toast-transition"
                role="alert">
                <div
                    class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-blue-500 bg-blue-200 rounded-lg dark:bg-blue-700 dark:text-blue-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z" />
                    </svg>
                    <span class="sr-only">Info icon</span>
                </div>
                <div class="ms-3 text-sm font-normal">{{ session('info') }}</div>
                <button type="button"
                    class="ms-auto -mx-1.5 -my-1.5 bg-blue-100 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex items-center justify-center h-8 w-8 dark:bg-blue-800 dark:text-blue-300 dark:hover:bg-blue-700"
                    data-dismiss-target="#toast-info-global" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('warning'))
            <div id="toast-warning-global"
                class="flex items-center w-full p-4 text-gray-600 bg-yellow-100 rounded-lg shadow dark:text-gray-300 dark:bg-yellow-800 toast-transition"
                role="alert">
                <div
                    class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-yellow-500 bg-yellow-200 rounded-lg dark:bg-yellow-700 dark:text-yellow-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z" />
                    </svg>
                    <span class="sr-only">Warning icon</span>
                </div>
                <div class="ms-3 text-sm font-normal">{{ session('warning') }}</div>
                <button type="button"
                    class="ms-auto -mx-1.5 -my-1.5 bg-yellow-100 text-yellow-500 rounded-lg focus:ring-2 focus:ring-yellow-400 p-1.5 hover:bg-yellow-200 inline-flex items-center justify-center h-8 w-8 dark:bg-yellow-800 dark:text-yellow-300 dark:hover:bg-yellow-700"
                    data-dismiss-target="#toast-warning-global" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
        @endif
    </div>

    <main class="min-h-screen pt-16">
        @yield('content')
    </main>

    @include('base.footer')

    <script>
        let confirmationModalInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            const modalEl = document.getElementById('confirmationModal');
            if (modalEl) {
                confirmationModalInstance = new Modal(modalEl, {});
            } else {
                console.error('Elemen modal dengan ID "confirmationModal" tidak ditemukan.');
            }
        });

        function showConfirmationModal(formAction, httpMethod = 'POST', message = 'Apakah Anda yakin?', buttonClass =
            'bg-red-600 hover:bg-red-800', buttonText = 'Ya, Saya Yakin') {
            const modalForm = document.getElementById('confirmationModalForm');
            const modalMessage = document.getElementById('confirmationModalMessage');
            const confirmButton = document.getElementById('confirmationModalConfirmButton');
            const methodInput = document.getElementById('confirmationModalMethod');

            if (modalForm && modalMessage && confirmButton && methodInput && confirmationModalInstance) {
                modalForm.action = formAction;

                if (httpMethod.toUpperCase() === 'PUT' || httpMethod.toUpperCase() === 'PATCH' || httpMethod
                .toUpperCase() === 'DELETE') {
                    methodInput.value = httpMethod.toUpperCase();
                } else {
                    methodInput.value = 'POST';
                }

                modalMessage.innerHTML = message;
                confirmButton.setAttribute('class',
                    `text-white ${buttonClass} focus:ring-4 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center`
                    );
                confirmButton.textContent = buttonText;

                confirmationModalInstance.show();
            } else {
                console.error(
                    'Gagal menampilkan modal konfirmasi. Pastikan semua elemen modal ada dan Flowbite terinisialisasi.');
            }
        }
    </script>
    @stack('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>

</html>
