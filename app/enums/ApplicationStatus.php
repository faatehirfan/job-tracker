<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Wishlist = 'wishlist'; // Kiri: Kod PHP | Kanan: Nilai Database
    case Applied = 'applied';
    case Interviewing = 'interviewing';
    case Offer = 'offer';
    case Rejected = 'rejected';

    public function label(): string //users kat web punye view, user akan nampak belah kanan '...'
    {
        return match ($this) {
            self::Wishlist => 'Wishlist',
            self::Applied => 'Applied',
            self::Interviewing => 'Interviewing',
            self::Offer => 'Offer',
            self::Rejected => 'Rejected',
        };
    }
}