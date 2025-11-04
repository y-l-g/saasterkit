<?php

declare(strict_types=1);

namespace App\Data\AppNotifications;

use App\Models\AppNotification;
use DateTime;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class AppNotificationData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $body,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly DateTime $createdAt,
    ) {}

    public static function fromModel(AppNotification $notification): self
    {
        return new self(
            id: $notification->id,
            title: $notification->title,
            body: $notification->body,
            createdAt: $notification->created_at,
        );
    }
}
