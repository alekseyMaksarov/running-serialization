<?php

namespace Running\Serialization\Serializers;

use Running\Serialization\SerializerInterface;

/**
 * Class Php
 * @package Running\Serialization\Serializers
 */
class Php
    implements SerializerInterface
{

    /**
     * Serialize method
     * @param mixed $data
     * @return string
     */
    public function encode($data): string
    {
        return preg_replace(['~^(\s*)array\s*\($~im', '~^(\s*)\)(\,?)$~im', '~\s+$~im'], ['$1[', '$1]$2', ''], var_export($data, true));
    }

    /**
     * Deserialize method
     * @param string $data
     * @return mixed
     */
    public function decode(string $data)
    {
        throw new \BadMethodCallException;
    }
}