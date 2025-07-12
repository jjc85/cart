# API REST para la gestión de carrito de la compra con vistas basicas para añadir productos, ver el carrito y persistir un pedido.

Esta aplicación expone varios endpoints para la gestión de un carrito de la compra.

## ✨ Requisitos técnicos
- Git
- Docker version 28.3.0
- Docker Compose version v2.37.1

Si no vas a usar Docker debes tener instalado previamente lo siguiente:
- Composer (v2.8.9)
- PHP >= 8.3 (con extension pdo_mysql)
- Servidor web (Por ejemplo Symfony CLI con el servidor web integrado de symfony)
- MySQL 8.0.
- Mailhog (Opcional) para capturar correos electrónicos en local al realizar un pedido.
 
## 🛠️ Primeros pasos
1.  Clona el repositorio:
    ```bash
    git clone https://github.com/jjc85/cart.git
    ```
2.  Navega al directorio del proyecto:
    ```bash
    cd cart
    ```
## 🛠️ Instalación con docker (preparación del entorno de desarrollo y test)

1.  Levantar los contenedores:

    Crea un entorno de desarrollo y test automaticamente con fixtures y datos de prueba.
    ```bash
    docker compose up -d
    ```

2. Ejecutar test:
    ```bash
    docker exec -it -e APP_ENV=test symfony_php php bin/phpunit
    ```

## 🛠️ Otra información relevante
- Acceso a Mailhog en la máquina local para ver los emails que se reciben: http://localhost:8026
- http://localhost/products para ver los productos disponibles y añadirlos al carrito.
- http://localhost/api/doc Documentación de la API REST generada con NelmioApiDocBundle.
- Performance: Se puede habilitar el profiler de Symfony cambiando el valor de `toolbar` a `true` en el archivo `config/packages/web_profiler.yaml` y accediendo a http://localhost/_profiler/ para ver las métricas de rendimiento de la aplicación.
   
   
