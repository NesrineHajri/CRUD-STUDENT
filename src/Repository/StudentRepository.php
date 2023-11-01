<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 *
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

//    /**
//     * @return Student[] Returns an array of Student objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Student
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findByNom($name)
    {
        return $this->createQueryBuilder('s')
        ->where('s.name = :name')
        ->setParameter('name', $name)
        ->getQuery()
        ->getOneOrNullResult();
    }

    //dql
    public function fetchStudentByName($name)
    {
        $em=$this->getEntityManager();
        $req=$em->createQuery("select s from App\Entity\Student s where s.name=:n");
        $req->setParameter('n',$name);
        $result=$req->getResult();
        return $result;
    }

    //QueryBuilder
    public function listQB()
        {
            $req=$this->createQueryBuilder('s');
            $preresult=$req->getQuery();
            $result=$preresult->getResult();
            return $result;
    
        }

    public function listQB1()
        {
            $req=$this->createQueryBuilder('s')
            ->select('s.name')
            ->join('s.classroom',"c")
            ->addSelect("c.name as classname")
            ->where("c.name='3a15'");
            $preresult=$req->getQuery();
            $result=$preresult->getResult();
            return $result;
    
        }
}
