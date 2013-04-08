require 'spec_helper'

feature 'Do a concordance' do

  $a_title = a_string()

  background do
    visit '/work/index'
    click_on 'Déposer un nouveau texte' 
    fill_in 'Titre', :with => $a_title
    fill_in 'Auteur', :with => 'Wikipedia (Français)'
    fill_in 'Contenu du Texte', :with => "L'IA souligne la difficulté à expliciter toutes les connaissances utiles à la résolution d'un problème complexe."
    select 'Langue Original', :from => 'Français'    
    click_on 'Déposer'
	click_on 'Créer une traduction'
	fill_in '*Translator', :with => 'Wikipedia (Anglais)'
	fill_in '*Titre', :with => $a_title + ' (Translation)'
	select '*Translate to', :from => 'Anglais'
	click_on 'Déposer'
	fill_in 'Bloc 1', :with => "AI highlights the difficulty of explaining all the knowledge needed to solve a complex problem."
	visit '/login/logout'
  end

  scenario 'View an extract of a private traduction in a concordance' do
  visit '/translation/concord'
    fill_in 'Recherche', :with => 'AI'
    click_on 'Recherche'
    page.should have_content $a_title + ' (Translation)'
    page.should have_content "AI highlights the difficulty of explaining all the knowledge needed to solve a complex problem."
    page.should have_content "L'IA souligne la difficulté à expliciter toutes les connaissances utiles à la résolution d'un problème complexe."
    page.should have_content 'Wikipedia (Français)'
  end

end
