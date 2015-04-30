require '../spec_helper.rb'

feature 'Create work original' do
	
	scenario 'Create work original' do

		visit 'works/'
	        click_on 'Ajouter une oeuvre'
	        fill_in 'title', :with => 'The Raven'
	        fill_in 'work-creator', :with => 'Edgar Allan Poe'
	        find('input[type="submit"][name=""]').click
		page.should have_content 'The Raven'
		page.should have_content 'Edgar Allan Poe'

	end

end
