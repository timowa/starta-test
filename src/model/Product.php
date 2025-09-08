<?php

namespace App\Model;

use App\Core\Model;
use DateTime;
use voku\db\DB;

class Product extends Model
{
    private int $id;
    private int $stock;
    private float $price;
    private float $rating;
    private int $categoryId;
    private string $name;
    private ?DateTime $createdAt;
    private ?Category $category;

    public function __construct(array $data) {
        $this->id = intval($data['id']);
        $this->stock = intval($data['stock']);
        $this->price = floatval($data['price']);
        $this->rating = floatval($data['rating']);
        $this->categoryId = intval($data['category_id']);
        $this->name = $data['name'];
        try {
            $this->createdAt = new DateTime($data['created_at']);
        } catch (\Exception $e) {
            $this->createdAt = null;
        }
    }
    public function getId(): int{
        return $this->id;
    }
    public function getStock(): int{
        return $this->stock;
    }
    public function getPrice(): float{
        return $this->price;
    }
    public function getRating(): float{
        return $this->rating;
    }
    public function getCategoryId(): int{
        return $this->categoryId;
    }
    public function getName(): string{
        return $this->name;
    }
    public function getCreatedAt($format = ''){
        if ($format !== '') {
            try {
                return rdate($format, $this->createdAt);
            } catch (\Exception $e) {
                return null;
            }
        }
        return $this->createdAt;
    }

    public function getIsNew() {
        $dt = new DateTime();
        return $dt->diff($this->createdAt)->days <= 30;
    }

    public function getIsTop() {
        return $this->rating >= 4.7;
    }
    public function getIsProfit() {
        $category = $this->getCategory();
        return $this->price <= $category->getMedianPrice() * 0.15;
    }
    public function getIsLast() {
        return $this->stock <= 3;
    }

    public function getCategory(): Category{
        if (!isset($this->category)) {
            $this->category = Category::findById($this->categoryId);
        }
        return $this->category;
    }

    public static function findById(int $id)
    {
        // TODO: Implement findById() method.
    }

    public static function findByParams(array $params = [])
    {
        $where = [];
        $order = [];
        $limit = intval($params['limit']);
        $offset = intval($params['offset']);
        $allowedParams = [
            'query' => 'string',
            'sort' => 'string',
            'category' => 'integer',
            'price_min' => 'integer',
            'price_max' => 'integer',
            'in_stock' => 'boolean'
        ];
        foreach ($params as $key => &$value) {
            if (!in_array($key, array_keys($allowedParams))) {
                unset($params[$key]);
            }
            switch ($allowedParams[$key]) {
                case 'string':
                    $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                    if ($value === '') {
                        unset($params[$key]);
                    }
                    break;
                case 'integer':
                    $value = intval($value);
                    break;
                case 'boolean':
                    $value = boolval($value);
                    break;
            }
        }
        $db = DB::getInstance();
        if (!empty($params['query'])) {
            $where[] = "`name` LIKE '%{$params['query']}%'";
        }
        if (!empty($params['sort'])) {
            $sortParam = explode('-', $params['sort']);
            $sortBy = $sortParam[0];
            $sortOrder = strtoupper($sortParam[1]);
            if (in_array($sortOrder, ['ASC', 'DESC'])) {
                switch ($sortBy) {
                    case 'price':
                        $order[] = "`price` {$sortOrder}";
                        break;
                    case 'rating':
                        $order[] = "`rating` {$sortOrder}";
                        break;
                    case 'created':
                        $order[] = "`created_at` {$sortOrder}";
                        break;
                }
            }
        }
        if (intval($params['category_id']) > 0) {
            $where[] = "`category_id` = {$params['category_id']}";
        }
        if (intval($params['price_min']) > 0) {
            $where[] = "`price` > {$params['price_min']}";
        }
        if (intval($params['price_max']) > 0) {
            $where[] = "`price` < {$params['price_max']}";
        }
        if ($params['is_stock'] === true) {
            $where[] = "`stock` > 0";
        }
        if (intval($params['category_id'])) {
            $where[] = "`category_id` = {$params['category_id']}";
        }
        $where = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
        $order = empty($order) ? '' : 'ORDER BY ' . implode(', ', $order);
        $limit = $limit > 0 ? ' LIMIT ' . $limit : '';
        $offset = $offset > 0 ? ' OFFSET ' . $offset : '';
        $sql = "SELECT * FROM `products` $where $order $limit $offset";
        $query = $db->query($sql);
        $result = $query->fetchAllArray();
        if (!empty($result)) {
            $result = array_map(function ($item) {
                return new self($item);
            }, $result);
        }

        return $result;
    }

    protected function validate()
    {
        if ($this->name === '') {
            throw new \Exception('Название товара не может быть пустым');
        }
        if ($this->categoryId === 0) {
            throw new \Exception('Не указана категория товара');
        }
    }

    protected function save()
    {
        $db = DB::getInstance();
        $this->validate();
        if (!$this->exists()) {
            try {
                $this->id = $db->insert('products', [
                    'id' => $this->id,
                    'name' => $this->name,
                    'category_id' => $this->categoryId,
                    'price' => $this->price,
                    'rating' => $this->rating,
                    'stock' => $this->stock,
                ]);
            } catch (\PDOException $e) {
                throw new \Exception('Ошибка создания категории');
            }
        } else{
            try {
                $db->update('products', [
                    'name' => $this->name,
                    'category_id' => $this->categoryId,
                    'price' => $this->price,
                    'rating' => $this->rating,
                    'stock' => $this->stock,
                    'updated_at' => 'NOW()'
                ], "id = {$this->id}");
            } catch (\PDOException $e) {
                throw new \Exception('Ошибка обновления товара');
            }
        }

    }

    private function needsUpdate() {
        $needsUpdate = false;

        if ($this->name !== $this->newName) {
            $needsUpdate = true;
        }
        if ($this->categoryId !== $this->newCategoryId) {
            $needsUpdate = true;
        }
        if ($this->price !== $this->newPrice) {
            $needsUpdate = true;
        }

        if ($this->rating !== $this->newRating) {
            $needsUpdate = true;
        }
        if ($this->stock !== $this->newStock) {
            $needsUpdate = true;
        }
        return $needsUpdate;
    }

    public function update(array $productData)
    {
        $this->newName = trim($productData['name']);
        $this->newCategoryId = intval($productData['category_id']);
        $this->newPrice = floatval($productData['price']);
        $this->newRating = floatval($productData['rating']);
        $this->newStock = intval($productData['stock']);
        if ($this->needsUpdate()) {
            $category = Category::findById($this->newCategoryId);
            if (!$category->exists()) {
                throw new \Exception('Нет такой категории');
            }
            $db = DB::getInstance();
            $this->name = $this->newName;
            $this->categoryId = $this->newCategoryId;
            $this->price = $this->newPrice;
            $this->rating = $this->newRating;
            $this->stock = $this->newStock;
            $this->save();
            return true;
        }
        return false;
    }
}