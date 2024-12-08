<?php

namespace App\Controller\Admin;

use App\Entity\Shop;
use App\Entity\User;
use App\Service\ShopContext;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

abstract class AdminCrudController extends AbstractCrudController
{
    private ShopContext $shopContext;

    public function __construct(ShopContext $shopContext)
    {
        $this->shopContext = $shopContext;
    }

    /**
     * Filtre les entités par le `shop` courant
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $entityManager = $this->container->get('doctrine')->getManagerForClass($entityDto->getFqcn());
        $shop = $this->getCurrentShopFromSessionOrUser($entityManager);

        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($shop) {
            $queryBuilder->andWhere('entity.shop = :shop')
                ->setParameter('shop', $shop);
        }

        return $queryBuilder;
    }

    /**
     * Persiste l'entité avec le shop courant lors de la création.
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Vérifie si l'entité a la méthode setShop (donc si elle est liée à un magasin)
        if (method_exists($entityInstance, 'setShop')) {
            $shop = $this->getCurrentShopFromSessionOrUser($entityManager);

            // Si un magasin a été trouvé, on l'associe à l'entité
            if ($shop) {
                $entityInstance->setShop($shop);
            } else {
                throw new \Exception("Shop not found for the current user.");
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    protected function getCurrentShopFromSessionOrUser(EntityManagerInterface $entityManager): ?Shop
    {
        // Récupère la session et le magasin courant
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $session = $request->getSession();
        $shopId = (int) $session->get('current_shop');

        $shopRepository = $entityManager->getRepository(Shop::class);

        // Si un shopId est trouvé dans la session, on récupère le magasin associé
        if ($shopId) {
            $shop = $shopRepository->find($shopId);
            if ($shop) {
                return $shop;
            }
        }

        // Si aucun shopId dans la session ou magasin non trouvé, on récupère le premier magasin de l'utilisateur
        $currentUser = $this->getUser();
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(["email" => $currentUser->getUserIdentifier()]);

        // Récupère les magasins de l'utilisateur
        $shops = $user->getShops();

        // Retourne le premier magasin de l'utilisateur s'il en possède
        return !empty($shops) ? $shops[0] : null;
    }
}
