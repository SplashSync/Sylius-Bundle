
##### Sylius Bundles Config. reference

Here is the reference configuration for Splash Sylius bundles:

```yml

    splash:
        # Your Splash Server Id
        id:             812b124aa746e04c                                        
        # Your Server Secret Encryption Key
        key:            NTdlMDI2YWQ1NTQ5NjAuOTI3OTgxMTQ1N2UwMjZhZDU1NjFiMS      
        # Expert Mode. Set this url to route to your didicated server.
        host:           https://www.splashsync.com/ws/soap
        # Enable Doctrine ORM Entity Mapping
        use_doctrine:   true
        # Enable Doctrine MongoDB Documents Mapping
        use_doctrine_mongodb

        # Server Page Informations (Optionnal)
        infos:
            company:    My Company Name
            address:    Postal Address
            zip:        Address Post Code
            town:       Address Town
            www:        Server Url
            email:      Company Email
            phone:      Company Phone
            logo:       Company Logo Public Url

    splash_sylius:
        # Select here you shop's default channel
        default_channel:    US_WEB      
        # Default Folder for Storage of new Images.
        images_folder:      "%kernel.root_dir%/../web/media/image"  

```
