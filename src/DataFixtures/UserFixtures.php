<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user$i@example.com");
            $user->setRoles(['ROLE_USER']);
            $user->setPassword('password'); // Assign plain text password

            // Assign a random profile picture URL
            $profilePictureUrl = "https://picsum.photos/200/200?random=$i";
            $user->setProfilePicture($profilePictureUrl);

            // Assign first name and last name
            $user->setFirstName("FirstName$i");
            $user->setLastName("LastName$i");

            // Generate a unique username
            $user->setUsername("user$i");

            $manager->persist($user);

            // Save a reference for use in other fixtures
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}
