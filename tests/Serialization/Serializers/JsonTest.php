<?php

namespace Running\tests\Serialization\Serializers\Json;

use Running\Serialization\SerializerInterface;
use Running\Serialization\Serializers\Json;

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
}