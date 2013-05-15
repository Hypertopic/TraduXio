require 'spec_helper'

feature 'Translation' do

    scenario 'Translation' do
          visit '/'
          click_on 'The plus'
          fill_in 'Title'
          click_on 'Choose your language'
          page.should have_author 'Edgar Allan Poe'
          page.should have_traductor'Martin Dupont'
          fill_in 'contents'
          click_on 'link'
          page.should have_content 'une fois, sur le minuit lugubre ...  Sur maint précieux et curieux volume ...'
          page.should have_button 'cut'
          click_on 'contents'
          edit 'contents'
          click_on 'cut'
          page.should have_content 'une fois, sur le minuit ...'
          page.should have_content 'Sur maint précieux et curieux volume ...'
          page.should have_button 'link'
    end

end
