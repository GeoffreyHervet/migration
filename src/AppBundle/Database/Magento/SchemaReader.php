<?php

namespace AppBundle\Database\Magento;

use AppBundle\Database\AbstractSchemaReader;
use Doctrine\DBAL\Connection;
use Illuminate\Support\Collection;

class SchemaReader extends AbstractSchemaReader
{
    /**
     * @var null|string
     */
    protected $tablePrefix;

    /**
     * SchemaReader constructor.
     *
     * @param Connection $connection
     * @param null|string $tablePrefix
     */
    public function __construct(Connection $connection, ?string $tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;
        parent::__construct($connection);
    }
}
