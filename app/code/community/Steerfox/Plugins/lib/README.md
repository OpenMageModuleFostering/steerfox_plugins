# Steerfox Plugins Library

## Requirements
* PHP 5.3+

## Installation
1. Clone this repository in your application
2. add require_once in your application on SteerfoxContainer.php
3. Set the configuration file

## Adapters
### Models
Models adapters are used to access specific data CMS through data abstraction.

### Services
Adapters services used to implement specific CMS treatments to be used by the overall functionality of the library

## Configuration File
The configuration file must be define in you application
This file contains every adapters declaration
A sample configuration file is available in "Samples" folder

## Container Instantiation
Container must be instantiate with a specific CMS configuration (for adapters).
After th first instantiation, every services can be loaded with a simple call on container : get('serviceName') 
A sample use is available in "Samples" folder

## Samples
Many samples adapters are available in "Sample" folder.

## License
This project is released under version 2.0 of the [Apache License](http://www.apache.org/licenses/LICENSE-2.0).