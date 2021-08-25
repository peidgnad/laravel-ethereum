<?php

namespace Peidgnad\LaravelEthereum\Types;

use JetBrains\PhpStorm\Pure;
use Peidgnad\LaravelEthereum\Helpers\Hex;

class Bytes extends EthType
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
        return Hex::validate($data);
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
        return new static(Hex::removeHexPrefix($hex));
    }

    /**
     * Check type is dynamic or static.
     *
     * @return bool
     */
    public function isDynamic(): bool
    {
        return true;
    }

    /**
     * Decode data from hex string.
     *
     * @return string
     */
    #[Pure] public function decoded(): string
    {
        return Hex::ensureHexPrefix($this->data);
    }

    /**
     * Encode the data to hex string.
     *
     * @return string
     */
    protected function encode(): string
    {
        return $this->data;
    }
}
