<?php

namespace Running\Serialization\Serializers;

use Running\Serialization\EncodeJsonException;
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
     * @return string
     * @throws \Running\Serialization\EncodeJsonException
     */
    public function encode($data, int $options = 0, int $depth = 512): string
    {
        $encoded = json_encode($data, $options, $depth);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new EncodeJsonException(json_last_error_msg());
        }

        return $encoded;
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
        $decoded = json_decode($data, $assoc, $depth, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new DecodeException(json_last_error_msg());
        }

        return $decoded;
    }
}