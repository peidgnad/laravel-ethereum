<?php

namespace Peidgnad\LaravelEthereum\Types;

use Peidgnad\LaravelEthereum\Helpers\Hex;

class Address extends Bytes
{
    /**
     * Validate passed raw data.
     *
     * @param mixed      $data
     * @param mixed|null $extend
     * @return bool
     */
    public static function validate(mixed $data, mixed $extend = null): bool
    {
        return Hex::validate($data, 20);
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
}
