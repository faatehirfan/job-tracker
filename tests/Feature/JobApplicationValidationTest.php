<?php

use App\Models\Company;
use App\Models\JobApplication;

/**
 * A request body that passes validation. Each test breaks one field.
 *
 * @return array<string, mixed>
 */
function validApplication(array $overrides = []): array
{
    return array_merge([
        'company_id' => Company::factory()->create()->id,
        'role_title' => 'Junior Laravel Developer',
        'status' => 'wishlist',
        'salary_min' => 4000,
        'salary_max' => 5000,
    ], $overrides);
}

test('a valid application passes validation', function () {
    $this->post(route('applications.store'), validApplication())
        ->assertSessionHasNoErrors();

    expect(JobApplication::count())->toBe(1);
});

test('bad input is rejected and nothing is saved', function (array $input, string $field) {
    $this->post(route('applications.store'), validApplication($input))
        ->assertSessionHasErrors($field);

    expect(JobApplication::count())->toBe(0);
})->with([
    'salary_max below salary_min' => [['salary_min' => 5000, 'salary_max' => 4000], 'salary_max'],
    'salary_max without salary_min' => [['salary_min' => null, 'salary_max' => 5000], 'salary_max'],
    'negative salary_min' => [['salary_min' => -1], 'salary_min'],
    'status not in the enum' => [['status' => 'ghosted'], 'status'],
    'missing company_id' => [['company_id' => null], 'company_id'],
    'company_id that does not exist' => [['company_id' => 999], 'company_id'],
    'empty role_title' => [['role_title' => ''], 'role_title'],
    'role_title over 255 characters' => [['role_title' => str_repeat('a', 256)], 'role_title'],
    'applied_on that is not a date' => [['applied_on' => 'last Tuesday-ish'], 'applied_on'],
    'notes over 5000 characters' => [['notes' => str_repeat('a', 5001)], 'notes'],
]);

test('the error message says which rule failed', function () {
    $this->post(route('applications.store'), validApplication(['status' => 'ghosted']))
        ->assertSessionHasErrors(['status' => 'The selected status is invalid.']);
});

test('update uses the same rules', function () {
    $application = JobApplication::factory()->create();

    $this->put(route('applications.update', $application), validApplication(['status' => 'ghosted']))
        ->assertSessionHasErrors('status');
});

test('a failed submission sends you back to the form with your input', function () {
    $this->from(route('applications.create'))
        ->post(route('applications.store'), validApplication(['role_title' => '']))
        ->assertRedirect(route('applications.create'))
        ->assertSessionHasInput('salary_min', 4000);
});
