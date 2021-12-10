<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\Header;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // On redirige vers un Controller existant
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(OrderCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('La Boutique Francaise');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        // On crée un menu pour les users
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
        // On crée un menu pour les transporteurs
        yield MenuItem::linkToCrud('Orders', 'fa fa-shopping-cart', Order::class);
        // On crée un menu pour les catégories
        yield MenuItem::linkToCrud('Catégories', 'fa fa-list', Category::class);
        // On crée un menu pour les produits
        yield MenuItem::linkToCrud('Produits', 'fa fa-tag', Product::class);
        // On crée un menu pour les transporteurs
        yield MenuItem::linkToCrud('Carriers', 'fa fa-truck', Carrier::class);
        // On crée un menu pour les transporteurs
        yield MenuItem::linkToCrud('Header', 'fa fa-desktop', Header::class);
    }
}
