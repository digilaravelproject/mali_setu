<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('districts') || ! Schema::hasTable('talukas') || ! Schema::hasTable('localities')) {
            return;
        }

        $now = now();
        $normalize = static function ($value): ?string {
            if (! is_scalar($value)) {
                return null;
            }

            $value = trim((string) $value);
            return $value === '' || in_array(strtolower($value), ['n/a', 'na', 'null', '-'], true) ? null : $value;
        };

        $districtIds = [];
        $talukaIds = [];

        $districtId = static function (string $name) use (&$districtIds, $now): int {
            $key = mb_strtolower($name);
            if (isset($districtIds[$key])) {
                return $districtIds[$key];
            }

            $id = DB::table('districts')->whereRaw('LOWER(name) = ?', [$key])->value('id');
            if (! $id) {
                $id = DB::table('districts')->insertGetId(['name' => $name, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
            }

            return $districtIds[$key] = (int) $id;
        };

        $talukaId = static function (int $districtId, string $name) use (&$talukaIds, $now): int {
            $key = $districtId.'|'.mb_strtolower($name);
            if (isset($talukaIds[$key])) {
                return $talukaIds[$key];
            }

            $id = DB::table('talukas')->where('district_id', $districtId)->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->value('id');
            if (! $id) {
                $id = DB::table('talukas')->insertGetId(['district_id' => $districtId, 'name' => $name, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
            }

            return $talukaIds[$key] = (int) $id;
        };

        $import = static function (array $location) use ($normalize, $districtId, $talukaId, $now): void {
            $district = $normalize($location['district'] ?? null);
            $taluka = $normalize($location['taluka'] ?? null);
            $city = $normalize($location['city'] ?? null);
            $village = $normalize($location['village'] ?? null);

            if (! $district && ! $taluka && ! $city && ! $village) {
                return;
            }

            $districtKey = $districtId($district ?: 'Unassigned District');
            if (! $taluka && ! $city && ! $village) {
                return;
            }

            $talukaKey = $talukaId($districtKey, $taluka ?: 'Unassigned Taluka');
            foreach (['city' => $city, 'village' => $village] as $type => $name) {
                if (! $name) {
                    continue;
                }

                DB::table('localities')->updateOrInsert(
                    ['taluka_id' => $talukaKey, 'name' => $name, 'type' => $type],
                    ['is_active' => true, 'updated_at' => $now, 'created_at' => $now]
                );
            }
        };

        if (Schema::hasTable('users')) {
            $columns = collect(['district', 'city', 'village'])->filter(fn ($column) => Schema::hasColumn('users', $column))->values()->all();
            DB::table('users')->select($columns)->orderBy('id')->chunk(500, function ($rows) use ($import) {
                foreach ($rows as $row) {
                    $import((array) $row);
                }
            });
        }

        if (Schema::hasTable('businesses')) {
            $columns = collect(['district', 'taluka', 'city', 'village'])->filter(fn ($column) => Schema::hasColumn('businesses', $column))->values()->all();
            if ($columns !== []) {
                DB::table('businesses')->select($columns)->orderBy('id')->chunk(500, function ($rows) use ($import) {
                    foreach ($rows as $row) {
                        $import((array) $row);
                    }
                });
            }
        }

        if (Schema::hasTable('business_locations') && Schema::hasColumn('business_locations', 'city')) {
            DB::table('business_locations')->select('city')->orderBy('id')->chunk(500, function ($rows) use ($import) {
                foreach ($rows as $row) {
                    $import((array) $row);
                }
            });
        }

        if (Schema::hasTable('matrimony_profiles')) {
            DB::table('matrimony_profiles')->select('location_details')->orderBy('id')->chunk(500, function ($rows) use ($import) {
                foreach ($rows as $row) {
                    $location = is_string($row->location_details) ? json_decode($row->location_details, true) : (array) $row->location_details;
                    if (is_array($location)) {
                        $import($location);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        // Imported records may have been edited by admins; keep them on rollback.
    }
};
