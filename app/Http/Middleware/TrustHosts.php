<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Percayai host aplikasi. Null pattern dari allSubdomainsOfApplicationUrl
     * diabaikan supaya tidak menolak request valid.
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        return array_values(array_filter([
            $this->allSubdomainsOfApplicationUrl(),
            '^localhost$',
            '^127\\.0\\.0\\.1$',
            '^pmb-demo\\.smartpayment\\.co\\.id$',
            '^.*\\.smartpayment\\.co\\.id$',
        ]));
    }
}
