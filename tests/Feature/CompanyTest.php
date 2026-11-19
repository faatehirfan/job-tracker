<?php

use App\Models\Company;
use App\Models\JobApplication;

test('index lists companies with their application count', function () {
    Company::factory()
        ->hasJobApplications(2)
        ->create(['name' => 'Rimba Labs']);

    $this->get(route('companies.index'))
        ->assertOk()
        ->assertSee('Rimba Labs')
        ->assertSee('<td>2</td>', escape: false);
});

test('create shows the form', function () {
    $this->get(route('companies.create'))
        ->assertOk()
        ->assertSee('Add a company');
});

test('store saves a new company', function () {
    $this->post(route('companies.store'), [
        'name' => 'Kedai Kopi Digital Sdn Bhd',
        'website' => 'https://kedaikopi.example',
        'location' => 'Cyberjaya',
    ])->assertRedirect(route('companies.show', Company::first()));

    $this->assertDatabaseHas('companies', ['name' => 'Kedai Kopi Digital Sdn Bhd']);
});

test('company names are unique', function () {
    Company::factory()->create(['name' => 'Rimba Labs']);

    $this->post(route('companies.store'), ['name' => 'Rimba Labs'])
        ->assertSessionHasErrors(['name' => 'The name has already been taken.']);

    expect(Company::count())->toBe(1);
});

test('show lists the company applications', function () {
    $company = Company::factory()->create();
    JobApplication::factory()->for($company)->create(['role_title' => 'Junior PHP Developer']);

    $this->get(route('companies.show', $company))
        ->assertOk()
        ->assertSee($company->name)
        ->assertSee('Junior PHP Developer');
});

test('companies cannot be edited or deleted yet', function () {
    $company = Company::factory()->create();

    $this->get("/companies/{$company->id}/edit")->assertNotFound();
    $this->delete("/companies/{$company->id}")->assertMethodNotAllowed();
});