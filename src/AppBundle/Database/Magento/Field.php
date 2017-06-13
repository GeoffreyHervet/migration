<?php

namespace AppBundle\Database\Magento;

use ClassesWithParents\F;

class Field
{
    public const TYPES = [
        'string',
        'int',
        'datetime',
        'text'
    ];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $querySelect;

    /**
     * @var bool
     */
    protected $isTag;

    /**
     * Field constructor.
     *
     * @param string $name
     * @param string $querySelect
     * @param string $type
     */
    public function __construct(string $name, string $querySelect, string $type)
    {
        $this->name = $name;
        $this->querySelect = $querySelect;
        $this->type = $type;
        $this->isTag = false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->querySelect;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getQuerySelect(): string
    {
        return $this->querySelect;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isTag(): bool
    {
        return $this->isTag;
    }

    public function setIsTag(bool $value): Field
    {
        $this->isTag = $value;

        return $this;
    }
}
