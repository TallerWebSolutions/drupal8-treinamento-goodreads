<?php

namespace Drupal\Tests\goodreads\Kernel;
use Drupal\Core\Form\FormState;
use Drupal\goodreads\GoodReadsClientService;
use Drupal\KernelTests\KernelTestBase;
use Drupal\goodreads\Form\ImportBookGoodReadsForm;

/**
 * Tests form to import books.
 *
 * @group goodreads
 */
class ImportBookGoodReadsFormTest extends KernelTestBase {

  public static $modules = [
    'system',
    'user',
    'field',
    'text',
    'node',
    'goodreads',
    'menu_ui'
  ];

  protected function setUp() {
    parent::setUp();

    $this->installSchema('system', 'action');
    $this->installConfig('menu_ui');

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installConfig('node');

    $this->installConfig('goodreads');

    $responseMock = (object) [
      'book' => (object) [
        'title' => 'Novo Livro',
        'description' => 'Descrição do novo livro.'
      ]
    ];

    $clientMock = $this->prophesize(GoodReadsClientService::class);
    $clientMock->getBookByISBN('1234')->willReturn($responseMock);

    $this->container->set('goodreads.client', $clientMock->reveal());
  }

  public function testImport() {
    $form_state = new FormState();
    $form_state->setValue('isbn', '1234');

    $this->container->get('form_builder')
      ->submitForm(ImportBookGoodReadsForm::class, $form_state);

    $bookNode = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->load(1);

    $this->assertContains('Novo Livro', $bookNode->get('title')->value);
    $this->assertContains('Descrição', $bookNode->get('body')->value);
  }

}
