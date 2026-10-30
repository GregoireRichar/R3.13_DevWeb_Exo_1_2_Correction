<?php

/**
 * Exercice 2, Q1 et Q4 : une classe fille.
 *
 * Regardez ce qu'elle contient : deux constantes, et rien d'autre. Pas de proprietes, pas d'accesseurs, aucune methode metier. Tout le comportement est herite de Vehicule.
 *
 * C'est le resultat de la Q4. En Q3, roule() et faitLePlein() avaient ete recopies dans chaque fille pour y changer une valeur. En factorisant, il ne reste ici que ce qui est VRAIMENT specifique au modele : son autonomie et son carburant. Si vous avez du recopier du code, c'est en general le signe qu'il n'est pas au bon endroit.
 *
 * Et c'est ce qui permet d'ajouter un quatrieme modele en trois lignes, sans toucher a la classe mere.
 */
class VehiculeDiesel extends Vehicule {
    // Ces deux constantes REDEFINISSENT celles de Vehicule.
    //
    // Elles ne sont jamais lues ici : c'est le code de la classe mere qui les lit, par $this::AUTONOMIE et $this::CARBUTYPE. Et comme ces ecritures sont resolues a l'execution sur la classe reelle de l'objet, la mere obtient bien 1 000 pour un VehiculeDiesel.
    
    // Avec self:: dans la mere, ces deux lignes seraient purement decoratives et le diesel roulerait 800 km, sans la moindre erreur.
    // 1_000 et 1000 sont le meme nombre : le souligne est un separateur de
    // milliers, ignore par PHP, uniquement la pour la lisibilite.
    protected const AUTONOMIE = 1_000;
    protected const CARBUTYPE = Carburant::Ethanol;

    /**
     * Ce constructeur ne fait que relayer a celui de la mere.
     *
     * Il est donc facultatif : sans lui, PHP utiliserait directement celui de Vehicule, avec le meme resultat. On le garde ici parce qu'il rend le contrat de la classe lisible d'un coup d'oeil, et parce qu'il montre la syntaxe de parent::__construct(), dont vous aurez besoin le jour ou une fille aura une propriete supplementaire a initialiser.
     */
    public function __construct(string $marque, int $puissance, float $kilometrage)
    {
        parent::__construct($marque, $puissance, $kilometrage);
    }
}
