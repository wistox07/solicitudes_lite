<?php

namespace App\Jobs;

use App\Mail\EmailRegisterRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $request; 
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {

        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
       $email = new EmailRegisterRequest($this->request);
        Mail::to($this->request->petitioner->email)->send($email);
    }
}
