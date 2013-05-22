require 'spec_helper'

feature 'Translation' do
    $title = 'The Raven'
    $author = 'Edgar Allan Poe'


    background do
        visit '/'
        click_on '+'
        fill_in 'title', :with => 'The Raven'
        fill_in 'author', :with => 'Edgar Allan Poe'
        fill_in 'core', :with => '0nce upon a midnight dreary ... Over many a quaint and curious volume ... '
        click_on 'Enregistrer'
    end
  

    scenario 'Translation' do
          visit '/work/index'
          click_on 'Edgar Allan Poe'
          click_on 'The Raven'
          
          visit '/work/newread/id/41894#tr42023'
          
          click_on '+'
          fill_in 'Title', :with => 'Le Corbeau'
          click_on 'Choose your language', :with => 'Français'
          page.should have_field  'Translator', :with => 'Martin Dupont'
          page.should have_field 'author', :with => 'Edgar Allan Poe'
          
          fill_in 'content1' , :with => 'Une fois, sur le minuit lugubre ...'
          fill_in 'content2' , :with => 'Sur maint précieux et curieux volume ...'
          
          click_on 'link'
          
          page.should have_field 'content1' , :with => 'Une fois, sur le minuit lugubre ...  Sur maint précieux et curieux volume ...'
          
          page.should_not have_field 'content2' 
          
          page.should have_button 'cut_fields'
          
          click_on 'content1'
          fill_in 'content1'
          
          click_on 'cut_fields'
          
          page.should have_field 'content1' , :with => 'une fois, sur le minuit lugubre ...'
          page.should have_field 'content2' , :with => 'Sur maint précieux et curieux volume ...'
          
          page.should have_button 'link_fields'
    end

end
