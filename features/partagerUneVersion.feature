#language: fr

Fonctionnalité: Pouvoir partager des VO et des VT

Scénario: L'utilisateur créer une VO libre de droits et veut la rendre publique

Etant donné que l'utilsateur a une VO libre de droit
Quand il veut la rendre publique,
Alors sa VO originale sera visible par tous,
Et cette action sera irréversible.

Scénario: L'utilisateur créer une VO libre de droits et veut la partager

Etant donné que l'utilsateur a une VO libre de droit
Quand il a choisi les utilisateurs avec qui il veut la partager,
Alors ces utilisateurs recoivent le droit de lecture et de traduction sur cette V0.

Scénario: L'utilisateur a une VO partagée et veut la rendre publique

Etant donné que l'utilsateur a une VO libre de droit
Et qu'il veut la rendre publique,
Quand il choisit de la rendre publique,
Alors tous les utilisateurs de TraduXio recevront les droits de lecture et de traduction sur la VT.

Scénario: L'utilisateur partage une VT d'une VO ayant des droits réservés qui lui a été partagé

Etant donné qu'un utilisateur partage une VT de droits réservés qui lui a été partagé
Quand il a choisi les utilisateurs avec qui il veut la partager,
Alors ces utilisateurs recoivent le droit de lecture sur cette VT,
Et il peut s'il le souhaite rendre sa version traduite privée à nouveau.

Scénario: L'utilisateur rend public une VT d'une VO ayant des droits réservés qui lui a été partagé

Etant donné qu'un utilisateur partage une VT d'une VO ayant des droits réservés qui lui a été partagé
Quand il choisit de la partager,
Alors les utilisateurs qui ont bénéficié du premier partage de la VO ont les droits de lecture sur cette VT,
Et il peut s'il le souhaite rendre sa version traduite privée à nouveau.

Scénario: L'utilisateur rend public une VT d'une VO publique 

Etant donné que l'utilisateur a une VT d'une VO publique,
Et qu'il veut la rendre publique,
Quand il choisit de la rendre publique,
Alors tous les utilisateurs de TraduXio recevront le droit de lecture sur la VT.

Scénario: L'utilisateur travaille sur une VT d'une VO publique et veut partager sa VT

Etant donné que l'utilisateur a une VT d'une VO publique,
Quand il a choisi les utilisateurs avec qui il veut la partager,
Alors les utilisateurs qu'il a choisi recevront le droit de lecture sur la VT.

Scénario: L'utilisateur travaille sur une VT d'une VO publique et veut la rendre publique

Etant donné que l'utilisateur a une VT d'une VO publique,
Quand il a choisi les utilisateurs avec qui il veut la partager,
Alors tous les utilisateurs de TraduXio recevront les droits de lecture sur la VT,
Et il peut s'il le souhaite rendre sa VT privée à nouveau.
