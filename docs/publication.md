# Prévisualisation et ouverture au public

Le code livré prépare une V1 à prévisualiser. Il ne constitue ni une mise en production ni une validation des données professionnelles. Aucun DNS, hébergement public ou envoi réel à Christophe n'a été modifié ou déclenché dans cette réalisation. Les résultats exécutés sont consignés dans [recette.md](recette.md).

## État attendu selon l'environnement

| Environnement | Accès et indexation | Email et contenu |
| --- | --- | --- |
| Local | Adresses locales ; `WP_ENVIRONMENT_TYPE=local` ; noindex | Mailpit ; données synthétiques seulement. Fixtures sur demande explicite. |
| Staging | HTTPS + authentification Nginx ; noindex applicatif et en-tête | Destinataire de test maîtrisé par Aliant ; aucun email réel de visiteur. |
| Production avant validation | Ouverture bloquée tant que les validations privées manquent | Aucune transformation d'un formulaire inactif en faux succès. |
| Production validée | `https://feret-peinture.fr`, sans www ; redirections vérifiées | SMTP privé testé ; contenus approuvés ; aucun chantier de démonstration. |

Les indicateurs privés d'approbation ne sont pas des autorisations obtenues automatiquement. Aliant les active uniquement après les validations ci-dessous. Les données légales et la configuration SMTP ne sont pas confiées au rôle Christophe ni placées dans Git.

| Réglage privé | Condition à satisfaire |
| --- | --- |
| `FP_CONTACT_APPROVED` | Coordonnées approuvées. |
| `FP_SERVICES_APPROVED` | Prestations et textes métier approuvés. |
| `FP_LEGAL_APPROVED` | Identité juridique, mentions et renseignements obligatoires finalisés. |
| `FP_PRIVACY_APPROVED` | Information du formulaire et politique validées. |
| `FP_PRIVACY_RETENTION` | Durée effective et point de départ définis et renseignés. |
| `FP_MAIL_DELIVERY_VERIFIED` | Livraison réelle constatée lors du test Aliant. |
| `FP_LAUNCH_APPROVED` | Autorisation finale de lancement, qui ne remplace aucune validation précédente. |

Le formulaire exige aussi une configuration de transport complète. Voir la configuration du plugin et le README pour renseigner ces valeurs hors Git. Aucun indicateur ne remplit automatiquement les mentions ou ne prouve la qualité de la recette.

## Données à faire approuver

Obtenir une confirmation écrite de Christophe pour les téléphones, le nom d'usage, les quatre prestations, les communes affichées, les textes métier et les droits sur les images. Un annuaire peut préremplir la prévisualisation, mais ne confirme pas une disponibilité, un périmètre ou une promesse commerciale.

Relire les informations administratives sur un justificatif récent. L'adresse de référence du brief emploie « avenue » et non « rue ». Une implantation administrative ne signifie pas un lieu d'accueil des clients.

## Mentions légales : trame interne à compléter

Ce canevas sert à préparer les pages ; il ne doit jamais être publié avec des crochets ou une mention « à compléter » présentée comme définitive. Les champs manquants sont recensés, pas déduits du nom du projet.

| Rubrique | Texte ou information à préparer |
| --- | --- |
| Éditeur | CHRISTOPHE FERET, [forme juridique validée], au capital de [montant validé], siège [adresse validée]. |
| Immatriculation | SIREN et RCS [confirmés] ; SIRET si affiché ; TVA [confirmée]. |
| Contact | [Téléphone confirmé] ; [email public réellement consulté]. |
| Publication | Directeur ou directrice de publication : [nom et qualité confirmés]. |
| Hébergement | [Dénomination de l'hébergeur], [adresse postale], [téléphone]. Distinguer l'hébergeur de la maintenance Aliant. |
| Médiation | [Médiateur de la consommation applicable], [adresse], [site et modalités vérifiés]. Vérifier l'adhésion et l'information à donner aux consommateurs ; ne pas choisir un organisme arbitrairement. |
| Contenus | Identité graphique provisoire, crédits des ressources, conditions de réutilisation et droits des photographies effectivement publiées. |

Les informations d'identification sont fondées sur la [fiche officielle Service Public](https://entreprendre.service-public.gouv.fr/vosdroits/F37351), consultée le 15 septembre 2026 UTC. Faire vérifier aussi les informations précontractuelles / CGV applicables au parcours réel de devis et au contrat de travaux ; cette V1 ne conclut pas de vente en ligne. La page officielle est générale et ne remplace pas la qualification du cas précis.

## Données personnelles : texte de travail

La [CNIL précise les conditions de la base légale contractuelle et précontractuelle](https://www.cnil.fr/fr/les-bases-legales/contrat). Pour cette V1, traiter une demande de devis à l'initiative du visiteur peut relever des mesures précontractuelles, si les données sont nécessaires à cette demande. La lutte contre l'abus et les éventuelles obligations de conservation doivent être examinées séparément. Aucune case de consentement marketing obligatoire, ni inscription publicitaire par défaut.

### Mention au voisinage du formulaire, à finaliser

> Les informations saisies permettent à [responsable confirmé] d'étudier votre projet et de vous recontacter. Le nom, la commune ou le code postal, le type de travaux, la description et au moins un moyen de contact sont nécessaires pour traiter votre demande ; la période souhaitée et le second moyen de contact sont facultatifs. Les destinataires sont [personnes et prestataires autorisés]. Les données sont conservées [durée et point de départ approuvés]. Pour exercer vos droits, contactez [coordonnées validées]. Consultez la politique de confidentialité pour connaître les conditions du traitement et vos droits.

Cette mention n'est activable en production qu'après remplacement de tous les éléments variables et validation de la politique liée.

### Politique à finaliser avec le responsable du site

| Sujet | Décision et réalité technique à documenter |
| --- | --- |
| Responsable | Société et contact d'exercice des droits ; ne pas inventer de DPO. |
| Demandes | Finalité de devis et rappel ; mesures précontractuelles si applicables. Pas d'usage marketing secondaire prévu. |
| Données | Nom, commune/code postal, prestation, description, téléphone et/ou email, période facultative. Aucun document joint demandé. |
| Destinataires | Christophe et, uniquement pour les opérations nécessaires, Aliant / hébergeur / prestataire email identifiés. Décrire leur rôle réel. |
| Stockage | Message email et éventuelles copies dans la boîte / sauvegarde ; vérifier le comportement du code et de la messagerie. Ne pas promettre « aucune conservation » parce que WordPress ne crée pas de dossier client. |
| Conservation | Fixer durée et point de départ pour demandes sans suite, demandes devenues contrats, logs antispam, journaux serveur et sauvegardes ; organiser la purge. Aucune durée chiffrée du modèle n'est une durée approuvée. |
| Droits | Accès, rectification, effacement, limitation et portabilité selon conditions ; opposition pour les traitements où elle est applicable ; réclamation auprès de la CNIL. Indiquer le contact effectif et la procédure. |
| Localisation | Pays d'hébergement des données, messagerie et sauvegardes ; transferts hors EEE éventuels et garanties à établir. Ne pas déduire cette localisation de celle d'Aliant. |
| Sécurité | Accès limités, transmission HTTPS, sauvegardes protégées ; information proportionnée sans promesse de sécurité absolue. |

La [CNIL sur les cookies et traceurs](https://www.cnil.fr/fr/cookies-et-autres-traceurs/que-dit-la-loi) distingue les opérations nécessitant un consentement des exemptions. Contrôler dans le navigateur cookies, requêtes, stockage local et éventuels ajouts d'extensions avant de décider du bandeau. L'absence d'outil publicitaire n'est pas une déclaration globale de conformité.

## Messagerie et conversion

1. Choisir un compte de destination effectivement consulté et un expéditeur autorisé. Renseigner la configuration privée `FP_QUOTE_TO`, `FP_MAIL_FROM`, `FP_SMTP_HOST`, `FP_SMTP_PORT`, `FP_SMTP_USER`, `FP_SMTP_PASS`, `FP_SMTP_SECURE` selon le fournisseur.
2. Vérifier TLS et certificats du serveur SMTP. Préparer SPF/DKIM/DMARC avec le fournisseur, sans modifier les DNS pendant cette mission.
3. Tester sur staging les deux modes de contact, les champs invalides, l'antispam, le quota et l'indisponibilité SMTP. L'erreur garde la saisie et permet un appel.
4. Aliant déclenche un test de livraison réelle convenu et constate sa présence en boîte de réception, avec objet, destinataire et Reply-To corrects. Un retour positif de `wp_mail` prouve l'acceptation par le transport, pas cette livraison.
5. Consigner la date et le résultat sans enregistrer d'email réel ou de secret dans le dépôt ; activer les validations de transport seulement ensuite.

## Ordre de publication, après autorisation explicite

- [ ] Confirmer toutes les données et les textes ; renseigner les mentions et confidentialité définitives ; vérifier les mécanismes de conservation.
- [ ] Vérifier la recette et corriger les blocages réels, notamment rôles, formulaire et rendu de production.
- [ ] Choisir l'hébergeur et préparer le staging protégé selon [maintenance.md](maintenance.md).
- [ ] Sauvegarder base, médias et configuration privée ; restaurer sur une instance isolée et conserver le compte rendu.
- [ ] Créer des comptes réels séparés, secrets uniques et récupération testée ; supprimer les comptes/identifiants locaux du serveur public.
- [ ] Installer le code et ses dépendances fixées ; importer uniquement les contenus approuvés ; ne pas importer de fixtures.
- [ ] Vérifier HTTPS, certificats, redirections http et www, puis obtenir l'autorisation finale de publication/DNS.
- [ ] Au basculement autorisé, régler les URL WordPress de production ; activer les approbations privées ; garder la page de remerciement non indexable.
- [ ] Contrôler accueil → prestation → devis et livraison réelle, liens internes, sitemap, canonicals, JSON-LD et absence d'URL de staging.
- [ ] Vérifier les pages publiques sans session : aucun exemple fictif, texte juridique incomplet, secret, barre de debug ou donnée de compte.
- [ ] Retirer le noindex du seul site public ouvert avec `wp option update blog_public 1` après validation ; conserver protection et noindex du staging.

## Référencement et mesure après ouverture

Créer/raccorder Search Console seulement avec autorisation du compte concerné ; soumettre le sitemap natif. Aligner ensuite les informations confirmées avec la fiche Google Business existante, sans créer de doublon ni modifier cette fiche de sa propre initiative. Tester le JSON-LD avec les [outils et règles Google LocalBusiness](https://developers.google.com/search/docs/appearance/structured-data/local-business) et le type [HousePainter](https://schema.org/HousePainter). Aucun avis, horaire d'accueil ou point géographique inventé ; aucun résultat Google garanti.

Les événements `click_phone`, `click_quote`, `form_start`, `form_success`, `form_error` préparent une mesure sans collecter les champs du formulaire. Ne pas connecter Twenty/PostHog d'Aliant. Si un outil analytique est décidé, utiliser un projet client séparé, définir finalité/rétention et réévaluer l'information/consentement nécessaires. `form_success` n'est ni un devis accepté ni un client gagné ; `click_phone` n'est ni une durée d'appel ni un appel abouti.
