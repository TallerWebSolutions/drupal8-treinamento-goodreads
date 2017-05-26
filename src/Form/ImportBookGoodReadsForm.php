<?php

namespace Drupal\goodreads\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
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

  /**
   * Constructs a new ImportBookGoodReadsForm object.
   */
  public function __construct(
    GoodReadsClientService $goodreads_client,
    EntityTypeManager $entity_type_manager
  ) {
    $this->goodreadsClient = $goodreads_client;
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('goodreads.client'),
      $container->get('entity_type.manager')
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
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }

  }

}
