<?php

/**
 * Classe Vehicule : l'entite centrale des exercices 1 et 2.
 *
 * Elle est construite par couches successives, au fil des questions :
 *   Ex. 1 Q1  proprietes, constructeur, affichage des caracteristiques
 *   Ex. 1 Q2  accesseurs : l'application decide de ce qu'elle affiche
 *   Ex. 1 Q3  typage, readonly, kilometrage qui ne peut pas diminuer
 *   Ex. 1 Q4  roule(), constante d'autonomie, exception et avertissement
 *   Ex. 1 Q5  reservoir qui se vide, panne seche, faitLePlein()
 *   Ex. 2 Q3  carburant accepte, verifie au moment du plein
 *   Ex. 2 Q4  le test du carburant n'est ecrit qu'ici, une seule fois
 *
 * Lisez-la en gardant cet ordre en tete : chaque bloc repond a une question.
 */

// Les exceptions vivent dans le namespace Exceptions. Ces quatre lignes permettent d'ecrire « new ReservoirVideException(...) » plus bas, au lieu du nom complet « new \Exceptions\ReservoirVideException(...) ».
// Un « use » ne charge aucun fichier : il ne fait que raccourcir un nom. Le chargement, lui, est assure par les require() de td1.php et td2.php.
use Exceptions\DistanceException;
use Exceptions\MauvaisCarburantException;
use Exceptions\ReservoirVideException;
use Exceptions\PaiementRefuseException;

/**
 * Exercice 2, Q3 : l'ensemble ferme des carburants possibles.
 *
 * Interet d'une enumeration par rapport a des constantes de chaines : le langage REFUSE toute valeur qui n'est pas dans la liste. Une faute de frappe devient une erreur a l'execution, au lieu de devenir un bug silencieux.
 *
 * Celle-ci est une enumeration « pure » : ses cas n'ont pas de valeur scalaire. A l'exercice 3, il faudra l'adosser a une chaine (enum Carburant: string) pour pouvoir la stocker en base de donnees, car une base ne connait ni vos classes ni vos enumerations.
 */
enum Carburant {
    case Ethanol;
    case SansPlomb;
    case Electricite;
}

class Vehicule {
    // --- Exercice 1, Q1 et Q3 : les proprietes, typees des le depart. -------
    //
    // readonly (Q3) = une seule ecriture, jamais deux, et peu importe d'ou elle vient. En pratique : on ecrit dans le constructeur, et c'est fini.
    // C'est ce qui rend setPuissance() inutilisable, voir tout en bas.
    private readonly string $marque;
    private readonly int $puissance;

    // Celles-ci evoluent pendant la vie de l'objet : pas de readonly.
    private float $kilometrage;
    private float $autonomie;
    private Carburant $carburant;

    // --- Les constantes de classe (Ex. 1 Q4 et Ex. 2 Q3 et Q4). ------------
    //
    // Une constante decrit LE MODELE : tous les vehicules de cette classe partagent la meme valeur. C'est la difference avec $autonomie juste au-dessus, qui decrit CET exemplaire-ci a cet instant.
    //
    // protected, et non private : les classes filles doivent pouvoir les redefinir. C'est exactement ce que font VehiculeDiesel et VehiculeElectrique, et c'est tout ce qu'elles font.
    protected const AUTONOMIE = 800;
    protected const CARBUTYPE = Carburant::SansPlomb;

    public function __construct(string $marque, int $puissance, float $kilometrage)
    {
        $this->marque = $marque;
        $this->puissance = $puissance;
        $this->kilometrage = $kilometrage;

        // ATTENTION, c'est ici que se joue tout l'exercice 2.
        //
        // $this::AUTONOMIE lit la constante de la CLASSE REELLE DE L'OBJET, determinee a l'execution. Une Zoe construite par ce constructeur recoit donc 400, et non 800.
        //
        // Avec self::AUTONOMIE, on lirait la constante de la classe OU LA LIGNE EST ECRITE, c'est-a-dire toujours Vehicule, donc toujours 800. Et rien ne planterait : la Zoe annoncerait simplement 800 km d'autonomie. Un resultat faux, sans la moindre erreur.
        //
        // static::AUTONOMIE fait la meme chose que $this::AUTONOMIE ici, et fonctionne en plus dans une methode statique, ou il n'y a pas d'objet. C'est l'ecriture a preferer par defaut.
        $this->autonomie = $this::AUTONOMIE;
        $this->carburant = $this::CARBUTYPE;
    }

    /**
     * Exercice 1, Q1 : la mise a jour naive du kilometrage.
     *
     * Le vehicule a roule, on avance le compteur. Rien d'autre : ni reservoir, ni refus. C'est volontairement pauvre, et c'est le point de depart dont roule() ci-dessous est l'aboutissement.
     *
     * abs() garantit la regle de la Q3 — le kilometrage ne peut pas diminuer — en transformant une distance negative en distance positive. C'est un choix : on aurait aussi pu lever une exception pour signaler l'appel absurde plutot que de le corriger en silence.
     */
    public function parcours(int $distance): void {
        $this->kilometrage += abs($distance);
    }

    /**
     * Exercice 1, Q4 et Q5 : rouler pour de vrai.
     *
     * Difference avec parcours() : ici, c'est le vehicule qui calcule, et il a le droit de refuser. L'application se contente d'ecrire roule(900).
     *
     * Choix de modelisation a remarquer : quand la distance demandee depasse l'autonomie restante, le vehicule ne refuse pas tout. Il roule jusqu'a la panne, credite les kilometres reellement parcourus, puis signale la panne seche. C'est ce qui rend coherent l'etat final de l'objet : un reservoir vide ET les kilometres qui ont ete faits avec.
     *
     * C'est aussi ce qui permet a la boucle de td2.php de se terminer pour la Zoe : avec 400 km d'autonomie et des etapes de 500 km, elle avance de 400 km a chaque tour au lieu de tourner indefiniment.
     */
    public function roule(int $distance): void {
        $distance = abs($distance);

        // max(..., 0) empeche l'autonomie de devenir negative.
        $newAutonomie = max($this->autonomie - $distance, 0);

        if ($newAutonomie === 0) {
            // On ajoute l'autonomie uniquement, car le véhicule ne peut pas parcourir plus que son autonomie.
            $this->kilometrage += $this->autonomie;
            $this->autonomie = 0;

            // L'objet est mis dans un etat coherent AVANT de lever l'exception. C'est la regle a retenir de la Q5 : une methode qui touche plusieurs proprietes ne doit jamais s'interrompre a mi-parcours en laissant l'objet dans un etat impossible.
            throw new ReservoirVideException($this);
        }

        // Exercice 1, Q5 : l'avertissement porte sur l'autonomie RESTANTE.
        //
        // trigger_error n'interrompt rien : le script continue, et les deux lignes suivantes s'executent. C'est toute la difference avec un throw. On avertit quand l'operation peut se poursuivre ; on leve une exception quand elle ne le peut pas.
        if ($newAutonomie < $this::AUTONOMIE * 0.25) {
            trigger_error("⚠️ Attention, le réservoir est bientôt vide !", E_USER_WARNING);
        }

        $this->kilometrage += $distance;
        $this->autonomie = $newAutonomie;
    }

    /**
     * Exercice 1 Q5, puis exercice 2 Q3 et Q4.
     *
     * Le carburant est un PARAMETRE : il vient de la personne qui tient le pistolet, pas du vehicule. C'est ce qui rend le test faillible, donc utile. Si on appelait faitLePlein($v->getCarbuType()), on comparerait une valeur a elle-meme et l'exception ne pourrait jamais se declencher.
     *
     * Exercice 2, Q4 : cette methode n'est ecrite QU'ICI. Les classes filles ne la redefinissent pas — elles se contentent de declarer leur propre CARBUTYPE, et $this::CARBUTYPE va la chercher a l'execution. Ce qui est commun s'ecrit une fois dans la mere ; ce qui est specifique se declare dans la fille.
     */
    public function faitLePlein(Carburant $carburant): void {
        if ($this::CARBUTYPE !== $carburant){
            throw new MauvaisCarburantException($this);
        }

        // Une chance sur cinq que le paiement soit refuse (Ex. 1 Q5).
        // Noter l'ordre : on verifie d'abord le carburant, puis le paiement, et l'autonomie n'est remise a son maximum qu'une fois les deux obstacles passes. Valider d'abord, muter ensuite.
        if (rand(0, 4) === 0) {
            throw new PaiementRefuseException;
        }

        $this->autonomie = $this::AUTONOMIE;
    }

    // --- Exercice 1, Q2 : les accesseurs. ----------------------------------
    //
    // La classe porte des donnees et un comportement ; elle ne decide pas de leur presentation. Ces methodes RENVOIENT, elles n'affichent pas : aucun echo dans ce fichier. C'est l'application qui choisit quoi montrer, et comment.

    /** La valeur du modele : identique pour tous les vehicules de la classe. */
    public function getAutonomieMax(): int {
        return $this::AUTONOMIE;
    }

    /** La valeur de cet exemplaire-ci, a cet instant. A ne pas confondre. */
    public function getAutonomie(): float {
        return $this->autonomie;
    }

    public function getKilometrage(): float { return $this->kilometrage; }
    public function getMarque(): string { return $this->marque; }

    public function getCarbuType(): Carburant { return $this::CARBUTYPE; }

    /**
     * Exercice 1, Q1 puis Q2.
     *
     * En Q1 cette methode affichait directement avec echo. En Q2 elle a ete transformee pour RENVOYER une chaine : c'est l'appelant qui l'affiche, ou l'enregistre, ou l'ignore.
     *
     * Les accolades dans "{$this->marque}" permettent d'interpoler une expression a l'interieur d'une chaine a guillemets doubles. Sans elles, "$this->marque" fonctionne aussi pour une propriete simple, mais pas pour un appel de methode : il faudrait "{$this->getMarque()}".
     */
    public function getCaracteristiques(): string {
        return "Modèle : {$this->marque}, Puissance : {$this->puissance} CV, Kilométrage : {$this->kilometrage} km; Autonomie : {$this->autonomie} km";
    }

    /**
     * Exercice 1, Q3 : le piege, laisse volontairement en place.
     *
     * $puissance est readonly et deja ecrite par le constructeur. Tout appel a cette methode leve donc :
     *
     *     Error: Cannot modify readonly property Vehicule::$puissance
     *
     * Et c'est une Error, pas une Exception : catch (Exception $e) ne l'attrape pas, catch (Throwable $e) oui. Mais la bonne reponse n'est pas de l'attraper — c'est de supprimer l'appel. Une Error signale une faute de programmation, qu'on corrige ; une Exception signale une situation anormale, qu'on traite.
     *
     * Voir l'appel commente dans td1.php : decommentez-le pour voir l'erreur.
     */
    public function setPuissance(int $puissance): void {
        $this->puissance = $puissance;
    }
}
