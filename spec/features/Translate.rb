require 'spec_helper'

feature 'Translation' do

    background do
        visit '/works'
        click_on '+'
        fill_in 'title', :with => 'The Raven'
        fill_in 'author', :with => 'Edgar Allan Poe'
        fill_in 'originalBlock1', :with => 'Once upon a midnight dreary, while I pondered, weak and weary,'
        fill_in 'originalBlock2', :with => 'Over many a quaint and curious volume of forgotten lore'
        click_on 'Save'
    end
  

    scenario 'translation' do

          
          click_on '+'
          fill_in 'Title', :with => 'Le Corbeau'
          click_on 'Choose your language', :with => 'Français'
          page.should have_field  'Translator', :with => 'Martin Dupont'
          page.should have_field 'author', :with => 'Edgar Allan Poe'
          
          fill_in 'translationBlock1' , :with => 'Une fois, sur le minuit lugubre pendant que je méditais, faible et fatigué,'
          fill_in 'translationBlock2' , :with => 'Sur maint précieux et curieux volume d une doctrine oubliée'
          
          click_on 'link'
          
          page.should have_field 'translationBlock1' , :with => 'Une fois, sur le minuit lugubre pendant que je méditais, faible et fatigué, Sur maint précieux et curieux volume d une doctrine oubliée'
          
          page.should_not have_field 'translationBlock2' 
          
          page.should have_button 'cut_fields'
          
          click_on 'translationBlock1'
          fill_in 'translationBlock1'
          
          click_on 'cut_fields'
          
          page.should have_field 'translationBlock1' , :with => 'Une fois, sur le minuit lugubre pendant que je méditais, faible et fatigué,'
          page.should have_field 'translationBlock2' , :with => 'Sur maint précieux et curieux volume d une doctrine oubliée'
          
          page.should have_button 'link_fields'
    end 

end
