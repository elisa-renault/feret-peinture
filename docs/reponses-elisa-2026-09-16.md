# Informations confirmées par Elisa

## Sauvegardes automatiques du site

Le 18 septembre 2026, Elisa décide de ne pas mettre en place de sauvegarde automatique du site. Le code reste versionné dans GitHub et Christophe Feret conserve ses brouillons sur son ordinateur avant publication.

Cette décision ne transforme pas GitHub ou les brouillons locaux en sauvegarde des données WordPress déjà publiées : base de données, médias, réglages et comptes n'y sont pas inclus. Une restauration complète n'est donc pas garantie après un incident affectant ces données. Le test isolé du 18 septembre a validé le mécanisme de restauration d'une archive technique antérieure, sans chantier ni média. Reconsidérer ce choix avant la publication de réalisations réelles ou de contenus qui ne peuvent pas être recréés.

## Ouverture publique

Ouverture publique effectuée le 16 septembre 2026 à la suite de la demande d’Elisa de retirer la protection. Le site est en production, sans authentification HTTP, avec indexation autorisée et formulaire actif. Sauvegarde préalable : `backups/public-launch-20260916T213221Z`. Les indicateurs de publication de la version courante sont activés ; cela ne constitue pas une certification juridique indépendante. Les mentions de protection et de validations en attente ci-dessous décrivent les étapes antérieures.

## Formulaire activé sur le site protégé

Elisa demande explicitement « activer le formulaire ». Activation réalisée avec `FP_PREVIEW_FORM_ENABLED=true`, sans activer l’ouverture publique ni les autres validations finales. Les demandes du formulaire peuvent désormais partir par IONOS ; les autres emails de prévisualisation restent bloqués. Le formulaire n’affiche plus la mention mensongère de test sans transmission. Test HTTP réel authentifié avec CAPTCHA et confirmation réussis, message `FP-ACTIVATION-20260916-2122` reçu dans INBOX IONOS le 16 septembre 2026 à 21:23:56 UTC, vérifié par IMAP en lecture seule. L’affichage du formulaire a été contrôlé dans Chrome ; l’envoi de recette a été effectué par HTTP après une instabilité de l’outil navigateur. Protection HTTP 401, staging et noindex conservés. Sauvegarde préalable : `backups/enable-private-form-20260916T212122Z`. Tests : 31 assertions du formulaire local et 10 assertions spécifiques à l’activation privée réussies. Cette décision remplace les mentions historiques de formulaire inactif ou de blocage de tous les emails de visiteurs.

## Préparation de production après audit

Elisa demande de corriger les constats de l’audit avant production. Les écarts de code local/VPS sont synchronisés avec sauvegarde, le titre de la page fusionnée est harmonisé, la durée déjà approuvée est renseignée dans `FP_PRIVACY_RETENTION` et `FP_CONTACT_APPROVED` est activé en application des accords acquis. Le formulaire simplifié est conservé. Une recette navigateur locale a abouti à un message reçu dans Mailpit, avec 31 assertions serveur, 9 contrôles CAPTCHA et 2 tests du bouton réussis. Le guide pratique des suppressions est désormais rédigé. La protection HTTP, staging, noindex et le blocage des emails de visiteurs restent actifs ; la demande de correction ne vaut pas accord d’ouverture ni validation éditoriale finale. Le suivi actuel est dans `publication.md` et les preuves dans `audit-avant-production-2026-09-16.md`.

## Réalisations : les illustrations sont des placeholders temporaires

Le 16 septembre 2026, Elisa précise que les exemples illustrés sont uniquement des placeholders avant leur remplacement par les vraies réalisations de Christophe Feret. Conserver l’intitulé « Réalisations » et la structure de cette rubrique. Ne pas créer d’intitulé « Exemples d’ambiances » ni de logique de renommage selon les contenus temporaires. Les signalements existants des illustrations et leur exclusion de la production restent conservés. Cette décision remplace la recommandation de renommage du deuxième audit copywriting.

## Zone d’intervention : ne pas afficher la limite de trajet

Le 16 septembre 2026, pendant l’application de l’audit copywriting, Elisa demande explicitement de ne pas indiquer la limite de deux heures sur la page de zone et de retenir cette décision. Présenter les secteurs et la confirmation de la possibilité d’intervention selon la commune, sans durée de trajet affichée. Cette décision remplace la recommandation de l’audit qui proposait de réintroduire cette limite. Ne plus proposer cet ajout ni demander une nouvelle confirmation. La limite interne antérieure n’est pas modifiée par cette décision d’affichage.

## Fusion des pages légales

Fusion autorisée et réalisée : page « Mentions légales et confidentialité » sur /mentions-legales/, avec sommaire et sections identifiées. /confidentialite/ redirige en 301 vers /mentions-legales/#confidentialite. Lien du formulaire dirigé vers cette section et pied de page unifié. Confidentialité conservée intégralement, date de mise à jour et précision sur la prise de contact avant devis ajoutées. Synchronisation locale et VPS vérifiée, sans changement de l'autorisation d'ouverture. Les contrôles de publication portent désormais sur la page fusionnée tout en conservant les approbations distinctes légales et confidentialité.

## Décisions sur les étapes restantes

Test email terminé : après correction DNS par Elisa, MX IONOS et SPF vérifiés sur le serveur DNS autoritaire. Le message FP-TEST-20260916-202700 a été envoyé par fp_quote_submit puis retrouvé dans INBOX IONOS, hors spam, via IMAP en lecture seule. Message-ID DM0QMndCZcp50R7gMJvBMsqsVhSPvhURsWnut8nCX7w@7dac0691cfe4, date 16 septembre 2026 à 20:27:04 UTC. Livraison réelle acquise, ne plus demander de confirmation. FP_MAIL_DELIVERY_VERIFIED activé sur le VPS, autres approbations inchangées. Site protégé, aucune ouverture autorisée. Test serveur isolé avec CAPTCHA réel, pas une recette navigateur complète. Les blocages netcup et DNS historiques ci-dessous sont résolus.

Dernier état du test : Elisa indique avoir levé le blocage netcup. Ports SMTP 465 et 587 désormais accessibles. Tests FP-TEST-20260916-201904 et FP-TEST-20260916-202108 envoyés par fp_quote_submit avec CAPTCHA réellement résolu et transport SMTP WordPress IONOS. Exécution isolée en CLI : configuration de test uniquement en mémoire, aucun indicateur d'ouverture persistant modifié. Réception non constatée dans INBOX ou Spam via IMAP. Diagnostic DNS autoritaire : MX 0 blackhole.tem.scaleway.com ; serveurs DNS ns0.dom.scw.cloud et ns1.dom.scw.cloud ; SPF v=spf1 include:_spf.tem.scaleway.com -all. Les MX sont à remplacer par ceux d'IONOS France et le SPF unique à compléter avec include:_spf-eu.ionos.com. Aucun DNS modifié dans cette intervention. Les constats historiques de ports bloqués ci-dessous sont levés, mais FP_MAIL_DELIVERY_VERIFIED reste faux. Protection HTTP 401, environnement staging et formulaire public inactif contrôlés après les tests.

Mise à jour du test email : fichier privé fourni par Elisa utilisé sans affichage du secret. Identifiants IONOS validés par connexion IMAP chiffrée, sans lecture des messages. Transport WordPress configuré sur smtp.ionos.fr:465 en SSL, destinataire et expéditeur contact@feret-peinture.fr. Secret dans /srv/apps/feret-peinture/wordpress/feret-mail-private.php, propriétaire 33:33, permissions 0600, hors dépôt. Copie temporaire du mot de passe sur le VPS supprimée. Les connexions SMTP sortantes du VPS vers IONOS expirent sur les ports 465 et 587 ; IMAP 993 accessible, aucun blocage sortant identifié dans nftables. Cause probable à vérifier dans le panneau netcup : politique « netcup Mail Block ». Aucun email envoyé, aucune réception ni livraison validée. Protection HTTP 401 et environnement staging vérifiés conservés. Prochaine action : vérifier la politique du pare-feu netcup et autoriser la connexion SMTP nécessaire, puis reprendre le test.

Elisa autorise explicitement le test du formulaire avec envoi et réception réelle sur contact@feret-peinture.fr. Cette autorisation ne vaut pas ouverture publique. Inspection du VPS : tous les paramètres FP_QUOTE_TO, FP_MAIL_FROM et FP_SMTP_* sont absents ; formulaire inactif, environnement staging. Aucun message envoyé. Installer le transport IONOS avec le secret conservé hors dépôt avant de tester ; ne pas marquer la livraison comme vérifiée sans réception constatée.

La gestion pratique des suppressions sera expliquée dans un guide pour Christophe Feret à préparer. La validation finale des textes, prestations, communes et droits des photos est en cours. Elisa décline les vérifications de restauration des sauvegardes et de récupération des accès : ne plus les présenter comme une condition préalable à l'ouverture, et ne pas les déclarer réalisées. L'accord final d'ouverture n'est pas encore donné.

## Confirmation finale pour IONOS Email Basic

Elisa confirme explicitement : « c’est 7j comme indiqué et pas d’accès hors EEE », en réponse à la question sur la durée maximale des copies après suppression et les accès hors EEE. Retenir pour cette messagerie : copies de sauvegarde conservées sept jours maximum après suppression des messages, aucun accès hors EEE. Source de validation : déclaration d’Elisa, sans nouvelle vérification contractuelle indépendante. Cette confirmation lève les réserves antérieures sur ces deux points ; ne plus les redemander. Elle ne s’étend pas à tous les services netcup ou autres prestataires. Politique de confidentialité actualisée.

## Offre de messagerie confirmée

Elisa confirme IONOS Email Basic pour contact@feret-peinture.fr. Offre acquise : ne plus la redemander. IONOS indique un hébergement des offres email dans des centres de données européens (https://www.ionos.fr/solutions-bureau/adresse-email). Son assistance annonce une récupération de boîte supprimée jusqu’à sept jours (https://www.ionos.fr/assistance/email/service-de-recuperation-des-emails/), ce qui ne prouve pas une suppression exhaustive de toutes les copies sous sept jours. La durée maximale des sauvegardes résiduelles et les éventuels accès hors EEE ne sont donc pas certifiés par ces pages. L’accord de sous-traitance est inclus aux CGV pour les nouveaux contrats depuis le 19 juillet 2022 selon l’assistance IONOS ; la date de création de la boîte ne prouve pas celle du contrat. Aucun service d’archivage optionnel n’est présumé activé.

## Localisation du VPS confirmée

La capture du panneau netcup fournie par Elisa indique « Nürnberg » avec le drapeau allemand pour le VPS. Localisation validée : Nuremberg, Allemagne. Ne plus demander le pays du serveur. Cette preuve concerne le VPS du site ; elle ne détermine pas les localisations de la messagerie IONOS, des éventuelles copies du fournisseur ni tous les accès de sous-traitants. La politique de confidentialité mentionne désormais Nuremberg.

Précision validée : la conservation des demandes sans suite est plafonnée à trois ans après le dernier échange ; une suppression plus précoce est possible lorsque les données ne sont plus nécessaires au suivi. Ce plafond ne constitue pas une obligation de conserver chaque demande pendant trois ans. La politique et la mention du formulaire sont harmonisées sur ce point.

## Messagerie active : décision actuelle

Elisa confirme que contact@feret-peinture.fr est active. Sa capture montre cette adresse connectée au webmail IONOS sous le nom Christophe Feret. La création de la boîte et le fournisseur IONOS sont acquis, sans nouvelle confirmation à demander. Cette information remplace toutes les mentions historiques « en cours de création » ou « à configurer » ci-dessous. Les mentions légales et la confidentialité sont actualisées en conséquence.

La configuration SMTP du formulaire et sa livraison réelle restent à vérifier séparément : la capture ne prouve pas un test de réception depuis le site. Aucun indicateur de livraison, déblocage des emails ou accord de lancement n’est activé par cette confirmation. Le fournisseur de la boîte ne prouve pas à lui seul le transport SMTP utilisé par le site, les pays de stockage ou les garanties contractuelles.

## Transmission à un comptable

Elisa confirme qu’aucune facture ni aucun autre document client n’est transmis à un comptable ou à un cabinet comptable. Réponse validée : ne plus redemander ce point et ne pas ajouter un comptable parmi les destinataires des données dans la politique de confidentialité. Cette réponse porte sur cette catégorie de destinataires uniquement.

## Traitement des demandes relatives aux données personnelles

Elisa confirme que Christophe Feret répond lui-même aux demandes d’accès, de rectification ou de suppression des données personnelles. Responsable opérationnel validé : ne plus redemander qui traite ces demandes. L’accès ponctuel d’Aliant pour la maintenance reste distinct et ne lui attribue pas la gestion de ces demandes. Les modalités pratiques et les règles applicables restent à documenter.

## Stockage des dossiers clients

Elisa confirme que les devis signés et factures des dossiers devenus clients sont conservés dans la messagerie et dans un dossier sur ordinateur. Elle précise que ce dossier est uniquement sur l’ordinateur, sans synchronisation cloud ni sauvegarde séparée (réponse « non » à la question sur une copie sur clé USB ou disque externe). Ces informations sont validées : ne plus redemander les supports, la synchronisation ou l’existence d’une sauvegarde séparée de ce dossier. Les règles de conservation devront couvrir la messagerie et le dossier local. Cette réponse ne décrit pas les sauvegardes du site ou du fournisseur de messagerie, qui restent des sujets distincts.

## Réponses confirmées : accès, usages et photographies

Réponses expressément confirmées par Elisa le 16 septembre 2026, à enregistrer comme validées et à ne plus redemander :

1. Christophe Feret seul consulte les demandes et assure le suivi des clients. Aliant dispose uniquement d’un accès ponctuel nécessaire à la maintenance technique.
2. Usage actuel : réponses aux demandes et suivi des travaux. Elisa souhaite désormais réserver la possibilité d’offres commerciales ultérieures. La politique précise information préalable, consentement lorsque nécessaire et opposition simple et gratuite. Cette évolution remplace l’interdiction rédactionnelle antérieure, sans activer de campagne ni transformer une demande de rendez-vous en consentement commercial. Avant un tel usage, adapter le recueil, la preuve du consentement, les durées et les mécanismes d’opposition selon le canal choisi. Source : https://www.cnil.fr/fr/la-prospection-commerciale-par-courrier-electronique.
3. Christophe Feret vérifie les droits d’utilisation et les autorisations nécessaires avant chaque publication de photos de chantiers. La responsabilité de cette vérification est acquise ; elle ne vaut pas autorisation globale de toutes les photos futures.

Les deux premiers points sont intégrés à la politique de confidentialité. Le troisième est consigné dans le guide de Christophe Feret. Ne plus présenter ces trois décisions comme des questions en attente.

## Nouvelle décision : rendez-vous et mobile public

Le 16 septembre 2026, après échange avec Christophe Feret, Elisa autorise l’affichage du mobile déjà enregistré : 06 83 82 45 16. Cette décision remplace toutes les consignes antérieures de confidentialité du mobile ci-dessous. Le fixe reste privé, sans recours automatique à ce numéro.

Christophe Feret ne réalise pas de devis en ligne : un rendez-vous sur place est nécessaire pour qu’il évalue lui-même les travaux avant d’établir le devis. Le formulaire sert uniquement à demander un rendez-vous ; la date est convenue directement avec Christophe Feret. L’URL /devis/ reste conservée pour les liens existants. Les protections de prévisualisation et le blocage des emails restent actifs.

## Historique des confirmations

Le 16 septembre 2026 : noms Feret Peinture et Christophe Feret, implantation à Écouen, déplacements jusqu’à deux heures de trajet, aucun accueil de clients à l’adresse professionnelle.

Les deux téléphones restent privés : aucun affichage, lien d’appel ou téléphone dans les données structurées. Les champs administratifs sont conservés. L’ouverture exige désormais un email public valide au lieu d’un téléphone public.

Email pour le site et les demandes relatives aux données : contact@feret-peinture.fr. Boîte désormais active chez IONOS, confirmée par Elisa ; livraison du formulaire non vérifiée. Le destinataire est consigné dans l’option privée fp_launch_decisions_20260916 ; le transport de prévisualisation et le blocage des emails restent inchangés.

Christophe Feret consulte les demandes et est responsable de publication. Conservation retenue pour les demandes sans suite : au maximum trois ans après la dernière interaction. Elisa a confirmé que Christophe Feret est responsable de leur suppression dans la boîte de réception : décision validée, ne plus demander qui s’en charge. L’organisation pratique reste à mettre en place ; cette attribution ne prouve pas une suppression déjà effective et ne couvre pas les sauvegardes ni les dossiers devenus clients. Les autres durées restent à finaliser.

Les prestations issues du fonds source sont déjà intégrées : intérieur, ravalement, bois et métal extérieurs, dépose et toile de verre, préparation et sols stratifiés ou PVC en pose flottante. Aucun ajout non documenté. Les documents ne prouvent pas une autorisation photographique.

## Identité juridique validée par Elisa le 16 septembre 2026

Après transmission du lien de l’Annuaire des entreprises et présentation du tableau d’identité, Elisa a demandé : « déjà, met à jour ces infos et valide les pour ne plus redemander ». Ces informations sont acquises et ne doivent plus faire l’objet de questions de confirmation.

| Information | Valeur validée |
| --- | --- |
| Dénomination | CHRISTOPHE FERET |
| Nom utilisé sur le site | Feret Peinture |
| Forme juridique affichée | Entreprise unipersonnelle à responsabilité limitée (EURL) |
| Capital social | 500 € |
| Siège social | 74 T avenue du Maréchal Leclerc, 95440 Écouen, France |
| SIREN | 502 545 874 |
| SIRET | 502 545 874 00021 |
| Immatriculation | RCS Pontoise 502 545 874 |
| TVA intracommunautaire | FR02502545874 |
| Gérant et directeur de publication | Christophe Feret |

Source fournie : https://annuaire-entreprises.data.gouv.fr/entreprise/christophe-feret-502545874. Consultation directe bloquée (403) ; informations retrouvées sur https://www.pappers.fr/entreprise/christophe-feret-502545874 puis validées par Elisa. Le libellé « avenue » remplace la mention antérieure « rue ». Correction transmise par Elisa le 16 septembre 2026 après échange avec Christophe Feret : la forme juridique à retenir et à afficher partout est EURL (entreprise unipersonnelle à responsabilité limitée). Cette confirmation remplace le libellé précédemment retenu et ne doit pas être redemandée.

Les informations validées figurent désormais directement dans le modèle des mentions légales, sans dépendre d’une ancienne option de brouillon. La validation porte sur l’identité ci-dessus, pas sur les rubriques encore incomplètes ni sur l’ouverture publique du site. Hébergeur complété et validé ci-dessous ; médiateur reporté sur instruction d’Elisa. Les décisions de confidentialité des téléphones et l’état de l’email consignés plus haut restent acquis.

## Contact et hébergeur confirmés, médiation reportée

Historique remplacé par les décisions ultérieures : le mobile est désormais autorisé (voir plus haut) et contact@feret-peinture.fr est active chez IONOS. Ne pas réappliquer les anciennes consignes de non-affichage du mobile ou de boîte à créer.

Hébergeur confirmé par Elisa et validé pour les mentions : netcup GmbH, Emmy-Noether-Straße 10, 76131 Karlsruhe, Allemagne ; téléphone de l’hébergeur +49 721 7540755-0 ; https://www.netcup.com/. Coordonnées vérifiées le 16 septembre 2026 sur https://www.netcup.com/en/contact/legal-notice. La consigne de non-affichage des téléphones concerne ceux de Christophe Feret, pas les coordonnées légales de netcup. Hébergeur intégré aux mentions et à la politique de confidentialité. L’adresse du siège de netcup ne constitue pas une preuve de localisation du serveur ou de toutes les données.

Médiateur : Elisa confirme ensuite que Christophe Feret n’en a pas et demande de retirer cette partie pour ne pas bloquer la mise en production. Décision acquise le 16 septembre 2026 : rubrique retirée du site et des conditions bloquantes du suivi de lancement ; adhésion reportée à une régularisation ultérieure. Ne pas redemander cette décision. L’obligation pour les prestations aux particuliers demeure (https://entreprendre.service-public.gouv.fr/vosdroits/F33338) ; le report ne vaut ni adhésion, ni conformité juridique, ni autorisation générale d’ouverture.

Il n’y a pas de médiateur attribué automatiquement à Christophe Feret. CM2C a été présenté comme une piste via le partenariat CAPEB (https://www.capeb.fr/partenaires-commerciaux/cm2c), sans choix ni adhésion validés. Afficher un organisme seulement après confirmation de la couverture ou de la convention.

La rubrique « Photographies et contenus » a été remplacée, à la demande d’Elisa, par une rédaction pérenne couvrant les illustrations et les photographies des vrais chantiers ajoutées ultérieurement : distinction des usages, droits et autorisations nécessaires, propriété intellectuelle avec exceptions légales et licences, contact pour une demande de retrait. Mise à jour déployée et affichage vérifié sur le site. Cette clause ne vaut pas autorisation pour chaque future photographie ; le contrôle des droits à l’ajout reste nécessaire. Références : https://www.service-public.gouv.fr/particuliers/vosdroits/F32103 et https://entreprendre.service-public.gouv.fr/vosdroits/F22667.

Le tableau de suivi central des points acquis et des actions restantes est dans [publication.md](publication.md). Présentation générale, validation définitive des textes, photos et ouverture : en attente de l’échange avec Christophe Feret. Aucun indicateur d’approbation finale activé. La rédaction pérenne de la rubrique photos ne rend pas opérationnelle la boîte email et ne régularise pas le report de la médiation.

Vérification : dix pages de la prévisualisation contrôlées, sans numéros ni liens d’appel ; email, zone, identité et conservation vérifiés. Protection par mot de passe et noindex conservés. Douze assertions locales du contrôle de publication et deux tests du bouton d’envoi réussis. Un avertissement WordPress lié à SERVER_NAME apparaît dans le contexte CLI du test de publication, sans échec des assertions. Sauvegarde préalable de la base et du code sur le serveur.
