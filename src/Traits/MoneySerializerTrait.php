<?php
declare(strict_types=1);

namespace Hasfoug\Money\Traits;

use Hasfoug\Money\Contracts\MoneySerializer;
use Hasfoug\Money\Serializers\ArrayMoneySerializer;
use InvalidArgumentException;

trait MoneySerializerTrait
{
    public function serialize(?MoneySerializer $serializer = null): mixed
    {
        $serializer = $serializer ?? config('money.defaultSerializer');

        if (is_null($serializer)) {
            return $this->serializeByArray();
        }

        if (is_string($serializer)) {
            $serializer = app($serializer);
        }

        if (is_array($serializer) && count($serializer) === 2) {
            $serializer = app($serializer[0], $serializer[1]);
        }

        if ($serializer instanceof MoneySerializer) {
            return $this->serializeBySerializer($serializer);
        }

        throw new InvalidArgumentException(sprintf('Invalid default serializer %s', json_encode($serializer)));
    }

    public function serializeByArray(): array
    {
        return $this->serializeBySerializer(new ArrayMoneySerializer);
    }

    public function serializeBySerializer(MoneySerializer $serializer): mixed
    {
        return $serializer->serialize($this);
    }
}
