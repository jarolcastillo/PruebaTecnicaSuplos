# suplosBackEnd
Prueba suplos desarrollador backend

Versión de php en el que se realizó 8.1.4
Base de datos Mysql

Funcionamiento:
En el apartado de bienes disponibles podemos ver el listado de todos los bienes tal cual se solicitó, al lado izquierdo con un filtro perfectamente funcional en los 3 campos, filtra tanto por ciudad, como por tipo, como por precio en bienes disponibles y en mis bienes, al filtrar aparece un botón que nos permite regresar a todos los bienes, permite guardar y eliminar bienes en tiempo real, aparte de esto nos permite descargar un reporte en formato Excel que nos muestra todos los bienes tanto disponibles como en mis bienes permitiendo filtrarlos.

Proceso para compilar:
1.Descargamos e instalamos xampp.
2.Instalamos git.
3.Iniciamos xammp y damos start en apache y Mysql.
4.Entramos donde instalamos xammp y en la carpeta htdocs creamos una carpeta con el nombre que le queramos dar al proyecto.
5.Entramos a la carpeta y le damos click derecho git bash here.
6.Copiamos el link que aparece en el botón verde que dice code y dentro de la ventana que se nos abrió en el punto anterior escribimos git clone y copiamos el link.
7.Entamos al navegador y escribimos localhost/ y ponemos el nombre de la carpeta donde descargamos el repositorio.
8.Por último, en el navegador escribimos localhost/phpmyadmin y aquí vamos a importar e importamos el archivo que está en el respositorio dentro de Bd.

Base de datos:
Se creó una base de datos con el nombre "intelcost_bienes" y una tabla en su interior que se llama "available_goods_saved" que se compone de dos campos: uno llamado "id" tipo int que es una llave primaria, y otro llamado "id_available" tipo int.
El archivo para importar se encuentra en la carpeta Bd.

Descarga de Excel:
se utilizó la librería PhpSpreadsheet para generar el archivo Excel descargable.
Al generar el archivo Excel se muestra un vínculo en la parte inferior del botón Generar Excel para poder descargar el Excel generado.


