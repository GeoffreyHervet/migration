<?php

namespace AppBundle\Database;

interface FieldInterface
{
    public const TYPE_STRING = 'string';
    public const TYPE_TEXT = 'text';
    public const TYPE_DATA = 'datetime';
    public const TYPE_INT = 'integer';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getType(): string;
}
