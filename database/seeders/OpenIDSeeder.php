<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class OpenIDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createAdminUser();
        $this->createTestUser();
    }


    protected function createTestUser(): User
    {

        /** @var User $testUser */
        $testUser = User::factory([
            'name' => 'test',
            'email' => 'test@example.com',
        ])->for(Contact::factory()->state([
            'name_first' => 'Test',
            'name_last' => 'Test',
        ]))->createOne();
        $testUser->ownedTeams()->save(Team::forceCreate([
            'user_id' => $testUser->id,
            'name' => explode(' ', $testUser->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));

        return $testUser;
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
