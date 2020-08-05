# Challenge démarrage de MovieDB

A partir du MCD _MovieDB_ suivant : 

![](https://github.com/O-clock-Alumni/fiches-recap/blob/master/symfony/themes/img/mcd-moviedb-full.png)

Vous devez créer (depuis zéro) les entités suivantes :

- Genre
- Movie => Et la relation entre les deux
- Person
- Casting => Qui est une association avec attributs au sens du MCD, entre Person et Movie

Voici un schéma plus détaillé de l'entité Casting : 

![](https://github.com/O-clock-Alumni/fiches-recap/blob/master/symfony/themes/img/moviedb-casting.png)

- Nous appellerons cette entité `Casting` et elle contiendra ces deux propriétés :
  - `role` : rôle de la personne dans le film.
  - `creditOrder` ordre d'affichage de ce rôle sur la fiche du film.
  - Et bien sûr les deux relations vers `Movie` et `Person` !

## Objectif #1

- Faites en sorte de créer le schéma Doctrine qui fonctionne (vérifiez dans le concepteur PMA si ça correspond).

## Objectif #2

- Ajoutez un maximum de données liées sur toutes ces entités, disons au moins pour un film donné).
- Affichez la liste des films sur la page principale.
- Affichez le détail de chaque film avec **toutes** les infos liées (le film, les genres, le casting, les personnes).

## Bonus au choix

- Trouver le moyen de classer automatiquement les acteurs par ordre de `creditOrder` sur la page film (indice : Doctrine permet de le faire en standard, à voir où ça se configure :wink:).
- En code (dans un contrôleur par exemple), ajoutez des personnes et des films à `Casting` et sauvegardez-les en BDD.
  - On verra demain soir en challenge in moyen ultra-cool poiur gérer ce genre de données, les fixtures !

## Ressources

- Si besoin [tuto disponible sur le site de Doctrine](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/composite-primary-keys.html#use-case-3-join-table-with-metadata) (cet exemple insiste plus particulièrement sur les clés primaires _composées_ de la table de jointure).

Exemple de MCD avec clé composite

<details>
  
  ![](https://github.com/O-clock-Alumni/fiches-recap/blob/master/symfony/themes/img/mcd-casting-m2m-m2o-concat-key.png)
  
</details>
