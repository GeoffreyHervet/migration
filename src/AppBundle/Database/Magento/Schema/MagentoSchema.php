<?php

namespace AppBundle\Database\Magento\Schema;

use AppBundle\Database\SchemaInterface;
use Illuminate\Support\Collection;

class MagentoSchema implements SchemaInterface
{
    /**
     * @var Collection
     */
    protected $fields;

    public function getFields(): Collection
    {
        return $this->fields->keys();
    }

    public function removeField(string $fields): Collection
    {
        // TODO: Implement removeField() method.
    }

    public function getElement($identifier): Collection
    {
        // TODO: Implement getElement() method.
    }
}
