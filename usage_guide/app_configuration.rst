#####################
Project Configuration
#####################

Project application settings is configured through the `config_app.ini` location in the `app/config` directory. Rename the file from `example.*.ini` 
then **change the configuration parameters** suitable to your machine environment.   

Configuration keys are explained below:

-   **ENV** : Server environment of deployed project application [ e.g development, beta, alpha, production etc. ]
-   **PRODUCTION_ENV** : Set project environment status. Purpose of this parameter is for testing 
features which are dependent on specific credential key's [ e.g. switch Stripe payment feature as sandbox test or live production etc. ]
-   