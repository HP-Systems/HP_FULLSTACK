<?php

namespace App\Jobs;

use App\Mail\SendMailActivation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendActivationURL implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $url;
    protected $user;
    protected $random;

    /**
     * Create a new job instance.
     */
    public function __construct( $url, $user, $random)
    {
         //
         $this->url = $url;
         $this->user = $user;
         $this->random = $random;
     
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new SendMailActivation($this->url,$this->user,$this->random));
    }
}
