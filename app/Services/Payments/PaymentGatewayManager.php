<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\Gateways\AbaPayWayGateway;
use App\Services\Payments\Gateways\AcledaGateway;
use App\Services\Payments\Gateways\CodGateway;
use App\Services\Payments\Gateways\KhqrBakongGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    /**
     * Resolve a payment gateway adapter by provider name.
     */
    public function resolve(string $provider): PaymentGatewayInterface
    {
        return match (strtoupper(trim($provider))) {
            'KHQR', 'BAKONG' => new KhqrBakongGateway(),
            'ABA', 'PAYWAY' => new AbaPayWayGateway(),
            'ACLEDA', 'XPAY' => new AcledaGateway(),
            'COD', 'CASH' => new CodGateway(),
            default => throw new InvalidArgumentException("Unsupported payment gateway provider [{$provider}]."),
        };
    }
}
