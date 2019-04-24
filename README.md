# Vogon
Lightweight CMV framework for prototyping and personal projects. I've personally found it useful for data driven projects that don't need much in the way of interface, but need enough internal organization that CMV makes sense.

## Features
Everything a growing project needs.
* CMV framework (folder structure and functions for loading controllers, models, or views and passing them data).
* Auto included bootstrap to establish your environment
* Auto included functions
* Auto-loading classes
* Error proof class loading for non-autoloaded classes (just call load_class('className'))
* A database class (thumb)
* a Curl library wrapper (fish).

Yes it is that bare bones, but that's kind of the point.

The Current configuration is set up to use database configured routes, but can easily be altered to us routes defined in the config.ini file.

## Why?
This is a collection of item's that I find myself reaching over and over again any time I start a data-driven project. It's enough that I can just start solving the problems the project is presenting, but flexible enough that it shouldn't get in the way of whatever needs to be done.

So far, I've used Vogon for:
* A self caching full website crawler for SEO and Optimization testing.
* Migrating a forum from a Microsoft Access database to a MySQL database in a different forum framework
* Pulling and consolidating bad actor IPs from server logs
* Splitting a 1,000,000+ row XLSX file into managable chunks
* Data translations large and small.

## Vogon?
They destroy the world and write mythically bad poetry.
