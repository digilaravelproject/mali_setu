<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FirebaseCredentialService
{
    public const PATH = 'firebase/service-account.json';

    public function store(UploadedFile $file): array
    {
        $credentials = json_decode($file->get(), true);
        $this->validate($credentials);
        Storage::disk('local')->put(self::PATH, json_encode($credentials, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $this->summary($credentials);
    }

    public function credentials(): array
    {
        if (! Storage::disk('local')->exists(self::PATH)) {
            throw new \RuntimeException('Firebase service-account credentials are not configured.');
        }

        $credentials = json_decode(Storage::disk('local')->get(self::PATH), true);
        $this->validate($credentials);

        return $credentials;
    }

    public function status(): array
    {
        try {
            $credentials = $this->credentials();

            return ['configured' => true] + $this->summary($credentials);
        } catch (\Throwable $e) {
            return ['configured' => false, 'project_id' => null, 'client_email' => null];
        }
    }

    private function validate(mixed $credentials): void
    {
        $required = ['type', 'project_id', 'private_key', 'client_email', 'token_uri'];
        if (! is_array($credentials) || ($credentials['type'] ?? null) !== 'service_account') {
            throw ValidationException::withMessages(['credentials' => 'Upload a valid Firebase service-account JSON file.']);
        }

        foreach ($required as $key) {
            if (empty($credentials[$key])) {
                throw ValidationException::withMessages(['credentials' => "Firebase credentials are missing {$key}."]);
            }
        }

        if ($credentials['token_uri'] !== 'https://oauth2.googleapis.com/token') {
            throw ValidationException::withMessages(['credentials' => 'The Firebase token URI is not supported.']);
        }

        if (! preg_match('/^[a-z0-9][a-z0-9-]{4,28}[a-z0-9]$/', $credentials['project_id'])) {
            throw ValidationException::withMessages(['credentials' => 'The Firebase project ID is invalid.']);
        }

        if (openssl_pkey_get_private($credentials['private_key']) === false) {
            throw ValidationException::withMessages(['credentials' => 'The Firebase private key is invalid.']);
        }
    }

    private function summary(array $credentials): array
    {
        return [
            'project_id' => $credentials['project_id'],
            'client_email' => $credentials['client_email'],
        ];
    }
}
