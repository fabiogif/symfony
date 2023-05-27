<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use DateTime;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PaginationService;

class BookController extends AbstractController
{

    private $bookRepository;
    private $paginationService;

    public function __construct(BookRepository $bookRepository, PaginationService  $paginationService)
    {
        $this->bookRepository = $bookRepository;
        $this->paginationService = $paginationService;
    }

    #[Route('/books', name: 'book_list', methods:['GET'])]
    public function index(BookRepository $bookRepository, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $query = $this->bookRepository->createQueryBuilder('b')->orderBy('b.id', 'ASC')
            ->getQuery();

        $pagination = $this->paginationService->paginate($query, $page, $limit);

        return $this->json([
            'data' => $pagination,
        ], 200);
    }

    #[Route('/books', name: 'book_create', methods:['POST'])]
    public function create(Request $request, BookRepository $bookRepository): JsonResponse
    {
        $data = $request->request->all();

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setCreatedAt(new \DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')));
        $book->setUpdatedAt(new \DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')));

        $bookRepository->save($book, true);

        return $this->json([
            'message' => 'Create Success!',
            'path' => 'src/Controller/BookController.php',
        ], 201);
    }

    #[Route('/books/{book}', name: 'book_update', methods:['PUT', 'PATCH'])]
    public function update(Request $request, Book $book, ManagerRegistry $managerRegistry): JsonResponse
    {
        $data = $request->request->all();

        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setUpdatedAt(new \DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')));

        $managerRegistry->getManager()->flush();

        return $this->json([
            'Book update success',
        ], 201);

        
    }

    #[Route('/books/{id}', name: 'book_single', methods:['GET'])]
    public function single(int $id, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($id);

        if(!$book) throw $this->createNotFoundException();

        return $this->json([
            'data'=> $book
        ], 201);


    }
}
