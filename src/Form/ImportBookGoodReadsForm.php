<?php

namespace Drupal\goodreads\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Utility\LinkGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\goodreads\GoodReadsClientService;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class ImportBookGoodReadsForm.
 *
 * @package Drupal\goodreads\Form
 */
class ImportBookGoodReadsForm extends FormBase {

  protected $goodreadsClient;
  protected $entityTypeManager;
  protected $linkGenerator;

  /**
   * Constructs a new ImportBookGoodReadsForm object.
   */
  public function __construct(
    GoodReadsClientService $goodreads_client,
    EntityTypeManager $entity_type_manager,
    LinkGenerator $linkGenerator
  ) {
    $this->goodreadsClient = $goodreads_client;
    $this->entityTypeManager = $entity_type_manager;
    $this->linkGenerator = $linkGenerator;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('goodreads.client'),
      $container->get('entity_type.manager'),
      $container->get('link_generator')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_book_good_reads_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['isbn'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Codigo ISBN'),
      '#maxlength' => 20,
      '#size' => 64,
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Importar'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    parent::validateForm($form, $form_state);

    // Find Book.
    try {
      $response = $this->goodreadsClient->getBookByISBN($form_state->getValue('isbn'));
    }
    catch (\Exception $exception) {
      return $form_state->setErrorByName('isbn', $this->t('Livro não encontrado.'));
    }

    $form_state->setStorage(['book' => $response->book]);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $storage = $form_state->getStorage();

    $title = $storage['book']->title;
    $body = $storage['book']->description;

    $bookNode = $this->entityTypeManager->getStorage('node')
      ->create([
        'type' => 'book',
        'title' => $title,
        'body' => ['value' => $body, 'format' => 'full_html'],
      ]);

    $bookNode->save();

    drupal_set_message($this->t('Livro @book importado com successo!', [
      '@book' => $this->linkGenerator->generate($title, $bookNode->toUrl())
    ]));
  }

}
