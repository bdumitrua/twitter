<?php

namespace Tests\Feature\Auth;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Modules\Auth\Models\AuthRegistration;
use App\Modules\Auth\Resources\RegistrationCodeResource;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_registration_start_route_basic(): void
    {
        $name = $this->faker->name();
        $email = $this->faker->email();
        $birthDate = $this->faker->date('Y-m-d');
        $startRegistrationData = [
            'name' => $name,
            'email' => $email,
            'birthDate' => $birthDate
        ];

        $response = $this->postJson(route('startRegistration'), $startRegistrationData);
        $createdRegistration = AuthRegistration::latest()->first();
        $createdResource = RegistrationCodeResource::make($createdRegistration->id)->resolve();

        $response
            ->assertStatus(200)
            ->assertJson($createdResource);
    }
}
