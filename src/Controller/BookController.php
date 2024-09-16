<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
	
	#[Route('/book-new', name: 'book_new')]
	public function new(Request $request, EntityManagerInterface $entityManager): Response
	{
		$book = new Book();
		$form = $this->createForm(BookType::class, $book);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($book);
			$entityManager->flush();
			return $this->redirectToRoute('book_list', array('message' => 'Нова книга додана: ' . $book->getTitle()));
		}
		return $this->render('book/new.html.twig', [
            'form' => $form->createView(),
        ]);
	}
	
	#[Route('/book-list', name: 'book_list')]
	public function list(BookRepository $bookRepository, PaginatorInterface $paginator, Request $request, $message): Response
	{
		$query = $bookRepository->findAllBooksQuery();
		$books = $paginator->paginate($query, $request->query->getInt('page', 1), 10);
		return $this->render('book/list.html.twig', [
			'books' => $books,
			'message' => !isset($message) ? 'Наявні книги: ' : $message,
		]);
	}

	#[Route('/book-edit', name: 'book_edit')]
	public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
	{
		$form = $this->createForm(BookType::class, $book);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->flush();
		}
		return $this->redirectToRoute('book_list', array('message' => 'Книга відредагована: ' . $book->getTitle()));
	}
	
	#[Route('/book-del', name: 'book_del')]
	public function delete(Book $book, EntityManagerInterface $entityManager): Response
	{
		$entityManager->remove($book);
		$entityManager->flush();
		return $this->redirectToRoute('book_list', array('message' => 'Книга видалена: ' . $book->getTitle()));
	}
	
}
