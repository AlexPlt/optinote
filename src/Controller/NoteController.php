<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Note;

class NoteController extends AbstractController
{
  /**
    * @Route("/note/add", name="add_note")
    */
  public function addNote(Request $request)
  {
    $note = new Note();
    $note->setTitre('Titre');
    $note->setText('Ecrire le texte ici...');

    $form = $this->createFormBuilder($note)
      ->add('titre', TextType::class)
      ->add('importance', ChoiceType::class,
        array(
          'multiple' => false,
          'expanded' => true,
          'choices' => array(
            'pas important' => '1',
            'important' => '2',
            'très important' => '3'
          )
        )
      )
      ->add('text', TextType::class)
      ->add('ajouterNote', SubmitType::class, array('label' => 'Ajouter la note'))
      ->getForm();

    $form->handleRequest($request);

    $date = new \DateTime();

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager = $this->getDoctrine()->getManager();

      $note = $form->getData();

      $note->setDate(new \DateTime());

      $entityManager->persist($note);
      $entityManager->flush();

      $notes = $entityManager->getRepository(Note::class)->findAll();

      return $this->redirectToRoute(
        'list_all_notes'
      );
    }

    return $this->render(
      'note/add.html.twig',
      array(
        'form' => $form->createView(),
        'date' => $date
      )
    );
  }

  /**
    * @Route("/note/modif/{id}", name="modif_note")
    */
  public function modifNote(Request $request, $id)
  {
    $entityManager = $this->getDoctrine()->getManager();

    $note = $entityManager->getRepository(Note::class)->find($id);

    if (!$note) {
        throw $this->createNotFoundException(
            'Aucune note n\'a été trouvée pour l\'id ' . $id
        );
    }

    $form = $this->createFormBuilder($note)
      ->add('titre', TextType::class)
      ->add('importance', ChoiceType::class,
        array(
          'multiple' => false,
          'expanded' => true,
          'choices' => array(
            'pas important' => '1',
            'important' => '2',
            'très important' => '3'
          )
        )
      )
      ->add('text', TextType::class)
      ->add('modifierNote', SubmitType::class, array('label' => 'Modifier la note'))
      ->getForm();

    $form->handleRequest($request);

    $date = new \DateTime();

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager = $this->getDoctrine()->getManager();

      $note = $form->getData();

      $note->setDate(new \DateTime());

      $entityManager->flush();

      $notes = $entityManager->getRepository(Note::class)->findAll();

      return $this->redirectToRoute(
        'list_all_notes'
      );
    }

    return $this->render(
      'note/modif.html.twig',
      array(
        'form' => $form->createView(),
        'date' => $date
      )
    );
  }

  /**
    * @Route("/", name="list_all_notes")
    */
  public function listAllNotes(Request $request)
  {
    $recherche = false;
    $motRech = '';

    $form = $this->createFormBuilder()
      ->add('recherche', TextType::class)
      ->add('lancerRech', SubmitType::class, array('label' => 'Rechercher'))
      ->getForm();

    $entityManager = $this->getDoctrine()->getManager();

    $notes = $entityManager->getRepository(Note::class)->findAll();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $recherche = true;
      $entityManager = $this->getDoctrine()->getManager();

      $rech = $form->getData();

      $motRech = $rech['recherche'];

      $query = $entityManager
        ->createQuery('SELECT n FROM App\Entity\Note n WHERE (n.titre LIKE :rech OR n.text LIKE :rech)')
        ->setParameter('rech', '%' . $motRech . '%');

      $notes = $query->execute();

      $entityManager->flush();
    }

    return $this->render(
      'note/listAll.html.twig',
      array(
        'form' => $form->createView(),
        'notes' => $notes,
        'recherche' => $recherche,
        'motRech' => $motRech
      )
    );
  }

  /**
    * @Route("/note/del/{id}", name="confirm_del_note")
    */
  public function confirmDeleteNoteById($id)
  {
    $entityManager = $this->getDoctrine()->getManager();

    $note = $entityManager->getRepository(Note::class)->find($id);

    if (!$note) {
        throw $this->createNotFoundException(
            'Aucune note n\'a été trouvée pour l\'id ' . $id
        );
    }

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
    $entityManager = $this->getDoctrine()->getManager();

    $note = $entityManager->getRepository(Note::class)->find($id);

    if (!$note) {
        throw $this->createNotFoundException(
            'Aucune note n\'a été trouvée pour l\'id ' . $id
        );
    }

    $entityManager->remove($note);
    $entityManager->flush();

    $notes = $entityManager->getRepository(Note::class)->findAll();

    return $this->redirectToRoute(
      'list_all_notes'
    );
  }
}
