<?php

namespace Kregel\Contractor\Commands;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Database\Eloquent\Model;
use Kregel\Contractor\Models\Contract;
use Mail;

class SendEmails extends Command implements SelfHandling
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'contractor:send-mail {--contract= : Contract ID} {--type= : Valid types are [new and update]} {--exclude=-1 : If you want to exclude any users.}';

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
    protected $description = 'Command for sending emails about a Contract';
    private $messages = [];

    /*
     * Custom Vars
     */
    private $contract;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->messages = [];
        $this->info("Firing up");
        if (is_numeric($this->option('contract'))) {
            $this->info("Finding Contract");
            $this->contract = Contract::find($this->option('contract'));
            $this->info("Found Contract");
            $this->jumpThroughContracts();
        } else {
            $this->error('You didn\'t declare a valid Contract id');
        }
    }

    /**
     * This will do the needed matching for the type of Contract creation and the
     * proper function to execute that type of Contract.
     */
    private function jumpThroughContracts()
    {
        $this->getRelatedOwners();
        switch (strtolower($this->option('type'))) {
            case 'new':
                $this->newContract();
                break;
            case 'update':
                $this->updatedContract();
                break;
            default:
                $this->error("No type selected {new, update}");
        }
    }

    public function getRelatedOwners()
    {
        foreach ($this->contract->contractors as $contractor) {
            if(!empty($contractor->user))
                $this->messages[$contractor->user->id] = [
                    'There is a new contract in your '. $contractor->name,
                    'contractor::home',
                    [
                        'user' => $contractor->user,
                    ]
                ];
        }
    }

    private function newContract()
    {
        $view = config('kregel.contractor.mail.template.new.Contract');
        $this->setOwner('There is a new contract in ' . $this->contract->name, $view);
        $this->sendDahEmails();
    }

    private function updatedContract()
    {
        $view = config('kregel.dispatch.mail.template.update.Contract');
        $this->setOwner('A contract in ' . $this->contract->name . ' has been updated.', $view);
        $this->sendDahEmails();
    }

    /**
     * Mail the owner of the Contract the information related to this Contract.
     * @param $subject
     * @param string $message
     */
    private function setOwner($subject, $view)
    {
        $user = $this->contract->user;
        $this->messages[$user->id] = [
            $subject,
            $view,
            [
                'user' => $user,
            ]
        ];
    }

    private function sendDahEmails()
    {
        if (!empty($this->option('exclude'))) {
            $excluding = explode(',', $this->option('exclude'));
            foreach ($excluding as $exclude) {
                unset($this->messages[$exclude]);
            }
        }
        foreach ($this->messages as $message_) {
            list($subject, $view, $data) = $message_;
            $user = $data['user'];
            if (!config('mail.pretend')) {
                Mail::queue($view, ['user' => $user, 'contract' => $this->contract], function ($message) use ($subject, $user) {
                    $message->subject($subject);
                    $message->to($user->email, $user->name);
                    $message->from(config('kregel.contractor.mail.from.address'), config('kregel.contractor.mail.from.name'));
                }, 'contract-emails');
            } else {
                $this->error("Mail not added to the queue! Mail pretend enabled!");
                $this->info('Mail was going to ' . $user->email);
            }
        }
    }
}
