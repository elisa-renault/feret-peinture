# Audit des mentions légales et de la confidentialité

Date : 16 septembre 2026. Médiation et email exclus à la demande d’Elisa, y compris la configuration du fournisseur de messagerie. Audit documentaire et technique, sans modification des pages ni activation des validations.

## Conclusion

### Corrections appliquées après l’audit

Les constats ci-dessous restent la photographie initiale. Corrections déployées le 16 septembre 2026 :

- Responsable identifié comme EURL CHRISTOPHE FERET, avec siège et représentant ; distinction des bases précontractuelle, contractuelle, obligation légale et intérêt légitime.
- Droits, contact postal, délai d’un mois, prolongation motivée, vérification proportionnée d’identité et lien de réclamation CNIL ajoutés.
- Données alignées sur le formulaire actuel simplifié de rendez-vous (nom, commune, description, téléphone et/ou email). Type et période ne sont plus présentés comme des champs affichés.
- Aliant et IONOS identifiés ; stockage messagerie/ordinateur et absence de prospection repris des réponses validées.
- Antispam et ALTCHA auto-hébergé décrits ; nettoyage fp_quote_cleanup exécuté avec succès. Défis ALTCHA : vingt minutes ; preuve consommée : une heure ; anti-doublon : vingt-quatre heures. Suppression lors de la tâche horaire.
- Conservation des factures précisée : dix ans depuis la clôture de l’exercice. Critères d’archivage des autres pièces exposés, sans inventer une purge déjà opérationnelle.
- Mention d’information et lien vers la politique ajoutés avant le bouton d’envoi.
- Mobile public autorisé ajouté aux mentions : le constat historique d’absence de téléphone est corrigé.
- Textes intégrés aux deux pages WordPress par scripts/sync-legal-pages.php avec sauvegarde des contenus précédents dans fp_legal_snapshot_20260916_193442. La cause « contenu vide » est résolue. Les indicateurs d’approbation ne sont pas activés : cela ne prétend pas faire passer l’ensemble du contrôle de lancement.

Contrôles effectués avec Edge via Playwright : accueil, contact (redirection depuis /devis/) et confidentialité, sans connexion WordPress ni envoi de formulaire. Aucun cookie, aucun stockage local, aucune ressource d’un domaine tiers relevés ; seul wpEmojiSettingsSupports dans le stockage de session. La politique décrit ce stockage technique. Pas de débordement horizontal sur confidentialité à 390 px. Syntaxe PHP du formulaire et diff contrôlés. Le test ne couvre pas une session administrateur ou un envoi réel.

Restent ouverts, sans les masquer : rétention globale des journaux et sauvegardes, lieux de stockage et transferts éventuels, mise en œuvre réelle de la suppression des emails/dossiers par Christophe Feret. Nginx tourne ses journaux quotidiennement avec 14 archives, mais Apache écrit dans journald : cette seule rotation ne permet pas de promettre une durée globale de 14 jours. Le site et la messagerie nécessitent des preuves de localisation et de conditions contractuelles ; le siège de netcup et la marque IONOS ne suffisent pas. La politique reste à compléter sur ces points avant validation RGPD finale. Les exclusions médiation et transport email sont conservées.

Références complémentaires utilisées : [conservation des documents](https://entreprendre.service-public.gouv.fr/vosdroits/F10029), [réponse aux demandes de droits](https://www.cnil.fr/fr/repondre-une-demande-de-droit-dacces).

Mise à jour après réponses d’Elisa : accès ponctuel de maintenance d’Aliant ajouté à la politique ; Christophe Feret seul assure le suivi client ; absence d’offres commerciales explicitée. Christophe Feret est confirmé comme responsable de vérifier les droits et autorisations des photos avant publication. Ces décisions sont acquises. Le constat initial ci-dessous sur l’omission d’Aliant est donc corrigé ; les autres aspects de ce constat (données techniques et journaux) restent à traiter.

Les deux pages ne sont pas encore prêtes pour la production dans le périmètre examiné. Les mentions d’identité, d’hébergement et de photographies sont en place. La confidentialité reste incomplète et le contrôle technique de publication ne reconnaît pas les textes affichés par le thème comme des contenus de pages finalisés.

## Constats et corrections

| Priorité | Constat vérifié | Correction attendue |
| --- | --- | --- |
| Haute | La confidentialité ne décrit aucun droit précisément : elle annonce que les modalités restent à finaliser. | Décrire accès, rectification, effacement, limitation et, selon la base légale, opposition et portabilité ; indiquer comment adresser une demande et traiter celle-ci, avec un lien vers la CNIL. |
| Haute | « Le responsable du traitement est Christophe Feret » ne distingue pas la personne du gérant de la société CHRISTOPHE FERET. | Identifier sans ambiguïté l’EURL CHRISTOPHE FERET, son siège et son représentant, en cohérence avec les mentions. L’EURL est déjà validée dans le suivi, ne pas la redemander. |
| Haute | « Peut relever de mesures précontractuelles » laisse la base légale indéterminée. Les traitements de sécurité et antispam ne sont pas décrits. | Définir une base précise par finalité : préparation des travaux à la demande du visiteur, sécurité/antispam, éventuelle conservation probatoire. Documenter l’intérêt légitime si retenu. |
| Haute | La page promet trois ans pour les demandes sans suite mais annonce des durées à préciser pour contrats, données techniques et sauvegardes. Le code ne prouve pas une purge des messages à trois ans. | Conserver la durée déjà validée ; organiser sa mise en œuvre et documenter les autres durées ou critères, avec le cycle d’effacement des sauvegardes. Ne pas présenter une absence de stockage de messages en base comme une absence de conservation globale. |
| Haute | La politique omet l’accès technique d’Aliant alors que la mention du formulaire le prévoit. Elle décrit les champs de contact mais pas les identifiants antispam dérivés de l’IP. | Aligner destinataires et catégories de données sur le fonctionnement réel. Vérifier aussi les journaux serveur, leurs accès et leur rétention. |
| Haute, technique | Sur le serveur, les pages 8 (mentions-legales) et 9 (confidentialite) ont un post_content vide. Le thème affiche les fichiers de remplacement, mais fp_launch_errors exige un contenu de plus de 150 caractères et une validation de chaque page. | Intégrer les textes finalisés aux pages WordPress ou harmoniser le mécanisme de validation avec les modèles. Vérifier ensuite le passage du contrôle sans contourner les autres conditions de lancement. Ce constat est indépendant des exclusions de cet audit. |
| Moyenne | « Les cookies [...] doivent être vérifiés » est une instruction de travail, pas une information destinée au visiteur. | Faire un relevé navigateur des cookies, stockages et requêtes du parcours public et rédiger l’information réelle. Un bandeau n’est pas automatiquement nécessaire pour des traceurs strictement nécessaires. |
| Moyenne | La liste des champs est correcte, mais la politique ne distingue explicitement que le contact nécessaire et la période facultative. | Préciser les autres champs obligatoires et les conséquences de leur absence : impossibilité de traiter la demande. Le formulaire fait déjà une partie de cette information. |
| À documenter | Pays de stockage et éventuels transferts non établis pour le serveur et les sauvegardes. | Vérifier les lieux et sous-traitants réels. Informer des transferts hors EEE et garanties s’il y en a ; l’adresse allemande de netcup ne démontre pas la localisation des données. |
| Écart connu, décision conservée | Aucun téléphone de l’entreprise n’est affiché, conformément au choix d’Elisa. Service Public inclut pourtant le téléphone de contact parmi les mentions d’identification d’une société. | Consigner l’écart sans republier un numéro privé ni redemander la décision. Un éventuel numéro professionnel dédié serait une piste de régularisation, non mise en œuvre dans cet audit. Le téléphone de netcup ne remplace pas celui de l’entreprise. |

## Éléments satisfaisants dans le périmètre

- Identité, EURL, capital, siège, SIREN/SIRET, RCS, TVA, directeur de publication et coordonnées de netcup présents dans la page servie.
- Rubrique photographies pérenne distinguant illustrations et réalisations, droits nécessaires, exceptions légales/licences et demandes de retrait. Elle ne vaut pas autorisation individuelle de publier une photo.
- Liens séparés vers les deux pages dans le pied de page.
- Données du formulaire cohérentes avec les champs traités : nom, commune/code postal, prestation, description, téléphone et/ou email, période facultative ; pièces jointes refusées.
- Aucun enregistrement des messages clients dans une table métier par le gestionnaire de formulaire inspecté. Cette observation ne couvre pas les journaux ou sauvegardes de l’infrastructure.
- Le script du thème émet seulement des événements locaux : aucun envoi analytique ni identifiant persistant trouvé dans ce fichier.

## Preuves techniques et limites

Lecture authentifiée des pages réellement servies sur feret-peinture.fr/mentions-legales/ et /confidentialite/, lecture des contenus WordPress par la commande de consultation des pages, inspection du thème et du module de formulaire. Aucune soumission de donnée personnelle ni changement de configuration.

Fichiers : `wp-content/themes/feret-peinture/inc/legal-draft.php`, `inc/privacy-draft.php`, `page.php`, `footer.php`, `assets/site.js` et `wp-content/plugins/feret-peinture-core/includes/form.php`, `includes/publication.php`.

Antispam dans le code : HMAC de l’IP avec fenêtre tournante de 15 minutes ; expiration du compteur une heure après sa création ; jeton anti-doublon conservé avec expiration à 24 heures ; nettoyage planifié toutes les heures. L’effacement effectif dépend de l’exécution de la tâche, non vérifiée ici. Ne pas confondre pseudonymisation et anonymisation.

L’encart « Document préparatoire » est conditionné au mode prévisualisation : il ne constitue pas à lui seul un défaut de la future version publique. Les paragraphes provisoires de confidentialité, eux, restent dans le contenu.

Pas de contrôle navigateur complet des traceurs, pas de lecture des journaux de visiteurs, pas d’audit des contrats de sous-traitance ou de preuve des lieux de stockage. L’absence de traceurs dans le script du thème ne prouve pas leur absence sur toute l’installation. Les CGV et documents commerciaux sont hors du périmètre de ces deux pages.

## Références consultées

- [Service Public : mentions obligatoires d’une société](https://entreprendre.service-public.gouv.fr/vosdroits/F37351) : identité et coordonnées du professionnel et de l’hébergeur.
- [CNIL : information des personnes](https://www.cnil.fr/fr/conformite-rgpd-information-des-personnes-et-transparence) : informations requises et clarté des formulations. Les constats de confidentialité ci-dessus appliquent ces exigences au texte observé.
- [CNIL : cookies et traceurs](https://www.cnil.fr/fr/cookies-et-autres-traceurs/que-dit-la-loi) : consentement et exemptions.

Les exclusions demandées ne sont pas réintroduites dans les conditions de lancement. Aucune conformité globale ni autorisation de mise en ligne n’est déduite de cet audit.
