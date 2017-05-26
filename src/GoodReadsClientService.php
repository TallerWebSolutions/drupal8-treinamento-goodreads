<?php

namespace Drupal\goodreads;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Class GoodReadsClientService.
 *
 * @package Drupal\goodreads
 */
class GoodReadsClientService implements GoodReadsClientServiceInterface {

  const key = 'pc79i9QZUmG5Eb7CWvnsGA';
  const urlBase = 'https://www.goodreads.com/';

  private $client;

  /**
   * Constructs a new GoodReadsClientService object.
   */
  public function __construct(Client $client) {
    $this->client = $client;
  }

  public function getBookByISBN($id) {
    $response = $this->client->get(self::urlBase . 'book/isbn/' . $id, [
      'query' => [
        'key' => self::key,
        'format' => 'xml'
      ],
    ]);

    return $this->parseResponse($response);
  }

  public function parseResponse(Response $response) {
    return new \SimpleXMLElement($response->getBody()->getContents());
  }

}
