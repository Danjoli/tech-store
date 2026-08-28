<?php

namespace App\Data;

use App\Enums\PaymentStatus;

readonly class PaymentAttemptData
{
    /** @param array<string, mixed> $metadata */
    public function __construct(
        public PaymentStatus $status,
        public string $externalId,
        public array $metadata = [],
    ) {}
}
