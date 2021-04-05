<?php

namespace App\Tests\Api\Product;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddTest extends WebTestCase
{
    public $id_current = null;

    public function testAdd()
    {
        $client = static::createClient();

        $crawler = $client->request('PUT', '/api/product/new/', [],[],
            ['CONTENT_TYPE' => 'application/json'],
            '{
                "name": "AirMax Classic Test",
                "price": 100,
                "description": "For testing",
                "imgUrl": "https://static.nike.com/a/images/t_prod_ss/w_960,c_limit,f_auto/b23107f5-3f24-468f-a882-03c08fb4ed2f/nike-x-travis-scott-air-max-270-cactus-trails-release-date.jpg"
            }'
        );
        $response = $client->getResponse();
        echo($response);
        // $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

}
