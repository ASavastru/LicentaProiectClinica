<?php

namespace App\Controller;

use App\Repository\LeaveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LeaveController extends AbstractController
{
    #[Route('/leave', name: 'leave', methods: ['GET'])]
    public function leave(): Response
    {
        return $this->render('app/leave.html.twig');
    }

    #[Route('/leave/create', name: 'leave_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $leave = new Leave();
        $form = $this->createForm(LeaveRequestType::class, $leave);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the practitioner for the leave request
            $leave->setPractitioner($this->getUser());

            // Persist the leave request
            $entityManager->persist($leave);
            $entityManager->flush();

            $this->addFlash('success', 'Leave request submitted successfully.');

            return $this->redirectToRoute('leave_list');
        }

        return $this->render('leave/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/leave/read', name: 'leave_read', methods: ['POST'])]
    public function read(LeaveRepository $leaveRepository): Response
    {
        $user = $this->getUser();

        // Fetch the leave requests for the current user
        $leaveRequests = $leaveRepository->findBy(['practitioner' => $user]);

        return $this->json(['leaveRequests' => $leaveRequests]);
    }
}