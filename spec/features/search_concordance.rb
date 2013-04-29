require 'spec_helper'

feature 'Search for a concordance' do

  #$regular_expression = '"la petite maison" OR "barricade ou démolition" '
  #$title = 'Le bac'
  #$title2 = 'Amandine ou les deux jardins'
  $expression = 'petit'

  background do
  visit '/'
    click_on 'Concordance' 
  end

  #scenario 'based on a regular expression' do
    #fill_in 'expression', :with => $regular_expression   
    #click_on 'Recherche'
	#page.should have_content $title
	#page.should_not have_content $title2
  #end
  
  scenario 'based on an expression' do
	fill_in 'expression', :with => $word
	click_on 'Recherche'
	each in_bold().should equals $word
	
	#page.should have_content 'pauvre petit bébé'
	#page.should have_content 'la petite maison'
	#page.should have_content 'quatre petits y étaient'
	#page.should have_content 'mes petites amies'
  end

end
