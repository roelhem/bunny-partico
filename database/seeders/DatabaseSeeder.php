<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->createAdminUser();

        $this->call([
            IndividualsSeeder::class
        ]);
    }

    /**
     * Creates a new admin user.
     *
     * @return User
     */
    protected function createAdminUser(): User
    {
        /** @var User $adminUser */
        $adminUser = User::factory([
            'name' => 'admin',
            'email' => 'admin@example.com',
        ])->createOne();
        $adminUser->ownedTeams()->save(Team::forceCreate([
            'user_id' => $adminUser->id,
            'name' => explode(' ', $adminUser->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
        return $adminUser;
    }
}
