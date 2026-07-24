<?php

namespace App\Enums;

enum InquiryStatusEnum: string
{
    case New = 'new';
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Open => 'Open',
            self::InProgress => 'In Progress',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'bg-blue-100 text-blue-800',
            self::Open => 'bg-amber-100 text-amber-800',
            self::InProgress => 'bg-amber-100 text-amber-800',
            self::Resolved => 'bg-green-100 text-green-800',
            self::Closed => 'bg-green-100 text-green-800',
            self::Archived => 'bg-gray-100 text-gray-800',
        };
    }
}
