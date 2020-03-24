Vitrine
=============
**Créez un fichier `<config.php>` dans la racine, le format est suivant:**
```php
<?php
    /**
     * Les valeurs par défaut
     */
    $soapUrl = "URL du WSDL";
    $soapUser = 'Username du compte Infogreffe';
    $soapPassword = "Mot de passe du compte Infogreffe";


    return array(
        'soapUrl'       => $soapUrl,
        'soapUser'      => $soapUser,
        'soapPassword'  => $soapPassword
    );
?>
```