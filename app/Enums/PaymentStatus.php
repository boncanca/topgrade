<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case NotRequired = 'not_required';
    case Unpaid = 'unpaid';
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::NotRequired => 'Not Required',
            self::Unpaid => 'Unpaid',
            self::Pending => 'Pending',
            self::Paid => 'Paid',
            self::Failed => 'Failed',
            self::Cancelled => 'Cancelled',
            self::Refunded => 'Refunded',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NotRequired => 'bg-slate-100 text-slate-800',
            self::Unpaid => 'bg-gray-100 text-gray-800',
            self::Pending => 'bg-amber-100 text-amber-800',
            self::Paid => 'bg-green-100 text-green-800',
            self::Failed => 'bg-red-100 text-red-800',
            self::Cancelled => 'bg-red-100 text-red-800',
            self::Refunded => 'bg-amber-100 text-amber-800',
        };
    }
}
