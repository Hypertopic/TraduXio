require_relative '../spec_helper'

feature 'Search for a concordance' do

	scenario 'Research a valid sequence of words' do
		visit '/'
		click_on 'The lamp (Fungi from Yuggoth, 6)'
		fill_in 'Rechercher', :with => 'the ancient oil'
		click_on 'Rechercher'
		in_bold().should equal 'the ancient oil'
		page.should have_content 'Trad. François Truchaud'
		page.should have_content 'Trad. Aurélien Bénel'
	end
	
	scenario 'Research a sequence of words in the wrong order' do
		visit '/'
		click_on 'The lamp (Fungi from Yuggoth, 6)'
		fill_in 'Rechercher', :with => 'ancient the'
		click_on 'Rechercher'
		page.should_not have_content 'Trad. François Truchaud'
		page.should_not have_content 'Trad. Aurélien Bénel'
	end
	
	scenario 'Research a beginning of a word' do
		visit '/'
		click_on 'The lamp (Fungi from Yuggoth, 6)'
		fill_in 'Rechercher', :with => 'anc'
		click_on 'Rechercher'
		in_bold().should equal 'anc'
		page.should have_content 'Trad. François Truchaud'
		page.should have_content 'Trad. Aurélien Bénel'
	end

end