<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'landing', methods: ['GET'])]
    public function landing(EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()){
            return $this->redirectToRoute('home');
        }
        return $this->redirectToRoute('app_login');
    }

    #[Route(path: '/home', name: 'home', methods: ['GET'])]
    public function home(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('app/home.html.twig');
    }

}
