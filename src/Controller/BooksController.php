<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Review;
use App\Form\BookFormType;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BooksController extends AbstractController
{
    protected BookRepository $bookRepository;
    protected EntityManagerInterface $entityManager;

    public function __construct(BookRepository $bookRepository, EntityManagerInterface $entityManager)
    {
        $this->bookRepository = $bookRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Handles the homepage and book listing with filters and pagination.
     */
    #[Route('/books', name: 'app_books')]
    #[Route('/', name: 'app_homepage')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $searchQuery = $request->query->get('q', '');
        $selectedGenre = $request->query->get('genre', '');
        $selectedAuthor = $request->query->get('author', '');

        $qb = $this->bookRepository->createQueryBuilder('b');

        if (!empty($searchQuery)) {
            $qb->andWhere('b.title LIKE :searchQuery OR b.author LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        if (!empty($selectedGenre)) {
            $qb->andWhere('b.genre = :genre')
                ->setParameter('genre', $selectedGenre);
        }

        if (!empty($selectedAuthor)) {
            $qb->andWhere('b.author = :author')
                ->setParameter('author', $selectedAuthor);
        }

        $query = $qb->getQuery();
        $page = $request->query->getInt('page', 1);
        $books = $paginator->paginate($query, $page, 10);

        $genres = array_map(fn($g) => $g['genre'], $this->bookRepository->findDistinctGenres());
        $authors = array_map(fn($a) => $a['author'], $this->bookRepository->findDistinctAuthors());

        return $this->render('books/index.html.twig', [
            'books' => $books,
            'genres' => $genres,
            'authors' => $authors,
            'searchQuery' => $searchQuery,
            'selectedGenre' => $selectedGenre,
            'selectedAuthor' => $selectedAuthor,
        ]);
    }

    /**
     * Handles adding a new book, including file uploads for the cover image.
     */
    #[Route('/books/add', name: 'app_books_add')]
    public function add(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the cover image upload
            $coverImageFile = $form->get('coverImage')->getData();
            if ($coverImageFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/cover_images';

                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }

                $fileName = uniqid() . '.' . $coverImageFile->guessExtension();

                try {
                    $coverImageFile->move($uploadsDir, $fileName);
                    $book->setCoverImage('/uploads/cover_images/' . $fileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload the cover image. Please try again.');
                    return $this->redirectToRoute('app_books_add');
                }
            }

            $now = new DateTimeImmutable();
            $book->setCreatedAt($now);
            $book->setUpdatedAt($now);

            $this->entityManager->persist($book);
            $this->entityManager->flush();

            $this->addFlash('success', 'Book added successfully!');
            return $this->redirectToRoute('app_reviews_create', ['bookId' => $book->getId()]);
        }

        return $this->render('books/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays reviews for a specific book with pagination.
     */
    #[Route('/book/{id}/reviews', name: 'app_book_reviews')]
    public function bookReviews(
        int $id,
        BookRepository $bookRepository,
        ReviewRepository $reviewRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Book not found.');
        }

        $query = $reviewRepository->createQueryBuilder('r')
            ->andWhere('r.book = :book')
            ->setParameter('book', $book)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 6);

        return $this->render('reviews/book_reviews.html.twig', [
            'book' => $book,
            'reviews' => $pagination,
        ]);
    }
}

