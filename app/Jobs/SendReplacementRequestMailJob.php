<?php

namespace App\Jobs;

use App\Mail\MailReplacementRequest;
use App\Mail\ReplacementRequestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReplacementRequestMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mail_to;
    protected $data;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mail_to, $data)
    {
        $this->mail_to = $mail_to;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->mail_to)->send(new ReplacementRequestMail($this->data));
    }
}
