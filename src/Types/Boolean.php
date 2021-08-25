<?php

namespace Peidgnad\LaravelEthereum\Types;

use InvalidArgumentException;

class Boolean extends EthType
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
        return is_bool($data);
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
        $hex = substr($hex, -1);

        if (!in_array($hex, ['0', '1'])) {
            throw new InvalidArgumentException('String must be a valid hex boolean.');
        }

        return new static((bool) $hex);
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
        return $this->data ? '1' : '0';
    }
}
