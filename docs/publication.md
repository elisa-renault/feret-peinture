# Prévisualisation et ouverture au public

État au 16 septembre 2026 : le site est déployé chez netcup sur feret-peinture.fr en prévisualisation protégée, sans indexation et avec les emails sortants bloqués. Voir [déploiement privé](deploiement-prive-vps.md). L’ouverture publique reste à autoriser. Les décisions acquises sont consignées dans [les réponses d’Elisa](reponses-elisa-2026-09-16.md) ; ne pas les redemander.

## Suivi des validations restantes

Audit du 16 septembre 2026, hors médiation et email : voir [le rapport des pages légales et confidentialité](audit-legal-confidentialite-2026-09-16.md). Il relève notamment les informations RGPD encore provisoires et le décalage entre les textes servis par le thème et les contenus WordPress exigés par le contrôle de publication.

Mise à jour après correction : textes réécrits et synchronisés dans WordPress, mention du formulaire harmonisée, droits/base légale/responsable précisés, mobile autorisé intégré et traceurs vérifiés sur le parcours consulté. Le contenu vide des pages est corrigé. Restent la rétention des journaux et sauvegardes, la localisation/les transferts, l’organisation effective des suppressions et les validations finales. Ne pas remettre ces corrections rédactionnelles dans la liste des questions à Elisa.

| Point | État et prochaine action | Responsable |
| --- | --- | --- |
| Identité juridique, capital, adresse, immatriculation, TVA | Validés et intégrés. Aucune nouvelle confirmation à demander. | Acquis |
| Hébergeur netcup et coordonnées légales | Validés, intégrés aux deux pages et affichage vérifié. | Acquis |
| Téléphones de Christophe | Mobile public autorisé : 06 83 82 45 16. Fixe privé. | Acquis |
| Email et publication | Adresse choisie : contact@feret-peinture.fr ; directeur de publication : Christophe Feret. | Acquis |
| Messagerie | Boîte contact@feret-peinture.fr active chez IONOS, confirmée par Elisa. Création et fournisseur acquis. Reste à configurer/vérifier le transport du formulaire et constater sa réception réelle. | Boîte acquise ; test technique Aliant |
| Accès aux demandes | Validé : Christophe seul pour le suivi client ; Aliant ponctuellement pour la maintenance. | Acquis |
| Transmission à un comptable | Validé : aucune facture ni autre document client transmis à un comptable ou cabinet comptable. Ne pas l’ajouter aux destinataires de la politique. | Acquis |
| Usage des coordonnées | Validé : réponses aux demandes et suivi des travaux uniquement, aucun envoi d’offres commerciales. | Acquis |
| Vérification des photos | Validé : Christophe vérifie droits et autorisations avant chaque publication. Ne plus demander qui s’en charge. | Acquis |
| Demandes sans suite | Validé : Christophe supprime les demandes sans suite de la boîte de réception trois ans après la dernière interaction. L’organisation pratique et la vérification de la suppression restent à mettre en place ; les sauvegardes sont traitées séparément. | Christophe |
| Autres conservations | Définir les durées et la purge des demandes devenues contrats, emails, journaux techniques, données antispam et sauvegardes. | Christophe et Aliant |
| Dossiers devenus clients | Supports confirmés : devis signés et factures dans la messagerie et dans un dossier uniquement sur l’ordinateur, sans synchronisation cloud ni sauvegarde séparée du dossier local. Définir la conservation sur les deux supports. Ne pas confondre avec les sauvegardes du site ou de la messagerie. | Stockage acquis ; conservation Christophe et Aliant |
| Prestataires et stockage | Accès ponctuel de maintenance d’Aliant validé et décrit dans la politique. Documenter la messagerie, les copies et sauvegardes, les pays de stockage et les éventuels transferts. Le siège allemand de netcup ne prouve pas la localisation du serveur. | Aliant |
| Confidentialité et formulaire | Finaliser les bases légales par usage, l’identité du responsable du traitement, les droits et leur procédure ; harmoniser politique et information près du formulaire. | Aliant, validation Christophe |
| Réponses aux demandes de droits | Validé : Christophe répond lui-même aux demandes d’accès, de correction et de suppression. Formaliser la procédure ; ne plus demander qui s’en charge. | Christophe |
| Cookies | Vérifier les traceurs réellement présents et adapter l’information et, si nécessaire, le consentement. | Aliant |
| Textes et images | Valider la présentation finale, les prestations et communes affichées, les crédits et les autorisations des photos réellement publiées. Les données déjà documentées ne sont pas à ressaisir. | Christophe |
| Documents commerciaux | Vérifier les informations précontractuelles et CGV adaptées aux devis/travaux. | Christophe |
| Recette et accès | Terminer la recette, vérifier restauration des sauvegardes, récupération des comptes et accès réels. | Aliant |
| Ouverture | Relire les pages finales, lever les points restants et obtenir l’accord explicite d’ouverture avant de retirer la protection et le noindex. | Elisa / Christophe, puis Aliant |

La validation d’une information ne vaut pas validation globale des pages légales. Aucun indicateur d’approbation finale n’est activé par cette mise à jour documentaire.

Décision d’Elisa du 16 septembre 2026 : Christophe n’a pas de médiateur ; l’adhésion est reportée et n’est pas retenue comme condition bloquante de mise en ligne dans le suivi du projet. La rubrique est retirée du site. Ce report ne constitue pas une conformité à l’obligation de médiation pour les prestations aux particuliers, qui reste à régulariser ultérieurement. Ne pas redemander cette décision ni réintroduire ce blocage sans nouvelle instruction. Aucune autorisation générale d’ouverture n’est donnée par ce seul report.

## État attendu selon l'environnement

| Environnement | Accès et indexation | Email et contenu |
| --- | --- | --- |
| Local | Adresses locales ; `WP_ENVIRONMENT_TYPE=local` ; noindex | Mailpit ; données synthétiques seulement. Fixtures sur demande explicite. |
| Staging | HTTPS + authentification Nginx ; noindex applicatif et en-tête | Destinataire de test maîtrisé par Aliant ; aucun email réel de visiteur. |
| Production avant validation | Ouverture bloquée tant que les validations privées manquent | Aucune transformation d'un formulaire inactif en faux succès. |
| Production validée | `https://feret-peinture.fr`, sans www ; redirections vérifiées | SMTP privé testé ; contenus approuvés ; aucun chantier de démonstration. |

Les indicateurs privés d'approbation ne sont pas des autorisations obtenues automatiquement. Aliant les active uniquement après les validations ci-dessous. Les réglages de publication et secrets SMTP restent privés. Les informations légales publiques validées figurent dans le modèle des mentions et la documentation.

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

Obtenir la validation finale de Christophe pour les textes métier, les prestations et communes affichées et les droits sur les images. Les noms, l’identité et la décision d’afficher uniquement son mobile sont acquis. Un annuaire ne confirme pas une disponibilité, un périmètre ou une promesse commerciale.

L’identité juridique est validée par Elisa le 16 septembre 2026 et consignée dans `reponses-elisa-2026-09-16.md` : ne plus demander de confirmation de ces informations. Le siège retenu est 74 T avenue du Maréchal Leclerc, 95440 Écouen. L’adresse professionnelle ne reçoit pas de clients.

## Mentions légales : trame interne à compléter

Ce canevas sert à préparer les pages ; il ne doit jamais être publié avec des crochets ou une mention « à compléter » présentée comme définitive. Les champs manquants sont recensés, pas déduits du nom du projet.

| Rubrique | Texte ou information à préparer |
| --- | --- |
| Éditeur | Validé : CHRISTOPHE FERET, EURL au capital de 500 €, 74 T avenue du Maréchal Leclerc, 95440 Écouen. |
| Immatriculation | Validé : SIREN 502 545 874 ; SIRET 502 545 874 00021 ; RCS Pontoise 502 545 874 ; TVA FR02502545874. |
| Contact | Mobile affiché pour convenir d’un rendez-vous sur place avant devis ; contact@feret-peinture.fr active chez IONOS. Livraison du formulaire à tester séparément. |
| Publication | Validé : Christophe Feret, gérant et directeur de publication. |
| Hébergement | Validé : netcup GmbH, Emmy-Noether-Straße 10, 76131 Karlsruhe, Allemagne ; +49 721 7540755-0 ; www.netcup.com. |
| Contenus | Identité graphique provisoire, crédits des ressources, conditions de réutilisation et droits des photographies effectivement publiées. |

Les informations d'identification sont fondées sur la [fiche officielle Service Public](https://entreprendre.service-public.gouv.fr/vosdroits/F37351), consultée le 15 septembre 2026 UTC. Faire vérifier aussi les informations précontractuelles / CGV applicables au parcours réel de devis et au contrat de travaux ; cette V1 ne conclut pas de vente en ligne. La page officielle est générale et ne remplace pas la qualification du cas précis.

## Données personnelles : texte de travail

La [CNIL précise les conditions de la base légale contractuelle et précontractuelle](https://www.cnil.fr/fr/les-bases-legales/contrat). Pour cette V1, traiter une demande de rendez-vous à l'initiative du visiteur peut relever des mesures précontractuelles, si les données sont nécessaires à cette demande. La lutte contre l'abus et les éventuelles obligations de conservation doivent être examinées séparément. Aucune case de consentement marketing obligatoire, ni inscription publicitaire par défaut.

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
| Conservation | Demandes sans suite : trois ans après la dernière interaction, validés. Définir les autres durées (contrats, logs antispam, journaux serveur, sauvegardes) et organiser la purge pour toutes les catégories. |
| Droits | Accès, rectification, effacement, limitation et portabilité selon conditions ; opposition pour les traitements où elle est applicable ; réclamation auprès de la CNIL. Indiquer le contact effectif et la procédure. |
| Localisation | Pays d'hébergement des données, messagerie et sauvegardes ; transferts hors EEE éventuels et garanties à établir. Ne pas déduire cette localisation de celle d'Aliant. |
| Sécurité | Accès limités, transmission HTTPS, sauvegardes protégées ; information proportionnée sans promesse de sécurité absolue. |

La [CNIL sur les cookies et traceurs](https://www.cnil.fr/fr/cookies-et-autres-traceurs/que-dit-la-loi) distingue les opérations nécessitant un consentement des exemptions. Contrôler dans le navigateur cookies, requêtes, stockage local et éventuels ajouts d'extensions avant de décider du bandeau. L'absence d'outil publicitaire n'est pas une déclaration globale de conformité.

## Messagerie et conversion

1. Choisir un compte de destination effectivement consulté et un expéditeur autorisé. Renseigner la configuration privée `FP_QUOTE_TO`, `FP_MAIL_FROM`, `FP_SMTP_HOST`, `FP_SMTP_PORT`, `FP_SMTP_USER`, `FP_SMTP_PASS`, `FP_SMTP_SECURE` selon le fournisseur.
2. Vérifier TLS et certificats du serveur SMTP. Préparer SPF/DKIM/DMARC avec le fournisseur, sans modifier les DNS pendant cette mission.
3. Tester sur staging les deux modes de contact du visiteur, les champs invalides, l'antispam, le quota et l'indisponibilité SMTP. L'erreur doit garder la saisie ; proposer un appel au mobile public de Christophe.
4. Aliant déclenche un test de livraison réelle convenu et constate sa présence en boîte de réception, avec objet, destinataire et Reply-To corrects. Un retour positif de `wp_mail` prouve l'acceptation par le transport, pas cette livraison.
5. Consigner la date et le résultat sans enregistrer d'email réel ou de secret dans le dépôt ; activer les validations de transport seulement ensuite.

## Ordre de publication, après autorisation explicite

- [ ] Finaliser uniquement les validations restantes du tableau ci-dessus ; renseigner les mentions et confidentialité définitives ; vérifier les mécanismes de conservation.
- [ ] Vérifier la recette et corriger les blocages réels, notamment rôles, formulaire et rendu de production.
- [x] Hébergeur netcup choisi et prévisualisation protégée déployée, voir [déploiement privé](deploiement-prive-vps.md).
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
