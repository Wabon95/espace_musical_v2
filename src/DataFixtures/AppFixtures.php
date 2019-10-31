<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Event;
use App\Utils\MyFunctions;
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
        $myFunctions = new MyFunctions();
        // Users
        $slugify = new Slugify();
        for ($i=0; $i < 10; $i++) { 
            $users[$i] = new User();
            $users[$i]
                ->setEmail('user'.$i.'@gmail.com')
                ->setUsername('User '.$i)
                ->setPassword($this->encoder->encodePassword($users[$i], 'dadadada'))
                ->setSlug($slugify->slugify('User '.$i))
            ;
            $manager->persist($users[$i]);
        }

        // Ads
        $instruments = ['Guitare', 'Piano', 'Triangle', 'Saxophone', 'Batterie', 'Basse', 'Violon'];
        for ($i=0; $i < 20; $i++) { 
            shuffle($instruments);
            shuffle($users);
            $ads[$i] = new Ad();
            $ads[$i]
                ->setTitle('Je vends un/une '.$instruments[0])
                ->setContent('Je vends ce/cette '.$instruments[0].' qui est quasi neuf/neuve et n\'a presque jamais servi(e).')
                ->setType('Vente')
                ->setPrice(mt_rand(50, 400))
                ->setAuthor($users[0])
                ->setSlug($slugify->slugify('Je vends un/une '.$instruments[0]))
            ;
            $manager->persist($ads[$i]);
        }

        // Events
        $artists = ['Lady Gaga', 'Nightwish', 'Epica', 'Beast In Black', 'Xandria', 'Sirenia', 'Adele', 'Icon For Hire', 'Sia', 'Queen'];
        for ($i=0; $i < 5; $i++) {
            shuffle($users);
            shuffle($artists);
            $events[$i] = new Event();
            $events[$i]
                ->setTitle('Concert de '.$artists[0].' !')
                ->setSlug($slugify->slugify('Concert de '.$artists[0].' !'))
                ->setType('Concert')
                ->setDescription('Venez assister à la tournée d\'adieu de '.$artists[0].' et revivre cette expérience unique une dernière fois !')
                ->setStartDate($myFunctions->timestampToDatetime('1570390200'))
                ->setEndDate($myFunctions->timestampToDatetime('1570401000'))
                ->setLocation('L\'Olympia')
                ->setArtists($artists[1].', '.$artists[2].', '.$artists[0])
                ->setPrice(mt_rand(30, 100))
                ->setAuthor($users[0])
            ;
            $manager->persist($events[$i]);
        }
        $manager->flush();
    }
}
