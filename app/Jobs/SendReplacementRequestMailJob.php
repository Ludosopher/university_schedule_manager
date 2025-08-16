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
use Log;
use Throwable;

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
        try {
            Mail::to($this->mail_to)->send(new ReplacementRequestMail($this->data));
            Log::channel('mail')->info('At the address "'.$this->mail_to.'" the mail message has been sent.');
        } catch (\Exception $e) {
            Log::channel('mail')->error($e->getMessage());
        } catch (\Swift_TransportException $e) {
            Log::channel('mail')->error($e->getMessage());
        }

        if (count(Mail::failures()) !== 0) {
            Log::channel('mail')->error(Mail::failures()[0]);
        }
    }

    /**
     * Обработать провал задания.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed (Throwable $exception)
    {
        Log::channel('queue')->error($exception->getMessage());
    }
}
