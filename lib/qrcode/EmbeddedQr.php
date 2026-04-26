<?php

declare(strict_types=1);

/**
 * QR helper embebido sin Composer.
 * Usa QuickChart para renderizar PNG remoto y lo incrusta como data URI.
 * Si no hay acceso externo, retorna una URL pública para fallback visual.
 */
final class EmbeddedQr
{
    public static function asDataUri(string $text, int $size = 280): string
    {
        $url = self::remoteUrl($text, $size);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 5,
            ],
        ]);

        $bin = @file_get_contents($url, false, $context);
        if ($bin === false) {
            return '';
        }

        return 'data:image/png;base64,' . base64_encode($bin);
    }

    public static function remoteUrl(string $text, int $size = 280): string
    {
        return 'https://quickchart.io/qr?text=' . rawurlencode($text) . '&size=' . max(120, $size);
    }
}
