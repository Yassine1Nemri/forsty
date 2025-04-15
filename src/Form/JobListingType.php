<?php

namespace App\Form;

use App\Entity\JobListing;
use App\Enum\JobType;
use App\Enum\ExperienceLevel;
use App\Enum\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ListingName', TextType::class, [
                'label' => 'Listing Name',
            ])
            ->add('Title', TextType::class, [
                'label' => 'Title',
            ])
            ->add('Location', TextType::class, [
                'label' => 'Location',
            ])
            ->add('JobType', ChoiceType::class, [
                'label' => 'Job Type',
                'choices' => array_combine(
                    array_map(fn($jobType) => $jobType->value, JobType::cases()),
                    JobType::cases()
                ),
                'choice_label' => function ($choice) {
                    return $choice->value;
                },
            ])
            ->add('SalaryRange', TextType::class, [
                'label' => 'Salary Range',
            ])
            ->add('Description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('ExperienceLevel', ChoiceType::class, [
                'label' => 'Experience Level',
                'choices' => array_combine(
                    array_map(fn($experienceLevel) => $experienceLevel->value, ExperienceLevel::cases()),
                    ExperienceLevel::cases()
                ),
                'choice_label' => function ($choice) {
                    return $choice->value;
                },
            ])
            ->add('Status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => array_combine(
                    array_map(fn($status) => $status->value, Status::cases()),
                    Status::cases()
                ),
                'choice_label' => function ($choice) {
                    return $choice->value;
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobListing::class,
        ]);
    }
}