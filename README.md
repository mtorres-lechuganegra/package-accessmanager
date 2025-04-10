# Lechuga Negra - AccessManager para Laravel

Este paquete de Laravel 11 proporciona una solución integral para la gestión de accesos en tus aplicaciones, permitiendo la definición de roles, módulos, permisos y rutas, con una lógica de relaciones muchos a muchos entre roles y permisos. Además, incluye un middleware para la validación de permisos en rutas, asegurando un control de acceso robusto y flexible.

## Características Principales

* **Gestión de Roles:** Define roles con distintos niveles de acceso, permitiendo una administración granular de privilegios.
* **Gestión de Módulos:** Organiza los permisos en módulos lógicos, facilitando la administración y comprensión de los mismos.
* **Gestión de Permisos:** Asigna permisos específicos a roles, controlando las acciones que cada rol puede realizar.
* **Gestión de Rutas:** Asocia permisos a rutas concretas, protegiendo el acceso a funcionalidades específicas de la aplicación.
* **Middleware de Validación:** Valida los permisos de las rutas mediante un middleware, asegurando que solo los usuarios autorizados puedan acceder a ellas.
* **Personalización del Modelo de Usuario:** Permite utilizar un modelo de usuario personalizado, adaptándose a las necesidades de cada proyecto.

## Instalación

1.  **Requerir el paquete vía Composer:**

    Abre tu terminal y ejecuta el siguiente comando para agregar el paquete a las dependencias de tu proyecto:

    ```bash
    composer require lechuganegra/accessmanager:@dev
    ```

    Este comando descargará el paquete y actualizará tu archivo `composer.json`.

2.  **Configurar el autoloading:**

    Edita tu archivo `composer.json` y añade el namespace del paquete al autoloading de PSR-4:

    ```json
    {
        "autoload": {
            "psr-4": {
                "App\\": "app/",
                "LechugaNegra\\AccessManager\\": "packages/lechuganegra/accessmanager/"
            }
        }
    }
    ```

    Luego, ejecuta el siguiente comando para regenerar el autoloading:

    ```bash
    composer dump-autoload
    ```

    Este paso asegura que Laravel pueda encontrar las clases del paquete.

3.  **Configurar el modelo de usuario (opcional):**

    Si deseas utilizar un modelo de usuario personalizado, publica el archivo de configuración del paquete:

    ```bash
    php artisan vendor:publish --provider="LechugaNegra\AccessManager\AccessManagerServiceProvider" --tag="config"
    ```

    Luego, edita el archivo `config/accessmanager.php` y modifica la entrada `user_entity` con la información de tu modelo:

    ```php
    'user_entity' => [
        'model' => App\Models\User::class, // Reemplaza con tu modelo
        'table' => 'users' // Reemplaza con el nombre de tu tabla
    ],
    ```

4.  **Ejecutar las migraciones:**

    Ejecuta las migraciones del paquete para crear las tablas necesarias en la base de datos:

    ```bash
    php artisan migrate --path=packages/lechuganegra/accessmanager/src/Database/Migrations
    ```

    **Nota:** Esta migración agrega un campo `admin` a la tabla de usuarios. Se recomienda no incluir este campo en el atributo `fillable` del modelo para evitar modificaciones accidentales.

5.  **Ejecutar el seeder:**

    Ejecuta el seeder del paquete para poblar las tablas con datos iniciales:

    ```bash
    php artisan db:seed --class="LechugaNegra\\AccessManager\\Database\\Seeders\\DatabaseSeeder"
    ```

6.  **Limpiar la caché:**

    Limpia la caché de configuración y rutas para asegurar que los cambios se apliquen correctamente:

    ```bash
    php artisan config:clear
    php artisan config:cache
    php artisan route:clear
    php artisan route:cache
    ```

## Uso

### Middleware de Validación

Para proteger tus rutas con el middleware de validación de permisos, utiliza `capability.access` en tus definiciones de rutas:

```php
Route::middleware(['capability.access'])->group(function () {
    // Rutas protegidas
});