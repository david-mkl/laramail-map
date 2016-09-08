# laramail-map
Laravel interface for mail-map

# Requirements
* >= php 5.6
* php imap extension
* php mb_string extension

# Installation
```
composer require mail-map/laramail-map
```

# Usage

## MailMap
See https://github.com/david-mk/mail-map for details on MailMap usage.

## Setup
This provides an adapter for [MailMap](https://github.com/david-mk/mail-map) to be used within laravel. Simply publish the configuration with laravel's artisan `vendor:publish` command to make the configuration file `config/mailmap.php`. There you can provide credentials and configuration for MailMap to connect to your mail server.

The [LaraMailMapServiceProvider](https://github.com/david-mk/laramail-map/blob/master/src/LaraMailMap/LaraMailMapServiceProvider.php) injects basic [MailMap](https://github.com/david-mk/mail-map/blob/master/src/MailMap/MailMap.php) and [MailMap\\MailFactory](https://github.com/david-mk/mail-map/blob/master/src/MailMap/MailFactory.php) classes into the application container as singletons. You'll need to include this in the service provider section of your `config/app.php` if you only need the basics.
```php
// config/app.php
'providers' => [
    // ... other providers
    LaraMailMap\LaraMailMapServiceProvider::class,
],
```

For more complex setups, you'll likely need to create your own service provider to handle different MailMap instances to your mailboxes/connections/mail-servers.

## The LaraMailMap Extension: Mailboxes as Models
Depending on the size and scope of your application, it may be necessary to keep your emails objects separate from another, each with a different set of methods and properties that can manipulate your individual emails.

MailMap and in turn LaraMailMap are both designed to be as configurable as possible, allowing you access into each part of the library, so the specifics of this implementation is largely left to the developer.

However, the following is an example of how this can be approached by using the provided LaraMailMap extension of MailMap.

In this example, we're going to make a section of the application that is designed to handle emails that we get from github.(This requires that the emails are already in the desired mailbox. In gmail, this can be accomplished with filters and labels). Assume then that we already have our mail server setup with a mailbox called 'Github'.

First, we'll create an extension of the [LaraMailMap](https://github.com/david-mk/laramail-map/blob/master/src/LaraMailMap/LaraMailMap.php) class, which is itself an extension of [MailMap](https://github.com/david-mk/mail-map/blob/master/src/MailMap/MailMap.php).

You can put this wherever you like, but I'll put it in `app/MailMaps`.
```php
// app/MailMaps/GithubMailMap.php
<?php

namespace App\MailMaps;

use LaraMailMap\LaraMailMap;

class GithubMailMap extends LaraMailMap
{
}
```

Then we need create a class that will act as our "models" for our github emails. I put them in a sub-directory of a Models directory in order to distinguish them from other types of models.
```php
// app/Models/Imap/GithubEmail.php
<?php

namespace App\Models\Imap;

use MailMap\Mail;

class GithubEmail extends Mail
{
    // Find the github user's username who sent the message.
    // 'Unknown' if it wasn't found
    public function findSenderUser()
    {
        return $this->header('X-GitHub-Sender', 'Unknown');
    }
}

```

As a side note, it is not required for the email "Models" to extend [MailMap\\Mail](https://github.com/david-mk/mail-map/blob/master/src/MailMap/Mail.php), although that class provides some helpful methods. They need only implement the [MailMap\\Contracts\\Mail](https://github.com/david-mk/mail-map/blob/master/src/MailMap/Contracts/Mail.php) interface.

The last thing to do is to generate a service provider and wire everything together.
```php
// app/Providers/MailMapProvider.php
<?php

namespace App\Providers;

use App\MailMaps\GithubMailMap;
use App\Models\Imap\GithubEmail;
use Illuminate\Support\ServiceProvider;
use MailMap\MailFactory;

class MailMapProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GithubMailMap::class, function () {
            $config = config('mailmap'); // connection information
            $mailbox = config('mailmap.mailboxes.github'); // name of the mailbox for this MailMap
            $factory = new MailFactory(GithubEmail::class); // This will generate our GithubEmail "models"

            return new GithubMailMap($config, $mailbox, $factory);
        });
    }
}
```

In `config/mailmap.php`, there is an option to list all of your mailboxes so that they can be easily accessed from the `config()` helper. In this case, the github emails are in the 'Github' mailbox, so an entry is added to `config/mailmap.php`
```php
'mailboxes' => [
    'inbox' => 'INBOX',
    'github' => 'Github'
],
```

Of course, adding additional mailboxes to the list is completely optional. It is just a way to allow your code to remain ignorant of the actual name so that in case the mailbox name changes, you just need to change the configuration. (`LaraMailMap` will use the default `inbox` entry by default. See the [service provider](https://github.com/david-mk/laramail-map/blob/master/src/LaraMailMap/LaraMailMapServiceProvider.php) for more details.)

Now all we need to do is type hint the GithubMailMap class anywhere in our application, and we have instant access to all of our github emails. For example, in a controller

```php
// app/Http/Controllers/MyController.php
<?php

namespace App\Https\Controllers;

use App\MailMaps\GithubMailMap;
use Carbon\Carbon;

class MyController
{
    public function githubMessages(GithubMailMap $githubMap, Carbon $since, $limit = 10)
    {
        $messages = $githubMap->queryMailbox(function ($query) use ($since, $limit) {
            return $query->since($since->format('d-M-Y'))->limit($limit);
        });

        return view('show.my.github.emails', compact('messages'));
    }
}
```
