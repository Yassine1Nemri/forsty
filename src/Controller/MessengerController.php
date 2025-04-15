<?php

namespace App\Controller;

use App\Entity\MessengerMessage;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessengerMessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/messages')]
#[IsGranted('ROLE_USER')]
class MessengerController extends AbstractController
{
    #[Route('/', name: 'app_messenger_index', methods: ['GET'])]
    public function index(MessengerMessageRepository $messageRepository): Response
    {
        $conversations = $messageRepository->findUserConversations($this->getUser());

        return $this->render('messenger/index.html.twig', [
            'conversations' => $conversations,
            'unread_count' => $messageRepository->findUnreadMessagesCount($this->getUser()),
        ]);
    }

    #[Route('/conversation/{id}', name: 'app_messenger_conversation', methods: ['GET', 'POST'])]
    public function conversation(
        User $otherUser,
        Request $request,
        EntityManagerInterface $entityManager,
        MessengerMessageRepository $messageRepository
    ): Response {
        $currentUser = $this->getUser();

        // Mark all messages from this user as read
        $unreadMessages = $entityManager->createQueryBuilder()
            ->update(MessengerMessage::class, 'm')
            ->set('m.isRead', ':isRead')
            ->where('m.sender = :sender')
            ->andWhere('m.receiver = :receiver')
            ->andWhere('m.isRead = :notRead')
            ->setParameter('isRead', true)
            ->setParameter('sender', $otherUser)
            ->setParameter('receiver', $currentUser)
            ->setParameter('notRead', false)
            ->getQuery()
            ->execute();

        // Get conversation messages
        $messages = $messageRepository->findConversation($currentUser, $otherUser);

        // New message form
        $newMessage = new MessengerMessage();
        $form = $this->createForm(MessageType::class, $newMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newMessage->setSender($currentUser);
            $newMessage->setReceiver($otherUser);

            $entityManager->persist($newMessage);
            $entityManager->flush();

            $this->addFlash('success', 'Message sent!');
            return $this->redirectToRoute('app_messenger_conversation', ['id' => $otherUser->getId()]);
        }

        return $this->render('messenger/conversation.html.twig', [
            'messages' => $messages,
            'other_user' => $otherUser,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_messenger_new', methods: ['GET', 'POST'])]
    public function newMessage(
        User $receiver,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $message = new MessengerMessage();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($this->getUser());
            $message->setReceiver($receiver);

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Message sent!');
            return $this->redirectToRoute('app_messenger_conversation', ['id' => $receiver->getId()]);
        }

        return $this->render('messenger/new.html.twig', [
            'receiver' => $receiver,
            'form' => $form->createView(),
        ]);
    }
}
