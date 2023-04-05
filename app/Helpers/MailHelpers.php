<?php

namespace App\Helpers;

use App\Jobs\SendReplacementRequestMailJob;


class MailHelpers
{
    public static function sendReplacementRequest($data) {
        
        foreach ($data['mails_to'] as $mail_to) {
            SendReplacementRequestMailJob::dispatch($mail_to, $data)->onQueue('email');
        }
           
    }

}
