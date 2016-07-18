## Información legal

El código de la aplicación se distribuye con licencia **GPLv2**

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or (at
    your option) any later version.

    This program is distributed in the hope that it will be useful, but
    WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
    or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
    for more details.

    You should have received a copy of the GNU General Public License
    along with this program as the file LICENSE.txt; if not, please see
    http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt.

El diseño e imágenes de la aplicación se distribuyen con licencia **Creative Commons Attribution**.

    https://creativecommons.org/licenses/by/3.0/

## Requisitos

Para instalar y usar la aplicación se requiere de un servidor con las siguientes características:

- **PHP 5.4** o superior
- **MongoDB 3.2** o superior
- **Composer** (https://getcomposer.org)
- **Node.js** y **npm** (https://nodejs.org)


## Instalación rápida

Teniendo todos los requisitos y una vez clonado el repositorio pueden instalarse todas las dependencias ejecutando el script de instalación:

    ./app/install.sh


## Instalación manual

### 1. Instalar dependencias del frontend

El frontend usa **Bower** para gestionar sus dependencias, para instalarlas es necesario tener `bower` instalado de forma global con `npm`:

    npm install -g bower

Con el comando **bower** se pueden instalar todas las dependencias del frontend ejecutando:

    bower install

### 2. Compilar hojas de estilo

Para compilar las hojas de estilo primero hay que instalar **stylus** de forma global con **npm**:

    npm install -g stylus

Y después ejecutar el comando **stylus** sobre la carpeta de estilos:

    stylus web/styles

### 3. Instalar dependecias del backend

El backend usa **Composer** para gestionar sus dependencias, para instalarlas lo más fácil es tener `composer` instalado de forma global, aunque tambien se puede descargar y usar desde la carpeta del repositorio. Más información: [https://getcomposer.org/download/](https://getcomposer.org/download/).

Teniendo Composer instalado globalmente:

    composer install

Teniendo Composer en la carpeta del repositorio:

    php composer.phar install

### 4. Comprobar requisitos del backend

Es probable que el backend necesite algunas extensiones y/o configuración extra para su óptimo funcionamiento. Para comprobar que todo esté

    php app/check.php

### 5. Añadir habilidades

Para añadir una lista de habilidades inicial ejecuta el siguiente comando:

    app/console neo:abilities:add

También puedes añadir todas las habilidades adicionales que quieras:

    app/console neo:abilities:add [habilidad] [habilidad] ...

### 6. Añadir usuarios

Como mínimo necesitarás un usuario de tipo gestor para empezar a usar la aplicación, puedes crear todos los usuarios que necesites con el siguiente comando:

    app/console neo:user:add


## Datos de prueba

Para añadir a la base de datos algunos datos de prueba hay que ejecutar el siquiente comando:

    app/console doctrine:mongodb:fixtures:load --fixtures src/Persistence/Fixtures

**Advertencia**: el comando borra toda la base de datos antes de cargar los datos de prueba.

## Estándares

Este proyecto sigue los estándares para estilo de código de Symfony:

- http://symfony.com/doc/current/contributing/code/standards.html

El cual incluye además los estándares PSR-0, PSR-1, PSR-2 and PSR-4:

- http://www.php-fig.org/psr/psr-0/
- http://www.php-fig.org/psr/psr-1/
- http://www.php-fig.org/psr/psr-2/
- http://www.php-fig.org/psr/psr-4/

Además se prentende seguir las prácticas recomendadas oficiales de Symfony mayoritariamente en la medida de lo posible:

- http://symfony.com/doc/current/best_practices/index.html


## Esctructura

En general la configuración y plantillas están en la carpeta `app`, el modelo de datos en `src/Persistence`, los componentes genéricos en `src/Component` y el código específico de la aplicación en `src/AppBundle`.

- La configuración se encuentra en la carpeta `app/config`
- Las plantillas se encuentran la carpeta `app/Resources/views`, organizadas principalmente por controladores
- Los modelos se encuentran en `src/Persistence/Model`, los cuales hacen uso intensivo de traits almacenados en `src/Persistence/Model/Properties`
- Cada modelo de tipo documento tiene asignado un repositorio en `src/Persistence/Repository`
- Los datos de prueba se encuentran en `src/Persistence/Fixtures`
- La validación de modelos esta definida en `src/AppBundle/Resources/config/validation`, haciendo uso de algunas restricciones personalidas en `src/Persistence/Validator/Constraints`
- Los formularios están ubicados en `src/AppBundle/Form`
- Los motores de búsqueda se pueden localizar en `src/AppBundle/SearchEngine`
- Los servicios suscritos a distintos eventos están en `src/AppBundle/EventListener`
- Los controladores se almacenan en `src/AppBundle/Controller`
- Tambien se pueden encontrar otros componentes útiles para la paginación en `src/Component/Pagination` o para el envío de emails en `src/Component/Mailer`
