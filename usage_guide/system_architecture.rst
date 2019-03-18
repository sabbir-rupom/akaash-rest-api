###################
System Architecture
###################

A lot of web developers started their career on PHP language by learning some MVC framework like CodeIgniter, Laravel etc. 
So I have organized my project's system structure little similar like an MVC architecture. Meaning, 

-   API classes will act as controller classes in ``app\api`` directory
-   All database and logical code execution will be written as model class in ``app/model`` directory
-   Any response or viewable data will be written in ``app/view`` directory

As we are talking about **REST API**, the *View* part of an MVC is not a concern. So, my view output is restricted to only the 
*Output Class* - whose member function receives a JSON data as parameter and show them as ``application/json`` content-type.

