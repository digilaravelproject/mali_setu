<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-super';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a super admin user for Mali Setu application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating Super Admin User...');
        
        // Check if super admin already exists
        $existingAdmin = User::where('email', 'admin@malisetu.com')->first();
        
        if ($existingAdmin) {
            $this->warn('Super admin already exists with email: admin@malisetu.com');
            
            if ($this->confirm('Do you want to update the existing admin?')) {
                $existingAdmin->update([
                    'name' => 'Super Admin',
                    'password' => Hash::make('admin123'),
                    'user_type' => 'general',
                    'status' => 'active',
                    'caste_verification_status' => 'approved',
                    'email_verified_at' => now(),
                    'admin_notes' => 'Super Administrator with full system access'
                ]);
                
                $this->info('Super admin updated successfully!');
            } else {
                $this->info('Operation cancelled.');
            }
            
            return;
        }
        
        // Create new super admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@malisetu.com',
            'password' => Hash::make('admin123'),
            'phone' => '+91-9999999999',
            'user_type' => 'general',
            'status' => 'active',
            'caste_verification_status' => 'approved',
            'email_verified_at' => now(),
            'admin_notes' => 'Super Administrator with full system access',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        if ($superAdmin) {
            $this->info('✅ Super Admin created successfully!');
            $this->line('');
            $this->line('📧 Email: admin@malisetu.com');
            $this->line('🔑 Password: admin123');
            $this->line('👤 Role: Super Admin');
            $this->line('🌐 Access: http://127.0.0.1:8000/admin/login');
            $this->line('');
            $this->warn('⚠️  Please change the default password after first login!');
        } else {
            $this->error('❌ Failed to create super admin user.');
        }
    }
}