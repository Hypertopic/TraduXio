#language: fr

Fonctionnalité: Pouvoir s'identifier avec son login et son mot de passe afin d'accéder aux fonctionnalités du site

Scénario: L'utilisateur rentre des identifiants corrects du premier coup

Etant donné qu'un utilisateur souhaite s'identifier pour accéder au site TraduXio.
Quand il se rend sur la page d'authentification et remplit les champs avec ses identifiants personnels.
Alors il est connécté est peut accéder aux traductions (comme les simples visiteurs) mais peut aussi écrire, modifier, comparer les traductions du site.

Scénario: Les identifiants indiquées sont incorrects

Etant donné qu'un utilisateur souhaite s'identifier pour accéder au site TraduXio
Quand il est sur la page d'authentification et remplit les champs ses identifiants personnels mais ces derniers sont éronnées.
Alors le site ne connecte pas cet utilisateur et lui indique que ces identifiants sont faux. 
Alors l'utilisateur doit recommencer la connection.
