<?php

namespace App\Actions;

class GenerateRandomSlug
{
    public function execute($adjectiveCount = 1): string
    {
        $adjectives = explode("\n", file_get_contents(resource_path('text/english-adjectives.txt')));
        $nouns = explode("\n", file_get_contents(resource_path('text/english-nouns.txt')));

        $slug = '';

        for ($i = 0; $i < $adjectiveCount; $i++) {
            $slug .= $adjectives[array_rand($adjectives)] . '-';
        }

        $slug .= $nouns[array_rand($nouns)];

        return $slug;
    }
}