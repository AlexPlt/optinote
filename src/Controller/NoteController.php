<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Note;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NoteController extends AbstractController
{
  /**
    * @Route("/note/add", name="add_note")
    */
  public function addNote(Request $request)
  {
    $form = $this->createFormBuilder()
      ->add('Titre', TextType::class)
      ->add('Text', TextType::class)
      ->add('save', SubmitType::class, array('label' => 'Create Task'))
      ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $repository = $this->getDoctrine()->getRepository(Note::class);
      $entityManager = $this->getDoctrine()->getManager();

      $noteArray = $form->getData();

      $note = new Note();
      $note->setTitre($noteArray['Titre']);
      $note->setDate(new \DateTime());
      $note->setText($noteArray['Text']);

      $entityManager->persist($note);
      $entityManager->flush();

      $notes = $repository->findAll();

      return $this->redirectToRoute(
        'list_all_notes',
        array(
          'notes' => $notes
        )
      );
    }

    return $this->render(
      'note/add.html.twig',
      array(
        'form' => $form->createView()
      )
    );
  }

  /**
    * @Route("/note/listAll", name="list_all_notes")
    */
  public function listAllNotes()
  {
    $repository = $this->getDoctrine()->getRepository(Note::class);

    $notes = $repository->findAll();

    return $this->render(
      'note/listAll.html.twig',
      array(
        'notes' => $notes
      )
    );
  }

  /**
    * @Route("/note/del/{id}", name="confirm_del_note")
    */
  public function confirmDeleteNoteById($id)
  {
    $repository = $this->getDoctrine()->getRepository(Note::class);

    $note = $repository->find($id);

    return $this->render(
      'note/confirmDel.html.twig',
      array(
        'note' => $note
      )
    );
  }

  /**
    * @Route("/note/del/{id}/confirm", name="del_note")
    */
  public function DeleteNoteById($id)
  {
    $repository = $this->getDoctrine()->getRepository(Note::class);
    $entityManager = $this->getDoctrine()->getManager();

    $note = $repository->find($id);

    $entityManager->remove($note);
    $entityManager->flush();

    $notes = $repository->findAll();

    return $this->redirectToRoute(
      'list_all_notes',
      array(
        'notes' => $notes
      )
    );
  }
}
