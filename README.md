## Despliegue

luego de descargar el fichero, asegurarse de que docker está instalado y funcionando correctamente, "abrir" la carpeta raíz del proyecto y en consola ejecutar 
```
Docker-compose up
```
Esperar un momento y la aplicación funcionará en https://localhos:8000

## Documentación

enlace para ver la documentación en postman
https://solar-astronaut-120115.postman.co/workspace/3d81a255-1128-40d2-8fd9-339b04830168/overview

si hay problema para visualizar el anterior link otra opcion es https://solar-astronaut-120115.postman.co/workspace/My-Workspace~3d81a255-1128-40d2-8fd9-339b04830168/folder/12905489-5419bf31-83fc-4dc8-b343-2492f501e7aa?action=share&creator=12905489&ctx=documentation&active-environment=12905489-177bdbbf-aa76-46c1-9e86-47615e3b9f7b


## Apreciaciones sobre lógica de negocio usada en la aplicación

- Un producto puede ser borrado solo si NO se encuentra relacionado con alguna orden de compra (solo hay ordenes de compra)

- El borrado de orden de compra cambiará el estatus de la orden a "cancelled", y se devolveran los productos  

## link 
en el siguiente documento comparto el link de app desplegada y el link de una documentacion en postman adicional con las urls relativas a la app desplegada para probar la app con postman sin hacer despliegue 
https://docs.google.com/document/d/1kSrADJoRPbWXyHhv_M_foEZLjhs1o1pTPwIZIglEjkM/edit?usp=sharing 
## Preguntas o comentarios
 johandanielcruz@gmail.com
 -w  +57 3188705952

## Despliegue en DigitalOcean
con el siguiente comando asegurarse de que la respuesta es  8.2 
```
ls /etc/php/
```
teniendo una versión inferior actualizarlo se puede siguiendo intrucciones del siguiente link https://php.watch/articles/install-php82-ubuntu-debian 

instalar composer haciendo sudo composer update
si hay problemas puede intentar:

sudo apt-get install -y php-xml
sudo apt-get install -y php8.2-xml
sudo service php8.2-fpm restart
sudo composer install
sudo composer update


