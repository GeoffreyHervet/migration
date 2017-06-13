<?php

namespace AppBundle\Database\Magento;

use Illuminate\Support\Collection;

class CustomerSchemaReader extends SchemaReader
{
    /**
     * @var Collection|Field[]
     */
    protected $fields;

    public function getFields(): Collection
    {
        $this->buildFields();

        return $this->fields;
    }

    public function filterFields(callable $callback): Collection
    {
        $this->fields = $this->getFields()->filter($callback);

        return $this->fields;
    }

    public function addField(Field $field)
    {
        $this->fields->push($field);
    }

    public function getSelectFields(): string
    {
        return $this->fields->implode(null, "\nUNION ALL ");
    }

    /**
     * @return Collection
     */
    protected function buildFields(): Collection
    {
        if (null !== $this->fields) {
            return $this->fields;
        }

        $this->fields = $this->fetchRequest($this->getBuildFieldSql())->map(function (array $field) {
            if ('email' === $field['name']) {
                return new Field(
                    $field['name'],
                    "SELECT `email` as `value`, '{$field['name']}' as `field` FROM `customer_entity` WHERE entity_id  = @entityId",
                    $field['type']
                );
            }

            return new Field(
                $field['name'],
                $this->getSelectStatement($field['type'], $field['attribute_id'], $field['name']),
                $field['type']
            );
        });

        return $this->fields;
    }

    public function getCustomerSchema(): Collection
    {
        return $this->getCustomerTables();
    }

    protected function getCustomerTables(): Collection
    {
        $tableNameStart = "{$this->tablePrefix}customer_entity";

        return $this->showTables()->filter(function ($tableName) use ($tableNameStart) {
            return strpos($tableName, $tableNameStart) === 0;
        });
    }

    protected function getBuildFieldSql()
    {
        $this->connection->exec('SET @entityId = (SELECT max(entity_id) FROM customer_entity)');

        return <<<SQL
          SELECT `customer_entity_varchar`.`attribute_id`, 'string' AS `type`, eav_attribute.attribute_code AS `name` FROM `customer_entity_varchar` LEFT JOIN eav_attribute ON eav_attribute.attribute_id = customer_entity_varchar.attribute_id WHERE (entity_id = @entityId) 
UNION ALL SELECT `customer_entity_int`.`attribute_id`, 'int' AS `type`, eav_attribute.attribute_code AS `name` FROM `customer_entity_int` LEFT JOIN eav_attribute ON eav_attribute.attribute_id = customer_entity_int.attribute_id WHERE (entity_id = @entityId) 
UNION ALL SELECT `customer_entity_datetime`.`attribute_id`, 'datetime' AS `type` , eav_attribute.attribute_code AS `name` FROM `customer_entity_datetime` LEFT JOIN eav_attribute ON eav_attribute.attribute_id = customer_entity_datetime.attribute_id WHERE (entity_id = @entityId) 
UNION ALL SELECT `customer_entity_text`.`attribute_id`, 'text' AS `type` , eav_attribute.attribute_code AS `name` FROM `customer_entity_text` LEFT JOIN eav_attribute ON eav_attribute.attribute_id = customer_entity_text.attribute_id WHERE (entity_id = @entityId)
UNION ALL SELECT NULL AS `attribute_id`, 'string' AS `type`, 'email' AS `name` FROM customer_entity WHERE entity_id = @entityId
SQL;
    }

    /**
     * @param string $type
     * @param int $attributeId
     * @param string $fieldName
     *
     * @return string
     */
    protected function getSelectStatement(string $type, int $attributeId, string $fieldName): string
    {
        $table = [
            'string' => 'customer_entity_varchar',
            'int' => 'customer_entity_int',
            'datetime' => 'customer_entity_datetime',
            'text' => 'customer_entity_text'
        ][$type];

        return "SELECT `value`, '{$fieldName}' as `field` FROM `{$table}` WHERE attribute_id = {$attributeId} AND entity_id = @entityId";
    }
}
