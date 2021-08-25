<?php

namespace Peidgnad\LaravelEthereum\Helpers;

use Str;

class Utils
{
    /**
     * Should be used to create full function/event name from json abi.
     *
     * @param array $interface
     * @return string
     */
    public function jsonInterfaceMethodToString(array $interface): string
    {
        $name = Str::of($interface['name']);

        if ($name->contains(['(', ')'])) {
            return $name;
        }

        return $name . '(' . implode(',', $this->flattenTypes($interface['inputs'])) . ')';
    }

    /**
     * Should be used to flatten json abi inputs/outputs into an array of type-representing-strings.
     *
     * @param array $inputs
     * @param bool  $includeTuple
     * @return array
     */
    public function flattenTypes(array $inputs, bool $includeTuple = false): array
    {
        $types = [];

        foreach ($inputs as $input) {
            $type = Str::of($input['type']);

            if ($includeTuple || !$type->startsWith('tuple')) {
                $types[] = $type;

                continue;
            }

            $types[] = '(' . implode(',', $this->flattenTypes($input['components'], true)) . ')' . $type->substr(5);
        }

        return $types;
    }
}
