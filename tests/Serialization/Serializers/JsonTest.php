<?php

namespace Running\tests\Serialization\Serializers\Json;

use Running\Serialization\SerializerInterface;
use Running\Serialization\Serializers\Json;
use Running\Core\Std;

class JsonTest extends \PHPUnit_Framework_TestCase
{

    public function testInterface()
    {
        $serializer = new Json();
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }

    /*
     * ----------
     */

    public function testEncodeScalar()
    {
        $serializer = new Json();

        $this->assertEquals('null', $serializer->encode(null));
        $this->assertEquals('true', $serializer->encode(true));
        $this->assertEquals('false', $serializer->encode(false));

        $this->assertEquals('0', $serializer->encode(0));
        $this->assertEquals('42', $serializer->encode(42));
        $this->assertEquals('-42', $serializer->encode(-42));

        $this->assertEquals('3.14159', $serializer->encode(3.14159));
        $this->assertEquals('-1.2e+34', $serializer->encode(-1.2e34));

        $this->assertEquals('"foobar"', $serializer->encode('foobar'));
        $this->assertEquals('"foo\'bar"', $serializer->encode('foo\'bar'));
    }

    public function testDecodeScalar()
    {
        $serializer = new Json();

        $this->assertEquals(null, $serializer->decode("null"));
        $this->assertEquals(true, $serializer->decode("true"));
        $this->assertEquals(false, $serializer->decode("false"));

        $this->assertEquals(0, $serializer->decode("0"));
        $this->assertEquals(42, $serializer->decode("42"));
        $this->assertEquals(-42, $serializer->decode("-42"));

        $this->assertEquals(3.14159, $serializer->decode("3.14159"));
        $this->assertEquals(-1.2e34, $serializer->decode("-1.2e+34"));

        $this->assertEquals('foobar', $serializer->decode('"foobar"'));
        $this->assertEquals('foo\'bar', $serializer->decode('"foo\'bar"'));
    }

    /*
     * ----------
     */

    public function testEncodeSimpleArray()
    {
        $serializer = new Json();

        $this->assertEquals(
            '["foo",42,3.14159,true,false,null]',
            $serializer->encode(['foo', 42, 3.14159, true, false, null])
        );

        $this->assertEquals(
            '{"1":"foo","2":42,"3":3.14159,"4":true,"5":false,"6":null}',
            $serializer->encode([1 => 'foo', 2 => 42, 3 => 3.14159, 4 => true, 5 => false, 6 => null])
        );

        $this->assertEquals(
            '{"foo":"bar","baz":42,"quux":3.14159,"quuux":null}',
            $serializer->encode(['foo' => 'bar', 'baz' => 42, 'quux' => 3.14159, 'quuux' => null])
        );
    }

    public function testDecodeSimpleArray()
    {
        $serializer = new Json();

        $this->assertEquals(
            ['foo', 42, 3.14159, true, false, null],
            $serializer->decode('["foo", 42, 3.14159, true, false, null]')
        );

        $this->assertEquals(
            [1 => 'foo', 2 => 42, 3 => 3.14159, 4 => true, 5 => false, 6 => null],
            $serializer->decode('{"1":"foo","2":42,"3":3.14159,"4":true,"5":false,"6":null}', true)
        );

        $this->assertEquals(
            ['foo' => 'bar', 'baz' => 42, 'quux' => 3.14159, 'quuux' => null],
            $serializer->decode('{"foo":"bar","baz":42,"quux":3.14159,"quuux":null}', true)
        );
    }

    /*
     * ----------
     */

    public function testEncodeNestedArray()
    {
        $serializer = new Json();

        $this->assertEquals(
            '[1,2,[3,4]]',
            $serializer->encode([1, 2, [3, 4]])
        );
    }

    public function testDecodeNestedArray()
    {
        $serializer = new Json();

        $this->assertEquals(
            [1, 2, [3, 4]],
            $serializer->decode('[1,2,[3,4]]')
        );
    }

    /*
     * ----------
     */

    public function testEncodeSimpleObject()
    {
        $serializer = new Json();
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertEquals(
            '{"foo":"bar","baz":42}',
            $serializer->encode($obj)
        );
    }

    public function testDecodeSimpleObject()
    {
        $serializer = new Json();
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $obj->baz = 42;
        $serializedObj = $serializer->encode($obj);

        $this->assertEquals($obj, $serializer->decode($serializedObj));
    }

    /*
     * ----------
     */

    public function testEncodeStdObject()
    {
        $serializer = new Json();
        $obj = new Std();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertEquals(
            '{"foo":"bar","baz":42}',
            $serializer->encode($obj)
        );
    }
}