Etantdonnéqu(/^un utilisateur crée une VO libre de droit$/) do
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

Quand(/^il a enregistré sa VO$/) do
  click_on('Lire')
end

Alors(/^lui seul possède les droits d écriture et de lecture sur cette VO$/) do
  expect(Lui seul à les droits de lecture, d ecriture et de traduction sur cette VO)
end

Etantdonnéqu(/^un utilisateur crée une VO ayant des droits réservés$/) do
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

Quand(/^il a enregistré sa VO$/) do
  click_on('Lire')
end

Alors(/^lui seul possède les droits d'écriture et de lecture sur cette VO$/) do
  expect(Lui seul à les droits de lecture, d ecriture et de traduction sur cette VO)
end

Etantdonnéqu(/^un utilisateur crée une VT d une VO libre de droit$/) do
  visit('Oeuvre original')
  click_on('Editer')
end

Quand(/^il a enregistré sa VT$/) do
  click_on('Lire')
end

Alors(/^lui seul possède les droits d'écriture et de lecture sur cette VT$/) do
  expect(Lui seul à les droits de lecture, d ecriture et de traduction sur cette VT)
end

Etantdonnéqu(/^un utilisateur crée une VT d une VO ayant des droits réservés$/) do
  visit('Oeuvre original')
  click_on('Editer')
end

Quand(/^il a enregistré sa version traduite$/) do
  click_on('Lire')
end

Alors(/^lui seul possède les droits d'écriture et de lecture sur cette VT$/) do
  expect(Lui seul à les droits de lecture, d ecriture et de traduction sur cette VT)
end
