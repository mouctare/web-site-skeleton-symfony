<?php

namespace App\DataFixtures;


use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
 /**
 * L'encoder de mot de passe
 *
 * @var UserPasswordEncoderInterface
 */
private $encoder; 

public function __construct(UserPasswordEncoderInterface $encoder)
{
    $this->encoder = $encoder;

}

public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR'); // Pour générer que des noms français
     
        for ($u = 0; $u < 10; $u++) {
            $user = new User();
             $hash = $this->encoder->encodePassword($user, "password");
            // L'entité pour laquelle on cherche à encoder , c'est un user
                 
            $user->setEmail($faker->email)
                ->setPassword($hash);
              $manager->persist($user);
             $manager->flush();
        }
    } 
}
