#language: fr

Fonctionnalité: Pouvoir partager des versions originales et traduites

Scénario: L'utilisateur partage une version traduction d'une version originale ayant des droits réservés qui lui a été partagé

Etant donné qu'un utilisateur partage une version traduction de droits réservés qui lui a été partagé
Quand il a choisi les utilisateurs avec qui il veut la partager
Alors ces utilisateurs recoivent le droit de lecture sur cette version traduction.

Scénario: L'utilisateur rend public une version traduction d'une version originale ayant des droits réservés qui lui a été partagé

Etant donné qu'un utilisateur partage une version traduction d'une version originale ayant des droits réservés qui lui a été partagé
Quand il choisit de la partager
Alors les utilisateurs qui ont bénéficié du premier partage de la VO ont les droits de lecture sur cette version traduction.

Scénario: L'utilisateur rend public une version traduction d'une version originale libre de droits et veut rendre sa version traduction publique

Etant donné que l'utilisateur a une version traduction d'une version originale libre de droits,
Et qu'il veut la rendre publique
Quand il choisit de la rendre publique
Alors tous les utilisateurs de TraduXio recevront les mêmes droits d'accès à la version traduction.

Scénario: L'utilisateur travaille sur une version traduction d'une version originale libre de droits et veut partager sa version traduction

Etant donné que l'utilisateur a une version traduction d'une version originale libre de droits,
Quand il a choisi les utilisateurs avec qui il veut la partager
Alors les utilisateurs qu'il a choisi recevront le droit de lecture sur cette traduction.

Scénario: L'utilisateur créer une version originale libre de droits et veut la rendre publique

Etant donné que l'utilsateur a une version originale libre de droit
Quand il veut la rendre publique
Alors sa version originale sera visible par tous
Et cette action sera irréversible.

Scénario: L'utilisateur créer une version traduite d'une version originale libre de droits et veut la rendre publique

Etant donnée que l'utilisateur a une version traduite d'une version originale libre de droits
Et qu'il veut la rendre publique
Alors sa version traduite sera visible par tous
Et il peut s'il le souhaite re rendre sa version traduite privée.
