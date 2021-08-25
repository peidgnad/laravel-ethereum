<?php

namespace Peidgnad\LaravelEthereum\Types;

use InvalidArgumentException;
use Peidgnad\LaravelEthereum\Helpers\Hex;

abstract class EthType
{
    /**
     * The min bytes.
     *
     * @var int|null
     */
    protected ?int $minBytes = null;

    /**
     * Create a new ethereum type instance.
     *
     * @param mixed $data
     * @param mixed $extend
     */
    public function __construct(protected mixed $data, protected mixed $extend = null)
    {
        if (!static::validate($this->data, $this->extend)) {
            throw new InvalidArgumentException('Invalid Ethereum Data');
        }
    }

    /**
     * Validate passed raw data.
     *
     * @param mixed $data
     * @param mixed $extend
     * @return bool
     */
    abstract public static function validate(mixed $data, mixed $extend = null): bool;

    /**
     * Initial instance from raw data.
     *
     * @param mixed $data
     * @param mixed $extend
     * @return static
     */
    public static function from(mixed $data, mixed $extend = null): static
    {
        return new static($data, $extend);
    }

    /**
     * Initial instance from hex string and validate it.
     *
     * @param string $hex
     * @param mixed  $extend
     * @return static
     */
    abstract public static function fromHex(string $hex, mixed $extend = null): static;

    /**
     * Check the string or object is valid EthType.
     *
     * @param mixed $type
     * @param bool  $throw
     * @return bool
     */
    protected static function isValidType(mixed $type, bool $throw = false): bool
    {
        if (!is_subclass_of($type, __CLASS__)) {
            if ($throw) {
                throw new InvalidArgumentException('Type must be an instance of EthType.');
            }

            return false;
        }

        return true;
    }

    /**
     * Set min bytes.
     *
     * @param int $byte
     * @return $this
     */
    public function minBytes(int $byte): static
    {
        $this->minBytes = $byte;

        return $this;
    }

    /**
     * Get the bytes of data.
     *
     * @param bool $removeZero
     * @return int
     */
    public function getBytes(bool $removeZero = false): int
    {
        return Hex::bytesOf($this->encoded(false), $removeZero);
    }

    /**
     * Get the encoded hex string.
     *
     * @param bool $withPrefix
     * @return string
     */
    public function encoded(bool $withPrefix = true): string
    {
        $hex = Hex::roundBytes($this->encode(), $this->minBytes, !$this->isDynamic());

        if ($withPrefix) {
            $hex = Hex::ensureHexPrefix($hex);
        }

        return $hex;
    }

    /**
     * Encode the data to hex string.
     *
     * @return string
     */
    abstract protected function encode(): string;

    /**
     * Check type is dynamic or static.
     *
     * @return bool
     */
    abstract public function isDynamic(): bool;

    /**
     * Decode data from hex string.
     *
     * @return mixed
     */
    public function decoded(): mixed
    {
        return $this->data;
    }
}
