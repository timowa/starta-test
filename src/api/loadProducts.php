<?php
header('Content-Type: application/json');
use App\Model\Product;

$params = $_POST;
$params['limit'] = 12;
$products = Product::findByParams($params);
$products = array_map(function ($product) {
    return [
        'id' => $product->getId(),
        'name' => $product->getName(),
        'price' => $product->getPrice(),
        'rating' => $product->getRating(),
        'stock' => $product->getStock(),
        'isNew' => $product->getIsNew(),
        'isTop' => $product->getIsTop(),
        'isProfit' => $product->getIsProfit(),
        'isLast' => $product->getIsLast(),
    ];
}, $products);
echo json_encode($products, true);