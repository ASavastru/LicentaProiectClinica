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
    public function timekeepingList(): Response
    {
        return $this->render('app/timekeeping.html.twig');
    }

    #[Route(path: '/timekeeping/retrieve', name: 'timekeeping_retrieve', methods: ['GET'])]
    public function retrieveTimekeeping(EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUserId = $this->getUser()->getId();

        $timekeepingRecords = $entityManager->getRepository(Timekeeping::class)->findBy(
            ['practitioner' => $currentUserId],
            ['start' => 'DESC']
        );

        return $this->json(['timekeepingRecords' => $timekeepingRecords]);
    }


    #[Route(path: '/timekeeping/create', name: 'timekeeping_create', methods: ['POST'])]
    public function createTimekeeping(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workHours = $request->request->getInt('work_interval');

        if ($workHours < 1 || $workHours > 24) {
            return $this->json(['error' => 'Invalid work interval']);
        }

        $timezone = new \DateTimeZone('Europe/Bucharest');
        $start = new \DateTime('now', $timezone);
        $end = (clone $start)->add(new \DateInterval('PT' . $workHours . 'H'));

        $practitionerId = $this->getUser()->getId();
        $startDate = clone $start;
        $startDate->setTime(0, 0, 0);
        $endDate = clone $start;
        $endDate->setTime(23, 59, 59);

        $existingRecord = $entityManager->createQueryBuilder()
            ->select('t')
            ->from(Timekeeping::class, 't')
            ->where('t.practitioner = :practitionerId')
            ->andWhere('t.start >= :startDate')
            ->andWhere('t.start <= :endDate')
            ->setParameter('practitionerId', $practitionerId)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();

        if (!$existingRecord) {
            // Create a new Timekeeping entity
            $timekeeping = new Timekeeping();
            $timekeeping->setPractitioner($this->getUser());
            $timekeeping->setStart($start);
            $timekeeping->setEnd($end);

            // Persist the timekeeping record
            $entityManager->persist($timekeeping);
            $entityManager->flush();

            return $this->redirectToRoute('timekeeping_list');
        }

        return $this->json(['duplicateRecord' => true]);
    }

    #[Route(path: '/timekeeping/read', name: 'timekeeping_read', methods: ['GET'])]
    public function readTimekeepingRecords(EntityManagerInterface $entityManager): Response
    {
        $timekeepingRecords = $entityManager->getRepository(Timekeeping::class)->findBy([], ['start' => 'DESC']);

        return $this->json(['timekeepingRecords' => $timekeepingRecords]);
    }
}
