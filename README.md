# tickets

"tickets, is an application that has functionalities such as authentication and registration of user accounts, which allows the interaction and exchange of data with the backend through JWT to guarantee its security between them, and allows the management of tickets such as creation, deletion, update and query thereof, all under the Larave and Angular frameworks and Postgres database

#Api documentation
http://127.0.0.1:8000/api/documentation#/

#Thunder Client
It is recommended to use the thunder client vscode plugin to correctly display the uris within the application

Recommendations:

After entering the corresponding folders, download the dependencies with their repetitive commands:
#npm install
#composer install



The next step is to execute the migrations to obtain the tables in the database, preferably PostgresSql,
After this we can perform the seeders to populate our tables with test records to facilitate
the use of it.

#php artisan migrate
#php artisan db:seed

#NOTE: REMEMBER IN THE REQUESTS TO IMPLEMENT THE Bearen ID AUTHORIZATION IN THE HEAD | TOKEN THAT IS RETURNED TO THEM WHEN THEY ARE LOGGED IN

After this I recommend viewing the API documentation to have a general idea of ​​the application,
link at the top where you can view different functionalities, User Authentication by doing
use of JWT, Laravel Permission, Ticket Creation, Ticket Update, Ticket Query, and Ticket Deletion
to complete the CRUD.

user admin:
"email": "admin@ticket.com",
"password": "admin"

user guest:
"email" : "guest@ticket.com",
"password": "guest"
