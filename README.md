# QR Code Based Attendance Tracking System

QR Code-Based Attendance Tracking System is a digital solution designed to
streamline the process
of attendance monitoring. It leverages Quick Response (QR) codes to efficiently
capture and record
attendance data. Each participant is assigned a unique QR code, which is scanned
upon entry or exit
using a mobile device or scanner. The system then logs the attendance details
automatically,
reducing manual effort and minimizing errors. This method offers improved
accuracy,
real-time data
tracking, and can be integrated into existing management systems, providing a
modern, scalable
solution for attendance tracking in educational institutions, events, or
organizations.


## Tech Stacks
 - Laravel ( PHP Framework )
 - Vue.JS ( Javascript Framework )
 - Mysql
 - Docker
 - Caddy ( WebServer )
## Pre requisites
Download the following packages
- Docker
- Git
- Node
## Deployment
To deploy this app you need to follow this steps:

1. Open Docker app
2. Create a folder in desktop name **QR Code Based Attendance Tracking System**
3. Inside of that folder run a terminal
4. Clone the app using git
5. To build he app run this command `docker compose build --no-cache && docker compose up`
6. run this command `docker exec -it attendance composer install && php artisan config:clear && php artisan migrate:fresh --seed && npm install && npm run build`
8. To stop the app run this inside of the mentioned folder `docker compose down`
9. To start the app run this inside of the mentioned folder `docker compose up`

## Accessing app to another platform to use camera
1. in chrome access this `chrome://flags/#unsafely-treat-insecure-origin-as-secure`
2. in the same folder open terminal run node appIp then copy the result
3. enabled it and paste the output of the appIp in field `http://{host of the app}`
4. then relaunch chrome 

### Developer
*Jurie Tylier Pedrogas ( Software Engineer )*

Development Time Range: 1month+