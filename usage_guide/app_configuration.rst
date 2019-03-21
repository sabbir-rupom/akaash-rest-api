#####################
Project Configuration
#####################

Project application settings is configured through the `config_app.ini` location in the `app/config` directory. Rename the file from `example.*.ini` 
then **change the configuration parameters** suitable to your machine environment.   

Configuration keys are explained below:

- **ENV**
    - Server environment of deployed project application 
    - e.g development, beta, alpha, production etc.
- **PRODUCTION_ENV** 
    - Set project environment status. 
    - Purpose of this parameter is for testing features which are dependent on specific credential key's [ e.g. switch Stripe payment feature as sandbox test or live production etc. ]
- **CLIENT_VERSION** 
    - Current application version in Server. 
    - Purpose of this parameter is to crosscheck the client request with the server application. 
    - If there exists multiple version of client software application, based on their client version parameter in API request, server application may decide whether the API request will be redirected to the proper url / directory path or disallow the request call  
- **CLIENT_UPDATE_LOCATION_ANDROID** 
    - Client application download link for android
    - Add the google play-store path
- **CLIENT_UPDATE_LOCATION_iOS** 
    - Client application download link for iPhone
    - Add the ios app-store path
- **CLIENT_UPDATE_LOCATION_WindowsApp** 
    - Client application download link for Windows phone 
    - Add the microsoft-store path