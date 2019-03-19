###################
System Architecture
###################

I have organized my project's system structure little similar like an MVC architecturesp that the web developers with minimum MVC knowledge can understand the structure before 
starting their development on this project. The system architecture is as follows:

-   API classes will act as controller classes in ``app\api`` directory
-   All database and logical code execution will be written as model class in ``app/model`` directory
-   Any response or viewable data will be written in ``app/view`` directory

As we are talking about **REST API**, the *View* part of an MVC is not a concern. So, my view is restricted to only the 
*Output Class* - whose member function receives a JSON data as parameter and show them as ``application/json`` content-type.

Application Flow
----------------

|Application Flow|



.. |Application Flow| image:: https://sabbirrupom.com/resources/git/rest-template-architecture.jpg
