<?php

namespace App\Providers\Filament;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

class LocalInitialsAvatarProvider implements AvatarProvider
{
    public function get(Model $user): string
    {
        $name = $user->name ?? 'User';
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper(mb_substr($w, 0, 1));
        }
        if (empty($initials)) {
            $initials = 'U';
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" width="128" height="128">'
            . '<rect width="128" height="128" rx="64" fill="#0F172A"/>'
            . '<text x="50%" y="54%" text-anchor="middle" dominant-baseline="middle" fill="#FFFFFF" font-family="Figtree, sans-serif" font-size="48" font-weight="700">'
            . htmlspecialchars($initials)
            . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }
}
