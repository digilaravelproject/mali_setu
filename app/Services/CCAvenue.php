<?php

namespace App\Services;

class CCAvenue
{
    private $merchantId;
    private $workingKey;
    private $accessCode;
    private $sandbox;

    public function __construct()
    {
        $this->merchantId = config('services.ccavenue.merchant_id');
        $this->workingKey = config('services.ccavenue.working_key');
        $this->accessCode = config('services.ccavenue.access_code');
        $this->sandbox = config('services.ccavenue.sandbox', true);
    }

    /**
     * Get the payment gateway submission URL.
     */
    public function getPaymentUrl()
    {
        return $this->sandbox
            ? 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction'
            : 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
    }

    /**
     * Get the access code.
     */
    public function getAccessCode()
    {
        return $this->accessCode;
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
