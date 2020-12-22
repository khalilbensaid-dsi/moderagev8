<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\VarDumper\VarDumper;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Topic;
use App\Repository\TopicRepository;

use App\Entity\Message;
use App\Repository\MessageRepository;

use App\Form\NewTopicType;
use App\Form\NewMessageType;

use App\Service\UserFunctions;

class ForumController extends AbstractController
{
    /**
     * @Route("/forumAll", name="forumall")
     */
    public function indexall(TopicRepository $topicRepository,UserRepository $userRepository, MessageRepository $messageRepository)
    {

        $topics = $topicRepository->getTopicsData();


        $messages=$messageRepository->findAll();


        return $this->render('forum/index.html.twig', [
            'topics' => $topics,
            'messages'=>$messages,

        ]);
    }
    /**
     * @Route("/forum/{idSondage}", name="forum")
     */
    public function index(TopicRepository $topicRepository,UserRepository $userRepository, MessageRepository $messageRepository, $idSondage)
    {

        $topics = $topicRepository->getTopicsData();


$messages=$messageRepository->findAll();
        foreach ($topics as $key => $value) {
            $countMessage = $messageRepository->getCountMessage($topics[$key]['id']);
            $topics[$key]['countMessage'] = $countMessage;
            $lastMessage = $messageRepository->getLastMessage($topics[$key]['id']);
            $topics[$key]['lastMessage'] = $lastMessage;
            
        }

        return $this->render('forum/index.html.twig', [
            'topics' => $topics,
            'messages'=>$messages,
            'idSondage'=>$idSondage

        ]);
    }

 

    /**
     * @Route("/forum/newTopic", name="newTopic")
     */
    public function newTopic(Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(NewTopicType::class, ['role' => $this->getUser()->getRoles()  ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $topic = new Topic();
            $topic->setName($form->get('name')->getData());
            $topic->setAuthor($this->getUser()->getId());
            $topic->setCreationDate(date_create(date('Y-m-d')));
            

            $manager->persist($topic);
            $manager->flush();

            $message = new Message();
            $message->setIdTopic($topic->getId());
            $message->setIdUser($this->getUser()->getId());
            $message->setPublicationDate(date_create(date('Y-m-d H:i:s')));
            $message->setContent($form->get('content')->getData());
         

            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute('topic', ['id' => $topic->getId()]);
        }

        return $this->render('forum/newTopic.html.twig', [
            'form'  =>  $form->createView()
        ]);
    }

    /**
     * @Route("/forum/topic/{id}", name="topic")
     */
    public function topic($id,Topic $topic = null,TopicRepository $topicRepository, MessageRepository $messageRepository, UserFunctions $functions, Request $request, EntityManagerInterface $manager)
    {
        if (empty($topic)) {
            return $this->render('exceptions/404.html.twig', [
                'reason' => 'topic'
            ]);
        }
        else {
            $messages = $messageRepository->getMessages($topic->getId());
            foreach ($messages as $key => $value) {
                $messages[$key]['roles'] = $functions->roleStr(end($messages[$key]['roles']));
            }

            $form = $this->createForm(NewMessageType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                if ($form->get('Publier')->isClicked()){
                $message = new Message();
                $message->setIdTopic($topic->getId());
                $message->setIdUser($this->getUser()->getId());
                $message->setPublicationDate(date_create(date('Y-m-d H:i:s')));
                $message->setContent($form->get('content')->getData());
              

                $manager->persist($message);
                $manager->flush();
            //actualiser
                return $this->redirectToRoute('topic', ['id' => $topic->getId()]);

                }
               
            }

            
            return $this->render('forum/topic.html.twig', [
                'topic' => $topic,
                'messages' => $messages,
                'form'  =>  $form->createView()
            ]);
        }
    }

    /**
     * @Route("/forum/editMessage/{id}", name="editMessage")
     */
    public function editMessage(Message $message, MessageRepository $messageRepository , Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(NewMessageType::class,$message);
        $form->handleRequest($request);
        $topic = $messageRepository->getTopicData($message->getIdTopic());

        if($form->isSubmitted() && $form->isValid()){
            $message->setContent($form->get('content')->getData());
          
            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute('topic', ['id' => $message->getIdTopic()]);
        }

        return $this->render('forum/editMessage.html.twig', [
            'topic' => $topic,
            'message' => $message,
            'form'  =>  $form->createView()
        ]);
    }


/**
     * @Route("/suppforum/{id}", name="suppforum")
     */
    public function supprofil($id,MessageRepository $messageRepository,Topic $topic, Request $request,TopicRepository $topicRepository , EntityManagerInterface $manager, UserFunctions $userFunctions)
    {

        $this->isGranted('Enqueteur');
            
            $x = $topicRepository->find($id);
            $manager->remove($x);
           $manager->flush();

            return $this->redirectToRoute('forum');
        





        }
      







}
