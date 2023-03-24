<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    User::firstOrCreate([
      'type' => 'admin',
      'email' => 'admin@gmail.com',
      'password' => app('hash')->make('secret'),
      'firstname' => 'Pro2',
      'lastname' => 'Admin',
      'rank' => 'PGEN',
      'address' => 'PRO2 Office',
      'designation' => 'Admin',
      'office' => 'IPPO',
      'mobile' => '09227342934',
    ]);

    Setting::firstOrCreate([
      'key' => 'last_car_code',
      'value' => "00000"
    ]);

    Setting::firstOrCreate([
      'key' => 'last_motor_code',
      'value' => "00000"
    ]);
  }
}
