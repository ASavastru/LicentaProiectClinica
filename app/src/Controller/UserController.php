<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    #[Route(path: '/user', name: 'user_list', methods: ['GET'])]
    public function userList(): Response
    {
        return $this->render('app/user_list.html.twig', []);
    }

    #[Route(path: '/user/list', methods: ['POST'])]
    public function patientPractitionerPrevCheck(EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        // Get the currently logged-in user
        $currentUser = $security->getUser();

        // Check if the current user is a practitioner
        $isPractitioner = in_array('ROLE_PRACTITIONER', $currentUser->getRoles());

        // Get the practitioner's ID
        $practitionerId = $currentUser->getId();

        // Retrieve users who have had past appointments with the practitioner
        $query = $entityManager->createQuery(
            'SELECT u.id, u.firstName, u.lastName
        FROM App\Entity\User u
        JOIN App\Entity\Appointment a WITH u = a.patient
        WHERE a.practitioner = :practitioner
        AND a.date <= CURRENT_DATE()'
        );
        $query->setParameter('practitioner', $currentUser);
        $users = $query->getResult();

        // Return the list of users, the currently logged-in user, and the practitioner flag as a JSON response
        return new JsonResponse([
            'users' => $users,
            'currentUser' => [
                'id' => $currentUser->getId()
            ],
            'isPractitioner' => $isPractitioner,
        ]);
    }
}
