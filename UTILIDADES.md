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

### Creamos un formulario  con "php bin/console make:form", le damos nombre y no lo asociamos a ninguna entidad. 

### Buildeamos solo con el campo nombre para la categoria

~~~
class CategoriaApiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nombre', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
        ]);
    }
}
~~~

~~~

//importamos el formulario
use App\Form\CategoriasApiType;

#[Route('/utilidades/api-rest/crear', name: 'utilidades_api_rest_crear')]
    public function api_rest_crear(Request $request): Response {

        // Creamos el formulario sin asociarlo a una entidad
        $form = $this->createForm(CategoriaAPiType::class, null);
        
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');
        
        if ($form->isSubmitted()) {
            if($this->isCsrfTokenValid('generico', $submittedToken)) {
                $campos = $form->getData(); 
                $response=$this->client->request(
                    'POST', //Hacemos la peticion de tipo POST
                    'https://www.api.tamila.cl/api/categorias', // A la URL de la api para categorias
                    [
                        //Le paso los valores con formato json
                        'json'=>['nombre'=>$campos['nombre']],
                        'headers'=>
                        [
                            'Authorization'=>'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzYsImlhdCI6MTY3OTg1NDU3NSwiZXhwIjoxNjgyNDQ2NTc1fQ.KqkepI4Q5Dls4SvR39JpdLP0P-HQ473Upen1Q3T2gBM'
                        ]
                    ]

                );
                $this->addFlash('css','success');
                $this->addFlash('mensaje','Se creó el registro exitosamente');
                return $this->redirectToRoute('utilidades_api_rest_crear');
            }else {
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('utilidades_api_rest_crear');
            }
        }

        return $this->render('utilidades/api_rest_add.html.twig', compact('form'));
    }
~~~

### En la vista creamos el formulario

~~~
<form action="{{ url('utilidades_api_rest_crear' ) }}" method="post">
     {% for message in app.flashes('mensaje') %}
        <div class="alert alert-{{ app.flashes('css')[0] }} alert-dismissible fade show" role="alert">
            {{ message }} 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    {% endfor %}
            
  <div class="form-group">   
      <label for="persona_entity_form_nombre"> Nombre  </label>
    {{ form_widget(form.nombre, {"required": false, "attr": {
        "placeholder": "Nombre"
    }}) }}
  <div>
   
    <input type="hidden" name="token" value="{{ csrf_token('generico') }}"/>
    <input type="submit" value="Enviar" class="btn btn-primary" />
</form>
~~~

## Editar registros con PUT

### En la ruta vamos a recibir un parametro que va a ser el id de la categoria a editar

~~~
#[Route('/utilidades/api-rest/editar/{id}', name: 'utilidades_api_rest_editar')]
    public function api_rest_editar(Request $request, int $id): Response {
        
        // Obtengo los datos via GET
        $datos = $this->client->request(
            'GET',
            'https://www.api.tamila.cl/api/categorias/'.$id, [
                 
                'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzYsImlhdCI6MTY3OTg1NDU3NSwiZXhwIjoxNjgyNDQ2NTc1fQ.KqkepI4Q5Dls4SvR39JpdLP0P-HQ473Upen1Q3T2gBM',
                ]]
        );

        $form = $this->createForm(CategoriaAPiType::class, null);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');
        if ($form->isSubmitted()) 
        {
            if($this->isCsrfTokenValid('generico', $submittedToken))
            {
                $campos = $form->getData(); 
                $response=$this->client->request(
                    'PUT',
                    'https://www.api.tamila.cl/api/categorias/'.$id,
                    [
                        'json'=>['nombre'=>$campos['nombre']],
                        'headers'=>
                        [
                            'Authorization'=>'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzYsImlhdCI6MTY3OTg1NDU3NSwiZXhwIjoxNjgyNDQ2NTc1fQ.KqkepI4Q5Dls4SvR39JpdLP0P-HQ473Upen1Q3T2gBM'
                        ]
                    ]

                );
                $this->addFlash('css','success');
                $this->addFlash('mensaje','Se modificó el registro exitosamente');

                //Agregamos en la lista de parametros el id para cuando redireccionamos
                return $this->redirectToRoute('utilidades_api_rest_editar', ['id'=>$id]);
            }else
            {
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');

                //Agregamos en la lista de parametros el id para cuando redireccionamos
                return $this->redirectToRoute('utilidades_api_rest_editar', ['id'=>$id]);
            }
        }

        return $this->render('utilidades/api_rest_editar.html.twig', compact('form', 'datos'));
    }
~~~

### En la vista que se ven las categorias

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
                {% for dato in response.toArray() %}
                    <tr>
                        <td>{{ dato.id }}</td>
                        <td>{{ dato.nombre }}</td>

                        <td style="text-align:center;">

                            // Agregamos este icono que pasa el id del dato en la url para modificarlo 
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

### Ahora en la ruta donde edito

~~~
// Le pasamos el id que recibimos por parametros de la ruta
<form action="{{ url('utilidades_api_rest_editar',{'id': datos.toArray().id}  ) }}" method="post">
     {% for message in app.flashes('mensaje') %}
        <div class="alert alert-{{ app.flashes('css')[0] }} alert-dismissible fade show" role="alert">
            {{ message }} 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    {% endfor %}
            
  <div class="form-group">   
        <label for="persona_entity_form_nombre"> Nombre  </label>
        {{ form_widget(form.nombre, {"required": false, "attr": {

            //Se coloca en el campo el valor de la categoria que se selecciono
            "placeholder": "Nombre", value: datos.toArray.nombre
        }}) }}
  <div>

    <input type="hidden" name="token" value="{{ csrf_token('generico') }}"/>
    <input type="submit" value="Enviar" class="btn btn-primary" />
 </form>
~~~

## Eliminar un registro con DELETE

### Usamos el metodo http de DELETE

~~~
#[Route('/utilidades/api-rest/delete/{id}', name: 'utilidades_api_rest_delete')]
    public function api_rest_delete(Request $request, int $id): Response
    {
        $this->client->request(
            'DELETE', // Usamos metodo DELETE
            'https://www.api.tamila.cl/api/categorias/'.$id, [ //Indicando el id
                'headers' => [
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzYsImlhdCI6MTY3OTg1NDU3NSwiZXhwIjoxNjgyNDQ2NTc1fQ.KqkepI4Q5Dls4SvR39JpdLP0P-HQ473Upen1Q3T2gBM',
                ]]
        );
        $this->addFlash('css','success');
        $this->addFlash('mensaje','Se eliminó el registro exitosamente');
        return $this->redirectToRoute('utilidades_api_rest');
    }
    #[Route('/utilidades/filesystem', name: 'utilidades_filesystem')]
    public function filesystem(): Response
    {
       
        $filesystem = new Filesystem();
        $ejemplo_mkdir = "/var/www/html/clientes/tamila/pruebas/symfony/midirectorio";
        if(!$filesystem->exists($ejemplo_mkdir))
        {
            $filesystem->mkdir($ejemplo_mkdir, 0777);
        }else
        {
            $filesystem->copy('/var/www/html/clientes/tamila/pruebas/symfony/descarga_cli.png', $ejemplo_mkdir."/descarga_cli.png");
            $filesystem->rename($ejemplo_mkdir."/descarga_cli.png", $ejemplo_mkdir."/descarga_cli_modificado2.png");
            //$filesystem->remove([$ejemplo_mkdir."/descarga_cli_modificado2.png"]);
        }
        
        return $this->render('utilidades/filesystem.html.twig');
    }
~~~

### En la vista agregamos el boton para borrar y los mensajes

~~~
{% for message in app.flashes('mensaje') %}
    <div class="alert alert-{{ app.flashes('css')[0] }} alert-dismissible fade show" role="alert">
        {{ message }} 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{% endfor %}


<a href="javascript:void(0);" onclick="confirmaAlert('Realmente desea eliminar este registro?', '{{ path('utilidades_api_rest_delete', {id: dato.id}) }}')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
~~~

## FileSystem Component

### Permite interactuar con el sistema de archivos del SO a modo de servidor