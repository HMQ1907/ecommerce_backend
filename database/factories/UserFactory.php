<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Roles\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$zjz8CGlGpUcBGZzoDlLp3eckOwnID76xCsAjDWdJu9yWhA6kc9cXS', // 123123
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function withEmployee()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(Role::USER);
            $user->syncPermissions($user->getAllPermissions()->pluck('id')->toArray());
            $user->employee()->create([
                'first_name' => fake()->title(),
                'last_name' => $user->name,
                'gender' => 'male',
                'created_by' => $user->id,
            ]);
        });
    }
}
