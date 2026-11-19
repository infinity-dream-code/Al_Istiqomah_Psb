<?php

namespace App\Models\Concerns;

/**
 * Paksa model master SPMB/SIKEU ke connection `sikeu` (DB 56).
 * Jangan andalkan default mysql — di server sering salah/forge.
 */
trait UsesSikeuConnection
{
    public function initializeUsesSikeuConnection(): void
    {
        $this->connection = config('database.sikeu_connection', 'sikeu');
    }

    public function getConnectionName()
    {
        return $this->connection ?: config('database.sikeu_connection', 'sikeu');
    }
}
