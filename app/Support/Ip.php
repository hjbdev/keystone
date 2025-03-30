<?php

namespace App\Support;

use Illuminate\Support\Str;

class Ip
{
    public static function inNetwork(string $ip, string $network): bool
    {
        if (Str::contains($network, '/')) {
            [$subnet, $mask] = explode('/', $network);
            $subnet = inet_pton($subnet);
            $ip = inet_pton($ip);

            $mask = (int) $mask;
            $maskBytes = (int) floor($mask / 8);
            $maskBits = $mask % 8;

            if ($maskBytes > 0) {
                $subnetBytes = substr($subnet, 0, $maskBytes);
                $ipBytes = substr($ip, 0, $maskBytes);

                if ($subnetBytes !== $ipBytes) {
                    return false;
                }
            }

            if ($maskBits > 0) {
                $maskValue = chr(pow(2, $maskBits) - 1);
                $subnetByte = ord($subnet[$maskBytes]);
                $ipByte = ord($ip[$maskBytes]);

                if (($subnetByte & $maskValue) !== ($ipByte & $maskValue)) {
                    return false;
                }
            }

            return true;
        }

        return $ip === $network;
    }
}
