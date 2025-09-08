<?php

namespace App\Model;

use App\Core\Model;
use voku\db\DB;

class Category extends Model
{
    private int $id;
    private string $name;
    private ?array $products;
    public function __construct(array $data) {
        $this->id = intval($data['id']);
        $this->name = trim($data['name']);
    }
    public function getId(): int {
        return $this->id;
    }
    public function getName(): string {
        return $this->name;
    }

    public static function findById(int $id)
    {
        $db = DB::getInstance();
        $query = $db->query("SELECT * FROM `categories` WHERE `id` = $id");
        $result = $query->fetchArray();
        return new self($result);
    }

    public static function findByParams(array $params = [])
    {
        $db = DB::getInstance();
        $query = $db->query("SELECT * FROM `categories`");
        $result = $query->fetchAllArray();
        if (!empty($result)) {
            $result = array_map(function ($item) {
                return new self($item);
            }, $result);
        }

        return $result;
    }



    protected function validate() {
        if ($this->name === '') {
            throw new \Exception('Название категории не может быть пустым');
        }
    }

    protected function save() {
        $db = DB::getInstance();
        $this->validate();
        try {
            $this->id = $db->insert('categories', ['name' => $this->name]);
        } catch (\PDOException $e) {
            throw new \Exception('Ошибка создания категории');
        }
    }

    public function getProducts() {
        if (!isset($this->products)) {
            $this->products = Product::findByParams(['category_id' => $this->id]);
        }
        return $this->products;
    }

    public function getMedianPrice() {
        $products = $this->getProducts();
        $allPrices = array_map(fn ($p) => $p->getPrice(), $products);
        if (!empty($allPrices)) {
            return array_sum($allPrices) / count($allPrices);
        }
        return 0;
    }
}