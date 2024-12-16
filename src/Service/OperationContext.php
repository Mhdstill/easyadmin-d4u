<?php

namespace App\Service;

use App\Entity\Operation;
use App\Repository\OperationRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class OperationContext
{
    private $session;

    public function __construct(
        private RequestStack $requestStack,
        private OperationRepository $operationRepository,
        private Security $security,
        private UserRepository $userRepository
    ) {
        $this->session = $requestStack->getSession();
    }

    /**
     * Définit le magasin actuel dans la session.
     */
    public function setCurrentOperation($operationId): void
    {
        $this->session->set('current_operation', $operationId);
    }

    /**
     * Récupère le magasin actuel à partir de la session ou sélectionne un magasin par défaut si aucun n'est défini.
     */
    public function getCurrentOperation(): Operation
    {
        // Récupère le magasin dans la session
        $operationId = $this->session->get('current_operation');

        // Si un magasin est déjà défini, le retourne
        if ($operationId) {
            return $this->operationRepository->find($operationId);
        }

        return $this->updateCurrentOperation();
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
