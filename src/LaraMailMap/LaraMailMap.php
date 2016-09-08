<?php

namespace LaraMailMap;

use Illuminate\Support\Collection;
use MailMap\Contracts\MailFactory as MailFactoryContract;
use MailMap\MailMap;

class LaraMailMap extends MailMap
{
    /**
     * Mailbox for the imap connection
     *
     * @var string
     */
    protected $mailbox;

    /**
     * Create interface to specific mailbox
     *
     * @param \MailMap\MailMap $mailMap
     * @param string $mailbox
     */
    public function __construct(array $config, $mailbox, MailFactoryContract $mailFactory = null)
    {
        $this->mailbox = $mailbox;
        parent::__construct($config, $mailFactory);
    }

    /**
     * Make a new query to execute on the connection
     *
     * @param callable $queryBuild
     * @return \Illuminate\Support\Collection
     */
    public function queryMailbox(callable $queryBuild = null)
    {
        return new Collection(parent::query($this->mailbox, $queryBuild));
    }
}
