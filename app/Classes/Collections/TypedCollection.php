<?php

namespace App\Classes\Collections;

use App\Classes\Collections\Exceptions\InvalidCollectionItemException;
use Illuminate\Support\Collection;

abstract class TypedCollection extends Collection
{
    public function __construct($items, protected string $type)
    {
        foreach ($items as $item) {
            $this->validateItem($item);
        }

        parent::__construct($items);
    }

    public function push(...$values)
    {
        foreach ($values as $value) {
            $this->validateItem($value);
        }

        return parent::push($values);
    }

    public function add($item)
    {
        $this->validateItem($item);

        return parent::add($item);
    }

    protected function validateItem(mixed $item): void
    {
        if (is_object($item) && (!$item instanceof $this->type)) {
            throw new InvalidCollectionItemException($item, $this->type);
        }
    }
}
