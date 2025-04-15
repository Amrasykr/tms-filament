<?php

namespace Database\Seeders;

use App\Models\SocialMedia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialMedias = [
            [
                'name' => 'Facebook',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M22 12.1c0-5.5-4.5-10-10-10S2 6.6 2 12.1c0 5 3.7 9.1 8.4 9.9V15h-2.5v-2.9h2.5v-2.2c0-2.5 1.5-3.8 3.7-3.8 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.7-1.6 1.5v1.8h2.7l-.4 2.9h-2.3v7c4.8-.7 8.5-4.9 8.5-9.9z"/></svg>',
                'link' => 'https://facebook.com',
            ],
            [
                'name' => 'Instagram',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M7.5 2h9A5.5 5.5 0 0 1 22 7.5v9A5.5 5.5 0 0 1 16.5 22h-9A5.5 5.5 0 0 1 2 16.5v-9A5.5 5.5 0 0 1 7.5 2zm0 2A3.5 3.5 0 0 0 4 7.5v9A3.5 3.5 0 0 0 7.5 20h9A3.5 3.5 0 0 0 20 16.5v-9A3.5 3.5 0 0 0 16.5 4h-9zM12 7a5 5 0 1 1 0 10A5 5 0 0 1 12 7zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6zm5.8-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>',
                'link' => 'https://instagram.com',
            ],
            [
                'name' => 'YouTube',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.77 2.77 0 0 0-1.94-1.94C18.27 4 12 4 12 4s-6.27 0-8.6.48a2.77 2.77 0 0 0-1.94 1.94A29.94 29.94 0 0 0 1 12a29.94 29.94 0 0 0 .48 5.58 2.77 2.77 0 0 0 1.94 1.94C5.73 20 12 20 12 20s6.27 0 8.6-.48a2.77 2.77 0 0 0 1.94-1.94A29.94 29.94 0 0 0 23 12a29.94 29.94 0 0 0-.46-5.58z"/><polygon fill="currentColor" points="9.75 15.02 15.5 12 9.75 8.98"/></svg>',
                'link' => 'https://youtube.com',
            ],
            [
                'name' => 'Twitter',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M22.46 6c-.8.4-1.7.7-2.6.8a4.5 4.5 0 0 0 2-2.5 9.2 9.2 0 0 1-2.9 1.1 4.5 4.5 0 0 0-7.6 4.1A12.8 12.8 0 0 1 3 4.5 4.5 4.5 0 0 0 4.4 10a4.5 4.5 0 0 1-2-.6v.1a4.5 4.5 0 0 0 3.6 4.4 4.5 4.5 0 0 1-2 .1 4.5 4.5 0 0 0 4.2 3.1 9.1 9.1 0 0 1-6.7 1.9 12.8 12.8 0 0 0 7 2 12.7 12.7 0 0 0 12.7-12.7v-.6a9 9 0 0 0 2.3-2.3z"/></svg>',
                'link' => 'https://twitter.com',
            ],
        ];

        foreach ($socialMedias as $socialMedia) {
            SocialMedia::create($socialMedia);
        }
    }
}
