require 'spec_helper'

feature 'Create a translation' do

    background do
        visit '/works'
        click_on '+'
        fill_in 'title', :with => 'The Raven'
        fill_in 'author', :with => 'Edgar Allan Poe'
        fill_in block(1,1), :with => 'Once upon a midnight dreary, while I pondered, weak and weary,'
        fill_in block(1,2), :with => 'Over many a quaint and curious volume of forgotten lore'
        click_on 'Save'
    end

    scenario 'Create a translation' do
		click_on '+'
		fill_in 'title', :with => 'Le Corbeau'
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
