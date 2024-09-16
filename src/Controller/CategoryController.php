<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
	
	#[Route('/category-list', name: 'category_list')]
	public function list(BookRepository $categoryRepository, PaginatorInterface $paginator, Request $request, $message): Response
	{
		$query = $categoryRepository->findAllCategoryQuery();
		$category = $paginator->paginate($query, $request->query->getInt('page', 1), 10);
		return $this->render('category/list.html.twig', [
			'category' => $category,
			'message' => !isset($message) ? 'Наявні категорії: ' : $message['message'],
		]);
	}
	
	#[Route('/category-new', name: 'category_new')]
	public function newCategory(Request $request, EntityManagerInterface $entityManager): Response
	{
		$category = new Category();
		$form = $this->createForm(CategoryType::class, $category);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($category);
			$entityManager->flush();
			return $this->redirectToRoute('category_list', array('message' => 'Нова категорія додана: ' . $category->getName()));
			
		}
		return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
	}

	#[Route('/category-edit', name: 'category_edit')]
	public function editCategory(Request $request, Category $category, EntityManagerInterface $entityManager): Response
	{
		$form = $this->createForm(CategoryType::class, $category);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($category);
			$entityManager->flush();
			return $this->redirectToRoute('category_list', array('message' => 'Категорія відредагована: ' . $category->getName()));
			
		}
		return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
	}

	#[Route('/category-del', name: 'category_del')]
	public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
	{
		$entityManager->remove($category);
		$entityManager->flush();

		return $this->redirectToRoute('category_list', array('message' => 'Категорія видалена: ' . $category->getName()));
	}

}
