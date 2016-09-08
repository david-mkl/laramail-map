<?php

namespace LaraMailMap;

use Illuminate\Support\Collection;
use MailMap\MailMap;

class MailMapModel
{
    /**
     * Mailbox for the imap model.
     * Defaults to 'INBOX'
     *
     * @var string
     */
    protected $mailbox = 'INBOX';

    /**
     * The MailMap interface
     *
     * @var \MailMap\MailMap
     */
    protected static $mailMap;

    /**
     * Set the connection interface on the model
     *
     * @param \MailMap\MailMap $mailMap
     */
    public static function setImapConnection(MailMap $mailMap)
    {
        self::$mailMap = $mailMap;
    }

    /**
     * Make a new query to execute on the connection
     *
     * @param callable $queryBuild
     * @return \Illuminate\Support\Collection
     */
    public function query(callable $queryBuild = null)
    {
        return new Collection(self::$mailMap->query($this->mailbox, $queryBuild));
    }
}
