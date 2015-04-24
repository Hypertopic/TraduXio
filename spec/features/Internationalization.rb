require 'spec_helper'

feature 'Localization' do

    scenario 'french' do
        prefer_language('ko;e=1,fr-FR;q=0.9,fr;q=0.8,en;q=0.7')
        visit '/works/'
        click_on 'Ajouter une œuvre'
        expect(page).to have_content 'Œuvre originale'
    end

    scenario 'english' do
        prefer_language('en-au,en-us,en,fr')
        visit '/works/'
        click_on 'Add a work'
        expect(page).to have_content 'Original work'
    end

end
