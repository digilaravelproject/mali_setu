<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $certs = DB::table('caste_certificates')->where('file_path', 'like', '%.pdf')->get();
        foreach ($certs as $cert) {
            $path = $cert->file_path;
            if (Storage::disk('public')->exists($path)) {
                $content = Storage::disk('public')->get($path);
                // Read first 2 bytes to check for JPEG magic bytes (ffd8)
                if (strlen($content) >= 2 && str_starts_with(bin2hex(substr($content, 0, 2)), 'ffd8')) {
                    $newPath = preg_replace('/\.pdf$/i', '.jpg', $path);
                    
                    try {
                        Storage::disk('public')->move($path, $newPath);
                        
                        // Update DB records
                        DB::table('caste_certificates')->where('id', $cert->id)->update(['file_path' => $newPath]);
                        DB::table('users')->where('id', $cert->user_id)->where('cast_certificate', $path)->update(['cast_certificate' => $newPath]);
                    } catch (\Exception $e) {
                        \Log::error("Failed to rename invalid PDF certificate: " . $e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse fixing corrupted extension data
    }
};
