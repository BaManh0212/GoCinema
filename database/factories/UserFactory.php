<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\VaiTro;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vaiTro = VaiTro::where('ten', 'khach_hang')->first();
        $vaiTroId = $vaiTro ? $vaiTro->id : 1;

        return [
            // NguoiDung uses 'ho_ten' for the full name
            'ho_ten' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            // set password attribute; model mutator will hash into mat_khau
            'password' => static::$password ??= 'password',
            'vai_tro_id' => $vaiTroId,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
