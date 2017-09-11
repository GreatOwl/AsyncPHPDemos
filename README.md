# Try out some async PHP!

## Environment

### Requirements:
#### Docker
If you don't already have it, install docker!

https://www.docker.com/community-edition

#### PHP > 7.1
Installing php is not a hard requirement if you already know how to open a terminal into a docker container. If you know how and do not want to install PHP on your machine you can run the setup commands from the "async container".

**Hint**:
Kitematic, which comes with docker, should allow you easy access to opening a terminal into the "async" container.

**Notice**:
The terminal commands I provide are optimized for a *nix based system. If you are running on windows you may want to run the commands inside the container to be sure they work as intended.

#### POST Man
These examples all return JSON and this repo is bundled with a POSTMan collection so that you can easilly try out and tinker with the examples included here.

[**POSTMan Collection**](https://github.com/GreatOwl/AsyncPHPDemos/blob/master/Async_Research.postman_collection)

## Start the containers
In a terminal navigate to the root of this project. Start the docker containers.
```sh
bin start
```
This will download some images, and could take some time the first time they install.

### When you're done turn it off
After you are finished experimenting, turn off the containers and free your system resources.
```sh
bin stop
```

## Build the project
This php project uses composer to pull in dependent PHP libraries. If you have PHP 7.1 installed from your system you can simply run the command below. If you do not have PHP installed you will need to run the command below in the async container.
```sh
bin/install
```

## Run the code
Every endpoint in this project return JSON, therefore Postman is recommended, but a summary of the demos is included below.

#### Demonstrations
These demos are named similarly to their corresponding Controller files.

[**Synchronous Single Curl:** http://async.dev:8000/statusConsumers](http://async.dev:8000/statusConsumers)

[**Lazy Load Curl:** http://async.dev:8000/lazyLoadSingleCurl](http://async.dev:8000/lazyLoadSingleCurl)

[**Synchronous Double Curl:** http://async.dev:8000/synchronousDoubleCurl](http://async.dev:8000/synchronousDoubleCurl)

[**Asynchronous Curl:** http://async.dev:8000/asynchronousCurl](http://async.dev:8000/asynchronousCurl)

[**Guzzle Async Curl:** http://async.dev:8000/guzzleHttpCurl](http://async.dev:8000/guzzleHttpCurl)

[**React Async Socket:** http://async.dev:8000/reactHttpSocket](http://async.dev:8000/reactHttpSocket)

[**Mixed Async Clients:** http://async.dev:8000/mixedClient](http://async.dev:8000/mixedClient)

[**Procedural Blocking** http://async.dev:8000/proceduralBlocking](http://async.dev:8000/proceduralBlocking)

[**Async Model** http://async.dev:8000/asyncModel](http://async.dev:8000/asyncModel)

[**Collection Blocking** http://async.dev:8000/collectionBlocking](http://async.dev:8000/collectionBlocking)

[**Collection Non-blocking** http://async.dev:8000/collectionNonBlocking](http://async.dev:8000/collectionNonBlocking)

[**Coroutines** http://async.dev:8000/coroutines](http://async.dev:8000/coroutines)

[**Producer** http://async.dev:8000/addMessages/500](http://async.dev:8000/addMessages/500)

[**Consumer Status** http://async.dev:8000/statusConsumers](http://async.dev:8000/statusConsumers)

[**Start Consumers** http://async.dev:8000/startConsumers/50](http://async.dev:8000/startConsumers/50)

The consumers started above are long running processes, you will want to stop them when you are done. Theoretically they should automatically stop after 15 minutes though. If they don't, remember that I warned you. If you have any reason to believe the provided stop script failed to shut down the consumers, you can simplify stop docker and all running processes will be terminated.

[**Stop Consumers** http://async.dev:8000/stopConsumers](http://async.dev:8000/stopConsumers)