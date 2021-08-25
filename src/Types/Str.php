<?php

namespace Peidgnad\LaravelEthereum\Types;

use InvalidArgumentException;
use Peidgnad\LaravelEthereum\Helpers\Hex;

class Str extends EthType
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
        return is_string($data);
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
        $string = pack('H*', Hex::validated(Hex::removeHexPrefix($hex)));

        if (!is_string($string) || !static::checkUtf8($string)) {
            throw new InvalidArgumentException('Hex is not a valid string.');
        }

        return new static($string);
    }

    /**
     * Verify valid UTF8 string.
     *
     * W3C recommended.
     *
     * @see https://www.w3.org/International/questions/qa-forms-utf-8.en
     *
     * @param string $str
     *
     * @return bool
     */
    private static function checkUtf8(string $str): bool
    {
        if (preg_match('%^(?:
              [\x09\x0A\x0D\x20-\x7E]            # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
        )*$%xs', $str)) {
            return true;
        }

        return false;
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
     * Encode the data to hex string.
     *
     * @return string
     */
    protected function encode(): string
    {
        $hex = unpack('H*', $this->data);

        return array_shift($hex);
    }
}
