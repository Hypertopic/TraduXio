Etantdonnéqu(/^un utilisateur souhaite s'identifier pour accéder au site TraduXio$/) do
  visit('Page d accueil')
  click_on('Se connecter')
end

Quand(/^il se rend sur la page d'authentification et remplit les champs avec ses identifiants personnels.$/) do |arg1|
  fill_in('login')
  fill_in('mot de passe')
  click_on('Se connecter')
end

Alors(/^il est connécté et peut accéder aux traductions \(comme les simples visiteurs\) mais peut aussi écrire, modifier, comparer les traductions du site$/) do
  expect(page).to_have_content('Page  d accueil')
end

Quand(/^il est sur la page d'authentification et remplit les champs ses identifiants personnels mais ces derniers sont éronnées\.$/) do |arg1|
  fill_in('login')
  fill_in('mot de passe')
  click_on('Se connecter')
end

Alors(/^le site ne connecte pas cet utilisateur et lui indique que ces identifiants sont faux$/) do
  expect(page).to_have_content('Page erreur de connection')
end

Alors(/^l'utilisateur doit recommencer la connection\.$/) do
  click_on('Se connecter')
end

Etantdonnéque(/^le futur utilisateur n'a pas encore de compte\.$/) do
  visit('Page d accueil')
  click_on('Créer un compte')
end

Quand(/^cette personne se rend sur la page de création de compte du site TraduXio, il complète alors les champs du formulaire de création de compte$/) do |arg1|
  fill_in('nom')
  fill_in('prenom')
  fill_in('école')
  fill_in('niveau')
  fill_in('email')
  fill_in('mdp')
end

Quand(/^il a terminé,il valide sa création de compte, se rend sur sa boite mail et confirme la création de son compte en cliquant sur le lien\.$/) do
  click_on('Valider la création du compte')
  click_on('lien de validation compte')
  expect(page).to_have_content('Page de confirmation de création de compte')
end

Alors(/^cette personne posséde un compte TraduXio et devient un utilisateur\.$/) do
  expect(avoir un comte sur le site TraduXio)
end
