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

    public static function findById(int $id)
    {
        // TODO: Implement findById() method.
    }

    public static function findByParams(array $params = [])
    {
        $db = DB::getInstance();
        $query = $db->query("SELECT * FROM `products`");
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