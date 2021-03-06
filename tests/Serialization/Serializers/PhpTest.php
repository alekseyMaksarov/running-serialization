<?php

namespace Running\tests\Serialization\Serializers\Php;

use Running\Core\Std;
use Running\Serialization\SerializerInterface;
use Running\Serialization\Serializers\Php;

class testClass
{
    public $foo;
    private $bar;
    protected $baz;

    public function setPrivateField($value)
    {
        $this->bar = $value;
    }

    public function setProtectedField($value)
    {
        $this->baz = $value;
    }

    public static function __set_state($array)
    {
        $testObj = new testClass();
        $testObj->foo = $array['foo'];
        $testObj->setProtectedField($array['baz']);
        $testObj->setPrivateField($array['bar']);
        return $testObj;
    }
}

class FileTest extends \PHPUnit_Framework_TestCase
{

    public function testInterface()
    {
        $serializer = new Php();
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }

    /*
     * ----------
     */

    public function testEncodeScalar()
    {
        $serializer = new Php();

        $this->assertEquals('NULL',  $serializer->encode(null));
        $this->assertEquals('true',  $serializer->encode(true));
        $this->assertEquals('false', $serializer->encode(false));

        $this->assertEquals('0',  $serializer->encode(0));
        $this->assertEquals('42', $serializer->encode(42));
        $this->assertEquals('-42', $serializer->encode(-42));

        $this->assertEquals('3.14159', $serializer->encode(3.14159));
        $this->assertEquals('-1.2E+34', $serializer->encode(-1.2e34));

        $this->assertEquals("'foobar'", $serializer->encode('foobar'));
        $this->assertEquals("'foo\\'bar'", $serializer->encode('foo\'bar'));
    }

    public function testDecodeScalar()
    {
        $serializer = new Php();

        $this->assertEquals(null,  $serializer->decode('NULL'));
        $this->assertEquals(null,  $serializer->decode('null'));
        $this->assertEquals(true,  $serializer->decode('TRUE'));
        $this->assertEquals(true,  $serializer->decode('true'));
        $this->assertEquals(false, $serializer->decode('FALSE'));
        $this->assertEquals(false, $serializer->decode('false'));

        $this->assertEquals(0,  $serializer->decode('0'));
        $this->assertEquals(42, $serializer->decode('42'));
        $this->assertEquals(-42, $serializer->decode('-42'));

        $this->assertEquals('3.14159', $serializer->decode('3.14159'));
        $this->assertEquals(-1.2e34, $serializer->decode('-1.2E+34'));
        $this->assertEquals(-1.2e34, $serializer->decode('-1.2e34'));

        $this->assertEquals('foobar', $serializer->decode("'foobar'"));
        $this->assertEquals('foo\'bar', $serializer->decode("'foo\\'bar'"));
    }

    /*
     * ----------
     */

    public function testEncodeSimpleArray()
    {
        $serializer = new Php();

        $this->assertEquals(
            "[\n  0 => 1,\n  1 => 2,\n  2 => 3,\n]",
            $serializer->encode([1, 2, 3])
        );
        $this->assertEquals(
            "[\n  'foo' => 100,\n  'bar' => 200,\n  'baz' => 300,\n]",
            $serializer->encode(['foo' => 100, 'bar' => 200, 'baz' => 300])
        );
    }

    public function testDecodeSimpleArray()
    {
        $serializer = new Php();

        $this->assertEquals(
            [1, 2, 3],
            $serializer->decode("[0 => 1, 1 => 2, 2 => 3,]")
        );
        $this->assertEquals(
            ['foo' => 100, 'bar' => 200, 'baz' => 300],
            $serializer->decode("['foo' => 100, 'bar' => 200, 'baz' => 300,]")
        );
    }

    /*
     * ----------
     */

    public function testEncodeNestedArray()
    {
        $serializer = new Php();

        $this->assertEquals(
            "[\n  0 => 1,\n  1 =>\n  [\n    0 => 2,\n    1 => 3,\n  ],\n]",
            $serializer->encode([1, [2, 3]])
        );
    }

    public function testDecodeNestedArray()
    {
        $serializer = new Php();

        $this->assertEquals(
            [1, [2, 3]],
            $serializer->decode("[0 => 1, 1 => [0 => 2, 1 => 3,],]")
        );
    }

    /*
     * ----------
     */

    public function testEncodeSimpleObject()
    {
        $serializer = new Php();
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertEquals(
            "stdClass::__set_state([\n   'foo' => 'bar',\n   'baz' => 42,\n])",
            $serializer->encode($obj)
        );
    }

    public function testEncodeStdObject()
    {
        $serializer = new Php();
        $obj = new Std();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertEquals(
            "Running\Core\Std::__set_state([\n   '__data' =>\n  [\n    'foo' => 'bar',\n    'baz' => 42,\n  ],\n])",
            $serializer->encode($obj)
        );
    }

    /*
     * ----------
     */

    /**
     * @expectedException \Running\Serialization\DecodeException
     */
    public function testDecodeParseError()
    {
        $serializer = new Php();
        $serializer->decode('foo() function does not exist');
        $this->fail();
    }

    /*
     * ----------
     */

    public function testEncodeArrayOfSimpleObjects()
    {
        $serializer = new Php();
        $obj1 = new \stdClass();
        $obj1->foo = 'bar';
        $obj1->baz = 42;
        $obj2 = new \stdClass();
        $obj2->bat = 'quux';
        $obj2->quuux = 1337;
        $arrayOfSimpleObjects = [$obj1, $obj2];

        $this->assertEquals(
            "[\n  0 =>\n  stdClass::__set_state([\n     'foo' => 'bar',\n     'baz' => 42,\n  ]),\n  1 =>\n  stdClass::__set_state([\n     'bat' => 'quux',\n     'quuux' => 1337,\n  ]),\n]",
            $serializer->encode($arrayOfSimpleObjects)
        );
    }

    public function testEncodeArrayOfStdObjects()
    {
        $serializer = new Php();
        $obj1 = new Std();
        $obj1->foo = 'bar';
        $obj1->baz = 42;
        $obj2 = new Std();
        $obj2->bat = 'quux';
        $obj2->quuux = 1337;
        $arrayOfStdObjects = [$obj1, $obj2];

        $this->assertEquals(
            "[\n  0 =>\n  Running\Core\Std::__set_state([\n     '__data' =>\n    [\n      'foo' => 'bar',\n      'baz' => 42,\n    ],\n  ]),\n  1 =>\n  Running\Core\Std::__set_state([\n     '__data' =>\n    [\n      'bat' => 'quux',\n      'quuux' => 1337,\n    ],\n  ]),\n]",
            $serializer->encode($arrayOfStdObjects)
        );
    }

    /*
     * ----------
     */

    public function testDecodeObject()
    {
        $serializer = new Php();
        $obj = new testClass();
        $obj->foo = 'quux';
        $obj->setProtectedField('quuux');
        $obj->setPrivateField(42);
        $serializedObj = $serializer->encode($obj);

        $this->assertEquals($obj, $serializer->decode($serializedObj));
    }

}