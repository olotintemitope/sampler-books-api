<?php

use Illuminate\Database\Seeder;
use App\Models\UserActionLog;

class UserActionLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        factory(UserActionLog::class, 10)->create();
    }
}
