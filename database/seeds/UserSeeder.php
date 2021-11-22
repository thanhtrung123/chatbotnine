<?php
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        $admin = User::create([
                'name' => 'admin',
                'display_name' => '管理者',
                'email' => 'admin@ai8341.jp',
                'password' => bcrypt("admin8341"),
                'remember_token' => str_random(10),
        ]);
        $admin->assignRole('admin');

        if (env('APP_DEBUG')) {
            $test = User::create([
                    'name' => 'test',
                    'display_name' => 'テスト',
                    'email' => 'test@ai8341.jp',
                    'password' => bcrypt("test8341"),
                    'remember_token' => str_random(10),
            ]);
            $test->assignRole('test');
        }

//        $manager = factory(User::class, 10)->create();
//        foreach ($manager as $user) {
//            $user->assignRole('manager');
//        }
//        $users = factory(User::class, 100)->create();
//        foreach ($users as $user) {
//            $user->assignRole('staff');
//        }
    }
}