<?php

namespace Running\Serializers;

use Running\Serialization\SerializerInterface;

/**
 * Class Php
 * @package Running\Serializers
 */
class Php
    implements SerializerInterface
{

    public function encode($data): string
    {
        return preg_replace(['~^(\s*)array\s*\($~im', '~^(\s*)\)(\,?)$~im', '~\s+$~im'], ['$1[', '$1]$2', ''], var_export($data, true));
    }

    public function decode(string $data)
    {
        // TODO: Implement decode() method.
    }
}