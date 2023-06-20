<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\User;
use App\Repository\UserRepository;
use ContainerJkNv83b\getUserControllerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Date;

class AppointmentController extends AbstractController
{
    #[Route(path: '/appointment', name: 'appointment_list', methods: ['GET'])]
    public function appointmentList(EntityManagerInterface $entityManager): Response
    {
        $patients = $entityManager->getRepository(User::class)->findBy([
            "behaviour" => "PATIENT",
        ]);
        return $this->render('app/appointment_list.html.twig', [
            "patients" => $patients
        ]);
    }

    #[Route(path: '/appointment/checkDate', methods: ['POST'])]
    public function appointmentCheckDate(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $practitionerId = $request->request->get("practitionerId"); // Get the practitioner ID from the request

        $appointments = $entityManager->getRepository(Appointment::class)->findBy([
            "date" => new \DateTime($request->request->get("dateToCheck")),
            "practitioner" => $entityManager->getRepository(User::class)->findOneBy(["id" => $practitionerId])
        ]);

        $timestart = [];
        $timeend = [];
        $practitionerfirstname = [];
        $practitionerlastname = [];
        $practitionerexamroom = [];
        $patientfirstname = [];
        $patientlastname = [];
        $cnp = [];
        $isInsured = [];
        $id = [];
        $count = 0;

        $currentUserRole = $security->getUser()->getRoles();

        if ($currentUserRole[0] == "ROLE_USER") {
            $userAppointments = $entityManager->getRepository(Appointment::class)->findBy([
                "date" => new \DateTime($request->request->get("dateToCheck")),
                "patient" => $security->getUser(),
                "practitioner" => $entityManager->getRepository(User::class)->findOneBy(["id" => $practitionerId])
            ]);
            if ($userAppointments != null) {
                foreach ($userAppointments as $key => $appointment) {
                    $timestart[$key] = $appointment->getTimeStart()->format("H");
                    $timeend[$key] = $appointment->getTimeEnd();
                    $practitionerfirstname[$key] = $appointment->getPractitioner()->getFirstName();
                    $practitionerlastname[$key] = $appointment->getPractitioner()->getLastName();
                    $practitionerexamroom[$key] = $appointment->getPractitioner()->getExamRoom();
                    $id[$key] = $appointment->getId();
                    $count = $key;
                }
            }

            return new JsonResponse([
                "currentUserRole" => $currentUserRole[0],
                "timestart" => $timestart,
                "timeend" => $timeend,
                "practitionerfirstname" => $practitionerfirstname,
                "practitionerlastname" => $practitionerlastname,
                "examRoom" => $practitionerexamroom,
                "id" => $id,
                "count" => $count,
                "status" => true
            ]);
        } elseif ($currentUserRole[0] == "ROLE_PRACTITIONER" || $currentUserRole[0] == "ROLE_SECRETARY") {
            if ($appointments != null) {
                foreach ($appointments as $key => $appointment) {
                    $timestart[$key] = $appointment->getTimeStart()->format("H");
                    $timeend[$key] = $appointment->getTimeEnd();
                    $patientfirstname[$key] = $appointment->getPatient()->getFirstName();
                    $patientlastname[$key] = $appointment->getPatient()->getLastName();
                    $isInsured[$key] = $appointment->getPatient()->isIsInsured();
                    $cnp[$key] = $appointment->getPatient()->getUniqueIdCode();
                    $id[$key] = $appointment->getId();
                    $count = $key;
                }
            }

            return new JsonResponse([
                "currentUserRole" => $currentUserRole[0],
                "timestart" => $timestart,
                "timeend" => $timeend,
                "patientfirstname" => $patientfirstname,
                "patientlastname" => $patientlastname,
                "isInsured" => $isInsured,
                "cnp" => $cnp,
                "id" => $id,
                "count" => $count,
                "status" => true
            ]);
        }

        return new JsonResponse([
            "currentUserRole" => $currentUserRole[0],
            "timestart" => $timestart,
            "timeend" => $timeend,
            "patientfirstname" => $patientfirstname,
            "patientlastname" => $patientlastname,
            "isInsured" => $isInsured,
            "cnp" => $cnp,
            "id" => $id,
            "count" => $count,
            "status" => true
        ]);
    }

    #[Route(path: '/appointment/secretary/list', name: 'appointment_list_secretary', methods: ['POST'])]
    public function appointmentListSecretary(): Response
    {
        return $this->render('app/appointment_list.html.twig', []);
    }

    #[Route('/appointments/practitioners', name: 'appointments_practitioners', methods: ['POST'])]
    public function getPractitioners(UserRepository $practitionerRepository, Security $security): JsonResponse
    {
        $currentUser = $security->getUser();
        $isPractitioner = in_array('ROLE_PRACTITIONER', $currentUser->getRoles());

        if (!$isPractitioner) {
            $practitioners = $practitionerRepository->findAll();

            $data = [];
            foreach ($practitioners as $practitioner) {
//            dd($practitioner->getRoles()[0]);
                $incrementer = 0;
                if ($practitioner->getRoles()[$incrementer] == "ROLE_PRACTITIONER") {
                    $data[] = [
                        'id' => $practitioner->getId(),
                        'firstName' => $practitioner->getFirstName(),
                        'lastName' => $practitioner->getLastName(),
                        'examRoom' => $practitioner->getExamRoom(),
                    ];
                    $incrementer++;
                }

            }

            return new JsonResponse($data);
        }
        return new JsonResponse('nope');
    }

    #[Route(path: '/appointment/create', name: 'appointment_create_process', methods: ['POST'])]
    public function appointmentCreateProcess(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy([
            "id" => intval($request->request->get("patientId"))
        ]);
        $appointment = new Appointment();
        $appointment->setPatient($user);//intval($request->request->get("patientId"))
        $appointment->setPractitioner($entityManager->getRepository(User::class)->findOneBy([
            "id" => intval($request->request->get("practitionerId"))
        ]));
        $date = new \DateTimeImmutable($request->request->get("year") . "-" . $request->request->get("month") . "-" . $request->request->get("day") . " " . $request->request->get("hour") . ":00:00");
        $dateEnd = new \DateTimeImmutable($request->request->get("year") . "-" . $request->request->get("month") . "-" . $request->request->get("day") . " " . ($request->request->get("hour") + 1) . ":00:00");
        $appointment->setTimeStart($date);
        $appointment->setTimeEnd($date);
        $appointment->setDate($date);
        $appointment->setIsCompensated($user->isIsInsured());
        $appointment->setPrice(150);
        $entityManager->persist($appointment);
        $entityManager->flush();

        return $this->render('app/appointment.html.twig', []);
    }

    #[Route(path: '/appointment/delete/{id}', name: 'appointment_delete', methods: ['POST'])]
    public function appointmentDelete(int $id, EntityManagerInterface $entityManager): Response
    {
        $appointment = $entityManager->getReference(Appointment::class, $id);
        $entityManager->remove($appointment);
        $entityManager->flush();
        return new JsonResponse([
            "status" => true
        ]);
    }
}