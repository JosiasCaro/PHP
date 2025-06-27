<?php

//incluye todo el archivo del parametro
require_once("conexionsql.php");
$d = new Datos();
$datos = $d->getDatos("select * from `categorias` where 1;");
print_r($datos);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <div class="card border-primary mb-3">
            <div class="card-header bg-primary text-white">
                <h1>PHP PDO</h1>
            </div>
            <div class="card-body text-primary">
                <p>
				<a href="add.php" class="btn btn-success">Crear</a>
				</p>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
						<tr>
							<th>ID</th>
							<th>Categoría</th>
                            <th>Acciones</th>
						</tr>
					</thead>
                    <tbody>
                        <?php
                        // Lista los datos de la consulta por campo
                        foreach($datos as $dato)
                        {
                           ?>
                           <tr>
                            <td><?php echo $dato['categoria_id']?></td>
                            <td><?php echo $dato['categoria']?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $dato['categoria_id']?>">Editar</a>
                                <a href="eliminar.php?id=<?php echo $dato['categoria_id']?>">Eliminar</a>
                            </td>
                           </tr>
                           <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>