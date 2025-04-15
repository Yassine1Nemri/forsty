<?php

// src/Controller/CommentController.php (continued)
namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comments')]
class CommentController extends AbstractController
{
    #[Route('/{id}/delete', name: 'app_comment_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $postId = $comment->getPost()->getId();

        // Check if user is the author of the comment
        if ($comment->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'You can only delete your own comments!');
            return $this->redirectToRoute('app_post_show', ['id' => $postId]);
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Your comment has been deleted!');
        }

        return $this->redirectToRoute('app_post_show', ['id' => $postId]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $postId = $comment->getPost()->getId();

        // Check if user is the author of the comment
        if ($comment->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'You can only edit your own comments!');
            return $this->redirectToRoute('app_post_show', ['id' => $postId]);
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Your comment has been updated!');
            return $this->redirectToRoute('app_post_show', ['id' => $postId]);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }
}
