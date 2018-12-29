<?php

namespace GYL\UsuarioBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProveedorActividadControllerTest extends WebTestCase
{
    public function testAgregaractividad()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/agregarActividad');
    }

}
