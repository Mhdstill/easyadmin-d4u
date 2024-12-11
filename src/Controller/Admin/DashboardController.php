<?php

namespace App\Controller\Admin;

use App\Entity\Operation;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private RequestStack $requestStack
    ) {}

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', ["custom_title" => "Dashboard"]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section();

        yield MenuItem::linkToDashboard('Dashboard', 'fa-regular fa-star');
        yield MenuItem::linkToCrud('User', 'fa-regular fa-user', User::class);
        yield MenuItem::linkToUrl('Github', 'fa-regular fa-code-branch', 'https://github.com');

        yield MenuItem::section('');
        yield MenuItem::subMenu('Sous-menu', 'fa-regular fa-bars')
            ->setSubItems([
                MenuItem::linkToUrl('Item 1', 'fa-regular fa-circle', '#'),
                MenuItem::linkToUrl('Item 2', 'fa-regular fa-circle', '#'),
                MenuItem::linkToUrl('Item 3', 'fa-regular fa-circle', '#'),
            ]);
    }
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/logo.svg" alt="Logo" style="max-height: 42px;">');
    }

    // src/Controller/Admin/DashboardController.php

    #[Route('/admin/switch-operation/{id}', name: 'admin_switch_operation')]
    public function switchOperation(Operation $operation, Request $request): Response
    {
        $this->requestStack->getSession()->set('current_operation', $operation);
        
        // Récupère l'URL précédente
        $referer = $request->headers->get('referer');
        
        // Si pas de referer, retourne à l'admin
        return $this->redirect($referer ?: $this->generateUrl('admin'));
    }
}
