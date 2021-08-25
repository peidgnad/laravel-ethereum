<?php

namespace Peidgnad\LaravelEthereum\Helpers;

use Illuminate\Support\Str;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

class Hex
{
    /**
     * Validate hex string and return.
     *
     * @param string   $hex
     * @param int|null $bytes
     * @return string
     */
    public static function validated(string $hex, ?int $bytes = null): string
    {
        if (!static::validate($hex, $bytes)) {
            throw new InvalidArgumentException('Invalid hex string.');
        }

        return $hex;
    }

    /**
     * Validate hex string.
     *
     * @param string   $hex
     * @param int|null $bytes
     * @return bool
     */
    public static function validate(string $hex, ?int $bytes = null): bool
    {
        $hex = static::removeHexPrefix($hex);

        return ctype_xdigit($hex) && ($bytes === null || static::bytesOf($hex) === $bytes);
    }

    /**
     * Remove 0x prefix from hex string.
     *
     * @param string $hex
     * @return string
     */
    public static function removeHexPrefix(string $hex): string
    {
        return str_replace('0x', '', $hex);
    }

    /**
     * Get bytes of hex string.
     *
     * @param string $hex
     * @param bool   $removeZero
     * @return int
     */
    public static function bytesOf(string $hex, bool $removeZero = false): int
    {
        $bytes = Str::of($hex)->split(2);

        if ($removeZero) {
            $bytes = $bytes->filter(fn($hex) => !in_array($hex, ['0', '00']));
        }

        return $bytes->count();
    }

    /**
     * Autoscaling bytes of hex to larger current bytes for get rounded bytes.
     *
     * @param string   $hex
     * @param int|null $step
     * @param bool     $addToLeading
     * @return string
     */
    public static function roundBytes(string $hex, ?int $step = null, bool $addToLeading = true): string
    {
        if (!$step) {
            return $hex;
        }

        $result = Str::of($hex)->split($step * 2)->map(fn($part) => static::toFixedBytes($part, $step, $addToLeading));

        return $result->implode('');
    }

    /**
     * Add leading or trailing zero to hex for match with fixed bytes.
     *
     * @param string $hex
     * @param bool   $addToLeading
     * @param int    $bytes
     * @return string
     */
    public static function toFixedBytes(string $hex, int $bytes = 0, bool $addToLeading = true): string
    {
        $bytes *= 2;
        $hexLength = strlen($hex);


        if ($bytes > $hexLength) {
            $zero = $bytes - $hexLength;
        } else {
            $zero = $hexLength % 2;
        }

        $zero = implode('', array_fill(0, $zero, '0'));

        if ($addToLeading) {
            return $zero . $hex;
        }

        return $hex . $zero;
    }

    /**
     * Ensure hex start with 0x prefix.
     *
     * @param string $hex
     * @return string
     */
    #[Pure] public static function ensureHexPrefix(string $hex): string
    {
        return static::hasPrefix($hex) ? $hex : "0x$hex";
    }

    /**
     * Check hex is start with 0x prefix or not.
     *
     * @param string $hex
     * @return bool
     */
    public static function hasPrefix(string $hex): bool
    {
        return str_starts_with($hex, '0x');
    }

    /**
     * Split hex string into multiple part with fixed bytes.
     *
     * @param string $hex
     * @param int    $bytes
     * @return array
     */
    public static function split(string $hex, int $bytes): array
    {
        return str_split(static::removeHexPrefix($hex), $bytes * 2);
    }
}
