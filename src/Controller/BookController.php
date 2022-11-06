<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    private $em;
    private $serializer;

    public function __construct(EntityManagerInterface $emInjected,SerializerInterface $serializer)
    {
        $this->em = $emInjected;
        $this->serializer = $serializer;
    }

    /**
     * return all name of books and author in json format
     */
    #[Route('/books/list', name: 'list-of-my-books', methods: ['POST'], format: 'json')]
    public function book(int $status = 200, array $headers = [])
    {

        $books = $this->em->getRepository(Book::class)->findAll();
        return new Response(
            $this->serializer->serialize($books, JsonEncoder::FORMAT, ["groups"=>"reader"]),
            $status,
            array_merge($headers, ['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    /**
     * Browse all books and add suffix on title
     */
    #[Route('/books/add/suffix', name: 'add-suffix-to-all-books', methods: ['POST'], format: 'json')]
    public function addSufix(Request $request, int $status = 200, array $headers = [])
    {
        if($request->query->get('suffix') != null){
            $books = $this->em->getRepository(Book::class)->findAll();
            $suffix = $request->query->get('suffix');

            /** @var Book $book */
            foreach ($books as $book) {
                $book->setTitle($book->getTitle().$suffix);
                $this->em->persist($book);
            }
            $this->em->flush();

            return new Response(
                $this->serializer->serialize($books, JsonEncoder::FORMAT, ["groups"=>"reader"]),
                $status,
                array_merge($headers, ['Content-Type' => 'application/json;charset=UTF-8'])
            );

        }else{
            return new Response(
                json_encode("Missing suffix in query"),
                403,
                array_merge($headers, ['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }
    }
}
