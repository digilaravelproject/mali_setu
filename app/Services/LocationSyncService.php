<?php

namespace App\Services;

use App\Models\District;
use App\Models\Locality;
use App\Models\State;
use App\Models\Taluka;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class LocationSyncService
{
    public function sync(string $level): int
    {
        return match ($level) {
            'states' => $this->syncStates(),
            'districts' => $this->syncDistricts(),
            'talukas' => $this->syncTalukas(),
            'localities' => $this->syncCities(),
            default => throw new RuntimeException('Unsupported location level.'),
        };
    }

    public function syncStates(): int
    {
        $rows = $this->fetchAll('states');

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                State::updateOrCreate(
                    ['external_code' => (string) $row['code']],
                    ['name' => $row['name'], 'is_active' => true, 'synced_at' => now()]
                );
            }
        });

        return count($rows);
    }

    public function syncDistricts(): int
    {
        if (! State::where('external_code', '!=', 'LEGACY')->exists()) {
            $this->syncStates();
        }
        $states = State::where('external_code', '!=', 'LEGACY')->pluck('id', 'external_code');
        $rows = $this->fetchAll('districts');

        DB::transaction(function () use ($rows, $states) {
            foreach ($rows as $row) {
                $stateId = $states[(string) $row['stateCode']] ?? null;
                if (! $stateId) {
                    continue;
                }
                District::updateOrCreate(
                    ['external_code' => (string) $row['code']],
                    ['state_id' => $stateId, 'name' => $row['name'], 'is_active' => true, 'synced_at' => now()]
                );
            }
        });

        return count($rows);
    }

    public function syncTalukas(): int
    {
        if (! District::whereNotNull('external_code')->exists()) {
            $this->syncDistricts();
        }
        $districts = District::whereNotNull('external_code')->pluck('id', 'external_code');
        $rows = $this->fetchAll('subdistricts');

        DB::transaction(function () use ($rows, $districts) {
            foreach ($rows as $row) {
                $districtId = $districts[(string) $row['districtCode']] ?? null;
                if (! $districtId) {
                    continue;
                }
                Taluka::updateOrCreate(
                    ['external_code' => (string) $row['code']],
                    ['district_id' => $districtId, 'name' => $row['name'], 'is_active' => true, 'synced_at' => now()]
                );
            }
        });

        return count($rows);
    }

    public function syncCities(): int
    {
        if (! District::whereNotNull('external_code')->exists()) {
            $this->syncDistricts();
        }
        $districts = District::whereNotNull('external_code')->get()->keyBy('external_code');
        $states = State::where('external_code', '!=', 'LEGACY')->get()->keyBy('external_code');
        $rows = $this->fetchAll('cities');

        DB::transaction(function () use ($rows, $districts, $states) {
            foreach ($rows as $row) {
                $district = $districts->get((string) ($row['districtCode'] ?? ''));
                if (! $district) {
                    $state = $states->get((string) ($row['stateCode'] ?? ''));
                    if (! $state) {
                        continue;
                    }
                    $districtCode = 'URBAN-STATE-'.$state->external_code;
                    $district = District::updateOrCreate(
                        ['external_code' => $districtCode],
                        ['state_id' => $state->id, 'name' => 'State-level urban areas', 'is_active' => true, 'synced_at' => now()]
                    );
                    $districts->put($districtCode, $district);
                }

                // LGD urban-local-body records identify a district but not a sub-district.
                $urbanTaluka = Taluka::updateOrCreate(
                    ['external_code' => 'URBAN-'.$district->external_code],
                    ['district_id' => $district->id, 'name' => 'Urban areas', 'is_active' => true, 'synced_at' => now()]
                );
                $code = (string) $row['code'];
                $locality = Locality::where('external_code', $code)->first()
                    ?? Locality::where('taluka_id', $urbanTaluka->id)
                        ->where('name', $row['name'])->where('type', 'city')->first();

                // The provider can contain two ULB codes with the same name while omitting
                // their district. The local schema intentionally keeps one display value.
                if ($locality && $locality->external_code && $locality->external_code !== $code) {
                    $locality->update(['is_active' => true, 'synced_at' => now()]);
                    continue;
                }

                $locality ??= new Locality;
                $locality->fill([
                    'external_code' => $code, 'taluka_id' => $urbanTaluka->id,
                    'name' => $row['name'], 'type' => 'city', 'is_active' => true, 'synced_at' => now(),
                ])->save();
            }
        });

        return count($rows);
    }

    private function fetchAll(string $endpoint): array
    {
        $page = 1;
        $rows = [];

        do {
            $payload = $this->client()->get($endpoint, ['page' => $page, 'limit' => 100])
                ->throw()->json();
            $batch = $payload['data'] ?? null;
            if (! is_array($batch)) {
                throw new RuntimeException("Location API returned an invalid {$endpoint} response.");
            }
            array_push($rows, ...$batch);
            $pages = max(1, (int) ($payload['pagination']['pages'] ?? 1));
            $page++;
        } while ($page <= $pages);

        return $rows;
    }

    private function client(): PendingRequest
    {
        $client = Http::baseUrl(rtrim((string) config('services.location_sync.base_url'), '/'))
            ->acceptJson()
            ->timeout((int) config('services.location_sync.timeout', 30))
            ->retry(2, 300);

        $key = config('services.location_sync.api_key');
        return $key ? $client->withToken($key) : $client;
    }
}
