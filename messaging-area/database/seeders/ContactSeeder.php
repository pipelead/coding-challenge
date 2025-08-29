<?php

namespace Database\Seeders;

use App\Modules\Messaging\Models\Contact;
use Database\Factories\ContactFactory;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            ['name' => 'João Silva', 'email' => 'joao@email.com', 'phone' => '+55 11 99999-1111'],
            ['name' => 'Maria Santos', 'email' => 'maria@email.com', 'phone' => '+55 11 99999-2222'],
            ['name' => 'Pedro Lima', 'email' => 'pedro@email.com', 'phone' => '+55 11 99999-3333'],
            ['name' => 'Ana Costa', 'email' => 'ana@email.com', 'phone' => '+55 11 99999-4444'],
        ];

        foreach ($contacts as $contact) {
            Contact::create($contact);
        }

        ContactFactory::new()->create();
    }
}
