<?php

namespace App\Listeners;

use App\Events\SignupMail as EventsSignupMail;
use App\Mail\SignupMail as MailSignupMail;
use App\Traits\response;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SignupMail
{
    use response;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(EventsSignupMail $event)
    {
        // CREATING SPECIAL URI
        $url = URL::temporarySignedRoute('verification.notice', now()->addMinutes(60), [
            $event->userId,
        ]);
        
        /**  INSERTING THE URI IN  THE DB */
        !$user = DB::table('verify_email_blacklist')->insert(['userId' => $event->userId, 'link' => $url]);
        if (!$user) {
            return $this->error('Failed to send mail', ' ', 404);
        }

        /** Mailing the user*/
        Mail::to($event->userEmail)->send(new MailSignupMail ($event->userEmail, $url));
    }
}
