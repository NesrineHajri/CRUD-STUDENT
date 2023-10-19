<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\ClassroomRepository;
use App\Repository\StudentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;






class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }

    #[Route('/fetch', name: 'fetch')]
    public function fetch(StudentRepository $repo):Response
    {
        $result=$repo->findAll();
        return $this->render('student/affi.html.twig',[
            'response' => $result,
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(ManagerRegistry $mr,ClassroomRepository $repo,HttpFoundationRequest $req):Response
    {
        $s=new Student(); //instance

        //$s->setName('test');
        //$s->setEmail('testgmail.com');
        //$s->setAge('28');

        $form=$this->createForm(StudentType::class,$s); // creation formulaire et binding
        $form->handleRequest($req);
        if ($form->isSubmitted()){

            $em=$mr->getManager();
            $em->persist($s);
            $em->flush();
            return $this->redirectToRoute('fetch');
        }

        return $this->render('student/addauthor.html.twig',[
            'f'=>$form->createView()
        ]);

    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(StudentRepository $repo,$id,ManagerRegistry $mr): Response
    {
        $student=$repo->find($id);
        $em=$mr->getManager();
        $em->remove($student);
        $em->flush();

        return new Response('Removed');
    
    }

      /**
     *@Route("/",name="student_list")
     */
    public function home()
    {
      //récupérer tous les articles de la table article de la BD
      // et les mettre dans le tableau $articles
      $students= $this->getDoctrine()->getRepository(Student::class)->findAll();
      return  $this->render('student/formstudent.html.twig',['students' => $students]);  
    }


    
    #[Route('/student/edit/{id}', name: 'edit_student', methods: ['GET', 'POST'])]
    public function edit(HttpFoundationRequest $request, StudentRepository $repo, ManagerRegistry $mr, int $id): Response
    {
        $student = $repo->find($id);

        if (!$student) {
            return new Response('Student non trouvé');
        }

        $form = $this->createForm(StudentType::class, $student); // Création du formulaire et binding
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $mr->getManager();
            $em->flush();

            return $this->redirectToRoute('student_list'); 
        }

        return $this->render('student/editStudent.html.twig', [
            'f' => $form->createView(),
        ]);
    }

}