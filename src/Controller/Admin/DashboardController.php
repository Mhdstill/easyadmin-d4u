<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Entity\Operation;
use App\Entity\Opportunity;
use App\Entity\Project;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->redirect($this->generateUrl('admin', [
            'crudAction' => 'index',
            'crudControllerFqcn' => CustomerCrudController::class
        ]));
    }

    #[Route('/admin/contact', name: 'admin_contact')]
    public function contact(): Response
    {
        
        return $this->render('admin/contact.html.twig', [
            'title' => 'Contact'
        ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section()->setCssClass('hidden-md');

        yield MenuItem::linkToCrud('Clients', 'fa-regular fa-address-book', Customer::class);
        yield MenuItem::linkToCrud('Opportunités', 'fa-regular fa-handshake', Opportunity::class);
        yield MenuItem::linkToCrud('Projets', 'fa-regular fa-file-lines', Project::class);

        yield MenuItem::section('');
        //if($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_ADMIN_CLIENT')) {
        yield MenuItem::subMenu('Accès', 'fa-regular fa-lock')
            ->setSubItems([
                MenuItem::linkToCrud('Utilisateurs', 'fa-regular fa-users', User::class),
                MenuItem::linkToCrud('Opérations', 'fa-regular fa-user-shield', Operation::class),
            ]);
        //}
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CRM');
    }
}
