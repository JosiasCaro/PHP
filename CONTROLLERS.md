# Rutas

## Crear una controlador por consola 

### PHP bin/console make:controller {nombre de controlador} (usar camel case)

- Crea un template para {nombre de controlador} como index 
- Crea un controller en src/controller

### En el conrtrolle aparece lo siguiente:

~~~
 Class HomeController extends AbstractController {

    #[Route ('/home'), name: 'home_inicio']
    public function index(): Response {
        return this->render('home/index.html.twig');
    }
 }
~~~

### Puedo agregar una nueva ruta

~~~
 Class HomeController extends AbstractController {

    #[Route ('/home'), name: 'home_inicio']
    public function index(): Response {
        return this->render('home/index.html.twig');
    }

    #[Route ('/home/saludo'), name: 'home_saludo']
    public function saludo(): Response {
        return this->render('home/saludo.html.twig');
    }
 }
~~~

## Puedo crear una ruta manual creando un nuevo archivo en src:

~~~
<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class {nombreDelArchivo} extends AbstractController {

    #[Route ('/template'), name: 'template_inicio']
    public function index(): Response {
        return this->render('template/index.html.twig');
    }

}
~~~

## Puedo crear una ruta con parametros:

~~~
<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class {nombreDelArchivo} extends AbstractController {

    #[Route ('/template'), name: 'template_inicio']
    public function index(): Response {
        return this->render('template/index.html.twig');
    }

    #[Route ('template/parametro/{id}'), name: 'template_parametro1']
    public function parametros(int $id): Response {
        return this->render('template/parametro1/par.html.twig');
    }

    tambien se puede dejar parametros por default

    #[Route ('template/parametro/{id}'), name: 'template_parametro2']
    public function parametros(int $id = 1): Response {
        return this->render('template/parametro2/par.html.twig');
    }

    #[Route ('template/parametro/{id}/{slug}'), name: 'template_parametro2']
    public function parametros(int $id = 1, String $slug = ''): Response {
        return this->render('template/parametro2/par.html.twig');
    }

}
~~~

## Puedo crear una ruta con una exception:

~~~
<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//si uso un throw new NotFoundHttpException
use Symfony\Component\HttpKernel\Exception\NotFoundHttpExcception;

Class {nombreDelArchivo} extends AbstractController {

    #[Route ('/template'), name: 'template_inicio']
    public function index(): Response {
        return this->render('template/index.html.twig');
    }

    #[Route ('template/exception'), name: 'template_exception']
    public function parametros(int $id): Response {
        throw $this->createNotFoundException('Esta URL no esta disponible');
    }

    o asi

    #[Route ('template/exception'), name: 'template_exception']
    public function parametros(int $id): Response {
        throw new NotFoundHttpException('Esta URL no esta disponible');
    }

}

?>
~~~

## Puedo listar las rutas creadas con 

### PHP bin/console debug:router