<?php

namespace App\Controller\Admin;

use App\Entity\Operation;
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
        return $this->render('admin/index.html.twig', ["title" => "Dashboard"]);
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

        yield MenuItem::linkToDashboard('Dashboard', 'fa-regular fa-star');
        yield MenuItem::linkToUrl('Github', 'fa-regular fa-code-branch', 'https://github.com');

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
