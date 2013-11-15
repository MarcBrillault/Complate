# Qu'est-ce que Complate ?

Complate est un gestionnaire de templates codé en PHP, basé sur des paires de commentaires HTML.
Son principe implique que les balises propres au template sont invisibles dans le fichier, et rendent ce
dernier consultable sans gêne visuelle, comme n'importe quel fichier HTML.

Il permet également de présenter des contenus de test ou de validation, qui seront remplacés lors du calcul de la page.

## Configuration de Complate

Pour charger Complate, il faut actuellement inclure sa classe, ainsi que celle de [SimpleHTMLDOM](simplehtmldom.sourceforge.net), dont Complate dépend.

### Chargement des librairies

```php
require('simple_html_dom.php');
require('complate.php');
```

### Chargement d'un fichier de template

```php
//  Le chemin vers le fichier de template peut être précisé lors de l'appel de la classe
$complate = new Complate('template.html');
// Il peut aussi être précisé plus tard
$complate = new Complate();
$complate->setTemplate('template.html');
```

### Transmission de données au template
Ces attributions se font depuis la fonction `setData()`, qui accepte un couple clé/valeur, ou un array comme arguments.

```php
$complate->setData('cle', 'valeur');
$complate->setData(
  array(
    'cle1' => 'valeur1',
    'cle2' => 'valeur2'
  )
);
```

La fonction `setData()` peut être appelée plusieurs fois, les données s'ajouteront aux données préexistantes.

Si deux clés sont en conflit, c'est la dernière ajoutée qui sera conservée.

### Affichage du template

```php
echo $complate->getHtml();
```

### Autres fonctionnalités
#### Assignation d'une url
Lors de la création d'un menu (par exemple), il est possible de passer à Complate l'url de la page active avec la
fonction `setUrl()`. Cette url servira à afficher l'élément actif d'une manière différente des autres.

Pour que cela fonctionne, il faut qu'un des éléments possède un attribut `url` qui lui soit identique.

```php
$complate->setUrl('mon-url.html');
$complate->setData(array(
  'element1' => array('url' => 'url1.html', 'texte' => 'Premier élément'),
  'element2' => array('url' => 'mon-url.html', 'texte' => 'Second élément')
));
```

Dans l'exemple précédent, c'est le second élément qui sera mis en valeur.

#### Récupération d'une partie du template
Lorsqu'on travaille avec AJAX, plutôt que de créer un template spécifique, il est possible de récupérer uniquement le contenu
du template principal qui sera actualisé. Avec Complate, cela s'effectue avec la fonction `useZone()`.

```php
$complate = new Complate('template.html');
$complate->useZone('ma_zone');
```

## Utilisation du template

### Affichage d'une donnée
Il suffit pour cela de créer une paire de balises de commentaires HTML contenant le nom de la clé à remplacer
`<!-- CLE --><!-- CLE -->`.

Tout ce qui est présent dans le template entre ces deux balises sera remplacé par le contenu fourni par le script,
ce qui permet de créer des templates avec des contenus de test.

```html
<h1>
  <!-- TITRE -->Le titre de ma page<!-- TITRE -->
</h1>
```

Il est également possible de préciser le contenu à remplacer entre hashes, de cette manière : `#CLE#`. Par contre il ne
sera forcément plus possible d'afficher un contenu différent dans le template.

### Gestion des valeurs booléennes
Lorsqu'une valeur booléenne est passée à Complate, cela implique le comportement suivant :
* `true` : le contenu est affiché sans modification, la paire de commentaires est conservée
* `false` : le contenu n'est pas affiché, la paire de commentaires est supprimée

### Affichage d'un array
Pour afficher un array, la syntaxe est légèrement différente :

```html
<!-- LISTE -->
<ul>
  <!-- CONTENT -->
  <li>Elément de présentation qui sera supprimé</li>
  <!-- REPEAT -->
    <li><!-- TEXTE -->Texte de l'élément<!-- TEXTE --></li>
  <!-- REPEAT -->
  <li>Elément de présentation qui sera supprimé</li>
  <!-- CONTENT -->
</ul>
<!-- LISTE -->
```

* `LISTE` est le nom de la clé du tableau
* `CONTENT` est une constante : tout ce qui se trouve entre ces deux balises sera supprimé, sauf :
* `REPEAT`, qui est également une constante. Son contenu sera affiché autant de fois qu'il y a de lignes dans notre array.
* `TEXTE` est le nom de la clé d'un élément du tableau.

Avec cette configuration, nous aurons les possibilités suivantes :
* Les contenus optionnels présents dans le template entre les commentaires `CONTENT` mais hors du commentaire `REPEAT`
  ne seront pas affichés
* Si l'array est vide, ce qui se trouve entre les balises principales (Ici, `LISTE`) ne sera pas affiché. Ainsi,
  nous évitons de générer un code HTML erroné (Un ul sans enfants).

### Affichage d'un menu
Le concept est exactement le même que pour un array, à l'exception d'une paire de balises supplémentaire, `REPEAT_IN`,
qui remplacera la paire de balises `REPEAT` lorsque la clé `url` de l'élément en cours correspondra à l'url de la page.

```html
<!-- MENU -->
  <ul>
  <!-- CONTENT -->
  <!-- REPEAT --><li><a href="#URL#"><!-- TITRE -->Titre de l'élément<!-- TITRE --></a></li><!-- REPEAT -->
  <!-- REPEAT_IN --><li><!-- TITRE -->Titre de l'élément<!-- TITRE --></li><!-- REPEAT_IN -->
  <!-- CONTENT -->
  </ul>
<!-- MENU -->
```
Dans l'exemple précédent, le menu actif ne comportera pas de lien.

### Affichage conditionnel
Complate permet de gérer des conditions simples d'affichage, en fonction de la valeur de la clé. Lors de la création 
de cette dernière (Appelons-la `X`, et si les clés correspondantes n'existent pas encore, deux clés sont créées :
* `IS_X` vaudra `true` (Voir la section sur les booléens) si X est rempli, false dans le cas contraire
* `IS_NOT_X` sera le contraire de `IS_X`

Ces deux clés supplémentaires permettent de conditionner l'affichage de tout un bloc de contenu en fonction de la
présence ou l'absence de contenu dans une clé.

Dans l'exemple ci-dessous, le code HTML servant à la mise en forme d'un message d'erreur ne sera inclus dans la page
que si la clé `error` passée à Complate n'est pas vide. Dans le cas contraire, le message "Aucune erreur" sera affiché.

```html
<!-- IS_ERROR -->
<p><!-- ERROR -->Message d'erreur<!-- ERROR --></p>
<!-- IS_ERROR -->

<!-- IS_NOT_ERROR -->
<p>Il n'y a aucune erreur à afficher</p>
<!-- IS_NOT_ERROR -->
```
