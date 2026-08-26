<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class CCAvenue
{
    private $merchantId;
    private $workingKey;
    private $accessCode;
    private $apiWorkingKey;
    private $apiAccessCode;
    private $sandbox;

    public function __construct()
    {
        $this->merchantId = config('services.ccavenue.merchant_id');
        $this->workingKey = config('services.ccavenue.working_key');
        $this->accessCode = config('services.ccavenue.access_code');
        $this->apiWorkingKey = config('services.ccavenue.api_working_key') ?: $this->workingKey;
        $this->apiAccessCode = config('services.ccavenue.api_access_code') ?: $this->accessCode;
        $this->sandbox = config('services.ccavenue.sandbox', true);
    }

    /**
     * Get the payment gateway submission URL.
     */
    public function getPaymentUrl()
    {
        // return $this->sandbox
        //     ? 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction'
        //     : 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

        return 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
    }

    /**
     * Get the access code.
     */
    public function getAccessCode()
    {
        return $this->accessCode;
    }

    /**
     * Fetch the authoritative status for an order from CCAvenue.
     *
     * @return array<string, mixed>
     */
    public function getOrderStatus(string $orderId): array
    {
        $requestPayload = json_encode(
            ['order_no' => $orderId],
            JSON_THROW_ON_ERROR
        );
        $encryptedRequest = $this->encryptAes($requestPayload, $this->apiWorkingKey);

        $response = Http::asForm()
            ->acceptJson()
            ->timeout((int) config('services.ccavenue.status_timeout', 15))
            ->post(config('services.ccavenue.status_url'), [
                'request_type' => 'JSON',
                'access_code' => $this->apiAccessCode,
                'command' => 'orderStatusTracker',
                'response_type' => 'JSON',
                'version' => '1.1',
                'enc_request' => $encryptedRequest,
            ]);

        $response->throw();
        $envelope = $response->json();

        // CCAvenue commonly returns the outer API envelope as
        // status=0&enc_response=... even when response_type is JSON. The
        // decrypted enc_response itself is JSON.
        if (! is_array($envelope)) {
            $envelope = [];
            parse_str(trim($response->body()), $envelope);
        }

        if (! is_array($envelope) || (string) ($envelope['status'] ?? '') !== '0') {
            $errorCode = trim((string) ($envelope['enc_error_code'] ?? $envelope['error_code'] ?? ''));
            $errorMessage = trim((string) ($envelope['enc_response'] ?? ''));
            throw new RuntimeException(
                'CCAvenue rejected the order-status request'
                .($errorCode !== '' ? " (code {$errorCode})" : '')
                .($errorMessage !== '' ? ": {$errorMessage}" : '.')
            );
        }

        $encryptedResponse = $envelope['enc_response'] ?? null;
        if (! is_string($encryptedResponse) || $encryptedResponse === '') {
            throw new RuntimeException('CCAvenue returned an empty order-status response.');
        }

        $decrypted = $this->decryptAes($encryptedResponse, $this->apiWorkingKey);
        $status = json_decode($decrypted, true);

        if (! is_array($status)) {
            throw new RuntimeException('CCAvenue returned an invalid order-status response.');
        }

        return $status;
    }

    /**
     * Encrypt request parameters.
     *
     * @param array $params
     * @return string
     */
    public function encrypt(array $params)
    {
        // Add merchant_id to parameters
        $params['merchant_id'] = $this->merchantId;

        // Build query string matching official CCAvenue integration standard
        $queryString = '';
        foreach ($params as $key => $value) {
            $queryString .= $key . '=' . $value . '&';
        }
        $queryString = rtrim($queryString, '&');

        // Encrypt query string using the working key
        return $this->encryptAes($queryString, $this->workingKey);
    }

    /**
     * Decrypt encrypted response from CCAvenue.
     *
     * @param string $encResponse
     * @return array
     */
    public function decrypt($encResponse)
    {
        $decryptedString = $this->decryptAes($encResponse, $this->workingKey);
        
        $params = [];
        parse_str($decryptedString, $params);
        
        return $params;
    }

    /**
     * AES Encryption (matching CCAvenue standard)
     */
    private function encryptAes($plainText, $key)
    {
        $secretKey = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        return bin2hex($encryptedText);
    }

    /**
     * AES Decryption (matching CCAvenue standard)
     */
    private function decryptAes($encryptedText, $key)
    {
        $secretKey = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedBin = $this->hextobin($encryptedText);
        return openssl_decrypt($encryptedBin, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
    }

    /**
     * Hexadecimal to binary conversion helper
     */
    private function hextobin($hexString)
    {
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length) {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0) {
                $binString = $packedString;
            } else {
                $binString .= $packedString;
            }
            $count += 2;
        }
        return $binString;
    }
}
