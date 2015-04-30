require '../spec_helper.rb'

feature 'Create a translation' do

    background do
        visit 'works/'
        click_on 'Ajouter une oeuvre'
        fill_in 'title', :with => 'The Raven'
        fill_in 'work-creator', :with => 'Edgar Allan Poe'
	find('input[type="submit"][name=""]').click
	sleep 5
	first('.edit').click
	fill_in '.fulltext', :with => 'This is a test \n\n This is not a test'
	first('.edit').click
    end

    scenario 'Create a translation' do
		click_on '.addVersion'
		fill_in '//#addPanel/input[type="text"]', :with => 'Le Corbeau'
		find('//#addPanel/input[type="submit"]').click
		click_on 'Choose your language', :with => 'Français'
		fill_in 'translator', :with => 'Martin Dupont'
		page.should have_field 'author', :with => 'Edgar Allan Poe'
		fill_in block(2,1), :with => 'Une fois, sur le minuit lugubre pendant que je méditais, faible et fatigué,'
		fill_in block(2,2), :with => 'Sur maint précieux et curieux volume d une doctrine oubliée'
		page.should have_field block(2,1), :with => 'Une fois, sur le minuit lugubre pendant que je méditais, faible et fatigué,'
		page.should have_field block(2,2), :with => 'Sur maint précieux et curieux volume d une doctrine oubliée'
		click_on 'link_fields'
		page.should have_field block(2,1), :with => 'Une fois, sur le minuit lugubre pendant que je méditais, faible et fatigué, Sur maint précieux et curieux volume d une doctrine oubliée'
		page.should_not have_field block(2,2)
		page.should have_button 'cut_fields'
		click_on 'block1'
		click_on 'cut_fields'
		page.should have_field block(2,1), :with => 'Une fois, sur le minuit lugubre pendant que je méditais, faible et fatigué,'
		page.should have_field block(2,2), :with => 'Sur maint précieux et curieux volume d une doctrine oubliée'
		page.should have_button 'link_fields'
    end 

end
