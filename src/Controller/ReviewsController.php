<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Review;
use App\Form\ReviewFormType;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewsController extends AbstractController
{
    private ReviewRepository $reviewRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ReviewRepository $reviewRepository, EntityManagerInterface $entityManager)
    {
        $this->reviewRepository = $reviewRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Displays a paginated and searchable list of reviews.
     */
    #[Route('/reviews', name: 'app_reviews')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $sort = $request->query->get('sort', 'newest');
        $searchQuery = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        $orderBy = match ($sort) {
            'newest' => ['r.createdAt' => 'DESC'],
            'oldest' => ['r.createdAt' => 'ASC'],
            default => ['r.createdAt' => 'DESC'],
        };

        $query = $this->reviewRepository->createSearchQuery($searchQuery, $orderBy);

        $pagination = $paginator->paginate($query, $page, 6);

        return $this->render('reviews/index.html.twig', [
            'reviews' => $pagination,
            'sort' => $sort,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * Handles review creation and updates for a specific book.
     */
    #[Route('/reviews/create', name: 'app_reviews_create')]
    public function create(
        Request $request,
        BookRepository $bookRepository,
        ReviewRepository $reviewRepository,
        Security $security
    ): Response {
        $bookId = $request->query->get('bookId');
        $book = $bookRepository->find($bookId);

        if (!$book) {
            throw $this->createNotFoundException('Book not found.');
        }

        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You need to log in to leave a review.');
        }

        $existingReview = $reviewRepository->findOneBy(['book' => $book, 'user' => $user]);
        $review = $existingReview ?? new Review();

        if (!$existingReview) {
            $review->setBook($book)
                ->setUser($user)
                ->setCreatedAt(new DateTimeImmutable());
        }

        $review->setUpdatedAt(new DateTimeImmutable());

        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($review);
            $this->entityManager->flush();

            $message = $existingReview ? 'Review updated successfully!' : 'Review created successfully!';
            $this->addFlash('success', $message);

            return $this->redirectToRoute('app_reviews_create', ['bookId' => $bookId]);
        }

        return $this->render('reviews/create.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
            'user' => $user,
            'existingReview' => $existingReview,
        ]);
    }

    /**
     * Handles rating submission for a specific book.
     */
    #[Route('/books/{id}/reviews/rate', name: 'app_reviews_rate', methods: ['GET', 'POST'])]
    public function rate(
        Book $book,
        Request $request,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $ratingValue = $data['rating'] ?? null;

        if ($ratingValue < 1 || $ratingValue > 5) {
            return new JsonResponse(['error' => 'Invalid rating value'], 400);
        }

        $existingReview = $this->reviewRepository->findOneBy(['book' => $book, 'user' => $user]);

        if ($existingReview) {
            $existingReview->setRating($ratingValue);
        } else {
            $review = (new Review())
                ->setBook($book)
                ->setUser($user)
                ->setRating($ratingValue)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());

            $this->entityManager->persist($review);
        }

        $this->entityManager->flush();

        return new JsonResponse(['success' => 'Rating submitted successfully']);
    }

    /**
     * Deletes a review by ID.
     */
    #[Route('/reviews/{id}/delete', name: 'app_review_delete', methods: ['DELETE'])]
    public function deleteReview(
        Review $review,
        Security $security
    ): JsonResponse {
        $user = $this->getUser();

        if ($review->getUser() !== $user && !$security->isGranted('ROLE_MODERATOR')) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($review);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }

    /**
     * Toggles a "like" on a review.
     */
    #[Route('/reviews/{id}/like', name: 'app_review_like', methods: ['POST'])]
    public function like(Review $review, Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'You must be logged in to like a review.'], 403);
        }

        $likes = $review->isLikedBy($user)
            ? $review->removeLikedBy($user)->getLikedBy()->count()
            : $review->addLikedBy($user)->getLikedBy()->count();

        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'likes' => $likes,
            'liked' => $review->isLikedBy($user),
        ]);
    }
}

