<?php

namespace App\Tests\Api\Product;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RmTest extends WebTestCase
{
    public $id_current = null;

    public function testRm()
    {
        $client = static::createClient();
        $client->request('GET', '/api/product/getlast');

        $response = $client->getResponse();

        $responseData = json_decode($response->getContent(), true);

        $client->request('DELETE', '/api/product/rm/'.$responseData['id']);
        $response = $client->getResponse();
        // $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        // $this->assertJson($response->getContent());
        $this->assertEquals(202, $client->getResponse()->getStatusCode());
    }

}
