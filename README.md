# Application

Application runs with PHP 8.1 and MySQL 8

API Documentation http://127.0.0.1/api/doc

API http://127.0.0.1/api

Redirection http://127.0.0.1/{shortCode}

#### Docker

Build container

```
make build
```

Run container

```
make up
```

Stop container

```
make down
```

Enter into php container

```
make bash
```

#### Init Application

install dependencies

```
make install
``` 

initialize database

```
make init
``` 

#### Tests

run all tests

```
make test
``` 

run unit tests

```
make unit
``` 

run behat tests

```
make behat
``` 

#### Coding Standards

```
make cs
``` 

#### Static Code Analysis

```
make stan
``` 
