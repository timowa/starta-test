<?php
namespace App\Core;
abstract class Model {
    protected static array $toCreate;
    abstract public function getId(): int;
    abstract public static function findById(int $id);
    abstract public static function findByParams(array $params = []);
    abstract protected function save();
    abstract protected function validate();

    public function exists() {
        return $this->getId() !== 0;
    }
    public static function setToCreate($item) {
        self::$toCreate[] = $item;
    }

    public static function createMultiply() {
        if (empty(self::$toCreate)) {
            return false;
        }
        $new = [];
        $errors = [];
        foreach (self::$toCreate as $item) {
            try {
                $newItem = new static($item);
                $newItem->save();
                $new[] = $newItem;
            } catch (\Exception $e) {
                $errors[] = [
                    'data' => $item,
                    'error' => $e->getMessage()
                ];
            }
        }
        self::$toCreate = [];
        return [
            'errors' => $errors,
            'items' => $new
        ];
    }
}