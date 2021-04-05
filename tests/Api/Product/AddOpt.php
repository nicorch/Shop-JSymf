<?php

namespace App\Tests\Api\Product;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddOpt extends WebTestCase
{
    public function testAddOpt()
    { 
        $client = static::createClient();

        $client->request('GET', '/api/product/getlast');

        $response = $client->getResponse();

        $responseData = json_decode($response->getContent(), true);

        
        $json = '{
            "product": '.$responseData['id'].',
            "name": "Signature spéciale",
            "price_supp": 25.99
        }';
        echo($json);
        $client->request('PUT', '/api/option/new/', [],[],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );
        echo($client->getResponse());
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

}
