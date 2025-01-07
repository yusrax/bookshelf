<?php

namespace App\Controller;

use App\Form\EditUserFormType;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    protected $entityManager;

    public function __construct(BookRepository $bookRepository, ReviewRepository $reviewRepository, EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->reviewRepository = $reviewRepository;
    }

    #[Route('/users', name: 'app_users')]
    public function index(Request $request, PaginatorInterface $paginator, UserRepository $userRepository): Response
    {
        $searchQuery = $request->query->get('q', '');

        $queryBuilder = $userRepository->createQueryBuilder('u')
            ->orderBy('u.username', 'ASC');

        if (!empty($searchQuery)) {
            $queryBuilder->andWhere('u.username LIKE :searchQuery OR u.email LIKE :searchQuery OR u.firstName LIKE :searchQuery OR u.lastName LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10 // Number of users per page
        );

        return $this->render('user/index.html.twig', [
            'users' => $pagination,
            'searchQuery' => $searchQuery, // Pass the search query back to the template
        ]);
    }



    #[Route('/user/{id?}', name: 'app_user_profile')]
    public function show(
        Request $request,
        PaginatorInterface $paginator,
        UserRepository $userRepository,
        ?int $id = null
    ): Response {
        $user = $id ? $userRepository->find($id) : $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        // Ensure only authorized users can access this page (optional)
        if ($id && $this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You are not allowed to view this profile.');
        }

        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            return new Response(
                $this->renderView('user/show.html.twig', [
                    'user' => $user,
                    'editUserForm' => $form->createView(),
                ]),
                422
            );
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
        }

        $query = $request->query->get('q', '');
        $reviewsQuery = $this->reviewRepository->createQueryBuilder('r')
            ->where('r.user = :user')
            ->setParameter('user', $user);

        if (!empty($query)) {
            $reviewsQuery->andWhere('r.reviewText LIKE :query OR r.rating = :queryExact')
                ->setParameter('query', '%' . $query . '%')
                ->setParameter('queryExact', (int) $query);
        }

        $pagination = $paginator->paginate(
            $reviewsQuery->getQuery(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'editUserForm' => $form->createView(),
            'reviews' => $pagination,
            'searchQuery' => $query,
        ]);
    }


    #[Route('/users/{id}/delete', name: 'app_user_delete', methods: ['DELETE'])]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }



    #[Route('/user/profile-picture', name: 'app_user_profile_picture', methods: ['POST'])]
    public function updateProfilePicture(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $file = $request->files->get('profilePicture');
        if (!$file) {
            return new JsonResponse(['error' => 'No file uploaded.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Validate file type and size
            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
                return new JsonResponse(['error' => 'Invalid file type. Only JPEG, PNG, or WebP are allowed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($file->getSize() > 2 * 1024 * 1024) {
                return new JsonResponse(['error' => 'File size exceeds the 2MB limit.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Move file to upload directory
            $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/profile_pictures';
            $fileName = uniqid() . '.' . $file->guessExtension();
            $file->move($uploadsDir, $fileName);

            // Update user's profile picture
            $user->setProfilePicture('/uploads/profile_pictures/' . $fileName);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'imageUrl' => $user->getProfilePicture(),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Unexpected error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}
