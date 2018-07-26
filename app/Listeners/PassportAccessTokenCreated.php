<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Token;

class PassportAccessTokenCreated
{
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
    public function handle(AccessTokenCreated $event)
    {
        $tokenIdArr = Token::where('id', '!=', $event->tokenId)
            ->where('user_id', $event->userId)
            ->pluck('id')->toArray();
        \DB::table('oauth_refresh_tokens')->whereIn('access_token_id', $tokenIdArr)
            ->delete();

        Token::where('id', '!=', $event->tokenId)
            ->where('user_id', $event->userId)
            ->delete();
    }
}
