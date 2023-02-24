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
        
        // $message_errors = false;
        foreach ($data['mails_to'] as $mail_to) {
            SendReplacementRequestMailJob::dispatch($mail_to, $data)->onQueue('email');
            // try {
            //     Mail::to($mail_to)->send(new MailReplacementRequest($data));
            // } catch (\Exception $e) {
            //     $message_errors[] = $e->getMessage();
            //     return $message_errors;
            // } catch (\Swift_TransportException $e) {
            //     $message_errors[] = $e->getMessage();
            //     return $message_errors;
            // }
    
            // if (count(Mail::failures()) !== 0) {
            //     $message_errors = array_merge($message_errors, Mail::failures());
            // }
        }
        
        // return $message_errors;
    }

    

}
