<?php

namespace App\Services;

class QrisDynamicGenerator
{
    /**
     * Compute CRC-16 CCITT False checksum for EMVCo specification.
     *
     * @param string $str
     * @return string (4-digit hex string)
     */
    public static function crc16CcittFalse(string $str): string
    {
        $crc = 0xFFFF;
        $len = strlen($str);
        for ($c = 0; $c < $len; $c++) {
            $crc ^= (ord($str[$c]) << 8);
            for ($i = 0; $i < 8; $i++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }
        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /**
     * Convert a static QRIS string into a dynamic QRIS string with custom nominal.
     *
     * @param string $staticQris  The raw scanned static QRIS string (starts with 000201...)
     * @param float|int $amount   The amount to charge
     * @return string|null        The dynamic QRIS string ready to be generated as QR code
     */
    public static function makeDynamic(string $staticQris, $amount): ?string
    {
        // 1. Clean up whitespace
        $qris = trim($staticQris);
        if (empty($qris)) {
            return null;
        }

        // 2. Remove CRC16 tag at the end (Tag 63: e.g. 6304XXXX)
        // According to EMVCo, CRC16 must be the last tag: Tag 63, length 04, value 4 hex chars.
        if (preg_match('/6304[A-F0-9]{4}$/i', $qris)) {
            $qris = substr($qris, 0, -8);
        } elseif (str_ends_with($qris, '6304')) {
            $qris = substr($qris, 0, -4);
        }

        // 3. Remove existing Tag 54 (Amount) if present
        // Format of Tag 54: "54" + 2-digit length + value
        // Using regex to strip tag 54 cleanly if it exists.
        $qris = preg_replace('/54\d{2}\d+(\.\d{2})?/', '', $qris);

        // 4. Format amount (must be integer value or decimal with 2 digits, QRIS standard in IDR uses integer)
        $amountStr = (string) (int) round($amount);
        $lengthStr = str_pad((string) strlen($amountStr), 2, '0', STR_PAD_LEFT);
        
        // Tag 54 = Transaction Amount
        $tagAmount = '54' . $lengthStr . $amountStr;

        // 5. Insert Tag 54 before Tag 58 (Country Code) or Tag 59 (Merchant Name)
        // EMVCo tags are ordered, Tag 58 is commonly ID, Tag 59 is Merchant Name.
        // We will insert Tag 54 right before Tag 58.
        $pos58 = strpos($qris, '5802ID');
        if ($pos58 !== false) {
            $qris = substr($qris, 0, $pos58) . $tagAmount . substr($qris, $pos58);
        } else {
            // Fallback: append at the end
            $qris .= $tagAmount;
        }

        // 6. Append CRC16 tag identifier "6304" and compute the checksum
        $qris .= '6304';
        $checksum = self::crc16CcittFalse($qris);
        
        return $qris . $checksum;
    }
}
