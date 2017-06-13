<?php

namespace Tests\AppBundle\Database;

use AppBundle\Database\SchemaReaderInterface;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

class SchemaReaderTest extends TestCase
{
    public static function testInterfaceIsInterface()
    {
        self::assertTrue((new \ReflectionClass(SchemaReaderInterface::class))->isInterface());
    }

    public static function testHasMethodGetCustomerSchema()
    {
        $refectionClass = new \ReflectionClass(SchemaReaderInterface::class);

        try {
            $refectionClass->getMethod('getCustomerSchema');
            $hasMethod = true;
        } catch (\ReflectionException $exception) {
            $hasMethod = false;
        }

        self::assertTrue($hasMethod, 'has method getCustomerSchema');
    }

    public static function testSignatureGetCustomerSchema()
    {
        $refectionClass = new \ReflectionClass(SchemaReaderInterface::class);
        $refectionMethod = $refectionClass->getMethod('getCustomerSchema');

        self::assertTrue($refectionMethod->isPublic(), 'method is public');
        self::assertFalse($refectionMethod->isStatic(), 'method is not static');
        self::assertEquals(2, $refectionMethod->getNumberOfParameters(), 'method as 2 params');

        $params = $refectionMethod->getParameters();
        self::assertEquals(DriverManager::class, $params[0]->getType(), '1st method param type');
        self::assertFalse($params[0]->allowsNull(), '1st argument is cannot be null');


        self::assertEquals('string', $params[1]->getType(), '2nd method param type');
        self::assertTrue($params[1]->allowsNull(), '1st argument is can be null');

        self::assertEquals('array', $refectionMethod->getReturnType()->__toString(), 'return type is array');
    }

}
