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
See https://github.com/david-mk/mail-map for details on MailMap usage.

## Setup
This offers an adapter for MailMap to be used within laravel. Simply publish the configuration with laravel's `vendor:publish` command to make the configuration file `config/mailmap.php`. In that file you can provide credentials and configuration for MailMap to connect to your mail server.

A service provider is also provided which injects the `MailMap` and `MailMap\MailFactory` classes into the application container as singletons. You'll need to include this in the service provider section of your `config/app.php` if you want to use it (its required in order to use the `MailMapModel` described below).

## The MailMapModel
The primary usage of the package is to create model-like classes that interact with the different mailboxes in your mail server. Just extend the provided `MailMapModel` and set the mailbox that it should connect to. Then you can let laravel inject these "models" into your application, providing you quick and easy access to your email server anywhere.

# Example
The following is an example that illustrates creating a MailMapModel that corresponds to emails in a gmail account that have the "Github" label (note that this is just an example code, and not intended for actual use).

```php
<?php

namespace App\Models\MailMapModels;

use LaraMailMap\MailMapModel;

class GithubEmail extends MailMapModel
{
    protected $mailbox = 'Github';
}

```

Then in a controller, the `GithubEmail` can be used like

```php
<?php

use App\Models\MailMapModels\GithubEmail;
use Carbon\Carbon;

class MyController
{
    public function githubMessages(GithubEmail $githubEmail, Carbon $since, $limit = 10)
    {
        $messages = $githubEmail->query(function ($query) use ($since, $limit) {
            return $query->since($since->format('d-M-Y'))->limit($limit);
        });

        return view('show.my.github.emails', compact('messages'));
    }
}

```
