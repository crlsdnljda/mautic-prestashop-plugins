<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'apiUrl',
            TextType::class,
            [
                'label'       => 'PrestaShop URL',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'       => 'form-control',
                    'placeholder' => 'https://your-prestashop-store.com',
                ],
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'URL is required']),
                    new Url(['message' => 'Please enter a valid URL']),
                ],
            ]
        );

        $builder->add(
            'apiKey',
            TextType::class,
            [
                'label'       => 'API Key (Webservice Key)',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'       => 'form-control',
                    'placeholder' => 'Your PrestaShop Webservice API Key',
                ],
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'API Key is required']),
                ],
            ]
        );

        $builder->add(
            'shopId',
            IntegerType::class,
            [
                'label'      => 'Shop ID',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'placeholder' => '1',
                ],
                'required'   => false,
                'data'       => 1,
            ]
        );

        $builder->add(
            'language',
            TextType::class,
            [
                'label'      => 'Language Code',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'placeholder' => 'es',
                ],
                'required'   => false,
                'data'       => 'es',
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'integration' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'integration_config';
    }
}
