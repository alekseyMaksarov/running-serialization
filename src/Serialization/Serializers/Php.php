<?php

namespace Running\Serialization\Serializers;

use Running\Serialization\DecodeException;
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
        return preg_replace(
            [
                '~^([\s\S]+)__set_state\(array\s*\($~imU',
                '~^(\s*)array\s*\($~im',
                '~^(\s*)\)(\,?)$~im',
                '~^(\s*)\)\)(\,?)$~im',
                '~\s+$~im'
            ], [
                '$1__set_state([',
                '$1[',
                '$1]$2',
                '$1])$2',
                ''
            ],
            var_export($data, true)
        );
    }

    /**
     * Deserialize method
     * @param string $data
     * @return mixed
     * @throws \Running\Serialization\DecodeException
     */
    public function decode(string $data)
    {
        try {
            return eval('return ' . $data . ';');
        } catch (\ParseError $e) {
            throw new DecodeException($e->getMessage(), $e->getCode(), $e);
        }

    }
}