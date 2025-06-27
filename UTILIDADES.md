# Utilizades

## Envio de mails

### Primero hay que crear un controlador para el envio de mails

### Se debe utilizar una cuenta de SMTP para enviar correos, puedo crear una cuenta en la [Pagina de mail trap](mailtrap.io)

### En consola intalo lo siguiente "composer require symfony/mailer" esto crea un archivo en config/packages/mailer.yaml

### Al entrar al archivo me se creo una variable MAILER_DSN que debo descomentar en el archivo .env

~~~
// en el mail que utilices el arroba se escribe como %40

MAILER_DSN=smtp://{mailSmtpUtilizado}:contraseña@{servidor}:puerto
~~~

### Antes de configurar la ruta debo ir a config/packages/messenger.yaml y comentar la linea:

~~~
# Symfony\Component\Mailer\Messenger\SendEmailMessage: async
~~~

### En la ruta configuramos el envio de mail e importamos lo necesario

~~~
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


Class {nombreDelArchivo} extends AbstractController {

    #[Route ('/utilidades'), name: 'utilidades_inicio']
    public function index(): Response {
        return this->render('utilidades/index.html.twig');
    }

    #[Route ('/utilidades/enviar-mail'), name: 'utilidades_email']
    public function enviar_mail(MailerInterface $mailer): Response {

        $email =(new Email())
                ->from(new Address('{correoDeEnvio}','{PersonaQueLoEnvia}'))
                ->to('{mailReceptor}')
                ->subject('{Asunto}')
                ->text('{mensaje}')
                ->html('{codigoHtml}');
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e){
            die($e);
        }
        
        return this->render('utilidades/enviar_email.html.twig');
    }

}
~~~

## Consumir API Rest con HTTP client

### Client API Rest

### instalamos 'composer require symfony/http-client', nos da muchas herramientas para consumir una API rest

### Usamos una api de ejemplo del curso [API](https://www.api.tamila.cl/api/login)

### usamos estos datos de login que devuelve un token en POSTMAN, hacemos un request de tipo POST como raw:

~~~
{
    "correo": "info@tamila.cl",
    "password": "p2gHNiENUw"
}
~~~

### configuramos la terminal para que nos devuelva la respuesta de tipo JSON:

~~~
{
    "estado": "ok",
    "nombre": "César Hans Cancino Zapata",
    "correo": "info@tamila.cl",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzYsImlhdCI6MTc1MDYwMTEyOCwiZXhwIjoxNzUzMTkzMTI4fQ.mnc1FXeU9_QE3elkCK6Mxpq8bOlIHli8W_0S2qGZ7Hk"
}
~~~

### El token es temporal asi que solo dejo la respuesta de ejemplo, luego vamos a [API](https://www.api.tamila.cl/api/categorias) headers y agrego la key Authorization y el value Bearer con el token para hacer un request.

### Para consumirla desde una ruta necesito importar lo siguiente

~~~
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
use Symfony\Contracts\HttpClientInterface;

Class {nombreDelArchivo} extends AbstractController {

    //Con este constructor guardamos una instancia HttpClientInterface y la guardamos en $client para hacer consultas
    public function __construct( private HttpClientInterface $client ) {

    }

    #[Route ('/utilidades'), name: 'utilidades_inicio']
    public function index(): Response {
        return this->render('utilidades/index.html.twig');
    }

    #[Route ('/utilidades/api-rest'), name: 'utilidades_api_rest']
    public function api_rest(): Response {

        $response = $this->client->request(
            'GET', // tipo de REQUEST
            'https://www.api.tamila.cl/api/categorias',
            [
                'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzYsImlhdCI6MTc1MDYwMTEyOCwiZXhwIjoxNzUzMTkzMTI4fQ.mnc1FXeU9_QE3elkCK6Mxpq8bOlIHli8W_0S2qGZ7Hk'
                ]
            ]
        );

        $statusCode = $response->getStatusCode()
        
        //imprimo el estado de la respuesta
        echo $statusCode; exit();

        return this->render('utilidades/api_rest.html.twig', compact('response'));
    }

}
~~~

### En la vista agrego lo siguiente para ver los datos

~~~
<div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                // Hago que la respuesta sea un array
                {% for dato in response.toArray() %}
                    <tr>
                        <td>{{ dato.id }}</td>
                        <td>{{ dato.nombre }}</td>
                        <td style="text-align:center;">
                            <a href="{{ path('utilidades_api_rest_editar', {id: dato.id}) }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="confirmaAlert('Realmente desea eliminar este registro?', '{{ path('utilidades_api_rest_delete', {id: dato.id}) }}')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                {% endfor %}
            </tbody>
        </table>
    </div>
~~~

## Crear Regiistros POST