<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Каталог</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/src/assets/style/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<h1>Каталог товаров</h1>
<section id="content">
    <body class="p-4 sm:p-6 lg:p-8">

    <header class="text-center mb-8 sm:mb-12">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800">
            Каталог товаров
        </h1>
        <p class="mt-2 text-lg sm:text-xl text-gray-600">
            Ознакомьтесь с нашей последней коллекцией
        </p>
    </header>

    <div id="comparison-bar" class="fixed top-4 right-4 z-50 bg-white p-4 rounded-xl shadow-xl flex items-center space-x-2 lg:space-x-4">
        <div id="compare-items-container" class="flex items-center space-x-2 overflow-x-auto whitespace-nowrap">
        </div>
        <button id="clear-compare-btn" class="flex-shrink-0 bg-red-500 text-white font-medium p-2 rounded-lg shadow-md hover:bg-red-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="lg:grid lg:grid-cols-[280px_1fr] lg:gap-8 max-w-7xl mx-auto">

        <button id="filterToggle" class="lg:hidden fixed top-4 left-4 z-50 bg-violet-600 text-white p-3 rounded-lg shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zM3 16a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" />
            </svg>
        </button>

        <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 w-64 bg-white p-6 shadow-xl z-40 lg:relative lg:w-auto lg:shadow-none ">
            <div class="flex justify-between items-center mb-6 lg:hidden">
                <h3 class="text-2xl font-semibold text-gray-800">Фильтры</h3>
                <button id="closeSidebar" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Поиск по названию</label>
                <input type="text" id="search" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring focus:ring-violet-500 focus:ring-opacity-50 px-3 py-2" placeholder="Например, футболка">
            </div>

            <div class="mb-6">
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Сортировать по</label>
                <select id="sort" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring focus:ring-violet-500 focus:ring-opacity-50 px-3 py-2">
                    <option value="date-desc">По дате добавления (убывание)</option>
                    <option value="date-asc">По дате добавления (возрастание)</option>
                    <option value="price-asc">Цена (по возрастанию)</option>
                    <option value="price-desc">Цена (по убыванию)</option>
                    <option value="rating-asc">Рейтинг (по возрастанию)</option>
                    <option value="rating-desc">Рейтинг (по убыванию)</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Категория</label>
                <select id="category" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring focus:ring-violet-500 focus:ring-opacity-50 px-3 py-2">
                    <option value="all">Все категории</option>
                    <option value="Одежда">Одежда</option>
                    <option value="Аксессуары">Аксессуары</option>
                    <option value="Электроника">Электроника</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Диапазон цен</label>
                <div class="flex items-center gap-2">
                    <input type="number" id="minPrice" class="w-1/2 rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 focus:ring-opacity-50 px-3 py-2" placeholder="Мин">
                    <span class="text-gray-500">-</span>
                    <input type="number" id="maxPrice" class="w-1/2 rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 focus:ring-opacity-50 px-3 py-2" placeholder="Макс">
                </div>
            </div>

            <div class="mb-6 flex items-center">
                <input type="checkbox" id="inStock" class="rounded text-violet-600 focus:ring-violet-500">
                <label for="inStock" class="ml-2 text-sm font-medium text-gray-700">В наличии</label>
            </div>

        </aside>

        <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8">
        </div>

        <template id="productItem">
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl duration-300 overflow-hidden relative product-card transition-opacity">
                <div class="absolute top-2 left-2 flex flex-col space-y-1 z-10" data-badge-container>
                </div>
                <img class="w-full h-48 object-cover" src="/src/assets/noimage.png" alt="Изображение">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900"></h2>
                    <p class="text-gray-500 text-sm mt-1">Рейтинг: <span class="font-semibold" data-rating></span></p>
                    <p class="text-gray-500 text-sm mt-1">В наличии: <span data-stock></span></p>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-2xl font-bold text-gray-800"><span data-price></span>₽</span>
                        <button class="compare-btn bg-gray-200 text-gray-700 p-2 rounded-lg shadow-md hover:bg-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50 " title="Добавить к сравнению" data-product-id="">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"></path>
                                <line x1="8" y1="10" x2="16" y2="10"></line>
                                <line x1="8" y1="14" x2="16" y2="14"></line>
                                <line x1="8" y1="18" x2="13" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
        </template>

    </div>

    <div class="flex justify-center mt-8">
        <button id="showMoreBtn" class="bg-violet-600 text-white font-medium py-3 px-6 rounded-lg shadow-md hover:bg-violet-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-opacity-50">
            Показать еще
        </button>
    </div>
</section>
</body>
<script src="/src/assets/js/index.js"></script>

</html>