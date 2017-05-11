Etantdonnéqu(/^un utilisateur crée une version originale libre de droit$/) do
  visit('Ajouter une oeuvre')
  click_on('Editer la licence')
  toQuestion('Autorisez-vous les modifications de votre création ?')
  click_on('Oui')
  toQuestion('Autoriser-vous les utiliations commerciales de votre oeuvre?')
  clikc_on('Oui')
  toQuestion(Juridiction de la licence)
  fill_in('France')
  click_on('Enregistrer')
end

Quand(/^il a enregistré sa version originale$/) do
  click_on('Lire')
end

Alors(/^tous les utilisateurs ont accès a cette version originale$/) do
  expect(Tout les utilisateurs ont les droits de lecture sur cette VO)
end

Etantdonnéqu(/^un utilisateur crée une version originale ayant des droits réservés$/) do
  visit('Ajouter une oeuvre')
  click_on('Editer la licence')
  toQuestion('Autorisez-vous les modifications de votre création ?')
  click_on('Oui')
  toQuestion('Autoriser-vous les utiliations commerciales de votre oeuvre?')
  click_on('Non')
  toQuestion(Juridiction de la licence)
  fill_in('France')
  click_on('Enregistrer')
end

Quand(/^il a enregistré sa version originale$/) do
  click_on('Lire')
end

Alors(/^lui seul possède les droits d'écriture et de lecture sur cette version originale$/) do
  expect(Seul l'utilsateur ayant crée cette VO peut lire et écrire sur cette VO)
end

Etantdonnéqu(/^un utilisateur crée une version traduite d'une oeuvre libre de droit$/) do
  visit('Oeuvre original')
  click_on('Editer')
end

Quand(/^il a enregistré sa version traduite$/) do
  click_on('Lire')
end

Alors(/^lui seul possède les droits d'écriture et de lecture sur cette version traduite$/) do
  expect(Lui seul à les droits d'ecriture et de lecture sur sa VT)
end

Etantdonnéqu(/^un utilisateur crée une version traduite d'une oeuvre ayant des droits réservés$/) do
  visit('Oeuvre original')
  click_on('Editer')
end

Quand(/^il a enregistré sa version traduite$/) do
  click_on('Lire')
end

Alors(/^lui seul possède les droits d'écriture et de lecture sur cette version traduite$/) do
  expect(Lui seul à les droits d'ecriture et de lecture sur sa VT)
end
