<?php

namespace App\Models\Concerns;

trait HasLegacyAttributeAliases
{
    protected function legacyAttributeAliases(): array
    {
        return [];
    }

    public function getAttribute($key)
    {
        $key = $this->legacyAttributeAliases()[$key] ?? $key;

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        $key = $this->legacyAttributeAliases()[$key] ?? $key;

        return parent::setAttribute($key, $value);
    }
}
