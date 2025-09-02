<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AppointmentStatusColor: string implements HasLabel, HasColor
{
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case Scheduled = 'scheduled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Cancelled => 'cancelled',
            self::Completed => 'completed',
            self::Scheduled => 'scheduled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Cancelled => 'danger',
            self::Completed => 'success',
            self::Scheduled => 'warning',
        };
    }
}
