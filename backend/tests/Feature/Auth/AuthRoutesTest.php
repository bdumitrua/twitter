<?php

namespace Tests\Feature\Auth;

use App\Modules\Auth\Models\AuthRegistration;
use App\Modules\Auth\Models\AuthReset;
use App\Modules\Auth\Resources\PasswordResetCodeResource;
use App\Modules\Auth\Resources\PasswordResetConfirmedResource;
use App\Modules\Auth\Resources\RegistrationCodeResource;
use App\Modules\Auth\Resources\RegistrationConfirmedResource;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function generateStartRegistrationData(): array
    {
        $name = $this->faker->name();
        $email = $this->faker->email();
        $birthDate = $this->faker->date('Y-m-d');
        return [
            'name' => $name,
            'email' => $email,
            'birthDate' => $birthDate
        ];
    }

    protected function generateIncorrectStartRegistrationData(): array
    {
        $name = $this->faker->phoneNumber();
        $email = $this->faker->word();
        $birthDate = $this->faker->email();
        return [
            'name' => $name,
            'email' => $email,
            'birthDate' => $birthDate
        ];
    }

    public function test_registration_start_route_basic(): void
    {
        $startRegistrationData = $this->generateStartRegistrationData();
        $response = $this->postJson(route('startRegistration'), $startRegistrationData);

        $createdRegistration = AuthRegistration::latest()->first();
        $createdResource = RegistrationCodeResource::make($createdRegistration->id)->resolve();

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($createdResource);
    }

    public function test_registration_start_route_incorrect_request(): void
    {
        $startRegistrationData = $this->generateIncorrectStartRegistrationData();

        $response = $this->postJson(route('startRegistration'), $startRegistrationData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_registration_confirm_route_basic(): void
    {
        $authRegistration = AuthRegistration::factory()->create();
        $response = $this->postJson(
            route('confirmRegistrationCode', ['authRegistration' => $authRegistration->id]),
            ['code' => $authRegistration->code]
        );

        $createdResource = RegistrationConfirmedResource::make($authRegistration->id)->resolve();

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($createdResource);
    }

    public function test_registration_confirm_route_incorrect_request(): void
    {
        $authRegistration = AuthRegistration::factory()->create();
        $response = $this->postJson(
            route('confirmRegistrationCode', ['authRegistration' => $authRegistration->id]),
            ['code' => '1']
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_registration_confirm_route_incorrect_code(): void
    {
        $authRegistration = AuthRegistration::factory()->create();
        $response = $this->postJson(
            route('confirmRegistrationCode', ['authRegistration' => $authRegistration->id]),
            ['code' => '00000']
        );

        // * IncorrectCodeException::class
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_registration_end_route_base(): void
    {
        $authRegistration = AuthRegistration::factory()->create(['confirmed' => true]);
        $response = $this->postJson(
            route('endRegistration', ['authRegistration' => $authRegistration->id]),
            ['password' => '12341234']
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_registration_end_route_code_not_confirmed(): void
    {
        $authRegistration = AuthRegistration::factory()->create();
        $response = $this->postJson(
            route('endRegistration', ['authRegistration' => $authRegistration->id]),
            ['password' => '12341234']
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_registration_end_route_incorrect_password(): void
    {
        $authRegistration = AuthRegistration::factory()->create(['confirmed' => true]);
        $response = $this->postJson(
            route('endRegistration', ['authRegistration' => $authRegistration->id]),
            ['password' => 'short']
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_reset_start_route_basic(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson(
            route('checkEmailForPasswordReset'),
            ['email' => $user->email]
        );

        $createdReset = AuthReset::latest()->first();
        $createdResource = PasswordResetCodeResource::make($createdReset->id)->resolve();

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($createdResource);
    }

    public function test_reset_start_route_incorrect_request(): void
    {
        User::factory()->create();
        $response = $this->postJson(
            route('checkEmailForPasswordReset'),
            ['email' => 'NotAnEmail']
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_reset_start_route_incorrect_email(): void
    {
        User::factory()->create();
        $response = $this->postJson(
            route('checkEmailForPasswordReset'),
            ['email' => 'NotAnEmailExistingEmail@email.com']
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_reset_confirm_code_route_basic(): void
    {
        $user = User::factory()->create();
        $authReset = AuthReset::factory()->create(['user_id' => $user->id]);
        $response = $this->postJson(
            route('confirmPasswordResetCode', ['authReset' => $authReset->id]),
            ['code' => $authReset->code]
        );

        $createdResource = PasswordResetConfirmedResource::make($authReset->id)->resolve();

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($createdResource);
    }

    public function test_reset_confirm_code_route_incorrect_request(): void
    {
        $user = User::factory()->create();
        $authReset = AuthReset::factory()->create(['user_id' => $user->id]);
        $response = $this->postJson(
            route('confirmPasswordResetCode', ['authReset' => $authReset->id]),
            ['code' => '1']
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_reset_confirm_code_route_incorrect_code(): void
    {
        $user = User::factory()->create();
        $authReset = AuthReset::factory()->create(['user_id' => $user->id]);
        $response = $this->postJson(
            route('confirmPasswordResetCode', ['authReset' => $authReset->id]),
            ['code' => '00000']
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_reset_end_route_basic(): void
    {
        $user = User::factory()->create();
        $authReset = AuthReset::factory()->create([
            'user_id' => $user->id,
            'confirmed' => true
        ]);
        $response = $this->postJson(
            route('endPasswordReset', ['authReset' => $authReset->id]),
            ['password' => '12341234']
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_reset_end_route_incorrect_request(): void
    {
        $user = User::factory()->create();
        $authReset = AuthReset::factory()->create([
            'user_id' => $user->id,
            'confirmed' => true
        ]);
        $response = $this->postJson(
            route('endPasswordReset', ['authReset' => $authReset->id]),
            ['password' => 'short']
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_reset_end_route_code_not_confirmed(): void
    {
        $user = User::factory()->create();
        $authReset = AuthReset::factory()->create([
            'user_id' => $user->id,
        ]);
        $response = $this->postJson(
            route('endPasswordReset', ['authReset' => $authReset->id]),
            ['password' => '12341234']
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_login_route_basic(): void
    {
        $password = 'password';
        $email = 'test@test.com';

        User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        $response = $this->postJson(
            route('authLogin'),
            [
                'email' => $email,
                'password' => $password,
            ]
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_login_route_incorrect_request(): void
    {
        $password = 'short';
        $email = 'notAnEmail';
        $response = $this->postJson(
            route('authLogin'),
            [
                'email' => $email,
                'password' => $password,
            ]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_login_route_invalid_credentials(): void
    {
        $password = 'password';
        $email = 'test@test.com';

        User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        $response = $this->postJson(
            route('authLogin'),
            [
                'email' => 'maybenotmyemail@email.com',
                'password' => 'andnotmypassword',
            ]
        );

        // * InvalidCredetialsException::class
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }
}
