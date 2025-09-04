<?php

namespace App\Service;

class NipValidator
{
    // Basic PL NIP checksum validation; does not check GUS status yet
    public function isValidPlNip(string $nip): bool
    {
        $digits = preg_replace('/[^0-9]/', '', $nip ?? '');
        if (strlen($digits) !== 10) {
            return false;
        }
        $weights = [6,5,7,2,3,4,5,6,7];
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int)$digits[$i] * $weights[$i];
        }
        $control = $sum % 11;
        if ($control === 10) {
            return false;
        }
        return $control === (int)$digits[9];
    }
}

