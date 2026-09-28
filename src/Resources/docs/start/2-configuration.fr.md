---
lang: fr
permalink: start/configure
title: Configuration
description: Configuration de référence du plugin Splash pour Sylius.
updated: 2026-09-28
translation:
    from:        en
    source_hash: a734e61d
    mode:        llm
---

### Configuration de référence

Voici la configuration de référence du plugin Splash pour Sylius :

```yaml
splash:
    # Your Splash Server ID
    id:                 ThisIsSyliusWsId
    # Your Server Secret Encryption Key
    key:                ThisIsSyliusWsEncryptionKey
    # Expert Mode. Set this url to route to your dedicated server.
    host:               https://www.splashsync.com/ws/soap
    # Server Page Information (Optional)
    infos:
        company:        My Company Name
        address:        Postal Address
        zip:            Address Post Code
        town:           Address Town
        www:            Server Url
        email:          Company Email
        phone:          Company Phone
        logo:           https://public.url.com/path/to/logo.png

splash_sylius:
    # Select here you shop default channel
    default_channel:    FASHION_WEB
    # Default Folder for Storage of new Images.
    images_folder:      "%kernel.project_dir%/web/media/image"
```
