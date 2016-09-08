<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MailMap Host
    |--------------------------------------------------------------------------
    |
    | IMAP mail server host
    |
    */

    'host' => env('MAILMAP_HOST', 'imap.gmail.com'),

    /*
    |--------------------------------------------------------------------------
    | MailMap user
    |--------------------------------------------------------------------------
    |
    | User on imap server to login with
    |
    */

    'user' => env('MAILMAP_USER'),

    /*
    |--------------------------------------------------------------------------
    | MailMap password
    |--------------------------------------------------------------------------
    |
    | Password on imap server to login with
    |
    */

    'password' => env('MAILMAP_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | MailMap Port
    |--------------------------------------------------------------------------
    |
    | IMAP port to open connections on
    |
    */

    'port' => env('MAILMAP_PORT', 993),

    /*
    |--------------------------------------------------------------------------
    | MailMap Port
    |--------------------------------------------------------------------------
    |
    | IMAP service to open connections with
    |
    */

    'service' => env('MAILMAP_SERVICE', 'imap'),

    /*
    |--------------------------------------------------------------------------
    | IMAP Encryption
    |--------------------------------------------------------------------------
    |
    | IMAP connection encryption type
    |
    */

    'encryption' => env('MAILMAP_ENC', 'ssl'),

    /*
    |--------------------------------------------------------------------------
    | IMAP Mailboxes
    |--------------------------------------------------------------------------
    |
    | List of mailboxes that LaraMailMap interfaces can use
    |
    */

    'mailboxes' => [
        'inbox' => 'INBOX'
    ],

    /*
    |--------------------------------------------------------------------------
    | Mail wrapper class
    |--------------------------------------------------------------------------
    |
    | This is the primary class that the default LaraMailMap's MailFactory will
    | wrap found emails in. This class must implement the MailMap\Contracts\Mail
    | contract or else an InvalidArgumentException will be thrown
    |
    */

    'mailwrapper' => \MailMap\Mail::class
];
