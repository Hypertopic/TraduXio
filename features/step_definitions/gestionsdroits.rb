Etantdonnéqu(/^un utilisateur fait une traduction d'une oeuvre ayant des droits réservés$/) do
  visit('Page de l oeuvre')
  click_on('Créer une traduction')
end

Quand(/^il a enregistré sa traduction$/) do
  click_on('Enregistrer')
end

Alors(/^il possède les droits de lecture et d'écriture sur sa traduction$/) do
  expect(Avoir les droits de lecture/écriture sur sa traduction)
end

Etantdonnéqu(/^un utilisateur partage une oeuvre de droits réservés$/) do
  visit('Page de l oeuvre')
end

Quand(/^il a choisi les personnes avec qui il veut la partager$/) do
  click_on('Partager')
  fill_in('Liste des Participants')
  click_on('Valider')
end

Alors(/^ces personnes possèdent les même droits que la personne ayant fait le partage$/) do
  expect(Avoir les mêmes droits que la personne ayant fait le partage)
end

Alors(/^ils peuvent faire leur traduction de cette oeuvre$/) do
  expect(Pouvoir faire des traductions de l oeuvre partagée)
end

Alors(/^cette traduction est visible par les personnes qui ont bénéficié du partage$/) do
  expect(Traduction publique avec les personnes ayant bénéficié du partage)
end

Etantdonnéque(/^l'utilisateur a une traduction publique d'une oeuvre libre de droits, et il veut la rendre privée$/) do
  visit('Page de l oeuvre')
  click_on('Sa traduction')
end

Quand(/^il choisit de la rendre privée$/) do
  click_on('Cadenas en bas à gauche')
end

Alors(/^il peut décider de la partager avec les personnes qu'ils souhaient$/) do
  click_on('Partager')
  fill_in('Liste des partipants')
  click_on('Valider')
end

Alors(/^ces personnes recevront les mêmes droits d'accès à la traduction de l'oeuvre$/) do
  expect(ces personnes auront les mêmes droits que lui)
end
