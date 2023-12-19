<?php

namespace App\Firebase;

use App\Modules\Notification\Models\Notification;
use Kreait\Firebase\Database;

class FirebaseService
{
    protected Database $database;
    protected string $tableName;

    public function __construct()
    {
        $this->database = app('firebase.database');
        $this->tableName = 'notifications';
    }

    public function storeNotification(Notification $notification)
    {
        $this->database->getReference($this->tableName)->push($notification->toArray());
    }
}
