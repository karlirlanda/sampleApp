<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Helpers\EncryptDecrypt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateIdColumn
{
    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {

        $event->user->update(['_id' => EncryptDecrypt::encryptDecrypt($event->user->id, 'encrypt')]);
        
    }
}
