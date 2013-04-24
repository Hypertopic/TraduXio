# - Rechercher une oeuvre (en francais)
# - Rechercher une traduction (en anglais) => il n'y en a pas , seulement italien
# - Proposer une traduction (en anglais)
# - Rechercher une concordance " l'embrun aveuglant des ténèbres " => aucun résultat
# - Try again with " l'embrun aveuglant " => traduction allemande trouvée
# - Mettre une licence
# - Accéder à la traduction depuis un autre compte

require 'spec_helper'

feature 'Recherche une oeuvre' do
	$title = 'Germinal'
	$author = 'Émile Zola'
	$lang1 = 'Français'
	$lang2 = 'Italien'
	$lang3 = 'Anglais'
	$txt1 = 'Foo bar l\'embrun aveuglant des ténèbres FOO BAR LAND'
	$txt2 = 'FOO TEST DE TRADUCTION BAR';

	background do
		visit '/work/index'
		click_on 'Déposer un nouveau texte' 
		fill_in 'Titre', :with => $title
		fill_in 'Auteur', :with => $author
		fill_in 'Contenu du Texte', :with => $txt1
		select 'Langue Original', :from => $lang1
		click_on 'Déposer'
		click_on 'Créer une traduction'
		fill_in '*Titre', :with => $title
		fill_in 'Traduction', :with => $txt2
		select '*Translate to', :from => $lang2
		click_on 'Déposer'
	end

	scenario "Rechercher une oeuvre" do
		visit '/work/search'
		fill_in 'Titre', :with => $title
		click_on 'Rechercher'
		page.should have_content $title
	end

	scenario "Rechercher une traduction en anglais" do
		visit '/work/index'
		page.should have_content $author
		page.should have_content $lang1
		page.should have_content $title
		click_on $title
		page.should_not have_content $lang3
		# WHATS IN THE BOOOOOOOOOOOOOX ?!?!?!?!?
		page.should have_content $lang2
		# NO ENGLISH BUT ITALIAN WAS HERE
	end

	scenario "Proposer une traduction" do
		visit '/work/index'
		page.should have_content $author
		page.should have_content $lang1
		page.should have_content $title
		click_on $title
		click_on '*Plus'
		fill_in '*Titre', :with => $title
		fill_in 'Traduction', :with => $txt2
		select '*Translate to', :from => $lang2
		click_on 'Déposer'
	end
end
