This is a "Squirrel!" moment. Way too many things going on, but sometimes you have an idea you need to track down... 

Lord have mercy

# The Big Idea

The most pain-in-the-butt parts of running custom websites for small no-profits is data-management and training. 

What if the visual data wasn't in a database, but in folders and files? This repo explores what a file-system cms might look like if the presentation layer is decoupled from the data, but both are still Filesystem driven.

## Challenges

Structured Data in relationships! Databases are great at structuring data and defining relationships between objects. Probably will need to keep certain datasets in Database, and use this project for general layout and less structured ideas, like blogposts, articles, etc. 

## Roadmap
* Use Directories and files as collections and objects.
* Write drivers based on file extension. These should parse the file into an object for rendering
* Implement a configuration-driven controller. 
* * No Complex logic! We don't want to invent a bad coding language, so stay in-scope. 
* * Flat configurations. yaml/ini/json?
