<?php
namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Operation;
use App\Entity\Opportunity;
use App\Entity\Project;
use App\Entity\User;
use App\Enum\CustomerStatus;
use App\Enum\OpportunityStatus;
use App\Enum\ProjectStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $faker;

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // Création de l'admin
        $admin = new User();
        $admin->setEmail("demo@gmail.com");
        $admin->setPassword($this->hasher->hashPassword($admin, 'test'));
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setFirstName($this->faker->firstName());
        $admin->setLastName($this->faker->lastName());
        $admin->setJob('Administrateur');
        $manager->persist($admin);

        // Création des 3 opérations
        $operations = [];
        for ($i = 0; $i < 3; $i++) {
            $operation = new Operation();
            $operation->setName("Opération " . ($i + 1));
            $manager->persist($operation);
            $operations[] = $operation;
            $admin->addOperation($operation);
        }

        // Création des 15 utilisateurs
        $users = [];
        for ($i = 0; $i < 15; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email());
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $user->setRoles(["ROLE_CLIENT"]);
            $user->setFirstName($this->faker->firstName());
            $user->setLastName($this->faker->lastName());
            $user->setJob($this->faker->jobTitle());
            // Attribuer une opération aléatoire
            $user->addOperation($operations[0]);
            $manager->persist($user);
            $users[] = $user;
        }

        // Création des 15 clients
        $customers = [];
        for ($i = 0; $i < 15; $i++) {
            $customer = new Customer();
            $customer->setFirstname($this->faker->firstName());
            $customer->setLastname($this->faker->lastName());
            $customer->setEmail($this->faker->email());
            $customer->setPhone($this->faker->phoneNumber());
            $customer->setAddress($this->faker->address());
            $customer->setIsBusiness((bool)random_int(0, 1));
            $customer->setAcquisitionDate($this->faker->dateTimeBetween('-2 years', 'now'));
            $customer->setStatus(CustomerStatus::cases()[array_rand(CustomerStatus::cases())]);
            $customer->setOperation($operations[0]);
            $manager->persist($customer);
            $customers[] = $customer;
        }

        // Création des 5 opportunités
        for ($i = 0; $i < 5; $i++) {
            $opportunity = new Opportunity();
            $opportunity->setTitle("Opportunité " . ($i + 1));
            $opportunity->setDescription($this->faker->paragraph());
            $opportunity->setAmount($this->faker->randomFloat(2, 1000, 50000));
            $opportunity->setExpectedClosingDate($this->faker->dateTimeBetween('now', '+6 months'));
            $opportunity->setStatus(OpportunityStatus::cases()[array_rand(OpportunityStatus::cases())]);
            $opportunity->setCustomer($customers[array_rand($customers)]);
            $opportunity->setOperation($operations[0]);
            $manager->persist($opportunity);
        }

        // Création des 5 projets
        for ($i = 0; $i < 5; $i++) {
            $project = new Project();
            $project->setName("Projet " . ($i + 1));
            $project->setDescription($this->faker->paragraph());
            $project->setBudget($this->faker->randomFloat(2, 5000, 100000));
            $project->setStartDate($this->faker->dateTimeBetween('-1 month', 'now'));
            $project->setEndDate($this->faker->dateTimeBetween('+1 month', '+1 year'));
            $project->setStatus(ProjectStatus::cases()[array_rand(ProjectStatus::cases())]);
            $project->setCustomer($customers[array_rand($customers)]);
            $project->setOperation($operations[0]);
            $manager->persist($project);
        }

        $manager->flush();
    }
}
