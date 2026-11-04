# R3.DWeb-DI.13 — Corrigé commenté des exercices 1 et 2

BUT MMI 2, parcours Développement Web et Dispositifs Interactifs.
IUT de Laval.

Une proposition de correction, pas *la* correction : d'autres implémentations
sont défendables. **Les commentaires sont l'essentiel de ce dépôt.** Ils
renvoient chaque bloc de code à la question d'énoncé à laquelle il répond, et
expliquent les choix qui ne vont pas de soi.

## Lancer le projet

Il faut **PHP 8.1 minimum** (`readonly` et les énumérations n'existent pas
avant). Vérifiez avec `php -v`.

Dans le navigateur, depuis la racine du projet :

```bash
php -S localhost:3000 -t public/
```

puis `http://localhost:3000/td1.php` et `http://localhost:3000/td2.php`.
`Ctrl+C` pour arrêter le serveur.

En ligne de commande, depuis le dossier `public/` :

```bash
cd public
php td1.php
php td2.php
```

Aucune base de données n'est nécessaire : tout tient en mémoire.

`td2.php` met quelques secondes à répondre car il simule trois véhicules sur
100 000 km.

## Organisation

```
public/
  td1.php                  exercice 1 : l'application
  td2.php                  exercice 2 : héritage et boucle jusqu'à 100 000 km
src/
  Vehicule.php             la classe mère, et l'énumération Carburant
  VehiculeDiesel.php       deux constantes, et rien d'autre
  VehiculeElectrique.php   idem
  Exceptions/
    ReservoirVideException.php     la panne sèche
    PaiementRefuseException.php    une chance sur cinq
    MauvaisCarburantException.php  le mauvais pistolet
    DistanceException.php          une modélisation alternative, non utilisée
```

Les classes de `src/` ne contiennent **aucun `echo`** : elles portent les
données et le comportement, l'affichage est décidé dans `public/`.

## Par où commencer la lecture

1. `src/Vehicule.php`, en suivant l'ordre des questions rappelé en tête de
   fichier.
2. `src/VehiculeElectrique.php` : quatre lignes, mais c'est là que se joue la
   différence entre `self::` et `static::`.
3. `public/td1.php` pour le détail de l'alternance PHP / HTML et le rôle de la
   balise `<pre>`.
4. `src/Exceptions/ReservoirVideException.php` : l'anatomie d'une exception
   personnalisée, les trois autres en sont des variations.
