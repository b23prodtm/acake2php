<?php
class AppSchema extends CakeSchema {

	public $file = 'schema.php';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $achat = array(
		'fk_reference_commande' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'key' => 'primary'),
		'fk_reference_facture' => array('type' => 'string', 'null' => false, 'length' => 32, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'fk_reference_magasin' => array('type' => 'string', 'null' => false, 'length' => 4, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => array('fk_reference_commande', 'fk_reference_facture', 'fk_reference_magasin'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $cake_sessions = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'data' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'expires' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $categorie = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'autoIncrement' => true),
		'nom' => array('type' => 'string', 'null' => false, 'length' => 50, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'parent' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => false, 'key' => 'index'),
		'image' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => array('id', 'nom'), 'unique' => 1),
			'parent' => array('column' => 'parent', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $classification = array(
		'reference_classe' => array('type' => 'string', 'null' => false, 'length' => 4, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'nom' => array('type' => 'string', 'null' => false, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'fk_reference_categorie' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'reference_classe', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $client = array(
		'identifiant' => array('type' => 'string', 'null' => false, 'length' => 20, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'email' => array('type' => 'string', 'null' => false, 'length' => 60, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'fk_id_mdp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'nom' => array('type' => 'string', 'null' => false, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'prenom' => array('type' => 'string', 'null' => false, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'annee_de_naissance' => array('type' => 'text', 'null' => false, 'length' => 4),
		'adresse' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ville' => array('type' => 'string', 'null' => false, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'pays' => array('type' => 'string', 'null' => false, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'numero_tel' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 20, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'identifiant', 'unique' => 1),
			'fk_id_mdp' => array('column' => 'fk_id_mdp', 'unique' => 1),
			'fk_id_mdp_2' => array('column' => 'fk_id_mdp', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $commande = array(
		'reference' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'date_de_commande' => array('type' => 'date', 'null' => false, 'key' => 'index'),
		'fk_reference_produit' => array('type' => 'string', 'null' => false, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'fk_reference_promotion' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'reference', 'unique' => 1),
			'date_de_commande' => array('column' => 'date_de_commande', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $compte = array(
		'fk_identifiant' => array('type' => 'string', 'null' => false, 'length' => 20, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'nb_de_produits_achetes' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'montant_d_achat_total' => array('type' => 'decimal', 'null' => false, 'default' => '0.0', 'length' => '6,1', 'unsigned' => false),
		'date_ouverture_du_compte' => array('type' => 'date', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'fk_identifiant', 'unique' => 1),
			'nb_de_produits_achetes' => array('column' => array('nb_de_produits_achetes', 'montant_d_achat_total', 'date_ouverture_du_compte'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $articles = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
                'entete' => array('type' => 'string', 'null' => false, 'length' => 250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'fk_reference_categorie' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'corps' => array('type' => 'text', 'null' => false, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'date' => array('type' => 'date', 'null' => false),
                'published' => array('type' => 'date', 'null' => false),
                'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $disponibilite = array(
		'fk_id_produit' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 5, 'unsigned' => false, 'key' => 'primary'),
		'fk_reference_exemplaire' => array('type' => 'string', 'null' => false, 'length' => 20, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => array('fk_id_produit', 'fk_reference_exemplaire'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $editeur = array(
		'code_editeur' => array('type' => 'string', 'null' => false, 'length' => 4, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'image' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 5, 'unsigned' => false),
		'nom' => array('type' => 'string', 'null' => false, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'code_editeur', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $exemplaire = array(
		'code_reference' => array('type' => 'string', 'null' => false, 'length' => 20, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'date_de_livraison' => array('type' => 'date', 'null' => false),
		'fk_id_produit' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 5, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'code_reference', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $facture = array(
		'reference' => array('type' => 'string', 'null' => false, 'length' => 32, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'montant_facture' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '6,1', 'unsigned' => false),
		'date_de_facturation' => array('type' => 'date', 'null' => false),
		'mode_de_paiement' => array('type' => 'string', 'null' => false, 'length' => 4, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'fk_identifiant' => array('type' => 'string', 'null' => false, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'reference', 'unique' => 1),
			'mode_de_paiement' => array('column' => array('mode_de_paiement', 'fk_identifiant'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $fournisseur = array(
		'code_fournisseur' => array('type' => 'string', 'null' => false, 'length' => 4, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'nom' => array('type' => 'string', 'null' => false, 'length' => 30, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'adresse' => array('type' => 'string', 'null' => false, 'length' => 40, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'numero_tel' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'ville' => array('type' => 'string', 'null' => false, 'length' => 15, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'pays' => array('type' => 'string', 'null' => false, 'length' => 15, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'code_fournisseur', 'unique' => 1),
			'nom' => array('column' => array('nom', 'ville', 'pays'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB', 'comment' => 'Fournisseurs des produits')
	);

	public $image = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'nom' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'image' => array('type' => 'mediumbinary', 'null' => false, 'default' => null),
		'mime' => array('type' => 'string', 'null' => false, 'default' => 'image/png', 'length' => 250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $info = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'categorie' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'titre' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'auteur' => array('type' => 'string', 'null' => false, 'length' => 250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'contenu' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'langue' => array('type' => 'string', 'null' => false, 'length' => 32, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'date' => array('type' => 'date', 'null' => false),
        'published' => array('type' => 'date', 'null' => false),
		'images' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 35, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'titre' => array('type' => 'string', 'null' => false, 'length' => 250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'texte' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'pseudo' => array('type' => 'string', 'null' => false, 'length' => 250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'date' => array('type' => 'date', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'date' => array('column' => 'date', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $motdepasse = array(
		'id_unique' => array('type' => 'string', 'null' => false, 'length' => 32, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'motdepasse' => array('type' => 'string', 'null' => false, 'length' => 8, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id_unique', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $php4u_bookmarks = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true, 'key' => 'primary'),
		'dbase' => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user' => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'label' => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'query' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

}
