<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\JobListing;
use App\Form\ApplicationType;
use App\Form\JobListingType;
use App\Repository\JobListingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/job-listing')]
class JobListingController extends AbstractController
{
    #[Route('/front', name: 'job_listing_front', methods: ['GET'])]
    public function front(JobListingRepository $jobListingRepository): Response
    {
        // Assuming "Status" is used to determine if a job is active
        $activeJobs = $jobListingRepository->findBy(['Status' => 'active']);

        return $this->render('job_listing/indexf.html.twig', [
            'active_jobs' => $activeJobs,
        ]);
    }
    #[Route('/', name: 'job_listing_index', methods: ['GET', 'POST'])]
    public function index(Request $request, JobListingRepository $jobListingRepository, EntityManagerInterface $entityManager): Response
    {
        $jobListing = new JobListing();
        $addForm = $this->createForm(JobListingType::class, $jobListing);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $entityManager->persist($jobListing);
            $entityManager->flush();

            return $this->redirectToRoute('job_listing_index');
        }

        $editForms = [];
        foreach ($jobListingRepository->findAll() as $job) {
            $editForms[$job->getId()] = $this->createForm(JobListingType::class, $job)->createView();
        }

        return $this->render('job_listing/index.html.twig', [
            'job_listings' => $jobListingRepository->findAll(),
            'add_form' => $addForm->createView(),
            'edit_forms' => $editForms,
        ]);
    }

    #[Route('/new', name: 'job_listing_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jobListing = new JobListing();
        $form = $this->createForm(JobListingType::class, $jobListing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jobListing);
            $entityManager->flush();

            return $this->redirectToRoute('job_listing_index');
        }

        return $this->render('job_listing/new.html.twig', [
            'job_listing' => $jobListing,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'job_listing_show', methods: ['GET'])]
    public function show(JobListing $jobListing): Response
    {
        return $this->render('job_listing/show.html.twig', [
            'job_listing' => $jobListing,
        ]);
    }

    #[Route('/{id}/edit', name: 'job_listing_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, JobListing $jobListing, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JobListingType::class, $jobListing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('job_listing_index');
        }

        return $this->render('job_listing/edit.html.twig', [
            'job_listing' => $jobListing,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'job_listing_delete', methods: ['POST'])]
    public function delete(Request $request, JobListing $jobListing, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jobListing->getId(), $request->request->get('_token'))) {
            $entityManager->remove($jobListing);
            $entityManager->flush();
        }

        return $this->redirectToRoute('job_listing_index');
    }

    #[Route('/{id}/apply', name: 'job_listing_apply', methods: ['GET', 'POST'])]
    public function apply(Request $request, JobListing $jobListing, EntityManagerInterface $entityManager): Response
    {
        $application = new Application();
        $application->setUser($this->getUser());
        $application->setJob($jobListing);

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($application);
            $entityManager->flush();

            $this->addFlash('success', 'Your application has been submitted.');
            return $this->redirectToRoute('job_listing_front');
        }

        return $this->render('job_listing/apply.html.twig', [
            'job' => $jobListing,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/applications', name: 'job_listing_applications', methods: ['GET'])]
    public function applications(JobListing $jobListing): Response
    {
        return $this->render('job_listing/applications.html.twig', [
            'job' => $jobListing,
            'applications' => $jobListing->getApplications(),
        ]);
    }

    #[Route('/application/{id}/accept', name: 'application_accept', methods: ['POST'])]
    public function accept(Application $application, EntityManagerInterface $entityManager): Response
    {
        $application->setStatus('accepted');
        $entityManager->flush();

        $this->addFlash('success', 'Application accepted.');
        return $this->redirectToRoute('job_listing_applications', ['id' => $application->getJob()->getId()]);
    }

    #[Route('/application/{id}/reject', name: 'application_reject', methods: ['POST'])]
    public function reject(Application $application, EntityManagerInterface $entityManager): Response
    {
        $application->setStatus('rejected');
        $entityManager->flush();

        $this->addFlash('success', 'Application rejected.');
        return $this->redirectToRoute('job_listing_applications', ['id' => $application->getJob()->getId()]);
    }
}