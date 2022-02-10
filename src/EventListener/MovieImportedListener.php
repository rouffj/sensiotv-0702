<?php

namespace App\EventListener;

use App\Entity\Movie;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class MovieImportedListener
{
    public function postPersist(Movie $movie, LifecycleEventArgs $event): void
    {
        /** @var Movie */
        //$movie = $event->getObject();

        $email = [
            'to' => 'members@sensiotv.io',
            'title' => 'Un nouveau film ' . $movie->getTitle(). ' vient d\'arriver sur la plateforme',
        ];
        dump($email);

        // Put your logic here before or after a DB change occured inside an entity like:
        // - Log column change
        // - Send a notification / email each time an entity is added into DB
        // - ...
    }

    public function postUpdate(Movie $movie, LifecycleEventArgs $event): void
    {
        dump('update', $movie);
    }
}