<?php

namespace App\DataFixtures;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture {

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager) {
        $slugify = new Slugify();
        for ($i=0; $i < 10; $i++) { 
            $user[$i] = new User();
            $user[$i]
                ->setEmail('user'.$i.'@gmail.com')
                ->setUsername('User '.$i)
                ->setPassword($this->encoder->encodePassword($user[$i], 'dadadada'))
                ->setSlug($slugify->slugify('User '.$i))
            ;
            $manager->persist($user[$i]);
        }
        $manager->flush();
    }
}
