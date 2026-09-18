# Audit copywriting de Feret Peinture

16 septembre 2026. Audit initial, puis application autorisée par Elisa (« go »). Les corrections sont appliquées en local et sur la prévisualisation privée. Voir le compte rendu en fin de document.

## Diagnostic

Le site explique correctement le métier, l’implantation et le déroulement du premier contact. Son ton sobre est adapté à un artisan. Le principal progrès consiste à mieux aider le visiteur à reconnaître son projet et à prendre contact, avec moins de répétitions et de détails techniques présentés comme des prérequis.

Les corrections les plus certaines concernent les consignes du formulaire, la formulation de la demande de rendez-vous et une faute dans les métadonnées. Les améliorations de l’accroche et des preuves sont des recommandations éditoriales, pas des résultats de conversion mesurés.

## Périmètre et méthode

Lecture des modèles actuels de l’accueil, des prestations, de l’entreprise, de la zone, du contact, du remerciement, des réalisations et du pied de page ; lecture des quatre contenus de référence dans `config/pods/services.php`, du formulaire et des métadonnées. Les contenus personnalisables de WordPress peuvent différer de ces fichiers : la base et la prévisualisation distante n’ont pas été relues pendant cet audit. Les observations sur ces contenus sont donc conditionnelles à leur présence effective en base.

Les décisions de `docs/reponses-elisa-2026-09-16.md` et les vérifications métier de `docs/verification-prestations-2026-09-16.md` ont été prises en compte. Les ajouts documentés de PVC collé et de carrelage sont acquis. Le code actuel dirige vers `/contact/` et redirige `/devis/` ; l’ancienne mention de conservation de cette dernière URL dans l’historique ne décrit plus la destination actuelle.

Application de la skill [Copy-editing](C:/Users/Elisa/.codex/skills/copy-editing/SKILL.md) : clarté, ton, bénéfice client, preuve, précision, projection et réduction des hésitations. Sa grille est une méthode de relecture, pas une preuve scientifique. Pas de panel d’experts réel ni de note prétendument objective.

Références externes consultées :

| Référence | Principe retenu | Limite d’application |
| --- | --- | --- |
| [Nielsen Norman Group, How Users Read on the Web, 1997](https://www.nngroup.com/articles/how-users-read-on-the-web/) | Faciliter le survol par des titres informatifs et une rédaction concise et factuelle. | Étude historique d’utilisabilité ; aucun pourcentage de gain transposé à ce site. |
| [Nielsen Norman Group, Information Scent, 2020](https://www.nngroup.com/articles/information-scent/) | Le libellé d’un lien doit aider à prévoir sa destination. | Guide pour évaluer les intitulés, pas preuve qu’un bouton particulier convertira mieux. |
| [Nielsen Norman Group, Trustworthiness in Web Design](https://www.nngroup.com/articles/trustworthy-design/) | Évaluer la transparence et les éléments qui rendent une entreprise crédible. | Les preuves à publier doivent venir de Christophe Feret et de ses chantiers. |
| [GOV.UK Design System, Question pages](https://design-system.service.gov.uk/patterns/question-pages/) | Demander les informations nécessaires et expliciter ce qui est attendu. | Référentiel de services publics ; ses choix de structure ne sont pas automatiquement adaptés à un formulaire commercial court. |
| [GOV.UK Design System, Error message](https://design-system.service.gov.uk/components/error-message/) | Expliquer l’erreur et la manière de la corriger. | Application aux messages, sans audit technique d’accessibilité complet. |

## Ce qui fonctionne déjà

- « Peintre en bâtiment à Écouen » répond immédiatement au métier et à la localisation. À conserver.
- Christophe Feret est nommé. Le site évite de fabriquer une équipe ou une marque impersonnelle.
- « Un rendez-vous avant le devis » et les trois étapes rendent le fonctionnement concret.
- La peinture intérieure commence par des situations reconnaissables : salon, cage d’escalier, pièce abîmée.
- Les réserves sur l’humidité, le dégât des eaux et la météo évitent des promesses excessives.
- La FAQ indique que la surface exacte n’est pas nécessaire. C’est une bonne réponse à une hésitation avant contact.
- « Envoyer ma demande » et le message de remerciement ne prétendent pas que le rendez-vous est confirmé.
- Les prestations sont étayées par le dossier métier. Aucun besoin d’ajouter des superlatifs, une gratuité ou un délai de réponse non documentés.

## Recommandations prioritaires

Priorité 1 : correction claire ou obstacle potentiel au contact. Priorité 2 : amélioration du message. Priorité 3 : finition. Ces priorités expriment un jugement éditorial, sans mesure de trafic ni de conversion.

### 1. Dire immédiatement qu’un seul moyen de contact suffit

**Priorité 1. Constat certain dans le formulaire prévu lorsque l’envoi est actif.**

Emplacement : `wp-content/plugins/feret-peinture-core/includes/form.php`, fonction `fp_render_quote_form`, groupe `contact-methods`.

Les champs « Téléphone » et « Email » n’ont pas d’astérisque. Le serveur impose pourtant au moins l’un des deux. « Renseignez un téléphone ou un email » apparaît seulement après une erreur. Le visiteur peut croire les deux facultatifs ou hésiter à communiquer les deux.

**Ajouter avant les deux champs :**

> Indiquez votre téléphone ou votre email pour que Christophe Feret puisse vous recontacter. Un seul des deux suffit.

Ne pas rendre les deux obligatoires pour résoudre une ambiguïté de texte. Cette consigne applique le principe d’explicitation des questions de GOV.UK.

### 2. Éviter l’impression d’une réservation immédiate

**Priorité 2. Hypothèse d’amélioration, pas défaut bloquant.**

Emplacements : bouton commun dans `wp-content/themes/feret-peinture/functions.php`, titre du contact dans `page.php`, boutons des fiches et du pied de page.

« Prendre rendez-vous » est compréhensible et le texte d’introduction précise déjà le fonctionnement. Il peut néanmoins faire attendre un calendrier, alors que la page permet d’envoyer une demande.

**Proposition cohérente :**

- Boutons et titre du contact : « Demander un rendez-vous ».
- Introduction : « Indiquez la commune du chantier, les travaux envisagés et un moyen de vous joindre. Christophe Feret vous recontactera pour convenir d’une visite sur place, avant d’établir le devis. »
- Bouton d’envoi : conserver « Envoyer ma demande ».
- Confirmation : conserver le message actuel, qui annonce la reprise de contact.

Le libellé proposé décrit plus précisément l’action disponible, selon le principe d’anticipation de la destination de NN/g. Garder l’option d’appel bien visible.

### 3. Alléger les prérequis de la fiche papier peint

**Priorité 1. Constat dans le contenu de référence.**

Emplacement : `config/pods/services.php`, `revetements-muraux`, section « Votre projet de papier peint ».

**Actuel :** « indiquez la référence du papier peint, les dimensions des rouleaux et le type de raccord ».

Cette liste ressemble à un dossier technique à préparer. Elle cadre mal avec la promesse d’un premier contact simple et d’une visite d’évaluation.

**Proposition :**

> Pour un mur décoratif ou une pièce entière, décrivez les murs concernés. Si vous avez déjà choisi un papier peint, gardez sa référence pour l’échange. La préparation, les quantités, la fourniture et la pose seront précisées avec Christophe Feret avant toute commande.

Les informations techniques restent utiles au travail, mais ne doivent pas sembler obligatoires pour contacter Christophe Feret. Même logique pour les surfaces, références et couleurs des autres fiches : préciser « si vous les connaissez » lorsque nécessaire.

### 4. Corriger la préposition dans les métadonnées

**Priorité 1. Erreur rédactionnelle certaine.**

Emplacement : `wp-content/themes/feret-peinture/functions.php`, `fp_theme_seo_data`, entrées `contact` et `merci`.

**Actuel :** « Prendre rendez-vous à Christophe Feret », « convenir d’un rendez-vous à Christophe Feret », « demande de rendez-vous à Christophe Feret ».

**Propositions :**

- Titre contact : « Demander un rendez-vous avec Christophe Feret | Feret Peinture ».
- Description contact : « Présentez vos travaux et la commune du chantier à Christophe Feret. Convenez d’une visite sur place avant l’établissement du devis. »
- Description de remerciement : « Suivi de votre demande de rendez-vous avec Christophe Feret. »

Ces valeurs alimentent les métadonnées prévues par le thème ; un éventuel plugin SEO peut les remplacer. Aucun classement Google observé ni gain SEO revendiqué.

### 5. Faire davantage parler l’accueil du projet du visiteur

**Priorité 2. Recommandation éditoriale.**

Emplacement : `wp-content/themes/feret-peinture/front-page.php`, premier écran.

Le titre est bon. Le sous-titre énumère les métiers ; le paragraphe suivant énumère les supports. L’ensemble informe, mais la projection dans un projet peut être plus directe.

**Conserver le H1 et remplacer les deux textes sous le titre par :**

> Peinture intérieure et extérieure, revêtements de sols et murs.
>
> Rafraîchir une pièce, rénover une façade ou changer un sol : Christophe Feret prépare les supports et réalise les finitions adaptées à votre projet.

**Ajouter près du bouton, si la composition reste légère :**

> Une visite sur place pour évaluer les travaux avant le devis.

Cette version réutilise les prestations documentées. Elle ne promet ni résultat esthétique garanti ni absence de contraintes. Son intérêt doit être vérifié par la compréhension du visiteur, pas par une préférence de style seule.

### 6. Donner une utilité propre au bloc « Christophe Feret »

**Priorité 2. Constat conditionnel à la présentation enregistrée.**

Emplacements : bloc entreprise de l’accueil et page entreprise ; champ WordPress `presentation`.

Le texte de repli, également prévu par la migration éditoriale, répète « peintre en bâtiment à Écouen, dans le Val-d’Oise ». Le premier écran l’a déjà expliqué. Le bloc « Votre interlocuteur » gagnerait à préciser le rôle de Christophe Feret.

**Proposition pour ce bloc :**

> Christophe Feret échange directement avec vous sur votre projet. Il se déplace pour examiner les surfaces et évaluer les travaux avant d’établir le devis.

Ce rôle est confirmé. Ne pas le transformer en « Christophe Feret réalise seul tous les travaux » ou « sans sous-traitance » : ces engagements ne sont pas établis.

## Relecture des autres pages et composants

| Emplacement | Constat | Proposition | Priorité |
| --- | --- | --- | --- |
| Liste des prestations | Les quatre familles sont lisibles. Le résumé des sols mélange matériaux et techniques de pose. | « Stratifié, PVC et carrelage : préparation du sol, pose et finitions. » Garder flottant/collé dans la fiche. | 2 |
| Fiche intérieure | Bonne entrée par les usages. « Huisseries » est plus technique que le reste. | « Portes, encadrements et plinthes », si « encadrements » décrit bien le passage concerné. | 3 |
| Fiche extérieure | « Une couche d’impression » peut être mal compris. | « Une sous-couche adaptée, puis une peinture de ravalement. » Conserver les conditions liées au support et à la météo. | 2 |
| Fiche sols, introduction | « La pose d’un sol comprend… » décrit une procédure sans nommer les matériaux proposés. | « Stratifié, PVC ou carrelage : Christophe Feret prépare le sol et pose le revêtement adapté à votre pièce. » | 2 |
| Fiche sols, préparation | « Ragréage » n’est pas expliqué. | « Un ragréage, pour corriger les irrégularités du sol, ou une sous-couche peut être prévu selon le support et le revêtement. » | 2 |
| Fiche sols, finitions | Deux paragraphes successifs répètent plinthes et seuils. | Les réunir en un passage sur les finitions prévues au devis et les contraintes de portes et de mobilier. | 3 |
| Page entreprise | « Un devis détaillé, pièce par pièce » relie déjà une caractéristique à un bénéfice : savoir ce qui est prévu. | Conserver ce passage. Ne pas remplacer les détails par « qualité et savoir-faire ». | Conserver |
| Page zone | Les villes lointaines sont conditionnelles. | Décision explicite d’Elisa pendant l’application : ne pas afficher de limite de deux heures. Conserver les secteurs possibles et la confirmation selon la commune. | Décision acquise |
| FAQ accueil | Les réponses sur la visite et la surface exacte réduisent utilement l’incertitude. | Conserver ; éventuellement remplacer « Quelques réponses utiles » par « Avant votre demande de rendez-vous ». | 3 |
| Formulaire, commune | « Commune ou code postal » ne précise pas dans son libellé qu’il s’agit du chantier. | « Commune ou code postal du chantier ». | 2 |
| Formulaire, description | « Vos travaux en quelques mots » est clair, sans exemple pour démarrer. | Aide sous le libellé : « Par exemple : repeindre les murs et le plafond d’un salon. Une surface approximative suffit si vous la connaissez. » | 2 |
| Erreur de description | « Décrivez les travaux en 10 à 4 000 caractères » est exact mais administratif. | Distinguer : « Décrivez vos travaux en quelques mots (au moins 10 caractères). » et « Votre description dépasse 4 000 caractères. Raccourcissez-la pour envoyer votre demande. » | 3 |
| Formulaire indisponible | « L’envoi des demandes sera disponible à l’ouverture du site » convient à la prévisualisation, moins à une panne après ouverture. | Prévoir un message distinct en exploitation : « Le formulaire est momentanément indisponible. Vous pouvez contacter Christophe Feret par téléphone ou par email. » avec les liens effectivement disponibles. | 2 |
| Pied de page | « Mentions légales » mène désormais à une page contenant aussi la confidentialité. | « Mentions légales et confidentialité » pour rendre cette information trouvable depuis toutes les pages. | 2 |
| Remerciement | « Votre message a été envoyé. Christophe Feret vous recontactera pour fixer le rendez-vous. » décrit correctement la suite. | Conserver. Ne pas écrire « Rendez-vous confirmé ». | Conserver |

Les ajustements de vocabulaire sont une application au site du principe de lecture rapide de NN/g, non une recommandation de supprimer les explications métier utiles.

## Preuves : le levier à développer avec des contenus authentiques

L’ancienneté affichée (« Créée en 2008 »), le nom de Christophe Feret et les détails de prestations apportent déjà des repères. L’ancienneté de l’entreprise ne doit pas être reformulée en un nombre d’années d’expérience personnelle sans justification distincte.

Les modèles peuvent afficher des chantiers avec commune, prestation et photographies autorisées. Leur présence effective en base n’a pas été contrôlée ici : cet audit ne conclut donc pas à une absence totale de réalisations.

La meilleure piste est de compléter progressivement des fiches réelles : état initial, travaux effectués, finitions et photographies correspondantes. Une facture peut documenter un chantier effectué ; un devis seul documente une prestation proposée, pas sa réalisation. Garder les données privées des clients hors des fiches.

Les images d’ambiance de l’accueil et de la peinture intérieure n’ont pas de légende visible dans les modèles actuels. Sans conclure qu’elles trompent effectivement les visiteurs, leur distinction avec des photos de chantier pourrait être rendue plus explicite : « Illustration des travaux de peinture. » Les réserves placées dans les mentions légales ne sont pas un repère immédiat à côté de l’image.

Les avis clients pourront compléter ces preuves s’ils sont authentiques et attribuables. Ne pas ajouter de note, de citation, de certification, de garantie, de promesse de chantier propre ou de délai sans élément établi. Cette recommandation applique la transparence et la crédibilité de NN/g ; elle ne présume pas que chaque visiteur exige des témoignages.

## Ordre de travail proposé

1. Corriger les métadonnées et afficher la règle téléphone/email avant toute erreur.
2. Alléger les prérequis du papier peint et préciser le champ commune.
3. Harmoniser, si retenu, « Demander un rendez-vous » et conserver la visite avant devis dans le parcours.
4. Revoir l’accroche, la présentation de Christophe Feret et les introductions de prestations.
5. Clarifier les mots techniques et le lien de confidentialité. Conserver la page zone sans durée de trajet affichée, conformément à la décision d’Elisa.
6. Enrichir les preuves au rythme des contenus authentiques disponibles.

Le texte de confidentialité du formulaire n’est pas raccourci dans cet audit : sa lisibilité peut être améliorée séparément en préservant les informations établies. Les décisions légales, les coordonnées et l’autorisation d’ouverture ne sont pas rouvertes.

## Vérification après une éventuelle application

Relire le rendu WordPress réel sur mobile et ordinateur, y compris les contenus enregistrés et les métadonnées. Faire vérifier sur un environnement de test que le téléphone seul et l’email seul sont compris et acceptés, et que les erreurs expliquent quoi corriger. Vérifier qu’aucun texte ne laisse croire à un devis immédiat ou à une date réservée.

Un court test de compréhension auprès de personnes préparant des travaux peut départager les variantes : quels travaux sont proposés, où Christophe Feret intervient-il, que se passe-t-il après l’envoi, quelles informations faut-il fournir ? Ce test reste à réaliser. Aucune hausse de demandes, baisse d’abandon ou amélioration de classement n’a été mesurée pendant cet audit.

## Application et contrôles

Application autorisée par Elisa le 16 septembre 2026. Accroche, présentation de Christophe Feret, quatre prestations, libellés de demande de rendez-vous, métadonnées, aides du formulaire, messages de longueur, légendes des illustrations et lien de confidentialité mis à jour. Le message de formulaire indisponible distingue désormais prévisualisation et exploitation. Les informations légales du formulaire sont conservées.

La demande supplémentaire d’Elisa de ne pas afficher la limite de deux heures est enregistrée dans `docs/reponses-elisa-2026-09-16.md` et `docs/zone-intervention-2026-09-16.md`. La page zone conserve ses secteurs et la confirmation selon la commune. Aucune nouvelle photographie de chantier ni aucun avis client n’a été inventé ou ajouté.

La migration `scripts/update-copywriting.php` contrôle les versions antérieures, sauvegarde les contenus et la présentation avant modification, puis utilise les révisions WordPress. Une ancienne version locale de la fiche sols, identifiée et comparée, est explicitement reconnue. Les autres personnalisations inconnues interrompent la migration avant toute écriture. Exécution locale et distante : cinq contenus mis à jour et présentation actualisée ; seconde exécution : zéro changement.

Déploiement limité à onze fichiers sur la prévisualisation privée, avec sauvegarde préalable du code concerné et de la base : `/srv/apps/feret-peinture/backups/copywriting-20260916T204519Z`. Le titre de réalisations déjà présent sur le serveur a été préservé, car il différait du travail local et ne faisait pas partie de cette correction. Les réglages de publication et de messagerie sont conservés.

Contrôles réalisés :

- Syntaxe PHP du projet vérifiée en local ; onze fichiers transférés vérifiés avant installation distante.
- 31 assertions du formulaire et deux tests de son bouton réussis ; envois de test uniquement vers Mailpit local.
- 106 contrôles HTTP locaux réussis. Le test de confidentialité a été actualisé pour vérifier la redirection vers la page fusionnée, déjà décidée avant cet audit.
- Six pages vérifiées dans le navigateur à 320, 390 et 1 440 pixels : un H1 par page et aucun débordement horizontal. Captures de l’accueil ordinateur et du contact mobile examinées.
- Huit pages distantes vérifiées : nouveaux textes, liens de confidentialité, protection HTTP 401 sans authentification et noindex. Absence de durée de trajet contrôlée sur le texte de la page zone.

Le formulaire public reste désactivé dans la prévisualisation : ses aides sont vérifiées dans le rendu local et son code est synchronisé sur le serveur. Aucun email réel n’a été envoyé pendant cette intervention. Aucun test de conversion ou entretien utilisateur n’a été réalisé.
