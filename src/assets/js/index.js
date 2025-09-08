
const productGrid = document.getElementById('productGrid');
const showMoreBtn = document.getElementById('showMoreBtn');
const productItemTemplate = document.getElementById('productItem');
const filterForm = document.getElementById('filterForm');

const badgeNew = '<div class="flex items-center space-x-1 px-2 py-1 bg-purple-500 text-white text-xs font-semibold rounded-full shadow-md">\n' +
    '                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>\n' +
    '                                <span>Новинка</span>\n' +
    '                            </div>';
const badgeTop = '<div class="flex items-center space-x-1 px-2 py-1 bg-yellow-500 text-white text-xs font-semibold rounded-full shadow-md">\n' +
    '                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" stroke="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>\n' +
    '                                <span>Топ рейтинг</span>\n' +
    '                            </div>';
const badgeProfit = '<div class="flex items-center space-x-1 px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-full shadow-md">\n' +
    '                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" stroke="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line><line x1="12" y1="12" x2="17.66" y2="17.66"></line><line x1="12" y1="12" x2="6.34" y2="17.66"></line></svg>\n' +
    '                                <span>Выгодно</span>\n' +
    '                            </div>';
const badgeLast = '<div class="flex items-center space-x-1 px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded-full shadow-md">\n' +
    '                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" stroke="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="2" rx="1" ry="1"></rect></svg>\n' +
    '                                <span>Последний на складе</span>\n' +
    '                            </div>';

let upload = false;
function showLoadingState() {
    productGrid.innerHTML = '';
    const numberOfSkeletons = 12;
    for (let i = 0; i < numberOfSkeletons; i++) {
        const skeletonCard = document.createElement('div');
        skeletonCard.className = 'skeleton-card';
        skeletonCard.innerHTML = `
                    <div class="skeleton-img w-full h-48 mb-4"></div>
                    <div class="skeleton-text w-3/4 h-4 mb-2"></div>
                    <div class="skeleton-text w-1/2 h-4 mb-2"></div>
                    <div class="skeleton-text w-2/3 h-4 mb-4"></div>
                `;
        productGrid.appendChild(skeletonCard);
    }
}

function hideLoadingState() {
    document.querySelectorAll('.skeleton-card').forEach(el => {el.remove()})
}
function clearProductGrid() {
    document.querySelectorAll('.product-card').forEach(el => {el.remove()});
}

function loadProducts(firstLoad = false) {
    if (upload === false && firstLoad === false) {
        return;
    }
    const searchParams =  Object.fromEntries((new URLSearchParams(window.location.search)).entries());
    const productCards = document.querySelectorAll('.product-card');
    searchParams['offset'] = productCards.length;
    $.ajax({
        url: "/api/load-products",
        method: "POST",
        data: searchParams,
        beforeSend: function() {
            upload = false;
            if (document.querySelectorAll('.skeleton-card').length > 0) {
                showLoadingState();
            }
        },
        success: function(products) {
            if (products.length > 0) {
                const emptyRow = document.getElementById('empty');
                console.log(emptyRow)
                if (emptyRow) {
                    emptyRow.remove()
                }
                $.each(products, function (i, product) {
                    productGrid.append(composeProductItem(product))
                })
                hideLoadingState();
                upload = !(products < 12) && firstLoad === false;
            } else {
                productGrid.innerHTML = '<p class="col-span-full text-center text-gray-500" id="empty">Нет товаров, соответствующих вашим критериям.</p>';
            }

        }
    })
}

function composeProductItem(product) {
    let newItem = productItemTemplate.content.cloneNode(true);
    let ratingClass = '';
    newItem.querySelector('[data-rating]').textContent = product.rating
    switch (true) {
        case product.rating >= 4:
            ratingClass = 'rating-high'
            break;
        case product.rating > 2.7 && product.rating < 4:
            ratingClass = 'rating-medium';
            break;
        case product.rating <= 2.7:
            ratingClass = 'rating-bad';
            break;
    }
    newItem.querySelector('[data-rating]').classList.add(ratingClass);
    newItem.querySelector('[data-stock]').textContent = product.stock;
    newItem.querySelector('[data-price]').textContent = product.price;
    newItem.querySelector('h2').textContent = product.name;
    newItem.querySelector('button').dataset.productId = product.id;

    if (product.isNew) {
        newItem.querySelector('[data-badge-container]').innerHTML += badgeNew;
    }
    if (product.isTop) {
        newItem.querySelector('[data-badge-container]').innerHTML += badgeTop;
    }
    if (product.isProfit) {
        newItem.querySelector('[data-badge-container]').innerHTML += badgeProfit;
    }
    if (product.isLast) {
        newItem.querySelector('[data-badge-container]').innerHTML += badgeLast;
    }
    return newItem;
}



$(document).ready(function() {
    showLoadingState();
    loadProducts(true);
    showMoreBtn.addEventListener('click', function() {
        upload = true;
        loadProducts()
        this.classList.add('hidden')
    })
    window.addEventListener('scroll', function() {
        let productCards = document.querySelectorAll('.product-card');
        if (productCards.length > 0 && productCards[productCards.length - 1].offsetTop > window.scrollY - 200) {
            loadProducts()
        }
    })

    document.getElementById('applyFilter').addEventListener('click', function(e) {
        e.preventDefault()
        const formData = new FormData(filterForm);
        const url = window.location;
        let searchParams = new URLSearchParams();
        for (const pair of formData.entries()) {
            if (pair[1] !== "" && pair[1] !== 'date-desc' && pair[1] !== 'all') {
                searchParams.set(pair[0], pair[1]);
            }
        }
        if (searchParams.size > 0) {
            let newUrl = url.origin + url.pathname + '?' + searchParams.toString();
            window.history.pushState(null, null, newUrl);
        }
        clearProductGrid();
        loadProducts(true);
    })
    document.getElementById('resetFilter').addEventListener('click', function (e) {
        e.preventDefault();
        const url = window.location;
        const newUrl =  url.origin + url.pathname;
        window.history.pushState(null, null, newUrl);
        clearProductGrid();
        loadProducts(true);
    })
})