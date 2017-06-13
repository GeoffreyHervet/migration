<?php

namespace AppBundle\Database;

use Doctrine\DBAL\Connection;
use Illuminate\Support\Collection;

abstract class AbstractSchemaReader implements SchemaReaderInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * AbstractSchemaReader constructor.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     */
    protected function showTables(): Collection
    {
        return $this->fetchRequest('SHOW TABLES')->flatten(1);
    }

    protected function fetchRequest(string $request): Collection
    {
        return Collection::make($this->connection->executeQuery($request)->fetchAll());
    }

}
