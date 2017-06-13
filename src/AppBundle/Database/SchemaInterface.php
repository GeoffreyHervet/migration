<?php


namespace AppBundle\Database;

use Illuminate\Support\Collection;

interface SchemaInterface
{
    public function getFields(): Collection;

    public function removeField(string $fields): Collection;

    public function getElement($identifier): Collection;
}
