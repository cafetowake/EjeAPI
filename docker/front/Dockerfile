FROM node:18-alpine

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN apk update && apk add --no-cache curl iputils

EXPOSE 3000

CMD ["npm", "run", "dev", "--", "--host"]
