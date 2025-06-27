# Formularios

## Instalar creacion de Formularios

### composer require symfonu/form

## Formulario simple

### Sin base de datos ni entidades de por medio


~~~
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  

// Se importan para utilizar campos de TextType y un Boton de submit
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormulariosController extends AbstractController {

    #[Route('/formularios', name: 'formularios_inicio')]
    public function index(): Response {
        return $this->render('formularios/index.html.twig');
    }

    #[Route('/formularios/simple', name: 'formularios_simple')]
    public function simple(): Response {

        //Variable donde se crea el formulario y sus campos con add({nombre del campo}, {tipo del campo}, [{etiqueta del campo}]) 

        $formulario = $this->createFormBuilder(null) //El Nulll indica que no se asocia a una entidad
                ->add('nombre', TextType::class, ['label'=>'Nombre'])
                ->add('correo', TextType::class, ['label' => 'E-Mail',])
                ->add('telefono', TextType::class, ['label' => 'Teléfono',])
                ->add('save', SubmitType::class)
                ->getForm();
        }
        return $this->render('formularios/simple.html.twig', compact('formulario'));
}
~~~

### En el html me voy a referir al formulario que le pase en compact() como {{form(formulario)}} y esto crea automaticamente el fomrulario, es limitada la manera de editarlo.

### Para poder dar estilos a los formularios puedo ir al archivo twig.yaml y agregar:

~~~
twig:
    form_themes: ['bootstrap_5_layout.html.twig']
~~~

### Esto solo le da los estilos de bootstrap, creando elementos de mas en el html si sigo usando {{form(formulario)}}

## Formularios con estilos propios

~~~
// Indicamos el metodo que se usa y la ruta a la que se dirige usando {{url('name')}} es el name de la ruta
<form method="POST" action="{{url('formularios_simple')}}">
    
    // Creo los campos
    <div class="form-group">

        // Para el for de aqui devo inspeccionar el id que se le asigno al imput y colocarlo aqui
        <label for="form_nombre">Nombre:</label>
        
        // Cargo los valores con un formato personalizado, e indicamos si el campo es obligatorio y el placeholder 
        (Estos dos no son necesarios si se quiere)
        {{form_widget(formulario.nombre, {"required": false,"attr":{ "placeholder":"Nombre" }})}}
    </div>
    
    <div class="form-group">
        <label for="form_correo">E-Mail:</label>
        {{form_widget(formulario.correo, {"required": false,"attr":{ "placeholder":"E-Mail" }})}}
    </div>
    
    <div class="form-group">
        <label for="form_telefono">Teléfono:</label>
        {{form_widget(formulario.telefono, {"required": false,"attr":{ "placeholder":"Teléfono" }})}}
    </div>
    
    <hr />  
    <button type="submit" title="Enviar" class="btn btn-primary"><i class="fas fa-check"></i> Enviar</button>
  </form>
~~~

## Procesar formularios

### Importamos Requesta para recibir los formularios y podes utilizar sus valores.

~~~
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

//Para recibir los campos del form en la ruta
use Symfony\Component\HttpFoundation\Request;

class FormulariosController extends AbstractController {

    #[Route('/formularios', name: 'formularios_inicio')]
        public function index(): Response {
            return $this->render('formularios/index.html.twig');
        }

    #[Route('/formularios/simple', name: 'formularios_simple')]
    
    //Le pasamos el request aqui 
    public function simple(Request $request): Response {
        $form = $this->createFormBuilder(null)
                ->add('nombre', TextType::class, ['label'=>'Nombre'])
                ->add('correo', TextType::class, ['label' => 'E-Mail',])
                ->add('telefono', TextType::class, ['label' => 'Teléfono',])
                ->add('save', SubmitType::class)
                ->getForm();

        //Permite recibir los campos del formulario
        $form->handleRequest($request);

        //si viene la petición POST del formulario
        if($form->isSubmitted()) {
            $campos = $form->getData();
            //print_r($campos);
            echo "Nombre: ".$campos['nombre']." | E-Mail: ".$campos['correo']." | Teléfono: ".$campos['telefono'];
            die();
        }
        return $this->render('formularios/simple.html.twig', compact('form'));
    }
}
~~~

## Proteccion de CSRF

### Agrega un campo con un token asociado al formulario para poder validarlo, que se genera automaticamente. Para ello intalamos "composer require symfony/security-csrf"

### Vamos a config/packages/framework.yaml y agregamos "csrf_protection: ~"

~~~
framework:
    secret: '%env(APP_SECRET)%'
    #csrf_protection: true
    http_method_override: false
    handle_all_throwables: true
    csrf_protection: ~
~~~

### Se puede hacer de dos maneras pero primero vemos la manual

~~~
<form method="POST" action="{{url('formularios_simple')}}">
    <div class="form-group">
        <label for="form_nombre">Nombre:</label>
        {{form_widget(formulario.nombre, {"required": false,"attr":{ "placeholder":"Nombre" }})}}
    </div>
    
    <div class="form-group">
        <label for="form_correo">E-Mail:</label>
        {{form_widget(formulario.correo, {"required": false,"attr":{ "placeholder":"E-Mail" }})}}
    </div>
    
    <div class="form-group">
        <label for="form_telefono">Teléfono:</label>
        {{form_widget(formulario.telefono, {"required": false,"attr":{ "placeholder":"Teléfono" }})}}
    </div>
    
    <hr />  

    //Le ponemos cualquier nombre al token y esto genera un token para el formulario 
    <input type="hidden" name="token" value="{{ csrf_token('generico') }}"/>

    <button type="submit" title="Enviar" class="btn btn-primary"><i class="fas fa-check"></i> Enviar</button>
  </form>
~~~

### Ahora ese token hay que usarlo para validarlo desde la ruta

~~~
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class FormulariosController extends AbstractController {

    #[Route('/formularios', name: 'formularios_inicio')]
        public function index(): Response {
            return $this->render('formularios/index.html.twig');
        }

    #[Route('/formularios/simple', name: 'formularios_simple')]
    public function simple(Request $request): Response {
        
        $form = $this->createFormBuilder(null)
                ->add('nombre', TextType::class, ['label'=>'Nombre'])
                ->add('correo', TextType::class, ['label' => 'E-Mail',])
                ->add('telefono', TextType::class, ['label' => 'Teléfono',])
                ->add('save', SubmitType::class)
                ->getForm();

        //Recibo el token 
        $submittedToken=$request->request->get('token');

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            
            // Aca validamos que el token sea el correcto, para eso usamos esta condicion, notar que en nombre 'generico'
            debe coincideir con el que se uso en el html
             if($this->isCsrfTokenValid('generico', $submittedToken)) {
                $campos = $form->getData();
                //print_r($campos);
                echo "Nombre: ".$campos['nombre']." | E-Mail: ".$campos['correo']." | Teléfono: ".$campos['telefono'];
                die();
            }else {
                die("Error del token")
            }
        }
        return $this->render('formularios/simple.html.twig', compact('form'));
    }
}
~~~

### EL token expira por lo que si la pagina queda abierta mucho tiempo puede generar errores. Otra cosa a tener en cuenta es que la validacion se hace por dentro del framework, no seria facil hackear el token, tambien que la validacion no es sobre los campos sino sobre el origen del formulario sea el correcto.

## Mensaje de Error

### Instalamos "composer require symfony http-foundation", no es necesario pero no hay problema si se lo instala.

~~~
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class FormulariosController extends AbstractController {

    #[Route('/formularios', name: 'formularios_inicio')]
        public function index(): Response {
            return $this->render('formularios/index.html.twig');
        }

    #[Route('/formularios/simple', name: 'formularios_simple')]
    public function simple(Request $request): Response {
        
        $form = $this->createFormBuilder(null)
                ->add('nombre', TextType::class, ['label'=>'Nombre'])
                ->add('correo', TextType::class, ['label' => 'E-Mail',])
                ->add('telefono', TextType::class, ['label' => 'Teléfono',])
                ->add('save', SubmitType::class)
                ->getForm();

        //Recibo el token 
        $submittedToken=$request->request->get('token');

        $form->handleRequest($request);

        if($form->isSubmitted()) {
             if($this->isCsrfTokenValid('generico', $submittedToken)) {
                $campos = $form->getData();
                //print_r($campos);
                echo "Nombre: ".$campos['nombre']." | E-Mail: ".$campos['correo']." | Teléfono: ".$campos['telefono'];
                die();
            }else {

                //Con esto creamos el mensaje de error flash, con nombre del mensaje a enviar y el valor
                // Recomendado crear 2 uno con estilos css (en la pagina de bootstap puedo ver los valores distintos para css)
                $this->addFlash('css', 'warning');
                $this->addFlash('mensaje', 'Ocurrió un error inesperado');

                // Para que los mensajes sean visualizados tengo que retornar la pagina 
                return $this->redirectToRoute('formularios_simple');
            }
        }
        return $this->render('formularios/simple.html.twig', compact('form'));
    }
}
~~~

### Para mostrar los mensajes me dirijo a la vista y agrego por arriba del formulario:

~~~
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{path('home_inicio')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{path('formularios_inicio')}}">Formularios</a></li>
    <li class="breadcrumb-item active" aria-current="page">Formularios Simple</li>
  </ol>
</nav>
 <h1>Formulario Simple </h1>
  
    // recorro todos los mensajes buscando el que le indico, aqui debe coincidir con el mismo nombre que el de la ruta
    {% for mensaje in app.flashes('mensaje') %}

        //Coloco este div para el mensaje y el conbre de la sesion con "alert-{{ app.flashes('css')[0] }}" 
        y muestro el mensaje
        <div class="alert alert-{{ app.flashes('css')[0] }} alert-dismissible fade show" role="alert">
        {{mensaje}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

    {% endfor %}
~~~

## Formulario Entity

### Puedo crear una entidad.php de cero 

~~~
<?php
namespace App\Entity;

class Persona
{
    protected $nombre;
    protected $correo;
    protected $telefono;

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }
    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
    }
    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): void
    {
        $this->telefono = $telefono;
    }

}
~~~

### Para usarlo desde una vista importo la entidad en el controllador

~~~
<?php

namespace App\Controller;

// Aca
use App\Entity\Persona

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class FormulariosController extends AbstractController {

    #[Route('/formularios/entity', name: 'formularios_entity')]
    public function entity(Request $request): Response {

        //Creo la instancia de tipo Persona
        $persona = new Persona();

        // Asocio el formulario a la entidad creada, por lo tantoo no reconoce campos que no esten en la entity
        $form = $this->createFormBuilder($persona)
                ->add('nombre', TextType::class, ['label'=>'Nombre'])
                ->add('correo', TextType::class, ['label' => 'E-Mail',])
                ->add('telefono', TextType::class, ['label' => 'Teléfono',])
                ->add('save', SubmitType::class)
                ->getForm();
        
        $submittedToken=$request->request->get('token');
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            if($this->isCsrfTokenValid('generico', $submittedToken))
            {
                $campos = $form->getData();

                // Puedo usar los metodos getters de la clase Persona
                echo "Nombre: ".$campos->getNombre()." | E-Mail: ".$campos->getCorreo()." | Teléfono: ".$campos->getTelefono();
                die();
            }else
            {
                $this->addFlash('css', 'warning');
                $this->addFlash('mensaje', 'Ocurrió un error inesperado');
                return $this->redirectToRoute('formularios_entity');
            }
            
        }
        return $this->render('formularios/entity.html.twig', compact('form'));
    }
}
~~~

## TypeForm con createForm

### Ya no usamos el createformBuilder() sino el createForm()

### Creamos un formulario con "php bin/console make:form" con el nombre y el tipo de entidad, Se crea un archivo en src/Form con el nombre del formulario

~~~
<?php

namespace App\Form;


use App\Entity\PersonaEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductoFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        // Aca creo los campos como en createFormBuilder()
        ->add('nombre', TextType::class, ['label'=>'Nombre'])
        ->add('correo', TextType::class, ['label' => 'E-Mail',])
        ->add('telefono', TextType::class, ['label' => 'Teléfono',])
        ->add('pais', ChoiceType::class,[
            'choices'=>[
                'Chile'=>1,
                'Perú'=>2,
                'México'=>3,
                'España'=>4,
                'Bolivia'=>5,
                'Venezuela'=>6,
                'Costa Rica'=>7,
                'Noruega'=>8
            ],'placeholder' => 'Seleccione.....'
        ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            //Asocia el formulario a la entidad Persona
            'data_class' => Producto::class,

            //Proteccion forzada de csrf
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
        ]);
    }
}
~~~

### Ahora en la ruta no hace falta crear el formulario con los campos solo se crea indicando el formulario y la entidad a la que se asocia

~~~
<?php

namespace App\Controller;

// Aca
use App\Entity\PersonaEntity

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class FormulariosController extends AbstractController {

    #[Route('/formularios/type-form', name: 'formularios_type_form')]
        public function type_form(Request $request): Response {
            
            // Creamos la instancia de tipo persona
            $persona = new PersonaEntity();
            
            //Creamos el form y lo asociamos a $persona que es la entidad
            $form=$this->createForm(PersonaEntityFormType::class, $persona);

            $form->handleRequest($request);
            $submittedToken = $request->request->get('token');
            
            if ($form->isSubmitted()) {
                if($this->isCsrfTokenValid('generico', $submittedToken)) {
                    $campos = $form->getData();
                    echo "Nombre: ".$campos->getNombre()." | E-Mail: ".$campos->getCorreo()." | Teléfono: ".$campos->getTelefono(); 
                    die();
                }else {
                    $this->addFlash('css','warning');
                    $this->addFlash('mensaje','Ocurrió un error inesperado');
                    return $this->redirectToRoute('formularios_type_form');
                }
                
            }
            return $this->render('formularios/type_form.html.twig', compact('form'));
        }
}
~~~

## Campo de tipo ChoiseType

### Permite crear campos selects y checkboxs

~~~
<?php

namespace App\Form;

use App\Entity\PersonaEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

// importo esto para el nuevo campo
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PersonaEntityFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
        ->add('nombre', TextType::class, ['label'=>'Nombre'])
        ->add('correo', TextType::class, ['label' => 'E-Mail',])
        ->add('telefono', TextType::class, ['label' => 'Teléfono',])

        ->add('pais', ChoiceType::class,[
            // Agrego los valores de manera estatica
            'choices'=>[
                'Chile'=>1,
                'Perú'=>2,
                'México'=>3,
                'España'=>4,
                'Bolivia'=>5,
                'Venezuela'=>6,
                'Costa Rica'=>7,
                'Noruega'=>8
                // Agregamos el placeholder para que lo tome como el primer campo
            ],'placeholder' => 'Seleccione.....'
        ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults(
            [
                'data_class'=>PersonaEntity::class,
                'csrf_protection' => true,
                'csrf_field_name' => '_token',

            ]);
    }
}
~~~

### Y en la vista agrego para el select:

~~~
<div class="form-group">
        <label for="form_pais">País:</label>
        {{form_widget(form.pais)}}
</div>
~~~

## Validacion con Assert y ValidatorInterface

### Instalamos "composer require symfony/validator", aceptamos y se instala

### Se validan los datos con asserts desde el archivo de la entidad para especificar que no este en blanco el campo o cosas similares

~~~
<?php
namespace App\Entity;

//importamos los asserts
use Symfony\Component\Validator\Constraints as Assert;

class PersonaEntityValidacion {
    //Indicamos que el campo no sea vacio y un msj de error por si ocurre
    #[Assert\NotBlank(message: 'El campo nombre es obligatorio')]
    protected $nombre;

    // Se valida si lo que se ingresa es un mail. {{ value }} indica el valor ingresado del campo
    #[Assert\NotBlank(message: 'El campo E-Mail es obligatorio'), Assert\Email(message: 'El E-Mail {{ value }} no es un correo válido.')]
    protected $correo;

    #[Assert\NotBlank(message: 'El campo Teléfono es obligatorio')]
    protected $telefono; 

    //Valida si el valor es positivo
    #[Assert\Positive(message: 'Debe seleccionar un país')]
    protected $pais;
}
~~~

### Creamos un formulario adecuado con la entidad e importamos "use App\Entity\PersonaEntityValidacion;" que es la class de la entidad

### Y creamos una ruta que haga el manejo del form

~~~
// En los import tenemos que agregar lo que se usa
use App\Entity\PersonaEntityValidacion;
use App\Form\PersonaValidationType;

// Aplica las validaciones que especificamos en la entidad #[Assert\...]
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/formularios/validacion', name: 'formularios_validacion')]
    public function validacion(Request $request, ValidatorInterface $validator): Response
    {
        $persona = new PersonaEntityValidacion();
        $form=$this->createForm(PersonaValidationType::class, $persona);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');
        if ($form->isSubmitted()) 
        {
            if($this->isCsrfTokenValid('generico', $submittedToken))
            {
                // Se guardan los errores de cada validacion que creamos en la entidad
                $errors = $validator->validate($persona);
                if(count($errors)>0)
                {
                    //Le pasamos los parametros de esta manera para enviar los errores y mostrar en la vista
                    return $this->render('formularios/validacion.html.twig', ['form'=>$form, 'errors'=>$errors]);
                }else
                {
                    $campos = $form->getData();
                echo "Nombre: ".$campos->getNombre()." | E-Mail: ".$campos->getCorreo()." | Teléfono: ".$campos->getTelefono()." | País: ".$campos->getPais(); 
                die();
                }
            }else
            {
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('formularios_validacion');
            }
        }
        
        //Le pasamos los parametros de esta manera para enviar errors con un array vacio y no tener ploblemas
        return $this->render('formularios/validacion.html.twig', ['form'=>$form, 'errors'=>array()]);
    }
~~~

### En la vista podemos mostrar los errores de la siguiente manera

~~~
{% if errors %}
        // Danger para que se vea rojo
        <div class="alert alert-danger alert-dismissible fade show">
          <ul>
            {% for error in errors %}
                <li>{{ error.message }}</li>
            {% endfor %}
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
{% endif %}
~~~


## Upload de archivos

### Crear un directorio personalizado para guardar los archivos en config/services.yaml

~~~
parameters:
    fotos_directory: '% kernel.project_dir%/public/uploads/fotos'
~~~

### Todo lo que se suba al servidor de manera estatica tiene que estar en public, se pueden crear todos los directorios que se quiera

### Configuramos la ruta para que reciba el form

~~~
//importo la entidad
use App\Entity\PersonaEntityUpload;

#[Route('/formularios/upload', name: 'formularios_upload')]
    public function upload(Request $request, ValidatorInterface $validator, SluggerInterface $slugger): Response {
        $persona = new PersonaEntityUpload();
        $form=$this->createForm(PersonaUploadType::class, $persona);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');
        if ($form->isSubmitted()) {
            if($this->isCsrfTokenValid('generico', $submittedToken)) {
                $errors = $validator->validate($persona);
                if(count($errors)>0) {
                    return $this->render('formularios/upload.html.twig', ['form'=>$form, 'errors'=>$errors]);
                }else {
                    
                    //Obtenemos el valor de la foto
                    $foto = $form->get('foto')->getData();

                    if($foto) {
                        // Obtengo el valor original de la foto que se subio (solo el nombre)
                        $originalName=pathinfo($foto->getClientOriginalName(), PATHINFO_FILENAME);

                        //Le asignamos un nombre nuevo a la imagen con la hora y la extension de la imagen
                        $newfilename = time().'.'.$foto->guessExtension();

                        //$newfilename = $slugger->slug($originalName).'.'.$foto->guessExtension();
                        try {
                           $foto->move(
                            //Indicamos la variable que configuramos, osea el directorio donde se sube y el nombre con el que se guarda
                            $this->getParameter('fotos_directory'),
                            $newfilename
                           );
                        } catch (FileException $e) {
                            throw new \Exception("mensaje", 'Ups ocurrió un error al intentar subir el archivo'); 
                        }

                        // Asignamos el nuevo nombre de la foto en la entidad
                        $persona->setFoto($newfilename);
                    }
                    $campos = $form->getData();
                    echo "Nombre: ".$campos->getNombre()." | E-Mail: ".$campos->getCorreo()." | Teléfono: ".$campos->getTelefono()." | País: ".$campos->getPais()." | Foto: ".$campos->getFoto(); ; 
                    die();
                }
            }else {
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('formularios_upload');
            }
        }
        return $this->render('formularios/upload.html.twig', ['form'=>$form, 'errors'=>array()]);
    }
~~~

### Configuramos el formulario para que tenga el campo de foto

~~~
<?php

namespace App\Form;
use App\Entity\PersonaEntityUpload;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

//importamos el FileType para recibir archivos de tipo imagen
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PersonaUploadType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nombre', TextType::class)
        ->add('correo', TextType::class)
        ->add('telefono', TextType::class)
        ->add('pais', ChoiceType::class, [
            'choices'  => [
                'Seleccione....'=>0,
                'Chile'=>1,
                'Perú'=>2,
                'México'=>3,
                'España'=>4,
                'Bolivia'=>5,
                'Venezuela'=>6,
                'Costa Rica'=>7,
                'Noruega'=>8
            ],
        ])

        //Agregamos el campo para las foto de tipo FileType
        ->add('foto', FileType::class, ['mapped'=>true]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => PersonaEntityUpload::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
        ]);
    }
}
~~~

### Agregamos el campo en la vista

~~~
    // Agregamos la propiedad enctype para que el formulario pueda subir archivos al servidor
  <form method="POST" action="{{url('formularios_upload')}}" enctype="multipart/form-data">
    <div class="form-group">
        <label for="form_nombre">Nombre:</label>
        {{form_widget(form.nombre, {"required": false,"attr":{ "placeholder":"Nombre" }})}}
    </div>
    <div class="form-group">
        <label for="form_correo">E-Mail:</label>
        {{form_widget(form.correo, {"required": false,"attr":{ "placeholder":"E-Mail" }})}}
    </div>
    <div class="form-group">
        <label for="form_telefono">Teléfono:</label>
        {{form_widget(form.telefono, {"required": false,"attr":{ "placeholder":"Teléfono" }})}}
    </div>
    <div class="form-group">
        <label for="form_pais">País:</label>
        {{form_widget(form.pais)}}
    </div>

    //Agregamos el campo de foto
    <div class="form-group">
        <label for="form_foto">Foto (JPG|PNG):</label>
        {{form_widget(form.foto)}}
    </div>
    <hr />  
     <input type="hidden" name="token" value="{{ csrf_token('generico') }}"/>
    <button type="submit" title="Enviar" class="btn btn-primary"><i class="fas fa-check"></i> Enviar</button>
  </form>
~~~

### Para validad que el archivo agregamos en la Entidad

~~~
#[Assert\File(
        maxSize: "10M",
        mimeTypes:
        [
            "image/jpeg",
            "image/jpg",
            "image/png", 
        ],
        mimeTypesMessage: 'La foto debe ser JPG|PNG',
        maxSizeMessage: 'La foto no puede pesar más de 10 Megabytes'
    )]
    protected $foto;
~~~