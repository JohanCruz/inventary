## Despliegue

luego de descargar el fichero, asegurarse de que docker está instalado y funcionando correctamente, "abrir" la carpeta raíz del proyecto y en consola ejecutar 
```
Docker-compose up
```
Esperar un momento y la aplicación funcionará en https://localhos:8000
con ese comando la aplicación funciona en windows 11 y teniendo el ws configurado con debian 

## Documentación

enlace para ver la documentación en postman
https://solar-astronaut-120115.postman.co/workspace/My-Workspace~3d81a255-1128-40d2-8fd9-339b04830168/collection/12905489-d1e7f453-629f-4fb7-b42d-318626459fab?action=share&creator=12905489&active-environment=12905489-177bdbbf-aa76-46c1-9e86-47615e3b9f7b

**tener en cuenta que la documentacion sirve para dos casos
la app funcionando en local o la app desplegada con los dos funciona haciendo los respectivos send


## Apreciaciones sobre lógica de negocio usada en la aplicación

- Un producto puede ser borrado solo si NO se encuentra relacionado con alguna orden de compra (solo hay ordenes de compra)

- El borrado de orden de compra cambiará el estatus de la orden a "cancelled", y se devolveran los productos  



## Preguntas o comentarios
 johandanielcruz@gmail.com
 -w  +57 3188705952

## Despliegue en DigitalOcean o en local con imagen Docker
la imagen https://hub.docker.com/repository/docker/johancruzt/commerce-app/general
está configurada como publica

con un droplet activo hacer
docker pull johancruzt/commerce-app:latest
docker run -d --name commerce-app -p 8000:8000 johancruzt/commerce-app:latest
curl http://localhost:8000/api/products

para utilizar el comando docker
https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-20-04-es
tener en cuenta las primeras 5 indicaciones de comandos

