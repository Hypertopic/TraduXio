Etantdonnéqu(/^un utilisateur partage une version traduction de droits réservés qui lui a été partagé$/) do
  visit('Page de l oeuvre')
  click_on('Partager')

end

Quand(/^il a choisi les utilisateurs avec qui il veut la partager$/) do
  fill_in('Participants', with: 'bob,alice'
  click_on('Valider')
end

Alors(/^ces utilisateurs recoivent le droit de lecture sur cette version traduction\.$/) do
  expect(Les utilisateurs ont les droits de lecture sur cette VT)
end

Etantdonnéqu(/^un utilisateur partage une version traduction d'une version originale ayant des droits réservés qui lui a été partagé$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il choisit de la partager$/) do
  fill_in('Participants'), with: 'bob,alice'
  click_on('Valider')
end

Alors(/^les utilisateurs qui ont bénéficié du premier partage de la VO ont les droits de lecture sur cette version traduction\.$/) do
  expect(Les utilisateurs présent dans le partage inital de la VO auront les droits de lecture sut cette VT)
end

Etantdonnéque(/^l'utilisateur a une version traduction d'une version originale libre de droits,$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il choisit de la rendre publique$/) do
  click_on('Rendre VT publique')
end

Alors(/^tous les utilisateurs de TraduXio recevront les mêmes droits d'accès à la version traduction\.$/) do
  expect(Tout les utilisateurs de TraduXio ont les droits de lecutre sur cette VT)
end

Etantdonnéque(/^l'utilisateur a une version traduction d'une version originale libre de droits,$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il veut la partager$/) do
  fill_in('Participants')
  click_on('Valider')
end

Alors(/^les utilisateurs qu'il a choisi recevront le droit de lecture sur cette traduction\.$/) do
  expect(Les utilisateurs choisi auront les droits de lecture sur cette VT)
end

Etantdonnéque(/^l'utilsateur a une version originale libre de droit$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il veut la rendre publique$/) do
  click_on('Rendre VO publique')
end

Alors(/^sa version originale sera visible par tous$/) do
  expect(La VO est visible par tous les utilisateurs de TraduXio)
end

Et(/^cette action sera irréversible\.$/) do
  expect(Impossible de cliquer sur Rendre Privée)
end

Etantdonnée(/^que l'utilisateur a une version traduite d'une version originale libre de droits$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il veut la rendre publique$/) do
  click_on('Rendre VT publique')
end

Alors(/^sa version traduite sera visible par tous$/) do
  expect(La VT est visible par tous les utilisateurs de TraduXio)
end

Et(/^il peut s'il le souhaite re rendre sa version traduite privée\.$/) do
  expect(Il peut cliquer sur Rendre Privée)
end
