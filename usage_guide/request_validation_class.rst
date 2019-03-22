##################################################
Client Request Validation: *Base* class controller
##################################################

**Base Class** controller, resided in `app/api` directory initializes and validates all necessary parameters and client request for **API Class** execution.
Base class does the following things:

-   Checks and validates header token and session data as configured in `app/config/config_app.ini`
-   Checks and validates any method request with valid data type as API defination
-   Checks and validates session user from cache as API definition
-   Checks and executes server code if maintenance mode is on
-   Following string and image manipulation functions are added [ deprecated from version 2.0 ]
    -   **generate_random_string($length, $type)** : generates variable length random string based on string type
    -   **process_binary_image($ID, $binary_image, $type, $old_image_delete)** : create image file from valid base64 encoded image-string to specific directory
    -   **process_image_upload($ID, $binary_image, $type, $old_image_delete)** : upload image file from post data to specific directory\
    -   **resize_image($image_src, $image_name, $maxDimW, $maxDimH, $type)** : resize an existing image to specific dimension

Base class must be inherited during API class creation, thus validates and through necessary errors if improper request has been made to **Called API**