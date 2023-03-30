<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\UserCreated;
use App\Helpers\EncryptDecrypt;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDO;

class UpdateIdColumn
{
    /**
     * Handle the event.
     */
    public function handle(UserCreated $event)
    {
        if ($event->user['e'] == 1) {

            User::where('id', $event->user->id)->update(['_id' => EncryptDecrypt::encryptDecrypt($event->user->id, 'encrypt')]);

            // $event->user->update(['_id' => EncryptDecrypt::encryptDecrypt($event->user->id, 'encrypt')]);

        } else {
            unset($event->user['e']);

            for ($i = 0; $i < count($event->user); $i++) {
                User::where('id', $event->user[$i])
                     ->update(['_id' => EncryptDecrypt::encryptDecrypt($event->user[$i], 'encrypt')]);
            }
        }
    }
}
