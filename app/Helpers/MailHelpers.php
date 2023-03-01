<?php

namespace App\Helpers;

use App\Jobs\SendReplacementRequestMail;
use App\Jobs\SendReplacementRequestMailJob;
use App\Lesson;
use App\Mail\MailReplacementRequest;
use Illuminate\Support\Facades\Mail;

class MailHelpers
{
    public static function sendReplacementRequest($data) {
        
        foreach ($data['mails_to'] as $mail_to) {
            SendReplacementRequestMailJob::dispatch($mail_to, $data)->onQueue('email');
        }
           
    }

}
