<?php
use App\Model\Category;
use App\Model\Product;

if (!is_csrf_valid()) {
    abort();
}

$productUpdateError = 0;
$productInsertError = 0;
$productUpdateSuccess = 0;
$productInsertSuccess = 0;
$categoryInsertError = 0;
$categoryInsertSuccess = 0;

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileExtension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, ['json', 'csv'])) {
        echo "<pre>";
        var_dump('error: invalid extension');
        echo "</pre>";
        die();
    }
    switch ($fileExtension) {
        case 'json':
            $products = json_decode(file_get_contents($fileTmpName), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "<pre>";
                var_dump('error: invalid json');
                echo "</pre>";
                die();
            }
            break;

        case 'csv':
            // Создаем временный ресурс для чтения CSV
            $handle = fopen($fileTmpName, 'r');
            if ($handle === false) {
                echo "<pre>";
                var_dump('error: cannot open file for reading');
                echo "</pre>";
                die();
            }

            // Читаем строки из CSV-файла
            $keys = fgetcsv($handle);
            while (($values = fgetcsv($handle)) !== false) {
                $products[] = array_combine($keys, $values);
            }
            fclose($handle); // Закрываем ресурс
            break;
    }

    $products = array_combine(array_column($products, 'id'), $products);

    if (empty($products)) {
        echo "<pre>";
        var_dump('error: empty products');
        echo "</pre>";
        die();
    }
    $categories = array_unique(array_column($products, 'category'));
    $allCategories = Category::findByParams();
    $allProducts = Product::findByParams();
    $newCats = array_diff($categories, array_map(fn ($c) => $c->getName() ,$allCategories));
    if (!empty($newCats)) {
        foreach ($newCats as $category) {
            Category::setToCreate(['name' => $category]);
        }
        $newCats = Category::createMultiply();
        if ($newCats !== false) {
            $categoryInsertSuccess += count($newCats['items']);
            $categoryInsertError += count($newCats['errors']);
            if (count($newCats['items']) > 0) {
                $allCategories = array_merge($allCategories, $newCats['items']);
            }
        }


    }
    foreach ($products as $product) {
        $product['category_id'] = current(array_filter($allCategories, fn ($c) => $c->getName() === $product['category']))->getId();
        $productObj = current(array_filter($allProducts, fn ($p) => $p->getId() === intval($product['id'])));
        if ($productObj) {
            try {
                if ($productObj->update($product)) {
                    $productUpdateSuccess++;
                }
            } catch (\Exception $e) {
                $productUpdateError++;
            }
        } else {
            Product::setToCreate($product);
        }
    }
    $newProducts = Product::createMultiply();
    if ($newProducts !== false) {
        $productInsertSuccess += count($newProducts['items']);
        $productInsertError += count($newProducts['errors']);
    }

}
$res = sprintf("Категорий создано: %d\n
Ошибка создания категория: %d\n
Товаров создано: %d\n
Ошибка создания товара: %d\n
Товаров обновлено: %d\n
Ошибка обновления товаров: %d",
    $categoryInsertSuccess,
    $categoryInsertError,
    $productInsertSuccess,
    $productInsertError,
    $productUpdateSuccess,
    $productUpdateError);

echo $res;
exit();