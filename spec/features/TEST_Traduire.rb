require 'spec_helper'

feature 'Translation' do

    scenario 'Add a translation' do
          visit '/'
          click_on 'The plus'
          fill_in 'Title'
          click_on 'Choose your language'
          page.should have_author 'Edgar Allan Poe'
          page.should have_traductor'Martin Dupont'
          fill_in 'blocs'
    end

    scenario 'Link blocs' do
          visit '/'
          page.should have_author 'Edgar Allan Poe'
          page.should have_traductor'Martin Dupont'
          page.should have_blocs 'une fois, sur le minuit lugubre ...'
          page.should have_title 'Le corbeau'
          page.should have_language 'Français'
          click_on 'link'
          page.should have_blocs 'une fois, sur le minuit lugubre ...  Sur maint précieux et curieux volume ...'
          page.should have_button 'cut'
    end

    scenario 'Edit a translation' do
        visit '/'
        page.should have_author 'Edgar Allan Poe'
        page.should have_traductor'Martin Dupont'
        page.should have_blocs 'une fois, sur le minuit lugubre ...'
        page.should have_title 'Le corbeau'
        page.should have_language 'Français'
        click_on 'blocs'
        edit 'blocs'
    end

end
