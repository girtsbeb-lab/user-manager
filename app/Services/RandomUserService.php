<?php

namespace App\Services;

use App\Models\User;
use App\Models\Contact;
use App\Models\Address;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RandomUserService
{
    public function import(int $results = 50, bool $truncate = false): array
    {
        $url = config('app.random_user_api', 'https://randomuser.me/api/');
        
        $response = Http::timeout(30)->get($url, [
            'results' => $results,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Failed to fetch data from RandomUser API: ' . $response->status());
        }

        $data = $response->json();
        $users = $data['results'] ?? [];

        if (empty($users)) {
            throw new \RuntimeException('No users returned from API');
        }

        $imported = 0;
        $updated  = 0;
        $errors   = 0;

        DB::beginTransaction();
        try {
            if ($truncate) {
                Address::query()->forceDelete();
                Contact::query()->forceDelete();
                User::query()->forceDelete();
            }

            foreach ($users as $userData) {
                try {
                    $result = $this->processUser($userData);
                    if ($result === 'created') $imported++;
                    if ($result === 'updated') $updated++;
                } catch (\Throwable $e) {
                    Log::error('Failed to import user', ['error' => $e->getMessage(), 'data' => $userData]);
                    $errors++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return compact('imported', 'updated', 'errors');
    }

    private function processUser(array $data): string
    {
        $externalId = $data['login']['uuid'] ?? null;

        $user = User::withTrashed()->updateOrCreate(
            ['external_id' => $externalId],
            [
                'first_name'   => $data['name']['first'],
                'last_name'    => $data['name']['last'],
                'email'        => $data['email'],
                'username'     => $data['login']['username'],
                'gender'       => $data['gender'],
                'date_of_birth'=> substr($data['dob']['date'], 0, 10),
                'age'          => $data['dob']['age'],
                'nationality'  => $data['nat'],
                'avatar'       => $data['picture']['large'] ?? null,
                'thumbnail'    => $data['picture']['thumbnail'] ?? null,
                'deleted_at'   => null,
            ]
        );

        $wasCreated = $user->wasRecentlyCreated;

        Contact::withTrashed()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone'      => $data['phone'] ?? null,
                'cell'       => $data['cell'] ?? null,
                'email'      => $data['email'] ?? null,
                'deleted_at' => null,
            ]
        );

        $loc = $data['location'];
        Address::withTrashed()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'street_number'       => $loc['street']['number'] ?? null,
                'street_name'         => $loc['street']['name'] ?? null,
                'city'                => $loc['city'] ?? null,
                'state'               => $loc['state'] ?? null,
                'country'             => $loc['country'] ?? null,
                'postcode'            => (string)($loc['postcode'] ?? ''),
                'latitude'            => $loc['coordinates']['latitude'] ?? null,
                'longitude'           => $loc['coordinates']['longitude'] ?? null,
                'timezone_offset'     => $loc['timezone']['offset'] ?? null,
                'timezone_description'=> $loc['timezone']['description'] ?? null,
                'deleted_at'          => null,
            ]
        );

        return $wasCreated ? 'created' : 'updated';
    }
}
