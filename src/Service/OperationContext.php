<?php

namespace App\Service;

use App\Entity\Operation;
use App\Repository\OperationRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class OperationContext
{

    public function __construct(
        private RequestStack $requestStack,
        private OperationRepository $operationRepository,
        private Security $security,
        private UserRepository $userRepository
    ) {
    }

    /**
     * Définit le magasin actuel dans la session.
     */
    public function setCurrentOperation(Operation $operation): void
    {
        $session = $this->requestStack->getSession();
        $session->set('current_operation', $operation);
    }

    /**
     * Récupère le magasin actuel à partir de la session ou sélectionne un magasin par défaut si aucun n'est défini.
     */
    public function getCurrentOperation(): Operation
    {
        $session = $this->requestStack->getSession();
        $operation = $session->get('current_operation');

        return $operation ?: $this->updateCurrentOperation();
    }

    public function updateCurrentOperation(): Operation
    {
        $currentUser = $this->security->getUser();
        $user = $this->userRepository->findOneBy(["email" => $currentUser->getUserIdentifier()]);
        $operations = $user->getOperations();

        if (empty($operations)) {
            throw new \Exception("Aucun magasin trouvé pour l'utilisateur");
        }

        $this->setCurrentOperation($operations[0]);
        return $operations[0];
    }
}
