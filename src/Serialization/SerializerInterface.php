<?php

namespace Running\Serialization;

/**
 * Interface SerializerInterface
 * @package Running\Serialization
 */
interface SerializerInterface
{

    public function encode($data) : string;

    public function decode(string $data);

}