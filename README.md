# Lechuga Negra - AccessManager para Laravel

Este paquete de Laravel proporciona una solución integral para la gestión de accesos en tus aplicaciones, permitiendo la definición de roles, módulos, permisos y rutas, con una lógica de relaciones muchos a muchos entre roles y permisos. Además, incluye un middleware para la validación de permisos en rutas, asegurando un control de acceso robusto y flexible.

## Características Principales

* **Gestión de Roles:** Define roles con distintos niveles de acceso, permitiendo una administración granular de privilegios.
* **Banco de Módulos:** Organiza los permisos en módulos lógicos, facilitando la administración y comprensión de los mismos.
* **Banco de Permisos:** Asigna permisos específicos a roles, controlando las acciones que cada rol puede realizar.
* **Banco de Rutas:** Asocia permisos a rutas concretas, protegiendo el acceso a funcionalidades específicas de la aplicación.
* **Middleware de Validación:** Valida los permisos de las rutas mediante un middleware, asegurando que solo los usuarios autorizados puedan acceder a ellas.
* **Personalización del Modelo de Usuario:** Permite utilizar un modelo de usuario personalizado, adaptándose a las necesidades de cada proyecto.

## Instalación

1.  **Crear grupo de paquetes:**

    Crear la carpeta packages en la raíz del proyecto e ingresar a la carpeta:

    ```bash
    mkdir packages
    cd packages
    ```

    Crear el grupo de carpetas dentro de la carpeta creada, e ingresar a l carpeta:
    
    ```bash
    mkdir lechuganegra
    cd lechuganegra
    ```

2.  **Clonar el paquete:**

    Clonar el paquete en el grupo de carpetas creado y renombrarlo para que el Provider pueda registrarlo en la instalación

    ```bash
    git clone https://github.com/mtorres-lechuganegra/package-accessmanager.git accessmanager
    ```

3.  **Configurar composer del proyecto:**

    Dirígite a la raíz de tu proyecto, edita tu archivo `composer.json` y añade el paquete como repositorio:

    ```json
    {
        "repositories": [
            {
                "type": "path",
                "url": "packages/lechugaNegra/accessmanager"
            }
        ]
    }
    ```
    también deberás añadir el namespace del paquete al autoloading de PSR-4:

    ```json
    {
        "autoload": {
            "psr-4": {
                "LechugaNegra\\AccessManager\\": "packages/LechugaNegra/AccessManager/src/"
            }
        }
    }
    ```

4.  **Ejecutar composer require:**

    Después de editar tu archivo, abre tu terminal y ejecuta el siguiente comando para agregar el paquete a las dependencias de tu proyecto:

    ```bash
    composer require lechuganegra/accessmanager:@dev
    ```

    Este comando descargará el paquete y actualizará tu archivo `composer.json`.

5.  **Configurar el modelo de usuario (opcional):**

    Puedes editar el archivo `config/accessmanager.php` y modifica la entrada `user_entity` con la información de tu modelo:

    ```php
    'user_entity' => [
        'model' => App\Models\User::class, // Reemplaza con tu modelo
        'table' => 'users' // Reemplaza con el nombre de tu tabla
    ],
    ```

6.  **Ejecutar las migraciones:**

    Ejecuta las migraciones del paquete para crear las tablas necesarias en la base de datos:

    ```bash
    php artisan migrate --path=packages/lechuganegra/accessmanager/src/Database/Migrations
    ```

    **Nota:** Esta migración agrega un campo `admin` a la tabla de usuarios. Se recomienda no incluir este campo en el atributo `fillable` del modelo para evitar modificaciones accidentales.

7.  **Ejecutar el seeder:**

    Ejecuta el seeder del paquete para poblar las tablas con datos iniciales:

    ```bash
    php artisan db:seed --class="LechugaNegra\\AccessManager\\Database\\Seeders\\DatabaseSeeder"
    ```

8.  **Limpiar la caché:**

    Limpia la caché de configuración y rutas para asegurar que los cambios se apliquen correctamente:

    ```bash
    php artisan config:clear
    php artisan config:cache
    php artisan route:clear
    php artisan route:cache
    ```
    
9.  **Regenerar clases:**

    Regenerar las clases con el cargador automático "autoload"

    ```bash
    composer dump-autoload
    ```

## Uso

### Middleware de Validación

Para proteger tus rutas con el middleware de validación de permisos, utiliza `capability.access` en tus definiciones de rutas:

```php
Route::middleware(['capability.access'])->group(function () {
    // Rutas protegidas
});
```
