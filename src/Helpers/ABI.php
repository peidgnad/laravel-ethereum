<?php

namespace Peidgnad\LaravelEthereum\Helpers;

use Exception;
use InvalidArgumentException;
use kornrunner\Keccak;
use Peidgnad\LaravelEthereum\Facades\Utils;
use Peidgnad\LaravelEthereum\Types\Address;
use Peidgnad\LaravelEthereum\Types\Bytes;
use Peidgnad\LaravelEthereum\Types\EthType;
use Peidgnad\LaravelEthereum\Types\Integer;
use Peidgnad\LaravelEthereum\Types\Str;

class ABI
{
    /**
     * Map the solidity type name to Laravel Ethereum type class.
     */
    public const TYPES = [
        'address' => Address::class,
        'bytes' => Bytes::class,
        'uint' => Integer::class,
        'uint256' => Integer::class,
        'string' => Str::class,
    ];

    /**
     * Encodes a function call from its json interface and parameters.
     *
     * @param array $interface
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function encodeFunctionCall(array $interface, array $params = []): string
    {
        $hex = $this->encodeFunctionSignature($interface);

        if (!empty($params)) {
            $hex .= Hex::removeHexPrefix($this->encodeParameters($this->formatTypes($interface['inputs']), $params));
        }

        return $hex;
    }

    /**
     * Encodes the function name to its ABI representation, which are the first 4 bytes of the sha3 of the function
     * name including  types.
     *
     * @param array $interface
     * @return string
     * @throws Exception
     */
    public function encodeFunctionSignature(array $interface): string
    {
        return '0x' . substr(Keccak::hash(Utils::jsonInterfaceMethodToString($interface), 256), 0, 8);
    }

    /**
     * Should be used to encode list of params.
     *
     * @param EthType[]|string[] $types
     * @param array              $params
     * @return string
     */
    public function encodeParameters(array $types, array $params): string
    {
        $position = count($params) * 64;

        $result = [
            'static' => '',
            'dynamic' => '',
        ];

        foreach ($types as $index => $type) {
            $param = $params[$index];

            // Handle array type.
            if (str_ends_with($type, '[]')) {
                $arrayResult = Hex::removeHexPrefix(
                    $this->encodeParameters(array_fill(0, count($param), substr($type, 0, -2)), $param)
                );

                $result = $this->encodeParametersResult($result, $position, $arrayResult, true, count($param));

                continue;
            }

            // Handle tuple type.
            if (str_starts_with($type, 'tuple')) {
                $tupleTypes = \Str::of($type)->substr(6)->substr(0, -1);

                $tupleTypes = $tupleTypes->explode(',')->map(static function ($type) {
                    return trim($type);
                });

                $tupleResult = Hex::removeHexPrefix($this->encodeParameters($tupleTypes->all(), $param));

                $result = $this->encodeParametersResult(
                    $result,
                    $position,
                    $tupleResult,
                    \Str::of($type)->contains(['[]', 'string', 'bytes', 'tuple'])
                );

                continue;
            }

            // Handle normal type.
            $param = $this->getTypeInstance($type, $param);

            $result = $this->encodeParametersResult(
                $result,
                $position,
                $param->encoded(false),
                $param->isDynamic(),
                $param->getBytes(true)
            );
        }

        return '0x' . implode('', array_values($result));
    }

    /**
     * @param array    $result
     * @param int      $position
     * @param string   $hex
     * @param bool     $isDynamic
     * @param int|null $bytes
     * @return array
     */
    protected function encodeParametersResult(array $result, int $position, string $hex, bool $isDynamic, ?int $bytes = null): array
    {
        // Static type.
        if (!$isDynamic) {
            $result['static'] .= $hex;

            return $result;
        }

        // Calculator position for dynamic type.
        $result['static'] .= Integer::from(($position + strlen($result['dynamic'])) / 2)->minBytes(32)->encoded(false);


        // Dynamic type.
        if ($bytes !== null) {
            $result['dynamic'] .= Integer::from($bytes)->minBytes(32)->encoded(false);
        }

        $result['dynamic'] .= $hex;

        return $result;
    }

    /**
     * Get type instance from type class path or raw type name.
     *
     * @param EthType|string $type
     * @param mixed          $data
     * @return EthType
     */
    public function getTypeInstance(EthType|string $type, mixed $data): EthType
    {
        if ($type instanceof EthType) {
            return $type->minBytes(32);
        }

        if (!isset(static::TYPES[$type])) {
            throw new InvalidArgumentException('Missing handler for type: ' . $type);
        }

        $type = static::TYPES[$type];

        return $type::from($data)->minBytes(32);
    }

    /**
     * Format interface types.
     *
     * @param array $types
     * @return array
     */
    public function formatTypes(array $types): array
    {
        $result = [];

        foreach ($types as $type) {
            if (str_starts_with($type['type'], 'tuple')) {
                $tuple = 'tuple(' . implode(',', $this->formatTypes($type['components'])) . ')';

                if (str_ends_with($type['type'], '[]')) {
                    $tuple .= '[]';
                }

                $type['type'] = $tuple;
            }

            $result[] = $type['type'];
        }

        return $result;
    }

    /**
     * Encodes the function name to its ABI representation, including types.
     *
     * @param array $interface
     * @return string
     * @throws Exception
     */
    public function encodeEventSignature(array $interface): string
    {
        return '0x' . Keccak::hash(Utils::jsonInterfaceMethodToString($interface), 256);
    }

    /**
     * Should be used to encode plain param.
     *
     * @param EthType|string $type
     * @param mixed          $data
     * @return string
     */
    public function encodeParameter(EthType|string $type, mixed $data): string
    {
        return $this->encodeParameters([$type], [$data]);
    }
}
