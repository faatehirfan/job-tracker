<?php

use App\Enums\ApplicationStatus;
use App\Models\Company;
use App\Models\JobApplication;

test('the home page redirects to the applications list', function () {
    $this->get('/')->assertRedirect(route('applications.index'));
});

test('index lists applications with their company', function () {
    $application = JobApplication::factory()
        ->for(Company::factory()->state(['name' => 'Kedai Kopi Digital Sdn Bhd']))
        ->create(['role_title' => 'Junior Laravel Developer']);

    $this->get(route('applications.index'))
        ->assertOk()
        ->assertSee('Junior Laravel Developer')
        ->assertSee('Kedai Kopi Digital Sdn Bhd');
});

test('index runs the same number of queries for 1 or 20 applications', function () {
    JobApplication::factory()->count(20)->create();

    $this->expectsDatabaseQueryCount(2);

    $this->get(route('applications.index'))->assertOk();
});

test('create shows the form', function () {
    Company::factory()->create(['name' => 'Rimba Labs']);

    $this->get(route('applications.create'))
        ->assertOk()
        ->assertSee('Add an application')
        ->assertSee('Rimba Labs');
});

test('store saves a new application and redirects to it', function () {
    $company = Company::factory()->create();

    $response = $this->post(route('applications.store'), [
        'company_id' => $company->id,
        'role_title' => 'Junior PHP Developer',
        'status' => 'applied',
        'salary_min' => 3500,
        'salary_max' => 4500,
        'applied_on' => '2026-11-16',
        'notes' => 'Found on the company careers page.',
    ]);

    $application = JobApplication::first();

    $response->assertRedirect(route('applications.show', $application))
        ->assertSessionHas('status', 'Application added.');

    $this->assertDatabaseHas('job_applications', [
        'company_id' => $company->id,
        'role_title' => 'Junior PHP Developer',
        'status' => 'applied',
        'salary_min' => 3500,
    ]);
});

test('show displays one application', function () {
    $application = JobApplication::factory()->applied()->create([
        'role_title' => 'Backend Developer',
    ]);

    $this->get(route('applications.show', $application))
        ->assertOk()
        ->assertSee('Backend Developer')
        ->assertSee($application->company->name)
        ->assertSee('Applied');
});

test('edit shows the form filled in', function () {
    $application = JobApplication::factory()->create(['role_title' => 'Full-Stack Developer']);

    $this->get(route('applications.edit', $application))
        ->assertOk()
        ->assertSee('value="Full-Stack Developer"', escape: false);
});

test('update changes the application', function () {
    $application = JobApplication::factory()->create();

    $this->put(route('applications.update', $application), [
        'company_id' => $application->company_id,
        'role_title' => $application->role_title,
        'status' => 'interviewing',
    ])->assertRedirect(route('applications.show', $application));

    expect($application->fresh()->status)->toBe(ApplicationStatus::Interviewing);
});

test('destroy deletes the application', function () {
    $application = JobApplication::factory()->create();

    $this->delete(route('applications.destroy', $application))
        ->assertRedirect(route('applications.index'));

    $this->assertDatabaseMissing('job_applications', ['id' => $application->id]);
});

test('deleting a company deletes its applications', function () {
    $application = JobApplication::factory()->create();

    $application->company->delete();

    $this->assertDatabaseMissing('job_applications', ['id' => $application->id]);
});