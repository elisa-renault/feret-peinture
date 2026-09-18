# Deuxième audit copywriting

16 septembre 2026, après application du premier audit. Audit initial suivi d’une correction autorisée par Elisa. La recommandation de renommage de la galerie a été explicitement rejetée : les illustrations sont des placeholders temporaires avant les vraies réalisations. Conserver la rubrique « Réalisations ». Voir le compte rendu d’application ci-dessous.

## Bilan

Le parcours principal est désormais clair : identifier les travaux, demander un rendez-vous, puis recevoir un devis après visite. Les corrections du formulaire et des métadonnées sont présentes. Une réécriture générale n’est pas nécessaire.

Les points restants concernent surtout la cohérence des intitulés et les répétitions. Deux viennent de la dernière intervention : la nouvelle présentation de Christophe répète le processus de visite, et le paragraphe papier peint reprend les mêmes consignes que la section suivante. La simplification a fonctionné à l’échelle des phrases, mais doit être achevée à l’échelle des pages.

## Périmètre réellement vérifié

Lecture authentifiée du HTML servi par la prévisualisation privée sur douze URL : accueil, entreprise, prestations, quatre fiches prestations, zone, contact, réalisations, exemple de sol aspect bois et remerciement sans jeton de succès. Lecture des titres, descriptions, contenus principaux et liens. Les deux autres fiches fictives n’ont pas été relues individuellement.

Le formulaire actif reste masqué sur le serveur privé. Ses textes ont été relus dans le navigateur local à 390 pixels, ainsi que la fiche revêtements muraux. Pas d’envoi de demande, de nouveau test de livraison ni d’audit juridique pendant cette passe. Les observations ne constituent pas une mesure de conversion.

Méthode : les sept passes de la skill [Copy-editing](C:/Users/Elisa/.codex/skills/copy-editing/SKILL.md). Les recommandations s’appuient sur les principes de [lecture concise et structurée de NN/g](https://www.nngroup.com/articles/how-users-read-on-the-web/), de [cohérence entre libellé et destination de NN/g](https://www.nngroup.com/articles/information-scent/) et d’[explicitation des questions du GOV.UK Design System](https://design-system.service.gov.uk/patterns/question-pages/). Ces sources orientent le jugement ; elles ne démontrent pas un effet commercial sur Feret Peinture.

## 1. La galerie de prévisualisation mélange réalisations et illustrations

**Recommandation annulée sur instruction d’Elisa.** Le contenu ci-dessous conserve le constat historique de l’audit ; il ne doit pas être appliqué. Les placeholders seront remplacés par de vraies réalisations. Aucun intitulé « Exemples d’ambiances » ni adaptation dynamique de la rubrique ne doit être introduit.

**À clarifier en priorité dans la prévisualisation. Constat certain, limité à cet environnement.**

Sur l’accueil distant, « Les réalisations » et « Des chantiers, en images. » introduisent trois cartes portant « Photo d’illustration ». L’archive s’intitule « Nos réalisations ». La fiche de sol annonce correctement un exemple fictif, mais son encadré conserve « Le chantier », « Commune » et « Lieu fictif ».

Les avertissements existent : il ne s’agit pas d’une galerie dépourvue de signalement. C’est le titre général qui présente une promesse différente de son contenu. La dernière synchronisation avait préservé le titre distant, différent du titre local ; cette divergence est désormais vérifiée sur le rendu réel.

**Recommandation :** pour une galerie composée uniquement d’exemples de prévisualisation, afficher « Exemples d’ambiances » et préciser « Ces illustrations présentent des ambiances. Elles ne montrent pas des chantiers réalisés par Christophe Feret. » Réserver « Réalisations » aux vrais travaux. Sur les fiches fictives, éviter l’encadré « Le chantier » et la fausse commune.

Ne pas renommer durablement la rubrique des chantiers authentiques en « Ambiances ». Le code distingue déjà les exemples de prévisualisation et les exclut des requêtes de production ; ce garde-fou a été relu, pas réexécuté en production pendant l’audit. Ce constat ne signifie donc pas que les exemples seront publiés au lancement.

Repères : accueil distant ; `archive-fp_project.php` ; `single-fp_project.php` ; données des exemples ; `fp_projects()` dans le plugin métier.

## 2. Le fil d’Ariane du remerciement parle encore de devis

**Correction certaine, faible effort.**

Sur `/merci/`, le fil d’Ariane affiche « Votre demande de devis », tandis que le texte parle de demande de rendez-vous. Le titre enregistré en base est resté ancien, malgré les nouveaux textes du modèle et du contenu initial des futures installations.

**Remplacement :** « Votre demande de rendez-vous ».

La page a été consultée sans soumission et sans jeton de succès : le message visible était donc « Parlons de votre projet ». L’incohérence du fil d’Ariane est indépendante de ce message. Il faut corriger le titre WordPress existant, pas seulement le modèle ou le script de création.

Dans le même passage, harmoniser le fil d’Ariane de la zone : « Votre projet et sa localisation » peut devenir « Zone d’intervention », déjà utilisé dans la navigation. Ce second ajustement est une finition, pas une erreur de compréhension majeure.

## 3. La fiche papier peint donne deux fois les mêmes consignes

**À alléger. Constat certain, introduit en partie par la dernière passe.**

« Votre projet de papier peint » demande de décrire les murs et de conserver la référence du produit. Juste après, « Pour préparer votre demande » redemande de décrire les murs et de garder la référence.

**Proposition :** remplacer ces deux sections par une seule :

> Pour préparer votre demande
>
> Décrivez les murs concernés, le revêtement existant et les défauts visibles. Si vous avez déjà choisi un papier peint, gardez sa référence pour l’échange. Christophe précisera avec vous la préparation, les quantités, la fourniture et la pose avant toute commande.

Cela conserve l’information utile sans réintroduire les dimensions des rouleaux et les raccords comme prérequis au contact.

Repère : contenu WordPress de `/prestations/revetements-muraux/`, également dans `config/pods/services.php`.

## 4. L’accueil répète trop la visite avant devis

**Amélioration éditoriale, sans urgence.**

La même information revient dans le premier écran, la présentation de Christophe, les trois étapes et la première réponse de FAQ. Une répétition près du bouton est utile ; quatre explications détaillées ajoutent peu.

**Recommandation :** conserver la phrase courte du premier écran, les trois étapes et la réponse de FAQ, utile aux personnes qui cherchent précisément un devis en ligne. Réduire le bloc « Votre interlocuteur » à son rôle relationnel :

> Christophe est votre contact pour présenter vos travaux et convenir d’un rendez-vous sur place.

Cette phrase utilise uniquement un rôle confirmé. Elle ne promet ni absence de sous-traitance ni exécution de tous les travaux par une seule personne. Éviter une nouvelle liste de qualités abstraites pour remplir l’espace.

Repère : `front-page.php` et champ `presentation`, également utilisé sur la page entreprise. Si ce champ est modifié, relire les deux emplacements.

## 5. Sur les fiches, le résumé et l’introduction se doublonnent

**Amélioration éditoriale ciblée.**

La fiche sols enchaîne « Stratifié, PVC et carrelage : préparation du sol, pose et finitions » et « Stratifié, PVC ou carrelage : Christophe Feret prépare le sol et pose le revêtement adapté à votre pièce ». Le lecteur reçoit deux fois presque la même information.

La fiche murale présente également deux fois papier peint, toile de verre et préparation, avant d’expliquer les travaux.

**Recommandation :** conserver les résumés utiles aux listes de prestations et supprimer les paragraphes d’introduction qui les paraphrasent. Les sections « Préparer le sol existant » et « Préparer les murs » peuvent suivre directement. Ne pas supprimer l’introduction de peinture intérieure : ses exemples de salon et de cage d’escalier apportent une information différente.

Dans la fiche sols, supprimer aussi la dernière phrase « La technique de pose et les travaux prévus sont précisés dans le devis » : le rôle du devis a déjà été expliqué dans les sections précédentes.

Repère : contenus WordPress des deux fiches, également dans `config/pods/services.php`.

## 6. Quelques titres peuvent mieux correspondre à leur contenu

**Finitions facultatives.**

| Emplacement | Texte actuel | Observation | Proposition |
| --- | --- | --- | --- |
| Liste des prestations | « Vous hésitez entre plusieurs prestations ? » | La réponse porte sur le regroupement de travaux, pas sur le choix d’une prestation. | « Plusieurs types de travaux à prévoir ? » Garder la réponse actuelle. |
| Page entreprise | « Prendre le temps de préparer » | Suggère surtout une durée ; le paragraphe explique la préparation des surfaces. | « Préparer les surfaces avant de peindre ». |
| Contact actif local, aide de description | Exemple limité aux murs et plafond | Correct, mais moins parlant après une fiche sols. | « Par exemple : repeindre un salon ou remplacer le sol d’une chambre. » Aide commune, sans ajouter de champ. |

Les répétitions des boutons à différents endroits ne sont pas automatiquement un défaut. Sur mobile, le bloc latéral des prestations devient néanmoins voisin du bandeau de contact : si l’ensemble paraît long, réduire l’un de ces blocs plutôt que chercher des synonymes pour la même action. Aucune baisse d’abandon n’est démontrée.

## Éléments validés par cette seconde lecture

- « Demander un rendez-vous » décrit correctement l’action ; garder ce libellé.
- La règle « téléphone ou email, un seul suffit » est visible avant saisie dans le formulaire local.
- Le visiteur n’a plus à préparer un dossier technique avant le premier contact.
- La correction « rendez-vous avec Christophe Feret » apparaît dans les métadonnées distantes.
- Les illustrations de l’accueil et de la peinture intérieure ont désormais leur légende.
- La page zone ne comporte pas de limite de trajet. Cette décision est acquise et ne fait l’objet d’aucune recommandation contraire.
- Les réserves sur les supports, l’humidité et la météo sont utiles et doivent rester.
- Le formulaire fermé sur le serveur est un état volontaire de prévisualisation, pas un échec de conversion à corriger dans cet audit.

## Suite proportionnée

Priorité à la cohérence galerie/illustrations et au titre WordPress du remerciement, puis suppression des doublons des fiches et allègement du bloc interlocuteur. Les autres propositions sont des finitions. Les preuves photographiques authentiques restent une évolution de contenu à réaliser avec les documents disponibles, pas un manque à combler par de nouvelles promesses.

Aucune correction n’avait été appliquée lors de la remise de l’audit. Les actions suivantes ont ensuite été autorisées par Elisa.

## Corrections appliquées

- Galerie conservée comme rubrique « Réalisations ». Le renommage temporairement préparé en local a été retiré avant déploiement. Les avertissements existants des placeholders et les protections de production sont conservés.
- Titres WordPress du remerciement et de la zone harmonisés avec leurs contenus.
- Présentation raccourcie : « Christophe Feret est votre contact pour présenter vos travaux et convenir d’un rendez-vous sur place. »
- Consignes papier peint regroupées ; introductions redondantes des fiches murs et sols et répétition finale sur le devis supprimées.
- Titres entreprise et regroupement de prestations clarifiés ; exemple du formulaire ouvert aux travaux de peinture et de sol.

Migration ciblée `scripts/update-copywriting-v2.php`, avec contrôle des versions connues, sauvegarde et révisions. Quatre contenus et la présentation actualisés en local et sur le serveur ; seconde exécution sans changement. Sept fichiers déployés après sauvegarde : `/srv/apps/feret-peinture/backups/copy-v2-20260916T205700Z`.

Syntaxe PHP vérifiée ; 106 contrôles HTTP locaux réussis pendant la passe. Rendu final de six pages vérifié à 390 et 1 440 pixels sans débordement ; capture de la fiche murale mobile examinée. Après déploiement, douze pages distantes relues pour vérifier les corrections, le maintien de « Réalisations » et l’absence de durée de trajet. Contrôle du serveur privé réussi : accès protégé, ressources disponibles avec authentification, redirections et absence d’indexation conservés. Aucun email réel ni ouverture publique pendant cette intervention.
