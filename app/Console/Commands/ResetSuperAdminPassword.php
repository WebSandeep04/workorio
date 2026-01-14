<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetSuperAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:super-admin-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset super admin password to default';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('email', 'sadmin@triserv360.com')->first();
        
        if (!$user) {
            $this->error('Super admin user not found!');
            return 1;
        }
        
        $user->password = Hash::make('password');
        $user->save();
        
        $this->info('Super admin password reset successfully!');
        $this->info('Email: sadmin@triserv360.com');
        $this->info('Password: password');
        
        return 0;
    }
}
