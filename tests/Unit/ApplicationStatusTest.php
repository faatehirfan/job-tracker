<?php

use App\Enums\ApplicationStatus;

test('every status has a human-readable label', function () {
    expect(ApplicationStatus::Wishlist->label())->toBe('Wishlist')
        ->and(ApplicationStatus::Interviewing->label())->toBe('Interviewing');
});

test('a status can be created from its stored value', function () {
    expect(ApplicationStatus::from('offer'))->toBe(ApplicationStatus::Offer)
        ->and(ApplicationStatus::tryFrom('ghosted'))->toBeNull();
});
