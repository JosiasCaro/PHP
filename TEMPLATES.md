# Templates Twig

## Si se creo el archivo con composer en templates se crea la carpeta con el nombre del controlador sino hay que crear una carpeta con los archivos index.html.twig

~~~
#[Route ('/template'), name: 'template_inicio']
    public function index(): Response {
        return this->render('template/index.html.twig');
    }
~~~

# Interpolacion

## pasar parametros desde el controlador de rutas a la vista

~~~
#[Route ('/template'), name: 'template_inicio']
    public function index(): Response {
        return this->render('template/index.html.twig', ['nombre' => 'Cesar', 'apellido' => 'Cancino']);
    }

puedo hacerlo de manera estatica

$name = Cesar;
$lastname = Cancino;

#[Route ('/template'), name: 'template_inicio']
    public function index(): Response {
        return this->render('template/index.html.twig', ['nombre' => $name, 'apellido' => $lastname]);
    }
~~~

## En el archivo que renderizo le paso la variable que declare en el controlador

~~~
<h1> Hola {{nombre}} {{apellido}}</h1>
~~~

## Declarar una variable en twig

~~~
{% set edad = 21%}
<p> Tu edad es {{edad}} </p>
~~~

## Condicionales

~~~
{% if edad >= 18 %}
    es mayor de edad
{% else %}
    no es mayor de edad
{% endif %}
~~~

## Ciclo for (es necesario tener un arreglo para realizar esto)

~~~
{% for pais in paises %}
    
    <li> Nombre {{pais.nombre}} </li>

{% endfor %}
~~~

## Incluir el contenido de un archivo diferente (desde la carpeta templates o un archivo twig)

~~~
{{ include('include.html') }}
~~~

## Output Scaping (hacer que se renderice el contenido html de una variable)

~~~
{% set html = '<b>Hola soy un texto en negrita </b>' %}
{{html|raw}}
~~~

## Variables globales

### Voy a la carpeta config > packages > twig.yaml y agrego globals con las variables que qiero con 4 espacios de tabulacion

~~~
twig:
    deefault_path: '%kernel.project_dir%/templates'
    globals:
        URL:'www.google.com'
~~~

### y accedo desde un archivo como siempre

~~~
<p> Variable Custom: {{URL}} </p>
~~~

## Navegar con path

### Aqui es donde cobra relevancia el name de las rutas que creamos en los controladores, path tomara la ruta absoluta y la devuelve en el enlace

~~~
<a href='{{path('template_inicio')}}'> Ir a Inicio </a>
~~~

### En el caso de que la ruta tenga parametros

~~~
<a href='{{path('template_parametros2', {id: 12, slug: 'soy-slug'})}}'> Ir a Inicio </a>
~~~

# Implementacion de Templates

## Bloques

### En la creacion de templates utilizamos bloques que podremos exportar de manera que no repitamos codigo. (Cuando creamos un Controller se crea un archivo base.html.twig)

~~~
<!DOCTYPE html>
<html>
    <head>
        <meta charset = "UTF-8" />
        <meta name = "viewport" content = "width=device-width, initial-scale=1" />
        <title>Curso Symfony - {% block title %} {% endblock %}</title>
    </head>
</html>

{% block tituloArt %}
    <h1> Esto es un bloque </h1>
{% endblock %}

{% block body %}
    <h1> Esto es un bloque </h1>
{% endblock %}
~~~

## Extender un archivo twig

### para esto tomamos un archivo layout.html.twig por ejemplo y accedemos a el, en el escribimos la extencion del otro archivo y declaramos dentro de cada bloque el contenido que queremos que se exponga

~~~
{% extends 'base.html.twig' %}

{% block title %} Ejemplo de titulo {% endblock %}

{% block body %}
    agregamos algo al body
{% endblock %}
~~~

## Recursos estaticos (imgs, css, js)

### en l;a funcion asset le pasamos la ruta de la carpeta y el archivo y esta nos devuelve la ruta del archivo

~~~
<!DOCTYPE html>
<html>
    <head>
        <meta charset = "UTF-8" />
        <meta name = "viewport" content = "width=device-width, initial-scale=1" />
        <title>Curso Symfony - {% block title %} {% endblock %}</title>
        <link rel="stylesheet" href="{{asset('css/estilos.css')}}" />
        
        otra manera pero con la ruta de la url

        //<link rel="stylesheet" href="{{absolute_url(asset('css/estilos.css'))}}" />

    </head>
</html>

{% block tituloArt %}
    <h1> Esto es un bloque </h1>
{% endblock %}

{% block body %}
    <h1> Esto es un bloque </h1>
{% endblock %}
~~~