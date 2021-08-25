<?php

namespace Peidgnad\LaravelEthereum\Console;

use Illuminate\Console\Command;

class EventListenCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'ethereum:event:listen
        {provider? : The name of provider will be use}
        {--fromBlock : Will listen for new events start from this block}
        {--address=* : A list of addresses from which logs should originate}
        {--topics=* : A list of topic hash, only listen for new events have a topic in this list}
        {--memory=128 : The memory limit in megabytes}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for new events on any Ethereum blockchain.';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle(): ?int
    {
    }
}
