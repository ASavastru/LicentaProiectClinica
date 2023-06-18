<?php

namespace App\Controller;

use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Date;

class AppointmentController extends AbstractController
{
    #[Route(path: '/appointment', name: 'appointment_list', methods: ['GET'])]
    public function appointmentList(): Response
    {
        return $this->render('app/appointment_list.html.twig', []);
    }

    #[Route(path: '/appointment/checkDate', methods: ['POST'])]
    public function appointmentCheckDate(Request $request, EntityManagerInterface $entityManager): Response
    {
        $appointments = $entityManager->getRepository(Appointment::class)->findBy([
            "date" => new \DateTime($request->request->get("dateToCheck"))
        ]);

        $timestart = [];
        $timeend = [];
        $patientfirstname = [];
        $patientlastname = [];
        $cnp = [];
        $isInsured = [];
        $count = 0;

        if($appointments!=null){
            foreach($appointments as $key => $appointment){
                $timestart[$key] = $appointment->getTimeStart()->format("H");
                $timeend[$key] = $appointment->getTimeEnd();
                $patientfirstname[$key] = $appointment->getPatient()->getFirstName();
                $patientlastname[$key] = $appointment->getPatient()->getLastName();
                $isInsured[$key] = $appointment->getPatient()->isIsInsured();
                $cnp[$key] = $appointment->getPatient()->getUniqueIdCode();
                $count = $key;
            }
        }

        return new JsonResponse([
            "timestart" => $timestart,
            "timeend" => $timeend,
            "patientfirstname" => $patientfirstname,
            "patientlastname" => $patientlastname,
            "isInsured" => $isInsured,
            "cnp" => $cnp,
            "count" => $count,
            "status" => true
        ]);
    }

    #[Route(path: '/appointment/create', name: 'appointment_create_render', methods: ['GET'])]
    public function appointmentCreateRender(): Response
    {
        return $this->render('app/appointment.html.twig', []);
    }

    #[Route(path: '/appointment/secretary', name: 'appointment_list_secretary', methods: ['GET'])]
    public function appointmentListSecretary(): Response
    {
        return $this->render('app/appointment.html.twig', []);
    }

    #[Route(path: '/appointment/create', name: 'appointment_create_process', methods: ['POST'])]
    public function appointmentCreateProcess(): Response
    {
        return $this->render('app/appointment.html.twig', []);
    }

    #[Route(path: '/appointment/read', name: 'appointment_read_render', methods: ['GET'])]
    public function appointmentReadRender(): Response
    {
        return $this->render('app/appointment.html.twig', []);
    }

    #[Route(path: '/appointment/read', name: 'appointment_read_process', methods: ['POST'])]
    public function appointmentReadProcess(): Response
    {
        return $this->render('app/appointment.html.twig', []);
    }

    #[Route(path: '/appointment/update', name: 'appointment_update_render', methods: ['GET'])]
    public function appointmentUpdateRender(): Response
    {
        return $this->render('app/appointment.html.twig', []);
    }

    #[Route(path: '/appointment/update', name: 'appointment_update_process', methods: ['POST'])]
    public function appointmentUpdateProcess(): Response
    {
        return $this->render('app/appointment.html.twig', []);
    }

    #[Route(path: '/appointment/delete', name: 'appointment_delete', methods: ['POST'])]
    public function appointmentDelete(): Response
    {
        return $this->render('app/appointment.html.twig', []);
    }



}
