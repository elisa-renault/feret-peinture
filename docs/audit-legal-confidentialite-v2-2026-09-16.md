# Second audit des pages légales

Audit du 16 septembre 2026 après raccourcissement des textes et modification de la clause commerciale. Les décisions précédentes sont conservées. Médiation et livraison des emails restent hors périmètre. Aucune page ni configuration modifiée pendant cet audit.

## Résultat

Les mentions légales ne présentent pas de nouvel écart identifié dans le périmètre examiné. La confidentialité est plus concise et cohérente avec le formulaire actuel. Sa validation complète reste limitée par les informations techniques non établies et deux précisions rédactionnelles à apporter.

## Points restants

Confirmation ultérieure d’Elisa : IONOS Email Basic conserve les copies sept jours maximum après suppression et n’implique pas d’accès hors EEE. Ces éléments sont enregistrés comme validés sur déclaration d’Elisa et intégrés au texte ; la réserve sur la messagerie est levée dans le suivi. Pas de vérification contractuelle indépendante supplémentaire ni d’extension de cette confirmation aux autres prestataires.

Correction ultérieure de la rétention : MaxRetentionSec=30day et MaxFileSec=1day actifs dans journald, purge de sauvegardes du projet quotidienne à 03:17 après 30 jours, tests sur fixtures réussis. Première application : zéro sauvegarde éligible ; vacuum initial : zéro octet supprimé. Politique actualisée en conséquence. Le point 1 est résolu pour les journaux serveur et sauvegardes de déploiement ; la rétention des copies du prestataire de messagerie reste à documenter. Le réglage journald concerne tout le VPS.

Preuve reçue ensuite : capture du panneau netcup indiquant Nürnberg, Allemagne. Le pays du VPS est désormais confirmé, consigné et intégré à la politique. Le point 2 est donc partiellement résolu ; ne plus demander cette information. Cette capture ne documente pas à elle seule les conditions de la messagerie ou tous les transferts des prestataires.

Mise à jour après instruction « vasy » : les points 3 et 4 sont corrigés dans les modèles, les pages WordPress locales et le VPS. La base précontractuelle est explicite ; les durées antispam sont des expirations suivies du prochain nettoyage horaire. Les points 1 et 2 restent ouverts selon les vérifications ci-dessous.

Vérification technique complémentaire : journald n’a pas de MaxRetentionSec actif dans sa configuration effective (valeur commentée par défaut : 0). Les sauvegardes de déploiement sont présentes sous /srv/apps/feret-peinture/backups, sur le même serveur ; aucune purge de ces archives n’a été identifiée dans les tâches cron et timers consultés. Aucune archive n’a été supprimée et aucun réglage global du serveur n’a été modifié.

Localisation : aucun emplacement contractuel du VPS trouvé dans les fichiers du projet consultés. [IONOS indique des centres de données européens pour ses offres email](https://www.ionos.fr/solutions-bureau/adresse-email) ; cela ne prouve ni le pays de cette boîte, ni tous les accès ou transferts de ses sous-traitants, ni son cycle de sauvegarde. Demander uniquement l’emplacement du VPS dans le panneau netcup et les informations contractuelles utiles, sans redemander les décisions déjà acquises.

### 1. Conservation des journaux et sauvegardes non indiquée

La politique mentionne les journaux de connexion et les sauvegardes de messagerie sans préciser de durée ni de critère d’effacement. Les trois ans maximum des demandes ne définissent pas automatiquement le cycle des sauvegardes. Déterminer ces règles avec les paramètres réels des services puis ajouter une phrase courte. La précédente inspection de la rotation Nginx ne couvre pas les journaux Apache/journald ou les sauvegardes des prestataires.

### 2. Transferts éventuels non vérifiés

Les noms netcup et IONOS ne permettent pas de conclure à l’absence de transfert hors EEE. Vérifier les contrats et localisations du serveur, de la messagerie et de leurs sauvegardes. En présence de transferts, informer sur les garanties applicables. Il s’agit d’une preuve manquante, pas d’un transfert illicite constaté. Aucune obligation de détailler le disque personnel de Christophe Feret dans le texte public n’est déduite de ce point.

### 3. La base précontractuelle est moins explicite dans la version courte

« Préparer votre projet à votre demande » explique la finalité mais n’énonce plus clairement la base précontractuelle, contrairement à la mention près du formulaire. Préférer « Votre demande de rendez-vous est traitée pour préparer, à votre initiative, un éventuel contrat de travaux. » ou nommer directement les mesures précontractuelles. Correction rédactionnelle brève, sans nouvelle décision à demander.

### 4. Durées antispam : distinguer expiration et suppression

Le texte indique que les compteurs et preuves sont conservés une heure, alors que le code les fait expirer à une heure et ne les supprime qu’au passage du nettoyage horaire. Même différence pour les identifiants à vingt-quatre heures. Dire « expiration après une heure [...] puis suppression au prochain nettoyage horaire » évite une promesse de suppression exacte. En cas d’échec de la tâche, le délai peut être plus long ; son bon fonctionnement reste une mesure d’exploitation.

## Prospection future

La nouvelle phrase préserve la possibilité souhaitée par Elisa sans prétendre qu’un consentement a déjà été recueilli. Aucun dispositif commercial actif n’a été identifié dans le formulaire inspecté. Ce n’est donc pas un blocage actuel.

Avant une campagne : définir canal, base légale, information et durée propres à ce traitement, recueillir le consentement si nécessaire et permettre son retrait ou l’opposition. La simple demande de rendez-vous n’autorise pas des offres par email. Une phrase sur une utilisation future ne remplace pas ces démarches. Ne pas ajouter un opt-in ou activer une campagne sans demande.

## Points vérifiés satisfaisants

- Mentions : EURL, capital, siège, SIREN/SIRET, RCS, TVA, mobile autorisé, email actif, directeur de publication, identité/adresse/téléphone de netcup.
- Photos : distinction illustrations/réalisations, autorisations, propriété intellectuelle et contact pour un retrait. La clause ne remplace pas les autorisations de chaque photo.
- Confidentialité : société responsable et siège, finalités, Aliant pour maintenance, netcup et IONOS, droits et contact, délai de réponse avec prolongation, lien CNIL.
- Trois ans constituent clairement un maximum, suppression anticipée possible.
- Formulaire : nom, commune ou code postal, description et au moins un contact ; mention de confidentialité et lien présents dans la page locale réellement servie.
- Les textes sont désormais enregistrés dans WordPress ; le problème de contenu vide est résolu. Les validations de lancement restent distinctes.
- Le détail peu professionnel des supports de stockage n’a pas été réintroduit. La page emploie uniquement « Aliant ».

## Contrôles et limites

Pages locales mentions-legales, confidentialite et contact : HTTP 200. Lecture de la confidentialité servie par le VPS avec authentification : version courte et clause commerciale présentes. Inspection des modèles et du gestionnaire de formulaire actuels. Le formulaire local est affiché avec sa mention de confidentialité.

Les contrôles navigateur antérieurs du même jour restent la référence pour les traceurs ; pas de nouveau scan navigateur ni de test d’envoi dans cet audit. Ni audit des contrats de sous-traitance, ni preuve d’exécution des suppressions manuelles, ni certification globale de conformité. L’encart de prévisualisation est conditionnel au mode staging et n’est pas traité comme un défaut de la version de production.

## Sources consultées

- [CNIL : information des personnes](https://www.cnil.fr/fr/conformite-rgpd-information-des-personnes-et-transparence), pour les bases légales, durées ou critères et transferts éventuels.
- [Service Public : mentions d’une société](https://entreprendre.service-public.gouv.fr/vosdroits/F37351), pour les informations d’identification.
- [CNIL : prospection électronique](https://www.cnil.fr/fr/la-prospection-commerciale-par-courrier-electronique), pour les conditions d’un éventuel usage commercial.
