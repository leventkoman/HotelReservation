<?php

namespace App\Controller;

use App\Entity\Admin\Messages;
use App\Entity\Hotel;
use App\Entity\Setting;
use App\Form\Admin\MessagesType;
use App\Repository\Admin\CommentRepository;
use App\Repository\Admin\RoomRepository;
use App\Repository\HotelRepository;
use App\Repository\ImageRepository;
use App\Repository\SettingRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository,HotelRepository $hotelRepository): Response
    {
        $setting = $settingRepository->findAll();
        $slider = $hotelRepository->findBy([],['id'=>'DESC'],3);
        $hotels = $hotelRepository->findBy([],['id'=>'DESC'],4);
        $newhotels = $hotelRepository->findBy([],['id'=>'DESC'],10);

        $data = $settingRepository->findBy(['id'=>1]);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'data'=>$data,
            'setting'=>$setting,
            'slider'=>$slider,
            'hotels'=>$hotels,
            'newhotels'=>$newhotels,
        ]);
    }
    /**
     * @Route("/hotel/{id}", name="home_hotel_show", methods={"GET"})
     */
    public function show(Hotel $hotel,$id,RoomRepository $roomRepository,ImageRepository $imageRepository,CommentRepository $commentRepository,UserRepository $userRepository): Response
    {
        $images = $imageRepository->findBy(['hotel'=>$id]);
        $comments = $commentRepository->findBy(['hotelid'=>$id]);
        $users = $userRepository->findBy(['id'=>$id]);
        $comments=$commentRepository->findBy(['hotelid'=>$id, 'status'=>'True']);
        $rooms = $roomRepository->findBy(['hotelid'=>$id]);
        return $this->render('home/hotelshow.html.twig', [
            'hotel' => $hotel,
            'rooms' => $rooms,
            'images' => $images,
            'comments' => $comments,
            'users'=>$users,
        ]);
    }
    /**
     * @Route("/about", name="home_about")
     */
    public function aboutus(SettingRepository $settingRepository): Response
    {
        $setting = $settingRepository->findAll();
        return $this->render('home/aboutus.html.twig', [
            'setting' => $setting,
        ]);
    }
    /**
     * @Route("/contact", name="home_contact", methods={"GET","POST"})
     */
    public function contact(SettingRepository $settingRepository,Request $request): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');

        $setting=$settingRepository->findAll(); // Get setting data

        if ($form->isSubmitted() ) {
            if ($this->isCsrfTokenValid('comment',$submittedToken)){
                $entityManager = $this->getDoctrine()->getManager();
                $message->setStatus('New');
                $message->setIp($_SERVER['REMOTE_ADDR']);
                var_dump($message);
                exit();
                $entityManager->persist($message);
                $entityManager->flush();

                $this->addFlash('success','Mesajınız başarılı olarak alındı');
                //********** SEND EMAIL ***********************>>>>>>>>>>>>>>>
                $email = (new Email())
                    ->from($setting[0]->getSmtpemail())
                    ->to($form['email']->getData())
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    //->replyTo('fabien@example.com')
                    //->priority(Email::PRIORITY_HIGH)
                    ->subject('AllHoliday Your Request')
                    //->text('Simple Text')
                    ->html("Dear ". $form['name']->getData() ."<br>
                                 <p>We will evaluate your requests and contact you as soon as possible</p> 
                                 Thank You for your message<br> 
                                 =====================================================
                                 <br>".$setting[0]->getCompany()."  <br>
                                 Address : ".$setting[0]->getAddress()."<br>
                                 Phone   : ".$setting[0]->getPhone()."<br>"
                    );

                $transport = new GmailSmtpTransport($setting[0]->getSmtpemail(), $setting[0]->getSmtppassword());

                $mailer = new Mailer($transport);
                $mailer->send($email);


                //<<<<<<<<<<<<<<<<********** SEND EMAIL ***********************

                return $this->redirectToRoute('home_contact');
            }

        }

        $setting = $settingRepository->findAll();

        return $this->render('home/contact.html.twig', [
            'message' => $message,
            'setting' => $setting,
            'form' => $form->createView(),
        ]);

    }

}
