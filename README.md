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


## Preguntas o comentarios
 johandanielcruz@gmail.com
 -w  +57 3188705952
