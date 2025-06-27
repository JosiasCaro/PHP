<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//Enviar Email
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

// http client
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ConsultarApiController extends AbstractController
{
    public function __construct( private HttpClientInterface $client ) {

    }
    
    #[Route('/consultar', name: 'consultar_inicio')]
    public function index(): Response
    {
        return $this->render('consultar_api/index.html.twig');
    }


    #[Route('/consultar/api', name: 'consultar_api')]
    public function consultar_api(): Response
    {
        $response = $this->client->request(
            'GET', // tipo de REQUEST
            'https://www.api.tamila.cl/api/categorias',
            [
                'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzYsImlhdCI6MTc1MDYwMTEyOCwiZXhwIjoxNzUzMTkzMTI4fQ.mnc1FXeU9_QE3elkCK6Mxpq8bOlIHli8W_0S2qGZ7Hk'
                ]
            ]
        );

        $statusCode = $response->getStatusCode();
        

        return $this->render('consultar_api/api_rest.html.twig', compact('response'));
    }
}
