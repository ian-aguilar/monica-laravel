<?php

namespace App\Contact\ManageContactName\Web\ViewHelpers;

use App\Models\Contact;
use App\Models\User;

class ModuleContactNameViewHelper
{
    public static function data(Contact $contact, User $user): array
    {
        return [
            'name' => $contact->name,
            'url' => [
                'edit' => route('contact.edit', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }
}
