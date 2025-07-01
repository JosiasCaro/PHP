# [Doctrine ORM](doctrine-project.org)

## Instalacion

### Instalamos Doctrine "composer require symfony/orm-pack" y "composer require --dev symfony/maker-bundle"

### En el archivo .env configuramos el archivo con la bdd que usaremos y las credenciales para comunicarme con esta

~~~
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

//Este es el de mysql  USER:CONTRASEÑA:HOST:PUERTO/NOMBRE BDD
DATABASE_URL="mysql://pmauser:ZeQFHSYtdSUoXRtg:9K3@localhost:3306/curso_symfony_api?serverVersion=8.0"

#DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=15&charset=utf8"
~~~

### Observacion: Si ya tengo una bdd que no esta normalizada con id, no voy a poder usarla con doctrine o sera dificil de utilizar

## Configuracion de archivo php.ini

### Comentar extension curl, mbstring, mysqli, openssl pdo_mysql, soap (symfony usas sus formas de conexion a las bdd)

## Crear la base de datos

### "php bin/console doctrine:database:create" (crea la base de datos que se indica en el .env)

## Entidades

### Para podes interacturar con las tablas debo crear entidades desde symfony (son como objetos) para ello usamos "php bin/console make:entity" luego nos preguntan el nombre de la entidad, luego me pregunta a cerca de las propiedades y el tipo de dato que van a ser (con "?" me aparecen todos los tipos de datos), la longitud, si puede ser null y vuelve a preguntar si queres otra propiedad.

### Por cada campo se crea un metodo getter y setter, supongamos que cree nombre como string, longitud 100, que no sea null y slug de tipo string con 100 de longitud y no null, lo demas se genera con el comando 

~~~
<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriaRepository::class)]
class Categoria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]  //Genera el valor automatico
    #[ORM\Column] //Indica que es un campo de la tabla
    private ?int $id = null;

    #[ORM\Column(length: 100)] //Campo de la tabla de longitud 100
    private ?string $nombre = null;

    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
~~~

## Migraciones

### Todavia no se creo la tabla en phpMyAdmin o en sgbd (sistema gestor de base de datos), la entidad es la representacion a nivel objeto de una tabla.

### Para que la creacion o modificacion de la entidad tenga impacto en la sgbd debo ejecutar "php bin/console make:migration", crea la carpeta migracion en la raiz del proyecto con la estructura de la tabla que se debe generar

### Para aplicar la migracion ejecuto "php bin/console doctrine:migration:migrate", decimos que si y se ejecuta la migracion.

### Para relacionar tablas tengo que crear entidades con sus debidos campos, ejemplo Categoria y Productos, creo Producto con los campos nombre, slug, precio, stock, descripcion  y para ligarlo a Categorias creo la propiedad categoria e indico que es de tipo relation luego especificamos de la clase Categoria, el tipo de relacion (1:1, 1:n, n:1 o n:n), esta sera de ManyToOne y no puede ser null y que no se cree un campo nuevo en Categoria. Con esto tendriamos la tabla de productos asociada a Categoria.

### Observacion: En la tabla Producto el campo categoria sera categoria_id y de tipo llave foranea


~~~
<?php

namespace App\Entity;

use App\Repository\ProductoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductoRepository::class)]
class Producto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?int $precio = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descripcion = null;

    #[ORM\ManyToOne] //Indica el tipo de relacion muchos a uno (Muchos productos a una categoria)
    #[ORM\JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrecio(): ?int
    {
        return $this->precio;
    }

    public function setPrecio(int $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }
}
~~~

### Esta seria la tabla que se crea y realizamos la migracion.

## Producto foto

### Lo que guardamos en una bdd para referir una foto no es la foto sino el nombre del archivo y se guarda en una carpeta del proyecto.

~~~
<?php

namespace App\Entity;

use App\Repository\ProductoFotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductoFotoRepository::class)]
class ProductoFoto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $foto = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Producto $producto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): self
    {
        $this->producto = $producto;

        return $this;
    }
}
~~~

## Listar categorias con EntityManagerInterface

### En el archivo de controlador de rutas importamos "use App\Entity\Categoria", "use Doctrine\ORM\EntityManagerInterface"

~~~
use App\Entity\Categoria;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineController extends AbstractController {

    // Es mas recomendado hacer una variable global que pasar como parametro a $em
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em=$em;
    }
    
    #[Route('/doctrine', name: 'doctrine_inicio')]
    public function index(): Response
    {
        return $this->render('doctrine/index.html.twig');
    }

    #[Route('/doctrine/categorias', name: 'doctrine_categorias')]
    public function categorias(): Response {

        //$datos = $em->getRepository(Categoria::class)->findAll(); (trae todas las categorias)

        // Este filtra las categorias con lo del primer array y lo ordena con lo del segundo

        $datos = $this->em->getRepository(Categoria::class)->findBy(array(), array('id'=>'desc'));

        //print_r($datos);exit;

        return $this->render('doctrine/categorias.html.twig', compact('datos'));
    }
}
~~~

### EN la vista para listar las cosas

~~~
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead>
                <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
                </tr>
        </thead> 
        <tbody>
            {% for dato in datos %}
                <tr>
                    <td>{{dato.id}}</td>
                    <td>{{dato.nombre}}</td>
                    <td style="text-align:center;">
                        <a href="{{path('doctrine_categorias_editar', {id: dato.id})}}"><i class="fas fa-edit"></i></a>
                        <a href="javascript:void(0);" onclick="confirmarSweet('Realmente desea eliminar este registro?','{{path('doctrine_categorias_eliminar', {id: dato.id})}}')"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
            {% endfor %}
        </tbody>
    </table>
  </div>
~~~

## Agregar categorias

### Creo un formulario con "php bin/console make:form", agrego el nombre de la class del formulario y lo asociamos a la entidad Categoria

~~~
<?php

namespace App\Form;

use App\Entity\Categoria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class CategoriaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nombre', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categoria::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
        ]);
    }
}
~~~

### Creo una nueva ruta

~~~
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;  
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface; 


#[Route('/doctrine/categorias/add', name: 'doctrine_categorias_add')]
    public function categorias_add(Request $request, ValidatorInterface $validator, SluggerInterface $slugger): Response
    {
        $entity= new Categoria();
        $form = $this->createForm(CategoriaFormType::class, $entity);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');
        if ($form->isSubmitted()) 
        {
            if($this->isCsrfTokenValid('generico', $submittedToken))
            {
                $errors = $validator->validate($entity);
                if (count($errors) > 0) 
                {
                    return $this->render('doctrine/categorias_add.html.twig', compact('form', 'errors' ));
                }else
                {
                    // Obtenemos los campos del formulario
                    $campos = $form->getData();

                    // Modificamos los valores de la entidad basado en los campos
                    $entity->setNombre($campos->getNombre());
                    // Hacemos que el slug sea en minuscula
                    $entity->setSlug($slugger->slug(strtolower($campos->getNombre()) ));
                    
                    // Se ejecutan siempre que sea para crear, editar o eliminar para que se apliquen los cambios
                    $this->em->persist($entity);
                    $this->em->flush();
                    
                    // Mensaje de exito
                    $this->addFlash('css','success');
                    $this->addFlash('mensaje','Se creó el registro exitosamente');
                    return $this->redirectToRoute('doctrine_categorias_add');
                }
            }else
            {
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('doctrine_categorias_add');
            }
        }
        return $this->render('doctrine/categorias_add.html.twig', ['form'=>$form, 'errors'=>array()]);
    }
~~~

### En la entidad de Categoria

~~~
<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoriaRepository::class)]
class Categoria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    //Se valida que el campo no este en blanco
    #[ORM\Column(length: 100), Assert\NotBlank(message: 'El nombre es obligatorio')]
    private ?string $nombre = null;

    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
~~~

### En la vista

~~~
<div class="row">
        <form action="{{ url('doctrine_categorias_add') }}" method="POST">

            // Errores de Validacion de los campos
            {% if errors %}
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul>
                        {% for error in errors %}
                            <li>{{ error.message }}</li>
                        {% endfor %}

                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            {% endif %}

            // Mensajes de flass generados por Errores
            {% for message in app.flashes('mensaje') %}
                <div class="alert alert-{{ app.flashes('css')[0] }} alert-dismissible fade show" role="alert">
                    {{ message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            {% endfor %}
            <div class="form-group">
                <label for="categoria_form_nombre">
                    Nombre
                </label>
                {{ form_widget(form.nombre, {"required": false, "attr": {
    "placeholder": "Nombre" }}) }}
                <div>

                    <hr/>
                    <input type="hidden" name="token" value="{{ csrf_token('generico') }}"/>
                    <input type="submit" value="Enviar" class="btn btn-primary"/>
                </form>
            </div>
~~~

## Editar categorias

~~~
#[Route('/doctrine/categorias/editar/{id}', name: 'doctrine_categorias_editar')]
    public function categorias_editar(int $id, Request $request, ValidatorInterface $validator, SluggerInterface $slugger): Response
    {
        //select * from categoria where id=$id;
        $entity= $this->em->getRepository(Categoria::class)->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(
                'Esta URL no está disponible'
            );
        }
        $form = $this->createForm(CategoriaFormType::class, $entity);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');
        if ($form->isSubmitted()) 
        {
            if($this->isCsrfTokenValid('generico', $submittedToken))
            {
                $errors = $validator->validate($entity);
                if (count($errors) > 0) 
                {
                    // Le pasamos la entidad
                    return $this->render('doctrine/categorias_editar.html.twig', compact('form', 'errors', 'entity'));
                }else
                {
                    $campos = $form->getData();
                    $entity->setNombre($campos->getNombre());
                    $entity->setSlug($slugger->slug(strtolower($campos->getNombre()) ));
                    // Solo con flush se aplican los cambios seteados
                    //$this->em->persist($entity);
                    $this->em->flush();
                    $this->addFlash('css','success');
                    $this->addFlash('mensaje','Se modificó el registro exitosamente');
                    // Le pasamos la entidad
                    return $this->redirectToRoute('doctrine_categorias_editar', ['id'=>$entity->getId()]);
                }
            }else
            {
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                // Le pasamos la entidad
                return $this->redirectToRoute('doctrine_categorias_editar', ['id'=>$entity->getId()]);
            }
        }
        return $this->render('doctrine/categorias_editar.html.twig', ['form'=>$form, 'errors'=>array(), 'entity'=>$entity]);
    }
~~~

### En la vista

~~~
// le damos el id con la variable entity
<form action="{{ url('doctrine_categorias_editar', {id: entity.id}) }}" method="POST">
            {% if errors %}
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul>
                        {% for error in errors %}
                            <li>{{ error.message }}</li>
                        {% endfor %}

                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            {% endif %}

            {% for message in app.flashes('mensaje') %}
                <div class="alert alert-{{ app.flashes('css')[0] }} alert-dismissible fade show" role="alert">
                    {{ message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            {% endfor %}
            <div class="form-group">
                <label for="categoria_form_nombre">
                    Nombre
                </label>
                {{ form_widget(form.nombre, {"required": false, "attr": {
    "placeholder": "Nombre" }}) }}
                <div>

                    <hr/>
                    <input type="hidden" name="token" value="{{ csrf_token('generico') }}"/>
                    <input type="submit" value="Enviar" class="btn btn-primary"/>
                </form>
~~~

## Eliminar Categorias de la bdd

### Creamos la ruta

~~~
#[Route('/doctrine/categorias/eliminar/{id}', name: 'doctrine_categorias_eliminar')]
    public function categorias_eliminar(int $id, Request $request)
    {
        $entity= $this->em->getRepository(Categoria::class)->find($id);
        
        // Pregunto si el registro existe
        if (!$entity) {
            throw $this->createNotFoundException(
                'Esta URL no está disponible'
            );
        }
        // Elimina el registro
        $this->em->remove($entity);
        $this->em->flush();
        $this->addFlash('css','success');
        $this->addFlash('mensaje','Se eliminó el registro exitosamente');
        return $this->redirectToRoute('doctrine_categorias');
    }
~~~

### En la vista agregamos el boton de eleminar que llama una funcion de js para confirmar si se realiza la eliminacion, en caso que sea si redirecciona a la ruta indicada

~~~
<a href="javascript:void(0);" onclick="confirmarSweet('Realmente desea eliminar este registro?','{{path('doctrine_categorias_eliminar', {id: dato.id})}}')"><i class="fas fa-trash-alt"></i></a>
~~~