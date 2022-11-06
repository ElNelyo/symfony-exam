<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;


class AuthorController extends AbstractController
{
    private $em;
    private $serializer;

    public function __construct(EntityManagerInterface $emInjected,SerializerInterface $serializer)
    {
        $this->em = $emInjected;
        $this->serializer = $serializer;
    }
    /**
     * search author by name
     *
     */
    #[Route('/author/search', name: 'search-author', methods: ['POST'], format: 'json')]
    public function book(Request $request, int $status = 200, array $headers = [])
    {

        $authors = $this->em->getRepository(Author::class)->search($request->query->get("search"));

        return new Response(
            $this->serializer->serialize($authors, JsonEncoder::FORMAT, ["groups"=>"reader_user"]),
            $status,
            array_merge($headers, ['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }


    /**
     * create new author with books
     *
     */
    #[Route('/author/create', name: 'create-author', methods: ['POST'], format: 'json')]
    public function createAuthor(Request $request, int $status = 200, array $headers = [])
    {
        $data = json_decode($request->getContent(), true);
        if($data["firstname"] != null and $data["lastname"] ){

            $firstname = $data["firstname"];
            $lastname = $data["lastname"];

            $new_author = new Author();
            $new_author->setFirstname($firstname);
            $new_author->setLastname($lastname);
            $this->em->persist($new_author);
            $this->em->flush();

            if(isset($data["books"])){
                foreach ($data["books"] as $book){
                    $new_book = new Book();
                    $new_book->setAuthor($new_author);
                    $new_book->setTitle($book["title"]);
                    $new_book->setResume($book["resume"]);
                    $new_author->addBook($new_book);
                    $this->em->persist($new_book);
                }
            }

            $this->em->flush();
            return new Response(
                $this->serializer->serialize($new_author, JsonEncoder::FORMAT, ["groups"=>"reader_user"]),
                $status,
                array_merge($headers, ['Content-Type' => 'application/json;charset=UTF-8'])
            );

        }else{
            return new Response(
                json_encode("Missing data"),
                403,
                array_merge($headers, ['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }



    }
}
