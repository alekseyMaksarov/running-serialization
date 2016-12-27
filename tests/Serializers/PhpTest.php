<?php

namespace Running\tests\Serializers\Php;

use Running\Serialization\SerializerInterface;
use Running\Serializers\Php;

class FileTest extends \PHPUnit_Framework_TestCase
{

    public function testInterface()
    {
        $serializer = new Php();
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }

    public function testEncodeScalar()
    {
        $serializer = new Php();

        $this->assertEquals('NULL',  $serializer->encode(null));
        $this->assertEquals('true',  $serializer->encode(true));
        $this->assertEquals('false', $serializer->encode(false));

        $this->assertEquals('0',  $serializer->encode(0));
        $this->assertEquals('42', $serializer->encode(42));

        $this->assertEquals('3.14159', $serializer->encode(3.14159));
        $this->assertEquals('-1.2E+34', $serializer->encode(-1.2e34));

        $this->assertEquals('\'foobar\'', $serializer->encode('foobar'));
        $this->assertEquals('\'foo\\\'bar\'', $serializer->encode('foo\'bar'));
    }

}