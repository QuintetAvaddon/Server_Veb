<?php
namespace MyProject\Models;

use MyProject\Services\Db;

abstract class ActiveRecordEntity
{
    protected $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function __set(string $name, $value)
    {
        $camelCaseName = $this->underscoreToCamelCase($name);
        $this->$camelCaseName = $value;
    }

    private function underscoreToCamelCase(string $source): string
    {
        return lcfirst(str_replace('_', '', ucwords($source, '_')));
    }

    public static function findAll(): array
    {
        $db = Db::getInstance();
        return $db->query(
            'SELECT * FROM `' . static::getTableName() . '`;',
            [],
            static::class
        );
    }

    public static function getById(int $id): ?self
    {
        $db = Db::getInstance();
        $entities = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE id = :id;',
            [':id' => $id],
            static::class
        );
        return $entities ? $entities[0] : null;
    }

    public function save(): void
    {
        $mappedProperties = $this->mapPropertiesToDbFormat();
        if ($this->id !== null) {
            $this->update($mappedProperties);
        } else {
            $this->insert($mappedProperties);
        }
    }

    private function update(array $mappedProperties): void
    {
        $columnsToParams = [];
        $paramsToValues = [];
        $index = 1;
        foreach ($mappedProperties as $column => $value) {
            $param = ':param' . $index;
            $columnsToParams[] = $column . ' = ' . $param;
            $paramsToValues[$param] = $value;
            $index++;
        }
        $sql = 'UPDATE `' . static::getTableName() . '` SET ' . implode(', ', $columnsToParams) . ' WHERE id = ' . $this->id;
        $db = Db::getInstance();
        $db->query($sql, $paramsToValues, static::class);
    }

    private function insert(array $mappedProperties): void
    {
        $filteredProperties = array_filter($mappedProperties, function ($value) {
            return $value !== null;
        });

        $columns = [];
        $params = [];
        $paramsToValues = [];
        $index = 1;
        foreach ($filteredProperties as $column => $value) {
            $param = ':param' . $index;
            $columns[] = $column;
            $params[] = $param;
            $paramsToValues[$param] = $value;
            $index++;
        }

        $sql = 'INSERT INTO `' . static::getTableName() . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $params) . ')';
        $db = Db::getInstance();
        $db->query($sql, $paramsToValues, static::class);
        $this->id = $db->getLastInsertId();
    }

    private function mapPropertiesToDbFormat(): array
    {
        $reflector = new \ReflectionObject($this);
        $properties = $reflector->getProperties();
        $mappedProperties = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyNameAsUnderScore = $this->camelCaseToUnderscore($propertyName);
            $mappedProperties[$propertyNameAsUnderScore] = $this->$propertyName;
        }
        return $mappedProperties;
    }

    private function camelCaseToUnderscore(string $source): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $source));
    }

    abstract protected static function getTableName(): string;
}
