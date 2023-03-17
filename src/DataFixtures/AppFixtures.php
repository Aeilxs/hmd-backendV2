<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Drug;
use App\Entity\Food;
use App\Entity\Hydration;
use App\Entity\Sleep;
use App\Entity\Smoke;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    private const DRUGS_NAME = ['doliprane', 'efferalgan', 'aspirine', 'lysopaïne', 'levothyrox'];
    private const DRUGS_UNIT = ['cuillères', 'cachets', 'grammes'];

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));

        $user = new User();
        $user
            ->setEmail('john.doe@gmail.com')
            ->setPassword($this->hasher->hashPassword($user, 'user'))
            ->setRoles(['ROLE_USER'])
            ->setFirstname('john')
            ->setLastname('doe')
            ->setGender('homme')
            ->setSize(158)
            ->setWeight(57)
            ->setDateOfBirth(new DateTime('02-09-1994'));

        $manager->persist($user);

        for ($i = 0; $i < 30; $i++) {
            $fixtures = [
                $activity = new Activity(),
                $drug = new Drug(),
                $food = new Food(),
                $hydration = new Hydration(),
                $sleep = new Sleep(),
                $smoke = new Smoke(),
            ];

            $activity
                ->setType(Activity::TYPE_ACTIVTIES[mt_rand(0, count(Activity::TYPE_ACTIVTIES) - 1)])
                ->setDuration(mt_rand(0, 180))
                ->setIntensity(mt_rand(1, 3));

            $drug
                ->setName(AppFixtures::DRUGS_NAME[mt_rand(0, count(AppFixtures::DRUGS_NAME) - 1)])
                ->setUnit(AppFixtures::DRUGS_UNIT[mt_rand(0, count(AppFixtures::DRUGS_UNIT) - 1)])
                ->setQuantity(mt_rand(1, 5));

            $food
                ->setCaloricIntake(mt_rand(1800, 3000))
                ->setName($faker->foodName());

            $hydration
                ->setQuantity(mt_rand(1, 3));

            $sleep
                ->setDuration(mt_rand(5, 12))
                ->setQuality(mt_rand(1, 3));

            $smoke->setQuantity(mt_rand(5, 20));



            foreach ($fixtures as $fixture) {
                $fixture
                    ->setUser($user)
                    ->setDate($faker->dateTimeBetween('-4 month', 'now'));
                $manager->persist($fixture);
            }
        }

        $manager->flush();
    }
}
