## Installation et configuration
Cette application a été développée avec PHP 8.3. Certaines fonctionnalités avancées peuvent ne pas être compatibles avec des versions antérieures de PHP. Pour éviter tout problème, veuillez installer la dernière version stable de XAMPP.
## Étapes d’installation
1. Installez XAMPP (version finale recommandée).
2. Copiez le dossier de l’application dans le répertoire suivant : C:/xampp/htdocs/ONEP_archive
3. Ouvrez le fichier de configuration Apache : C:\xampp\apache\conf\httpd.conf
4. Modifiez les lignes suivantes pour définir le répertoire racine de l’application :

		DocumentRoot "C:/xampp/htdocs/ONEP_archive"
		<Directory "C:/xampp/htdocs/ONEP_archive">
6. Assurez-vous que le dossier ONEP_archive contient le fichier index.php.

La configuration est maintenant terminée.

## Utilisation de l’application

1. Lancez XAMPP et démarrez les services Apache et MySQL.
2. Ouvrez votre navigateur et accédez à l’adresse : http://localhost (c’est ici que votre application est hébergée localement).
3. Lors de la première utilisation, créez un compte administrateur pour la gestion des archives. Ce compte est unique et ne peut pas être modifié par la suite.
4. Pour les connexions suivantes, il vous suffira de vous authentifier pour garantir la sécurité des données sensibles.
5. Toutes les autres fonctionnalités sont expliquées dans l’interface utilisateur de l’application.

## Maintenance et support

En cas de problème ou de difficulté, n’hésitez pas à me contacter par email à : douihzakaria@gmail.com.

Si nécessaire, nous pourrons organiser une communication par téléphone ou visioconférence afin de diagnostiquer les erreurs rencontrées.

Pour faciliter la résolution des problèmes, vous pouvez m’envoyer le dossier ONEP_archive compressé (ZIP, WinRAR ou autre), accompagné de l’export de la base de données MySQL.

### Export de la base de données

1. Vérifiez que les services Apache et MySQL sont actifs.
2. Accédez à l’interface phpMyAdmin via : http://localhost/phpmyadmin
3. Sélectionnez la base de données nommée archive_onep dans le menu de gauche.
4. Cliquez sur l’onglet Exporter.
5. Choisissez l’option Personnalisée pour afficher toutes les options disponibles.
6. Cliquez sur Exécuter pour télécharger le fichier archive_onep.sql.

Veuillez inclure ce fichier SQL avec les fichiers de l’application lorsque vous me contactez.

### Restauration après correction

Une fois le problème corrigé de mon côté, je vous enverrai les nouveaux fichiers.

Pour mettre à jour votre installation :

1. Importez la nouvelle base de données SQL via phpMyAdmin (onglet Importer).
2. Remplacez les anciens fichiers de l’application par les nouveaux fournis.

## Remerciements
Merci d’utiliser cette application. Votre retour est précieux pour l’améliorer continuellement.

Fin du guide.
