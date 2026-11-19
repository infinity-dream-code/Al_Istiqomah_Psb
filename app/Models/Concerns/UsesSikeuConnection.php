<?php

namespace App\Models\Concerns;

trait UsesSikeuConnection
{
    public function getConnectionName(): ?string
    {
        return config('database.sikeu_connection', 'sikeu');
    }
}
