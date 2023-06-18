<?php

namespace App\Controller;

use App\Entity\Timekeeping;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Time;


class TimekeepingController extends AbstractController
{
    #[Route(path: '/timekeeping', name: 'timekeeping_list', methods: ['GET'])]
    public function timekeepingList(EntityManagerInterface $entityManager): Response
    {
        $timekeepingRecords = $entityManager->getRepository(Timekeeping::class)->findBy([], ['start' => 'DESC']);

        return $this->render('app/timekeeping.html.twig', [
            'timekeepingRecords' => $timekeepingRecords,
        ]);
    }

    #[Route(path: '/timekeeping/create', name: 'timekeeping_create', methods: ['POST'])]
    public function createTimekeeping(Request $request, EntityManagerInterface $entityManager): Response
    {
        $timezone = new \DateTimeZone('Europe/Bucharest');

        $start = new \DateTime('now', $timezone);
        $workInterval = $request->request->get('work_interval');
        $interval = new \DateInterval('PT' . $workInterval . 'H');

        $end = (clone $start)->add($interval);

        // Check if the end time rolls over to the next day
        if ($end->format('Y-m-d') !== $start->format('Y-m-d')) {
            $end = $end->setTime(23, 59, 59);
        }

        $practitionerId = $this->getUser()->getId();

        $existingRecord[] = $entityManager->getRepository(Timekeeping::class)->findAll([
            'practitioner' => $practitionerId,
            'start' => $start,
            'end' => $end,
        ]);
        
        if ($existingRecord[0] == []) {
            // Create a new Timekeeping entity
            $timekeeping = new Timekeeping();
            $timekeeping->setPractitioner($this->getUser());
            $timekeeping->setStart($start);
            $timekeeping->setEnd($end);

            // Persist the timekeeping record
            $entityManager->persist($timekeeping);
            $entityManager->flush();
        } else {
            // Return a JSON response indicating a duplicate record
            return $this->json(['duplicateRecord' => true]);
        }

        return $this->redirectToRoute('timekeeping_list');
    }

    #[Route(path: '/timekeeping/read', name: 'timekeeping_read', methods: ['GET'])]
    public function readTimekeepingRecords(EntityManagerInterface $entityManager): Response
    {
        $timekeepingRecords = $entityManager->getRepository(Timekeeping::class)->findBy([], ['start' => 'DESC']);

        return $this->json(['timekeepingRecords' => $timekeepingRecords]);
    }


    #[Route(path: '/timekeeping/{user_id}', name: 'timekeeping_user', methods: ['GET'])]
    public function timekeepingUser(): Response
    {
        return $this->render('app/timekeeping_list.html.twig', []);
    }

    #[Route(path: '/timekeeping/create', name: 'timekeeping_create_render', methods: ['GET'])]
    public function timekeepingCreateRender(): Response
    {
        return $this->render('app/timekeeping.html.twig', []);
    }

    #[Route(path: '/timekeeping/create', name: 'timekeeping_create_process', methods: ['POST'])]
    public function timekeepingCreateProcess(): Response
    {
        return $this->render('app/timekeeping.html.twig', []);
    }

    #[Route(path: '/timekeeping/read', name: 'timekeeping_read_render', methods: ['GET'])]
    public function timekeepingReadRender(): Response
    {
        return $this->render('app/timekeeping.html.twig', []);
    }

    #[Route(path: '/timekeeping/read', name: 'timekeeping_read_process', methods: ['POST'])]
    public function timekeepingReadProcess(): Response
    {
        return $this->render('app/timekeeping.html.twig', []);
    }

    #[Route(path: '/timekeeping/update', name: 'timekeeping_update_render', methods: ['GET'])]
    public function timekeepingUpdateRender(): Response
    {
        return $this->render('app/timekeeping.html.twig', []);
    }

    #[Route(path: '/timekeeping/update', name: 'timekeeping_update_process', methods: ['POST'])]
    public function timekeepingUpdateProcess(): Response
    {
        return $this->render('app/timekeeping.html.twig', []);
    }

    #[Route(path: '/timekeeping/delete', name: 'timekeeping_delete', methods: ['POST'])]
    public function timekeepingDelete(): Response
    {
        return $this->render('app/timekeeping.html.twig', []);
    }



}
