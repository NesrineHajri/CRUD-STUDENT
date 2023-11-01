<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Student;
use App\Form\StudentType;
use App\Entity\Classroom;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ClassroomRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/add1', name: 'add1')]
    public function add1(ClassroomRepository $repo,ManagerRegistry $mr): Response
    {
        $c=$repo->find('1');
        $s=new Student();
        $s->setName('nesrine');
        $s->setEmail('nesrine@gmail.com');
        $s->setAge('24');
        $s->setClassroom($c);
        $em=$mr->getManager();
        $em->persist($s);
        $em->flush();
        return $this->redirectToRoute('fetch');
    }

    #[Route('/add', name: 'add')]
    public function add(ManagerRegistry $mr,ClassroomRepository $repo,Request $req):Response
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

    #[Route('/remove1/{name}', name: 'remove1')]
    public function remove1(ManagerRegistry $mr,StudentRepository $repo,$name): Response
    {
       $entite=$repo->findByNom($name);
        //$entite=$repo->find($name);
        if(!$entite)
        {
            throw $this->createNotFoundException('Aucune entité trouvée avec ce nom.');
        }
        $em=$mr->getManager();
        $em->remove($entite);
        $em->flush();
        return $this->redirectToRoute('fetch');    
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


    
    #[Route('/student/edit/{id}', name: 'edit_student')]
    public function edit(StudentRepository $repo, ManagerRegistry $mr,Request $request, int $id): Response
    {
        $student = $repo->find($id);
        $form = $this->createForm(StudentType::class, $student); 
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $em = $mr->getManager();
            $em->persist($student);
            $em->flush();

            return $this->redirectToRoute('fetch'); 
        }

        return $this->render('student/editStudent.html.twig', [
            'f' => $form->createView(),
        ]);
    }

    #[Route('/dql', name: 'dql')]
    public function dqlStudent(EntityManagerInterface $em , Request $request , StudentRepository $repo):Response
    {
        $result=$repo->findAll();
        $req=$em->createQuery("select s from App\Entity\Student s where s.name = :n ");
        if($request->isMethod('post')){
            $value=$request->get('test');
            $req->setParameter('n' , $value);
            $result=$req->getResult();
        }
        return $this->render('student/searchStudent.html.twig',[
        'student' => $result]);
    }

    #[Route('/dql1', name: 'dql1')]
    public function dqlStudent1(EntityManagerInterface $em , Request $request , StudentRepository $repo):Response
        {
            $result=$repo->findAll();
            //$req=$em->createQuery("select s from App\Entity\Student s where s.name = :n ");
            if($request->isMethod('post')){
                $value=$request->get('test');
                
                $result=$repo->fetchStudentByName($value);
                //dd($result);
            }
            return $this->render('student/searchStudent1.html.twig',[
            'student' => $result]);
        }
    
    #[Route('/dql2', name: 'dql2')]
    public function dql2(EntityManagerInterface $em):Response
        {
            $req=$em->createQuery("select count(s) from App\Entity\Student s");//elle compte le nombre d'etudiants 
            $result=$req->getResult();
            dd($result);
        }

    #[Route('/dql3', name: 'dql3')]
    public function dql3(EntityManagerInterface $em):Response
        {
            $req=$em->createQuery("select s.name from App\Entity\Student s Order By s.name DESC");//tri
            $result=$req->getResult();
            dd($result);
        }
    
    #[Route('/dql4', name: 'dql4')]
    public function dql4(EntityManagerInterface $em):Response
        {
            $req=$em->createQuery("select s.name from App\Entity\Student s where s.classroom !='null' ");
            $result=$req->getResult();
            dd($result);
        }

    #[Route('/dql5', name: 'dql5')]
    public function dql5(EntityManagerInterface $em):Response
        {
            $req=$em->createQuery("select s.name t ,c.name from App\Entity\Student s join s.classroom c");
            $result=$req->getResult();
            dd($result);
        }

    #[Route('/dql6', name: 'dql6')]
    public function dql6(EntityManagerInterface $em):Response
        {
            $req=$em->createQuery("select s.name t , c.name from App\Entity\Student s join s.classroom c where c.name='3a15'");
            $result=$req->getResult();
            dd($result);
        }

    #[Route('/QB', name: 'QB')]
    public function QB(StudentRepository $repo):Response
        {
            $result=$repo->listQB();
            dd($result);   
        }

    #[Route('/QB1', name: 'QB1')]
    public function QB1(StudentRepository $repo):Response
        {
            $result=$repo->listQB1();
            dd($result);   
        }


}