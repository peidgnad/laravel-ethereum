<?php

namespace Peidgnad\LaravelEthereum\Types;

use Peidgnad\LaravelEthereum\Helpers\Hex;

class Integer extends EthType
{
    /**
     * Validate passed raw data.
     *
     * @param mixed $data
     * @param mixed $extend
     * @return bool
     */
    public static function validate(mixed $data, mixed $extend = null): bool
    {
        return is_numeric($data);
    }

    /**
     * Initial instance from hex string and validate it.
     *
     * @param string $hex
     * @param mixed  $extend
     * @return static
     */
    public static function fromHex(string $hex, mixed $extend = null): static
    {
        return new static(hexdec(Hex::validated(Hex::removeHexPrefix($hex))));
    }

    /**
     * Check type is dynamic or static.
     *
     * @return bool
     */
    public function isDynamic(): bool
    {
        return false;
    }

    /**
     * Encode the data to hex string.
     *
     * @return string
     */
    protected function encode(): string
    {
        return dechex($this->data);
    }
}
