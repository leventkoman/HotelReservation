<?php

namespace App\Controller;

use App\Entity\Admin\Comment;
use App\Entity\Admin\Rezervation;
use App\Entity\User;
use App\Form\Admin\CommentType;
use App\Form\Admin\RezervationType;
use App\Form\UserType;
use App\Repository\Admin\CommentRepository;
use App\Repository\Admin\RezervationRepository;
use App\Repository\Admin\RoomRepository;
use App\Repository\HotelRepository;
use App\Repository\ImageRepository;
use App\Repository\SettingRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, SettingRepository $settingRepository): Response
    {
        $setting = $settingRepository->findAll();
        return $this->render('user/show.html.twig',[
            'setting'=>$setting,
        ]);
    }
    /**
     * @Route("/comments", name="user_comments", methods={"GET"})
     */
    public function comments(CommentRepository $commentRepository): Response
    {
        //$user = new User();
        $user = $this->getUser();       //$user = new User(); iki satırda aynı
        $comments = $commentRepository->getAllCommentsUser($user->getId());
        return $this->render('user/comments.html.twig',[
            'comments'=>$comments,
        ]);
    }
    /**
     * @Route("/hotels", name="user_hotels", methods={"GET"})
     */
    public function hotels(HotelRepository $hotelRepository): Response
    {
        $user = $this->getUser(); //Get login user data
        return $this->render('user/hotels.html.twig',[
            'hotels'=>$hotelRepository->findBy(['userid'=>$user->getId()]),
        ]);
    }
    /**
     * @Route("/rezervations", name="user_rezervations", methods={"GET"})
     */
    public function rezervations(RezervationRepository $rezervationRepository): Response
    {
        $user = $this->getUser(); // Get login User data
        $rezervations =$rezervationRepository->getUserrezervation($user->getId());
        return $this->render('user/rezervations.html.twig', [
            'rezervations' =>$rezervations,
        ]);
    }
    /**
     * @Route("/rezervation/{id}", name="user_rezervation_show", methods={"GET"})
     */
    public function rezervationshow($id,RezervationRepository $rezervationRepository): Response
    {
        $user = $this->getUser(); // Get login User data
        // $rezervation=$rezervationRepository->findBy(['userid'=>$user->getId()]);
        $rezervation=$rezervationRepository->getrezervation($id);
        // dump($rezervations);
        // die();
        return $this->render('user/rezervation_show.html.twig', [
            'rezervation' =>$rezervation,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            //************** file upload ***>>>>>>>>>>>>
            /** @var file $file */
            $file = $form['image']->getData();
            if ($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'), // in Servis.yaml defined folder for upload images
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $user->setImage($fileName); // Related upload file name with Hotel table image field
            }
            //<<<<<<<<<<<<<<<<<******** file upload ***********>

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );


            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/newcomment/{id}", name="user_new_comment", methods={"GET","POST"})
     */
    public function newcomment(Request $request,$id): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');

        if ($form->isSubmitted()) {
            if ($this->isCsrfTokenValid('comment', $submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();

                $comment->setStatus('New');
                $comment->setIp($_SERVER['REMOTE_ADDR']);
                $comment->setHotelid($id);
                $user = $this->getUser();
                $comment->setUserid($user->getId());

                $entityManager->persist($comment);
                $entityManager->flush();

                $this->addFlash('success', 'Your comment has been sent successfuly');
                return $this->redirectToRoute('userhotel_show', ['id' => $id]);
            }
        }

        return $this->redirectToRoute('userhotel_show', ['id'=> $id]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,$id,UserPasswordEncoderInterface $passwordEncoder, User $user): Response
    {
        $user = $this->getUser();
        if($user->getId() != $id )
        {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //************** file upload ***>>>>>>>>>>>>
            /** @var file $file */
            $file = $form['image']->getData();
            if ($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'), // in Servis.yaml defined folder for upload images
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $user->setImage($fileName); // Related upload file name with Hotel table image field
            }
            //<<<<<<<<<<<<<<<<<******** file upload ***********>
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    public function generateUniqueFileName()
    {
        return md5(uniqid());
    }

    /**
     * @Route("/rezervation/{hid}/{rid}", name="user_rezervation_new", methods={"GET","POST"})
     */
    public function newRezervation(Request $request,$hid,$rid,RoomRepository $roomRepository,HotelRepository $hotelRepository): Response
    {

        $hotel = $hotelRepository->findOneBy(['id'=>$hid]);
        $room = $roomRepository->findOneBy(['id'=>$rid]);

        $days = $_REQUEST["days"];
        $checkin=$_REQUEST["checkin"];
        $checkout= Date("Y-m-d H:i:s", strtotime($checkin ." $days Day")); // Adding days to date
        $checkin= Date("Y-m-d H:i:s", strtotime($checkin ." 0 Day"));
        //$checkin=$_REQUEST["checkin"];

        $data["total"] = $days * $room->getPrice();
        $data["days"] = $days;
        $data["checkin"] =$checkin;
        $data["checkout"] =$checkout;

        $rezervation = new Rezervation();
        $form = $this->createForm(RezervationType::class, $rezervation);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');

        if ($form->isSubmitted()) {
            if ($this->isCsrfTokenValid('form-rezervation', $submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();

                $checkin=date_create_from_format("Y-m-d H:i:s",$checkin); //Convert to datetime format
                $checkout=date_create_from_format("Y-m-d H:i:s",$checkout); //Convert to datetime format

                $rezervation->setCheckin($checkin);
                $rezervation->setCheckout($checkout);
                $rezervation->setStatus('New');
                $rezervation->setIp($_SERVER['REMOTE_ADDR']);
                $rezervation->setHotelid($hid);
                $rezervation->setRoomid($rid);
                $user = $this->getUser(); // Get login User data
                $rezervation->setUserid($user->getId());
                $rezervation->setDays($days);
                $rezervation->setTotal($data["total"]);
                $rezervation->setCreatedAt(new \DateTime()); // Get now datatime

                $entityManager->persist($rezervation);
                $entityManager->flush();


                return $this->redirectToRoute('user_rezervations');
            }
        }

        return $this->render('user/newrezervation.html.twig', [
            'rezervation' => $rezervation,
            'room' => $room,
            'hotel' => $hotel,
            'data' => $data,
            'form' => $form->createView(),
        ]);
    }
}
