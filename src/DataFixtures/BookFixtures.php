<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $book = new Book();
            $book->setTitle("Book Title $i");
            $book->setAuthor("Author $i");
            $book->setPages(rand(100, 500));
            $book->setGenre("Genre " . ($i % 5 + 1));
            $book->setSummary("This is a summary for book $i.");
            $book->setCoverImage("https://picsum.photos/200/300?random=$i");

            // Set createdAt and updatedAt
            $book->setCreatedAt(new \DateTimeImmutable());
            $book->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($book);

            // Save a reference for use in ReviewFixtures
            $this->addReference('book_' . $i, $book);
        }

        $manager->flush();
    }
}
