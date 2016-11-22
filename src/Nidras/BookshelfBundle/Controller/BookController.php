<?php

namespace Nidras\BookshelfBundle\Controller;
use Nidras\BookshelfBundle\Form\BookType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Nidras\BookshelfBundle\Entity\Book;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BookController extends Controller{

	public function indexAction(Request $request){
		
		$form_search = $this->createFormBuilder()
			->add('title', TextType::class)
			->add('save', SubmitType::class, array('label' => 'SEARCH TITLE'))
			->getForm();
			
		if($form_search->handleRequest($request)->isValid()){
			$getTitle = $form_search['title']->getData();
			if ($getTitle != "")
			{
				return $this->redirect($this->generateUrl('nidras_bookshelf_list', array('search' => $getTitle)));
			}
		}
	
		$content = $this->get('templating')
		->render('NidrasBookshelfBundle:Book:index.html.twig', array('form_search' => $form_search->createView()));
	
		return new Response($content);
	}
	
	public function aboutAction(){
		
		$content = $this->get('templating')
		->render('NidrasBookshelfBundle:Book:about.html.twig');
	
		return new Response($content);
	}
	
	public function viewAction($id, Request $request){
		
		$book = $this->container->get('doctrine.orm.entity_manager')->getRepository('NidrasBookshelfBundle:Book')
																	->find($id);
		
		$form_edit = $this->createForm(BookType::class, $book);
		$form_supp = $this->createFormBuilder($book)
			->add('save', SubmitType::class, array('label' => 'Yes'))
			->getForm();
		
		if($form_edit->handleRequest($request)->isValid()){
			if ($book->getPages() > 0 && $book->getMark() >= 0 && $book->getMark() <= 5)
			{
				$em = $this->container->get('doctrine.orm.entity_manager');
				$em->flush();
				
				return $this->redirect($this->generateUrl('nidras_bookshelf_view', array('id' => $book->getId())));
			}
			else
			{
				$em = $this->container->get('doctrine.orm.entity_manager');
				$em->refresh($book);
			}
		}
		if($form_supp->handleRequest($request)->isValid()){
		
			$em = $this->container->get('doctrine.orm.entity_manager');
			$em->remove($book);
			$em->flush();
				
			return $this->redirect($this->generateUrl('nidras_bookshelf_list'));
		}
		
		return $this->render('NidrasBookshelfBundle:Book:view.html.twig', array('form_edit' => $form_edit->createView(),'book' => $book,'form_supp' => $form_supp->createView(),));							
	
	}
	
	public function listAction(Request $request, $search){
		
																	
		if ($search == null)
		{
			$books = $this->container->get('doctrine.orm.entity_manager')->getRepository('NidrasBookshelfBundle:Book')
																->findAll();
		}
		else
		{
			$criteria = new \Doctrine\Common\Collections\Criteria();
			$criteria->where($criteria->expr()->contains('title', $search));
			
			$books = $this->container->get('doctrine.orm.entity_manager')->getRepository('NidrasBookshelfBundle:Book')
																->matching($criteria);
		}
		
		$book = new Book();
		$form_add = $this->createForm(BookType::class, $book);
		
		if($form_add->handleRequest($request)->isValid()){
			if ($book->getPages() > 0 && $book->getMark() >= 0 && $book->getMark() <= 5)
			{
				$em = $this->container->get('doctrine.orm.entity_manager');
				$em->persist($book);
				$em->flush();
				
				return $this->redirect($this->generateUrl('nidras_bookshelf_view', array('id' => $book->getId())));
			}
			else
			{
				
			}
		}
		
		return $this->render('NidrasBookshelfBundle:Book:list.html.twig', array('form_add' => $form_add->createView(),'books' => $books,'search' => $search,));							
	}
	
	
}