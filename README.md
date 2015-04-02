# Logistics App

#### Manage logistics data by importing files from a remote ftp server to a local MySQL database 

[![Logistics App](https://github.com/jklepatch/logistics-app/raw/master/screenshot.png)](#features)

This is a small PHP app to manage logistics data. It downloads data from .csv files sitting on a remote ftp server (typically made available by a shipping company) and feed a local MySQL database. A responsive web interface allows to visualize downloaded data in a user-friendly dashboard. 

Technically, the app follows the MVC pattern, the WordPress coding standards (I am a WordPress developer myself) and the web interface is built with Twitter Bootstrap.

## Requirements

Needs PHP 5.0 or greater.
Needs MySQL 5.0 or greater.
Needs Apache 2.2 or greater.

I have developed and tested the app on Windows - XAMPP, but most other modern LAMP packages should also work.

## Installation on local

* Download the zip file or clone this repo
* On your MySQL client (PHPmyAdmin for example) create a database then run the `create_db.sql` script to create the database tables
* In `config-sample.php` at the project root update your ftp & local database credentials
* Rename `config-sample.php` to `config.php`
* Go to `app/models/` and change the parameters in `class-shipments`, `class-remarks` and `class-stock.php` according to your project
* Start your web-server and visit `http://localhost/projectroot/` (where `/projectroot` is the folder where you did the installation)

## Usage

Visit `http://localhost/projectroot/` and you will see the main view (where `/projectroot` is the folder where you did the installation)
On top there is a dashboard summarizing information related to stock level and latest logistics issues 
On the upper-right corner you can both see whether your local data is up-to-date (feature to finish, currently the indicator is hard-coded) and update it.
On the main section - below the dashboard - there is a table which contains three sub-views:
* Shipments
* Remarks
* Stock
You can switch from one view to another within the same table.
You can also search through Shipments data thanks to a search field on the right-hand side of the table header

##Testing

I have started to test the app with unit test. Two remarks:
1. The unit tests is just at its infancy stage, most of the tests are still missing
2. This is my first time using php unit so it is probably not the best way to do it 
(i.e I didn't use composer to install phpunit and to autoload files). Feel free to contact me if you want to suggest me better was to do it

## Possible Issues

### Data Update

During the data update process - that is, when you download new files from the remote ftp server for insertion in your local database - You may go over the maximum execution time if you have to download a load of files. You might want to change this setting in config.php.

## Troubleshooting

If you run into any problem, do not hesitate to [open an issue][issues]. I will keep on eye on this. Alternatively, you can send me an email at julien at julienklepatch dot com, or contact me on Twitter: [@jklepatch](https://twitter.com/jklepatch)

[issues]: https://github.com/jklepatch/logistics-app/issues