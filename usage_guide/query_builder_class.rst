######################
DB Query Builder Class
######################

The **Base Model Class** residing in ``app/model`` directory is an abstract class acting as a query builder class.
By extending this class with your custom model class will allow using its helper functions to build minimal query for data
insert, update, retrieve, delete with minimal scripting. There are also scope for escaping unwanted string for safer query execution. 
Some additional helper function to set, retrieve and delete data from cache alongside the database query builder functions.

The functions of query builder class are described below:

 