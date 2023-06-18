<?php

namespace App\Controller;

use App\Repository\LeaveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LeaveController extends AbstractController
{
    #[Route('/leave', name: 'leave_list', methods: ['GET'])]
    public function index(LeaveRepository $leaveRepository): Response
    {
        // Fetch the approved and unapproved leave requests for the current user
        $user = $this->getUser();
        $approvedRequests = $leaveRepository->findApprovedByUser($user);
        $unapprovedRequests = $leaveRepository->findUnapprovedByUser($user);

        return $this->render('leave/index.html.twig', [
            'approvedRequests' => $approvedRequests,
            'unapprovedRequests' => $unapprovedRequests,
        ]);
    }

    #[Route(path: '/leave/{user_id}', name: 'leave_user', methods: ['GET'])]
    public function leaveUser(): Response
    {
        return $this->render('app/leave_list.html.twig', []);
    }

    #[Route('/leave/create', name: 'leave_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $leave = new Leave();
        $form = $this->createForm(LeaveRequestType::class, $leave);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the user and status for the leave request
            $leave->setUser($this->getUser());
            $leave->setStatus(Leave::STATUS_PENDING);

            // Persist the leave request
            $entityManager->persist($leave);
            $entityManager->flush();

            $this->addFlash('success', 'Leave request submitted successfully.');

            return $this->redirectToRoute('leave');
        }

        return $this->render('leave/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/leave/read', name: 'leave_read', methods: ['POST'])]
    public function read(LeaveRepository $leaveRepository): Response
    {
        $user = $this->getUser();

        // Fetch the pending leave requests for the current user
        $pendingRequests = $leaveRepository->findPendingByUser($user);

        return $this->json(['pendingRequests' => $pendingRequests]);
    }

    #[Route(path: '/leave/read', name: 'leave_read_render', methods: ['GET'])]
    public function leaveReadRender(): Response
    {
        return $this->render('app/leave.html.twig', []);
    }

    #[Route(path: '/leave/read', name: 'leave_read_process', methods: ['POST'])]
    public function leaveReadProcess(): Response
    {
        return $this->render('app/leave.html.twig', []);
    }

    #[Route(path: '/leave/update', name: 'leave_update_render', methods: ['GET'])]
    public function leaveUpdateRender(): Response
    {
        return $this->render('app/leave.html.twig', []);
    }

    #[Route(path: '/leave/update', name: 'leave_update_process', methods: ['POST'])]
    public function leaveUpdateProcess(): Response
    {
        return $this->render('app/leave.html.twig', []);
    }

    #[Route(path: '/leave/delete', name: 'leave_delete', methods: ['POST'])]
    public function leaveDelete(): Response
    {
        return $this->render('app/leave.html.twig', []);
    }



}
