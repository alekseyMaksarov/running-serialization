<?php

namespace Running\Serialization\Serializers;

use Running\Serialization\DecodeException;
use Running\Serialization\SerializerInterface;

/**
 * Class Json
 * @package Running\Serialization\Serializers
 */
class Json
    implements SerializerInterface
{

    /**
     * Serialize method
     * @param mixed $data
     * @param int $options
     * @param int $depth
     * @return string | false
     */
    public function encode($data, int $options = 0, int $depth = 512)
    {
        return json_encode($data, $options, $depth);
    }

    /**
     * Deserialize method
     * @param string $data
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     * @throws \Running\Serialization\DecodeException
     */
    public function decode(string $data, bool $assoc = false, int $depth = 512, int $options = 0)
    {
    }
}