# Wäter

Trabajo final de PHP. Es la web de **Wäter**, una marca de cosmética inventada.

Hecho por **Alba Camacho Martín** con HTML, CSS, JavaScript, PHP y MySQL.

## Cómo entrar como administrador

- **Usuario:** administrador
- **Contraseña:** admin1212

Para probar como usuario normal, hay que crear una cuenta desde la página **Registro**.

## Cómo ponerlo en marcha

1. Copiar la carpeta `water` en `htdocs` (MAMP o XAMPP).
2. En phpMyAdmin, importar el archivo `bd/water_db.sql`.
3. Abrir `localhost:8888/water` (MAMP) o `localhost/water` (XAMPP).

Los datos de conexión están en `includes/conexion.php`. Están puestos para MAMP (puerto 8889, usuario `root`, contraseña `root`). Con XAMPP hay que cambiar el puerto a 3306 y dejar la contraseña vacía.

## Carpetas

- `bd` → la base de datos
- `css` → los estilos
- `js` → el JavaScript
- `img` → las imágenes
- `includes` → la conexión, la cabecera y el pie
- `paginas` → el resto de páginas (públicas, de usuario y de administrador)

## Qué se puede hacer

- **Visitante:** ver el inicio y las noticias, registrarse e iniciar sesión.
- **Usuario:** pedir, modificar y borrar sus citas, y cambiar sus datos y su contraseña en el perfil.
- **Administrador:** crear, modificar y borrar usuarios, citas y noticias.
