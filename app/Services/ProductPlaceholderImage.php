<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Generates a clean, branded SVG placeholder for a product that has no
 * uploaded photo yet — used both by ProductImageSeeder (bulk, for demo
 * data) and live by SellerDashboard when a Seller saves a product
 * without choosing a photo, so no product in SEAPEDIA is ever left with
 * a bare "no image" box.
 */
class ProductPlaceholderImage
{
    /** @var array<string,array{0:string,1:string,2:string}> keyword => [emoji, colorFrom, colorTo] */
    private static array $keywordStyles = [
        'sepatu' => ['👟', '#2563eb', '#1e40af'],
        'lari' => ['🏃', '#2563eb', '#1e40af'],
        'kaos' => ['👕', '#0ea5e9', '#0369a1'],
        'baju' => ['👕', '#0ea5e9', '#0369a1'],
        'botol' => ['🍶', '#10b981', '#047857'],
        'minum' => ['🍶', '#10b981', '#047857'],
        'keyboard' => ['⌨️', '#8b5cf6', '#6d28d9'],
        'mouse' => ['🖱️', '#8b5cf6', '#6d28d9'],
        'kopi' => ['☕', '#92400e', '#78350f'],
        'tas' => ['👜', '#ec4899', '#be185d'],
        'jam' => ['⌚', '#f59e0b', '#b45309'],
        'buku' => ['📚', '#ef4444', '#b91c1c'],
        'elektronik' => ['🔌', '#6366f1', '#4338ca'],
    ];

    /**
     * Generates the placeholder and stores it on the `public` disk,
     * returning the relative path to save into products.image.
     */
    public static function generateAndStore(int $productId, string $name): string
    {
        [$emoji, $from, $to] = self::styleFor($name);
        $svg = self::buildSvg($name, $emoji, $from, $to);

        $path = 'products/'.Str::slug($name).'-'.$productId.'-'.Str::random(6).'.svg';
        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    private static function styleFor(string $name): array
    {
        $lower = strtolower($name);

        foreach (self::$keywordStyles as $keyword => $style) {
            if (str_contains($lower, $keyword)) {
                return $style;
            }
        }

        return ['🛍️', '#64748b', '#334155'];
    }

    private static function buildSvg(string $name, string $emoji, string $from, string $to): string
    {
        $label = htmlspecialchars(Str::limit($name, 24), ENT_QUOTES, 'UTF-8');
        $gradientId = 'g'.crc32($name.microtime());

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400">
    <defs>
        <linearGradient id="{$gradientId}" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="{$from}"/>
            <stop offset="100%" stop-color="{$to}"/>
        </linearGradient>
    </defs>
    <rect width="400" height="400" fill="url(#{$gradientId})"/>
    <circle cx="200" cy="160" r="90" fill="rgba(255,255,255,0.12)"/>
    <text x="200" y="195" font-size="90" text-anchor="middle" dominant-baseline="middle">{$emoji}</text>
    <text x="200" y="320" font-size="22" font-family="sans-serif" font-weight="700" fill="white" text-anchor="middle">{$label}</text>
</svg>
SVG;
    }
}
