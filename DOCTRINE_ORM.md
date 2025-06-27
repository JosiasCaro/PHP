# [Doctrine ORM](doctrine-project.org)

## Instalacion

### Instalamos Doctrine "composer require symfony/orm-pack" y "composer require --dev symfony/maker-bundle"

### En el archivo .env configuramos el archivo con la bdd que usaremos y las credenciales para comunicarme con esta

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
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
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

### Para que la creacion o modificacion de la entidad tenga impacto en la sgbd tebo ejecutar "php bin/console make:migration", crea la carpeta migracion en la raiz del proyecto con la estructura de la tabla que se debe generar

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

    #[ORM\ManyToOne]
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

## Agregar categorias

### Creo un formulario con "php bin/console make:form", agrego el nombre de la class del formulario y lo asociamos a la entidad Categoria