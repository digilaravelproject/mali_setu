<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CasteCertificate;
use Carbon\Carbon;

class CasteCertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with pending caste verification status
        $pendingUsers = User::where('caste_verification_status', 'pending')
            ->whereDoesntHave('casteCertificate')
            ->limit(10)
            ->get();

        $casteCategories = ['OBC', 'SC', 'ST', 'General'];
        $subcastes = [
            'OBC' => ['Yadav', 'Kurmi', 'Koeri', 'Teli', 'Kumhar'],
            'SC' => ['Chamar', 'Dhobi', 'Bhangi', 'Dom', 'Musahar'],
            'ST' => ['Gond', 'Santhal', 'Oraon', 'Munda', 'Kharia'],
            'General' => ['Brahmin', 'Rajput', 'Kayastha', 'Bania', 'Thakur']
        ];

        foreach ($pendingUsers as $user) {
            $category = $casteCategories[array_rand($casteCategories)];
            $subcaste = $subcastes[$category][array_rand($subcastes[$category])];
            
            CasteCertificate::create([
                'user_id' => $user->id,
                'file_path' => 'certificates/sample_cert_' . $user->id . '.pdf',
                'verification_status' => 'pending',
                'admin_notes' => 'Sample certificate for testing purposes - ' . $category . ' (' . $subcaste . ')'
            ]);
        }

        $this->command->info('Created ' . $pendingUsers->count() . ' sample caste certificates for pending users.');
    }
}