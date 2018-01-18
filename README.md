# EasyDev :camel: :boom:

Easydev est un générateur de Backend Symfony3 fait avec :heart: . <br> 
Avec un simple upload de fichiers html et css, l'algorithme détecte
votre code, le parse et génère le backend correspondant en symfony3.
<br><br> **/!\ Fonctionnel mais comprend encore quelques bugs /!\\** 

Installation
======

``` 
git clone https://github.com/benj1299/easydev.git
cd easydev
composer install
php bin/console cache:clear
php bin/console server:run
```

Comment l'utiliser ?
======
* Démarrer le serveur et se rendre sur l'adresse du site.
* Remplir le formulaire et envoyer.
* L'algorithme compresse votre nouveau site et l'upload automatiquement.
* Dézipper le ficher et, avec votre terminal, utiliser dedans les commandes suivantes :
```
composer install
php bin/console cache:clear
php bin/console server:run
```

Documentation pour les options
======

Pour que l'algorithme puisse détecter votre code, 
une nomenclature HTML/CSS doit être respectée.<br>
Elle se présente sous la forme de <br>\<type class="#class#">\</type>

### Contact Form
 Type            | #Class#
------------     | -------------  
form             | contact-form
input            | contact-name
input            | contact-email
input            | contact-subject
input            | contact-phone
textarea         | contact-message
input            | contact-submit

### Sans option
Le générateur générera un projet symfony selon les informations remplies et configura <br> un base.html.twig, un layout.html.twig et les fichiers twig correspondant à chaque page.