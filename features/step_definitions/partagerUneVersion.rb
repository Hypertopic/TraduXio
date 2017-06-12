#Partage de VO

Etantdonnéque(/^l'utilsateur a une VO privé libre de droit$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il veut la rendre publique$/) do
  click_on('Rendre VO publique')
end

Alors(/^Tout les utilisateurs de TraduXio recoivent les droits de lecture et de traduction sur cette VO$/) do
  expect(Tout les utilisateurs de TraduXio ont les droits de lecture et de traduction sur cette VO)
end

Et(/^cette action sera irréversible\.$/) do
  expect(Impossible de cliquer sur Rendre Privée)
end

Etantdonnéque(/^l'utilsateur a une VO privé de droit réservés$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il veut la rendre publique$/) do
  click_on('Rendre VO publique')
end

Alors(/^Cette action est impossible$/) do
  expect(Impossible de cliquer sur Rendre Publique)
end

Etantdonnéque(/^l'utilsateur a une VO privé$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il a choisi les utilisateurs avec qui il veut la partager$/) do
  fill_in('Participants', with: 'bob,alice'
  click_on('Valider')
end

Alors(/^ces utilisateurs recoivent le droit de lecture et de traduction sur cette VO\.$/) do
  expect(Les utilisateurs ont le droit de lecture et de traduction sur cette VO)
end

Etantdonnéque(/^l'utilsateur a une VO qu'il a partagée$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il veut la rendre publique$/) do
  click_on('Rendre VO publique')
end

Alors(/^ces utilisateurs recoivent le droit de lecture et de traduction sur cette VO\.$/) do
  expect(Les utilisateurs ont le droit de lecture et de traduction sur cette VO)
end

#Partage de VT
#Partage de VT de VO partagée

Etantdonnéqu(/^un utilisateur partage une VT privé qui lui a été partagé$/) do
  visit('Page de l oeuvre')
  click_on('Partager')

end

Quand(/^il a choisi les utilisateurs avec qui il veut la partager$/) do
  fill_in('Participants', with: 'bob,alice'
  click_on('Valider')
end

Alors(/^ces utilisateurs recoivent le droit de lecture sur cette VT\.$/) do
  expect(Les utilisateurs ont les droits de lecture sur cette VT)
end

Et(/^il peut s'il le souhaite re rendre sa version traduite privée\.$/) do
  expect(Il peut cliquer sur Rendre Privée)
end

Etantdonnéqu(/^un utilisateur rend publique une VT privé qui lui a été partagé$/) do
  visit('Page de l oeuvre')
  click_on('Partager')

end

Quand(/^il veut la rendre publique$/) do
  click_on('Rendre VO publique')
end

Alors(/^cLes utilisateurs présent dans le partage inital de la VO recoivent le droit de lecture sur cette VT\.$/) do
  expect(Les utilisateurs présent dans le partage inital de la VO ont les droits de lecture sur cette VT)
end

Et(/^il peut s'il le souhaite re rendre sa version traduite privée\.$/) do
  expect(Il peut cliquer sur Rendre Privée)
end

#Partage de VT de VO publique

Etantdonnéque(/^l'utilisateur veut rendre publique une VT d'une VO publique libre de droits$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il choisit de la rendre publique$/) do
  click_on('Rendre VT publique')
end

Alors(/^tous les utilisateurs de TraduXio recoivent le droit de lecture sur la VT\.$/) do
  expect(Tout les utilisateurs de TraduXio ont les droits de lecutre sur cette VT)
end

Et(/^il peut s'il le souhaite re rendre sa version traduite privée\.$/) do
  expect(Il peut cliquer sur Rendre Privée)
end

Etantdonnéque(/^l'utilisateur veut partager une VT privé d'une VO publique libre de droits$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il a choisi les utilisateurs avec qui il veut la partager$/) do
  fill_in('Participants'), with: 'bob,alice'
  click_on('Valider')
end

Alors(/^les utilisateurs qu'il a choisi recevront le droit de lecture sur cette VT\.$/) do
  expect(Les utilisateurs choisi auront les droits de lecture sur cette VT)
end

Et(/^il peut s'il le souhaite re rendre sa version traduite privée\.$/) do
  expect(Il peut cliquer sur Rendre Privée)
end

Etantdonnée(/^que l'utilisateur a une VT privé qu'il a partagé d'une VO privé libre de droits$/) do
  visit('Page de l oeuvre')
  click_on('Partager')
end

Quand(/^il veut la rendre publique$/) do
  click_on('Rendre VT publique')
end

Alors(/^Tout les utilisateurs de TraduXio recoivent le droit de lecture sur cette VT$/) do
  expect(Tout les utilisateurs de TraduXio ont le droit de lecture sur cette VT)
end

Et(/^il peut s'il le souhaite re rendre sa version traduite privée\.$/) do
  expect(Il peut cliquer sur Rendre Privée)
end
