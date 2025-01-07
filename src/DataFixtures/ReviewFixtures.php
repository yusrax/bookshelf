<?php

namespace App\DataFixtures;

use App\Entity\Review;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $review = new Review();
            $review->setReviewText("This is a review text for review $i.");
            $review->setRating(rand(1, 5));
            $review->setCreatedAt(new DateTimeImmutable());
            $review->setUpdatedAt(new DateTimeImmutable());

            // Set random user and book references
            $review->setUser($this->getReference('user_' . rand(1, 10)));
            $review->setBook($this->getReference('book_' . rand(1, 10)));

            $manager->persist($review);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            BookFixtures::class,
        ];
    }
}
