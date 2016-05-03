<?php

namespace Kregel\Contractor\Commands;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Database\Eloquent\Model;
use Kregel\Contractor\Models\Contract;
use Mail;

class CheckDeadlines extends Command implements SelfHandling
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'contractor:check {--month= : Which month do you want to check? [0, 1, or 2]}';

    private $valid_months = [0,1,2];
    /**
     * This value is the difference between emailing all your clients at a random
     * time and just checking if the command works.
     * @var bool
     */
    protected $is_fake;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for all contracts';

    private $contracts = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->messages = [];
        $this->info("Firing up");
        if (in_array($this->option('month'), $this->valid_months)) {
            switch ($this->option('month')) {
                case 0:
                    $this->contracts = $this->getAllContracts(30);
                    break;
                case 1:
                    $this->contracts = $this->getAllContracts(60, 30);
                    break;
                case 2:
                    $this->contracts = $this->getAllContracts(90, 60);
                    break;
            }
            if(!empty($this->contracts)){
                $this->info('I found some contracts... I\'ll send out those emails.');
            }
            $this->sendDahEmails();
        } else {
            $this->error('You must define the option [month]');
        }
    }

    private function getAllContracts($days = 30, $prev = null)
    {
        $this->info('I will start by looking through all the contracts...');
        $now = is_null($prev) ? ' NOW() ' : '(NOW() + INTERVAL ' . $prev . ' DAY) ';
        $contracts = Contract::whereRaw(' ended_at > ' . $now . ' AND ended_at < (NOW() + INTERVAL ' . $days . ' DAY)')->get();
        return $contracts;
    }

    private function sendDahEmails()
    {
        foreach ($this->contracts as $contract) {
            if (!empty($contract)) {
                $this->info('Sending emails to [' . $contract->id . '] ' . $contract->ended_at->format('Y-m-d'));
                \Artisan::call('contractor:send-mail', [
                    '--contract' => $contract->id, '--type' => 'update'
                ]);
            }
        }
    }
}
