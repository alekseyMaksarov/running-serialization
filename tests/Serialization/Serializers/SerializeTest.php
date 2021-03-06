<?php

namespace Running\tests\Serialization\Serializers\Serialize;

use Running\Core\Std;
use Running\Serialization\SerializerInterface;
use Running\Serialization\Serializers\Serialize;

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
}

class SerializeTest extends \PHPUnit_Framework_TestCase
{

    public function testInterface()
    {
        $serializer = new Serialize();
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }

    /*
     * ----------
     */

    public function testEncodeScalar()
    {
        $serializer = new Serialize();

        $this->assertEquals('N;',  $serializer->encode(null));
        $this->assertEquals('b:1;',  $serializer->encode(true));
        $this->assertEquals('b:0;', $serializer->encode(false));

        $this->assertEquals('i:0;',  $serializer->encode(0));
        $this->assertEquals('i:42;', $serializer->encode(42));
        $this->assertEquals('i:-42;', $serializer->encode(-42));

        $this->assertThat(
            $serializer->encode(3.14159),
            $this->logicalOr(
                $this->equalTo('d:3.14159;'),
                $this->equalTo('d:3.1415899999999999;')
            )
        );

        $this->assertThat(
            $serializer->encode(-1.2e34),
            $this->logicalOr(
                $this->equalTo('d:-1.2E+34;'),
                $this->equalTo('d:-1.1999999999999999E+34;')
            )
        );

        $this->assertEquals('s:6:"foobar";', $serializer->encode('foobar'));
        $this->assertEquals('s:7:"foo\'bar";', $serializer->encode('foo\'bar'));
        $this->assertEquals('s:7:"foo"bar";', $serializer->encode('foo"bar'));
    }

    public function testDecodeScalar()
    {
        $serializer = new Serialize();

        $this->assertEquals(null,  $serializer->decode('N;'));
        $this->assertEquals(true,  $serializer->decode('b:1;'));
        $this->assertEquals(false, $serializer->decode('b:0;'));

        $this->assertEquals(0,  $serializer->decode('i:0;'));
        $this->assertEquals(42, $serializer->decode('i:42;'));
        $this->assertEquals(-42, $serializer->decode('i:-42;'));

        $this->assertEquals('3.14159', $serializer->decode('d:3.14159;'));
        $this->assertEquals(-1.2e34, $serializer->decode('d:-1.2E+34;'));
        $this->assertEquals(-1.2e34, $serializer->decode('d:-1.2e34;'));

        $this->assertEquals('foobar', $serializer->decode('s:6:"foobar";'));
        $this->assertEquals('foo\'bar', $serializer->decode('s:7:"foo\'bar";'));
        $this->assertEquals('foo"bar', $serializer->decode('s:7:"foo"bar";'));
    }

    /*
     * ----------
     */

    public function testEncodeSimpleArray()
    {
        $serializer = new Serialize();

        $this->assertEquals(
            'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}',
            $serializer->encode([1, 2, 3])
        );
        $this->assertEquals(
            'a:3:{s:3:"foo";i:100;s:3:"bar";i:200;s:3:"baz";i:300;}',
            $serializer->encode(['foo' => 100, 'bar' => 200, 'baz' => 300])
        );
    }

    public function testDecodeSimpleArray()
    {
        $serializer = new Serialize();

        $this->assertEquals(
            [1, 2, 3],
            $serializer->decode('a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}')
        );
        $this->assertEquals(
            ['foo' => 100, 'bar' => 200, 'baz' => 300],
            $serializer->decode('a:3:{s:3:"foo";i:100;s:3:"bar";i:200;s:3:"baz";i:300;}')
        );
    }

    /*
     * ----------
     */

    public function testEncodeNestedArray()
    {
        $serializer = new Serialize();

        $this->assertEquals(
            'a:2:{i:0;i:1;i:1;a:2:{i:0;i:2;i:1;i:3;}}',
            $serializer->encode([1, [2, 3]])
        );
    }

    public function testDecodeNestedArray()
    {
        $serializer = new Serialize();

        $this->assertEquals(
            [1, [2, 3]],
            $serializer->decode('a:2:{i:0;i:1;i:1;a:2:{i:0;i:2;i:1;i:3;}}')
        );
    }

    /*
     * ----------
     */

    public function testEncodeSimpleObject()
    {
        $serializer = new Serialize();
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertEquals(
            'O:8:"stdClass":2:{s:3:"foo";s:3:"bar";s:3:"baz";i:42;}',
            $serializer->encode($obj)
        );
    }

    public function testEncodeStdObject()
    {
        $serializer = new Serialize();
        $obj = new Std();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertEquals(
            'C:16:"Running\Core\Std":41:{a:2:{s:3:"foo";s:3:"bar";s:3:"baz";i:42;}}',
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
        $serializer = new Serialize();
        $serializer->decode('invalid data');
        $this->fail();
    }

    /*
     * ----------
     */

    public function testEncodeArrayOfSimpleObjects()
    {
        $serializer = new Serialize();
        $obj1 = new \stdClass();
        $obj1->foo = 'bar';
        $obj1->baz = 42;
        $obj2 = new \stdClass();
        $obj2->bat = 'quux';
        $obj2->quuux = 1337;
        $arrayOfSimpleObjects = [$obj1, $obj2];

        $this->assertEquals(
            'a:2:{i:0;O:8:"stdClass":2:{s:3:"foo";s:3:"bar";s:3:"baz";i:42;}i:1;O:8:"stdClass":2:{s:3:"bat";s:4:"quux";s:5:"quuux";i:1337;}}',
            $serializer->encode($arrayOfSimpleObjects)
        );
    }

    public function testEncodeArrayOfStdObjects()
    {
        $serializer = new Serialize();
        $obj1 = new Std();
        $obj1->foo = 'bar';
        $obj1->baz = 42;
        $obj2 = new Std();
        $obj2->bat = 'quux';
        $obj2->quuux = 1337;
        $arrayOfStdObjects = [$obj1, $obj2];

        $this->assertEquals(
            'a:2:{i:0;C:16:"Running\Core\Std":41:{a:2:{s:3:"foo";s:3:"bar";s:3:"baz";i:42;}}i:1;C:16:"Running\Core\Std":46:{a:2:{s:3:"bat";s:4:"quux";s:5:"quuux";i:1337;}}}',
            $serializer->encode($arrayOfStdObjects)
        );
    }

    public function testDecodeObject()
    {
        $serializer = new Serialize();
        $obj = new testClass();
        $obj->foo = 'quux';
        $obj->setProtectedField('quuux');
        $obj->setPrivateField(42);
        $serializedObj = $serializer->encode($obj);

        $this->assertEquals($obj, $serializer->decode($serializedObj));
    }
}