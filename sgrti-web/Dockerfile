# Usamos una imagen de Node.js para el desarrollo
FROM node:20-alpine

WORKDIR /app

# Instalamos el Angular CLI de forma global
RUN npm install -g @angular/cli@17

# Exponemos el puerto de desarrollo
EXPOSE 4200

# Comando para iniciar el servidor con acceso externo
CMD ["npm", "start"]





