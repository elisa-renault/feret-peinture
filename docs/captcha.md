# Protection anti-spam

ALTCHA est intégré au formulaire de rendez-vous, en français et en mode automatique. Widget 3.2.2 et bibliothèque PHP 2.1.0, sous licence MIT, copiés depuis les distributions officielles. Les licences sont conservées avec les fichiers.

Les scripts et défis sont servis par le site. Aucun compte, abonnement, cookie CAPTCHA ou appel à un service tiers. La collecte de signaux d’interaction est désactivée. Le contrôle côté serveur exige une preuve signée valable vingt minutes et à usage unique. Les protections existantes restent actives.

Sources : https://altcha.org/about/ et https://github.com/altcha-org/altcha-lib-php/releases/tag/v2.1.0 et https://www.npmjs.com/package/altcha/v/3.2.2.

Test : `wp eval-file /project/tests/captcha-integration.php`, uniquement en prévisualisation. Vérifie calcul réel, preuve absente, falsifiée, expirée et réutilisée, sans envoyer de message.
