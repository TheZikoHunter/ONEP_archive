**===========>				Guide de travail avec l'application de l'archivage				<===========**
**Installation et configuration sur des nouvelles machines**
Cette application est dévelopée en utilisant le PHP 8.3. Quelques fonctionalités avancées peuvent ne pas être accessibles pour des versions
précédentes. Pour éviter ce problème des versions, veuillez installer directement la version finale de Xampp.
Après l'installation de Xampp, on est prêt d'accuellir notre application.
La dérnière étape de la configuration s'agit de modifier le fichier C:\xampp\apache\conf\httpd.conf :


	DocumentRoot "C:/xampp/htdocs/ONEP_archive"
	<Directory "C:/xampp/htdocs/ONEP_archive">


Il s'agit de notre répertoire. Vérifie que le dossier ONEP_archive contient le fichier index.php. Maintenant, on a terminé la configuration.

**Comment travailler**

1- Lancer Xampp
2- Exécuter Apache et MySQL
3- Allez au navigateur et ecrire " localhost ". C'est le domaine de votre application.
4- Pour la première fois, vous devez ajouter l'admin de l'archive. Soyez clair et attentif. C'est inchangeable depuis l'application. Pour
les tentatives ultérieures, une connexion seulement est require pour but de sécurité de données sensibles.
5- Toutes les autres instructions vous les trouvez sur l'application.

**Maintenance**

Si jamais vous rencontrer un problème ou une difficulté, veuillez me contacter sur e-mail "douihzakaria@gmail.com".

Au pire des cas, une communication est requise par téléphone ou par appel vidéo pour consultrer les erreurs.

En tout cas, vous pouvez directement me communiquer le le dossier ONEP_archive sous format zip/winzip/rar tout au long avec la base de données
MySQL que j'expliqura comment l'exporter maintenant via Google Drive ou sur mail simplement.

Pour exporter la base de données, vous devez vérifier que Apache et MySQL sont activés et puis allez à l'adresse localhost/phpmyadmin.
Vous chercher à gauche la base de données "archive_onep" vous cliquez.
Vous allez à la rubrique "Exporter" et vous choisissez l'option "Personnalisée, afficher toutes les options possibles".

Vous cliquez sur exporter. Le fichier est téléchargé sous le nom "archive_onep.sql".

Il faut inclure ce fichier avec les fichiers de l'application.

Une fois le problème est réglé de ma coté, je vais partager les nouveaux fichiers et cette fois il faut faire un "importer" (en séléctionnant
le nouveau fichier sql) et remplacer les fichiers
anciens par le nouveau dossier.

**FIN**