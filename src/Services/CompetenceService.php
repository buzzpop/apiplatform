<?php


namespace App\Services;


use App\Entity\Competences;
use App\Entity\Niveau;
use App\Repository\GroupeCompetencesRepository;
use App\Repository\GroupeTagRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class CompetenceService
{
    private $manager;
    private $serializer;
    private $error;
    private $repository;



    public function __construct( EntityManagerInterface $manager, SerializerInterface $serializer,ErrorService $errorService,GroupeCompetencesRepository $groupeCompetencesRepository){

        $this->manager=$manager;
        $this->serializer=$serializer;
        $this->error=$errorService;
        $this->repository=$groupeCompetencesRepository;
    }


    public function putCompetence( $request ,int $id){

        $compTab= $this->serializer->decode($request->getContent(),'json');

        $compRepo= $this->manager->getRepository(Competences::class);
        $comp= $compRepo ->find($id);

        //dd($compTab['libelle']);

        if (isset($compTab['libelle'])){
            $comp->setLibelle($compTab['libelle']);
        }

        if ($compTab['niveaux']){
            foreach ($compTab['niveaux'] as $niveau){

                $niveauRepo= $this->manager->getRepository(Niveau::class)->find($niveau['id']);
                if ($niveauRepo){
                    $niveauRepo->setLibelle($niveau['libelle']);
                    $niveauRepo->setCritereEvaluation($niveau['critere_evaluation']);
                    $niveauRepo->setGroupeAction($niveau['groupe_action']);
                    $this->manager->persist($niveauRepo);
                }
            }
        }

        $this->manager->persist($comp);
      $this->manager->flush();
        return true;

    }

}