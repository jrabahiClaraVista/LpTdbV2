<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class KpiFilterType extends AbstractType
{
    private $em;
    private $user;
    private $user_actuel;
    private $week;
    private $month;
    private $year;
    private $scope;

    public function __construct(EntityManager $em, User $user_actuel, User $user, $week, $month, $year, $scope){
        $this->em = $em;
        $this->user = $user;
        $this->user_actuel = $user_actuel;
        $this->month = $month;
        $this->year = $year;
        $this->week = $week;
        $this->scope = $scope;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('year', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' => array(
              '2015'   => '2015',
              '2016'   => '2016',
              '2017'   => '2017',
              '2018'   => '2018',
              ),
            'choices_as_values' => true,
            'required' => false,
            'data' => '2017',
            'empty_value' => false,
            )
        )
        ->add('reseau', 'entity', array(
            'class' => 'ApplicationSonataUserBundle:User',
            'property' => 'username',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('u')
                ->where(' (u.role = :role)')
                ->setParameter('role', 'ROLE_MARQUE')
                ->add('orderBy','u.role DESC ,u.username ASC')
                ;
            },
            'empty_value' => false,
            'required' => false
            //,'data' => $this->em->getReference("ApplicationSonataUserBundle:User", null)
            )
        )
        ->add('submit', 'submit')
        ;


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            //var_dump($form);

            // Configuration des mois à afficher
            $form->add('year', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => array(
                    '2015'   => '2015',
                    '2016'   => '2016',
                    '2017'   => '2017',
                    '2018'   => '2018',
                    ),
                'choices_as_values' => true,
                'required' => false,
                'data' => $this->year,
                'empty_value' => false,
                )
            );

            if($this->scope == 'annuel'){
            //Cheat pour détourner le bug du ChoiceType Expended, on récupere la valeur de ChoiceType ici

                if($this->month != null and $this->week == null){
                    $form->add('month', 'hidden', array(
                        'required' => true,
                        )
                    );
                    $form->add('week', 'hidden', array(
                        'required' => false,
                        )
                    );

              // Configuration des mois à afficher
                    $form->add('month2', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                        'choices' => array(
                            'Janvier'     => '01',
                            'Février'     => '02',
                            'Mars'        => '03',
                            'Avril'       => '04',
                            'Mai'         => '05',
                            'Juin'        => '06',
                            'Juillet'     => '07',
                            'Août'        => '08',
                            'Septembre'   => '09',
                            'Octobre'     => '10',
                            'Novembre'    => '11',
                            'Décembre'    => '12',
                            ),
                        'choices_as_values' => true,
                        'required' => false,
                        'data' => $this->month,
                        'empty_value' => false,
                        'expanded' => true,
                        'multiple' => false,
                        )
                    );
                }

            }
            elseif($this->scope == 'mensuel'){
                        // Configuration des mois à afficher
                $form->add('month', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                    'choices' => array(
                        'Janvier'     => '01',
                        'Février'     => '02',
                        'Mars'        => '03',
                        'Avril'       => '04',
                        'Mai'         => '05',
                        'Juin'        => '06',
                        'Juillet'     => '07',
                        'Août'        => '08',
                        'Septembre'   => '09',
                        'Octobre'     => '10',
                        'Novembre'    => '11',
                        'Décembre'    => '12',
                        ),
                    'choices_as_values' => true,
                    'required' => false,
                    'data' => $this->month,
                    'empty_value' => false,
                    'expanded' => false,
                    'multiple' => false,
                    )
                );
                $form->add('week', 'hidden', array(
                  'required' => false,
                  )
                );
            }
            elseif($this->scope == 'hebdomadaire'){
                $date_start = new \DateTime();
                $form->add('month', 'hidden', array(
                  'required' => false,
                  )

                );
                $form->add('week', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                    'choices' => array(
                        $date_start->setISODate(intval($this->year), 1)->format("d/m/Y")."  - Semaine 1"  => 1,
                        $date_start->setISODate(intval($this->year), 2)->format("d/m/Y")."  - Semaine 2"  => 2,
                        $date_start->setISODate(intval($this->year), 3)->format("d/m/Y")."  - Semaine 3"  => 3,
                        $date_start->setISODate(intval($this->year), 4)->format("d/m/Y")."  - Semaine 4"  => 4,
                        $date_start->setISODate(intval($this->year), 5)->format("d/m/Y")."  - Semaine 5"  => 5,
                        $date_start->setISODate(intval($this->year), 6)->format("d/m/Y")."  - Semaine 6"  => 6,
                        $date_start->setISODate(intval($this->year), 7)->format("d/m/Y")."  - Semaine 7"  => 7,
                        $date_start->setISODate(intval($this->year), 8)->format("d/m/Y")."  - Semaine 8"  => 8,
                        $date_start->setISODate(intval($this->year), 9)->format("d/m/Y")."  - Semaine 9"  => 9,
                        $date_start->setISODate(intval($this->year), 10)->format("d/m/Y")." - Semaine 10"  => 10,
                        $date_start->setISODate(intval($this->year), 11)->format("d/m/Y")." - Semaine 11"  => 11,
                        $date_start->setISODate(intval($this->year), 12)->format("d/m/Y")." - Semaine 12"  => 12,
                        $date_start->setISODate(intval($this->year), 13)->format("d/m/Y")." - Semaine 13"  => 13,
                        $date_start->setISODate(intval($this->year), 14)->format("d/m/Y")." - Semaine 14"  => 14,
                        $date_start->setISODate(intval($this->year), 15)->format("d/m/Y")." - Semaine 15"  => 15,
                        $date_start->setISODate(intval($this->year), 16)->format("d/m/Y")." - Semaine 16"  => 16,
                        $date_start->setISODate(intval($this->year), 17)->format("d/m/Y")." - Semaine 17"  => 17,
                        $date_start->setISODate(intval($this->year), 18)->format("d/m/Y")." - Semaine 18"  => 18,
                        $date_start->setISODate(intval($this->year), 19)->format("d/m/Y")." - Semaine 19"  => 19,
                        $date_start->setISODate(intval($this->year), 20)->format("d/m/Y")." - Semaine 20"  => 20,
                        $date_start->setISODate(intval($this->year), 21)->format("d/m/Y")." - Semaine 21"  => 21,
                        $date_start->setISODate(intval($this->year), 22)->format("d/m/Y")." - Semaine 22"  => 22,
                        $date_start->setISODate(intval($this->year), 23)->format("d/m/Y")." - Semaine 23"  => 23,
                        $date_start->setISODate(intval($this->year), 24)->format("d/m/Y")." - Semaine 24"  => 24,
                        $date_start->setISODate(intval($this->year), 25)->format("d/m/Y")." - Semaine 25"  => 25,
                        $date_start->setISODate(intval($this->year), 26)->format("d/m/Y")." - Semaine 26"  => 26,
                        $date_start->setISODate(intval($this->year), 27)->format("d/m/Y")." - Semaine 27"  => 27,
                        $date_start->setISODate(intval($this->year), 28)->format("d/m/Y")." - Semaine 28"  => 28,
                        $date_start->setISODate(intval($this->year), 29)->format("d/m/Y")." - Semaine 29"  => 29,
                        $date_start->setISODate(intval($this->year), 30)->format("d/m/Y")." - Semaine 30"  => 30,
                        $date_start->setISODate(intval($this->year), 31)->format("d/m/Y")." - Semaine 31"  => 31,
                        $date_start->setISODate(intval($this->year), 32)->format("d/m/Y")." - Semaine 32"  => 32,
                        $date_start->setISODate(intval($this->year), 33)->format("d/m/Y")." - Semaine 33"  => 33,
                        $date_start->setISODate(intval($this->year), 34)->format("d/m/Y")." - Semaine 34"  => 34,
                        $date_start->setISODate(intval($this->year), 35)->format("d/m/Y")." - Semaine 35"  => 35,
                        $date_start->setISODate(intval($this->year), 36)->format("d/m/Y")." - Semaine 36"  => 36,
                        $date_start->setISODate(intval($this->year), 37)->format("d/m/Y")." - Semaine 37"  => 37,
                        $date_start->setISODate(intval($this->year), 38)->format("d/m/Y")." - Semaine 38"  => 38,
                        $date_start->setISODate(intval($this->year), 39)->format("d/m/Y")." - Semaine 39"  => 39,
                        $date_start->setISODate(intval($this->year), 40)->format("d/m/Y")." - Semaine 40"  => 40,
                        $date_start->setISODate(intval($this->year), 41)->format("d/m/Y")." - Semaine 41"  => 41,
                        $date_start->setISODate(intval($this->year), 42)->format("d/m/Y")." - Semaine 42"  => 42,
                        $date_start->setISODate(intval($this->year), 43)->format("d/m/Y")." - Semaine 43"  => 43,
                        $date_start->setISODate(intval($this->year), 44)->format("d/m/Y")." - Semaine 44"  => 44,
                        $date_start->setISODate(intval($this->year), 45)->format("d/m/Y")." - Semaine 45"  => 45,
                        $date_start->setISODate(intval($this->year), 46)->format("d/m/Y")." - Semaine 46"  => 46,
                        $date_start->setISODate(intval($this->year), 47)->format("d/m/Y")." - Semaine 47"  => 47,
                        $date_start->setISODate(intval($this->year), 48)->format("d/m/Y")." - Semaine 48"  => 48,
                        $date_start->setISODate(intval($this->year), 49)->format("d/m/Y")." - Semaine 49"  => 49,
                        $date_start->setISODate(intval($this->year), 50)->format("d/m/Y")." - Semaine 50"  => 50,
                        $date_start->setISODate(intval($this->year), 51)->format("d/m/Y")." - Semaine 51"  => 51,
                        $date_start->setISODate(intval($this->year), 52)->format("d/m/Y")." - Semaine 52"  => 52,
                        $date_start->setISODate(intval($this->year), 53)->format("d/m/Y")." - Semaine 53"  => 53,
                        ),
            'choices_as_values' => true,
            'required' => false,
            'data' => $this->week,
            'empty_value' => false,
            'expanded' => false,
            'multiple' => false,
            )
            );
            }

                      // Configuration de la liste des boutiques à afficher

                      //get fields value for custom queries
            if(in_array($this->user_actuel->getRole() , array('ROLE_MARQUE','ROLE_DR'))) {
                $form->add('dr', 'entity', array(
                  'class' => 'ApplicationSonataUserBundle:User',
                  'property' => 'username',
                  'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                        ->where('u.role = :roleRm')
                        ->andWhere('u.brand = :brand')
                        ->setParameter('roleRm', 'ROLE_DR')
                        ->setParameter('brand', $this->user->getBrand())
                        ->add('orderBy','u.role DESC ,u.username ASC')
                        ;
                    },
                    'empty_value' => 'Tous',
                    'required' => false
                    )
                );

                if($this->user->getRole() == "ROLE_DR"){
                    $form->add('boutique', 'entity', array(
                        'class' => 'ApplicationSonataUserBundle:User',
                        'property' => 'username',
                        'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                            ->where('u.role = :role')
                            ->andWhere('u.brand = :brand')
                            ->andWhere('u.dr = :dr')
                            ->setParameter('role', 'ROLE_BOUTIQUE')
                            ->setParameter('brand', $this->user->getBrand())
                            ->setParameter('dr', $this->user->getUsername())
                            ->add('orderBy','u.role DESC ,u.username ASC')
                            ;
                        },
                        'empty_value' => 'Tous',
                        'required' => false
                        )
                    );


                    $form->add('vendeur', 'entity', array(
                        'class' => 'ApplicationSonataUserBundle:User',
                        'property' => 'nameAndSurname',
                        'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                            ->where('u.role = :vendeur')
                            ->andWhere('u.dr = :dr')
                            ->setParameter('vendeur', 'ROLE_VENDEUR')
                            ->setParameter('dr', $this->user->getUsername())
                            ->andWhere('u.brand = :brand')
                            ->setParameter('brand', $this->user->getBrand())
                            ->add('orderBy','u.role DESC ,u.nomVendeur ASC')
                            ;
                        },
                        'empty_value' => 'Tous',
                        'required' => false
                        )
                    );
                }
                else{
                    $form->add('boutique', 'entity', array(
                        'class' => 'ApplicationSonataUserBundle:User',
                        'property' => 'username',
                        'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                            ->where('u.role = :role')
                            ->andWhere('u.brand = :brand')
                            ->setParameter('role', 'ROLE_BOUTIQUE')
                            ->setParameter('brand', $this->user->getBrand())
                            ->add('orderBy','u.role DESC ,u.username ASC')
                            ;
                        },
                        'empty_value' => 'Tous',
                        'required' => false
                        )
                    );

                    $form->add('vendeur', 'entity', array(
                        'class' => 'ApplicationSonataUserBundle:User',
                        'property' => 'nameAndSurname',
                        'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                            ->where('u.role = :vendeur')
                            ->setParameter('vendeur', 'ROLE_VENDEUR')
                            ->andWhere('u.brand = :brand')
                            ->setParameter('brand', $this->user->getBrand())
                            ;
                        },
                        'empty_value' => 'Tous',
                        'required' => false
                        )
                    );
                }
            }


            else{
                $form->add('dr', 'entity', array(
                  'class' => 'ApplicationSonataUserBundle:User',
                  'property' => 'username',
                  'query_builder' => function(EntityRepository $er) {
                      return $er->createQueryBuilder('u')
                      ->where('u.role = :roleRm')
                      ->andWhere('u.brand = :brand')
                      ->setParameter('roleRm', 'ROLE_DR')
                      ->setParameter('brand', $this->user->getBrand())
                      ->add('orderBy','u.role DESC ,u.username ASC')
                      ;
                  },
                  'empty_value' => 'Tous',
                  'required' => true
                  ,'data' => $this->em->getRepository("ApplicationSonataUserBundle:User")->findOneBy(array("username" => $this->user->getDr()))
                  )
                );          
                $form->add('boutique', 'entity', array(
                    'class' => 'ApplicationSonataUserBundle:User',
                    'property' => 'username',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                        ->where('u.role = :role')
                        ->andWhere('u.dr = :dr')
                        ->setParameter('role', 'ROLE_BOUTIQUE')
                        ->setParameter('dr', $this->user->getDr())
                        ->add('orderBy','u.role DESC ,u.username ASC')
                        ;
                    },
                    'empty_value' => false,
                    'required' => false
                    ,'data' => $this->user
                    )
                );

                if($this->user->getRole() != "ROLE_VENDEUR"){
                  $form->add('vendeur', 'entity', array(
                    'class' => 'ApplicationSonataUserBundle:User',
                    'property' => 'nameAndSurname',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                        ->where('u.role = :vendeur')
                        ->setParameter('vendeur', 'ROLE_VENDEUR')
                        ->add('orderBy','u.role DESC ,u.nomVendeur ASC')
                        ->andWhere('u.boutique = :boutique')
                        ->setParameter('boutique', $this->user->getUsername())
                        ;
                    },
                    'empty_value' => 'Tous',
                    'required' => false
                    )
                  );
              }
              else{
                  $form->add('vendeur', 'entity', array(
                    'class' => 'ApplicationSonataUserBundle:User',
                    'property' => 'nameAndSurname',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                        ->where('u.role = :vendeur')
                        ->setParameter('vendeur', 'ROLE_VENDEUR')
                        ->add('orderBy','u.role DESC ,u.nomVendeur ASC')
                        ->andWhere('u.boutique = :boutique')
                        ->setParameter('boutique', $this->user->getBoutique())
                        ;
                    },
                    'empty_value' => 'Tous',
                    'required' => false
                    )
                  );
              }

            }

        });

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_kpi_filter';
    }
}
