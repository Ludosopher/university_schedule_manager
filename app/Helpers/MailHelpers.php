<?php

namespace App\Helpers;

use App\Jobs\SendReplacementRequestMailJob;
use Log;


class MailHelpers
{
    public static function sendReplacementRequest(array $data): void 
    {
        foreach ($data['mails_to'] as $mail_to) {
            SendReplacementRequestMailJob::dispatch($mail_to, $data)->onQueue('email');
        }
           
    }

}
