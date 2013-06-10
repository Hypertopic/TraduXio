require 'spec_helper'

feature 'Copyright translations' do

    scenario 'Free work, free and non-free translations' do
        visit '/works'
        click_on 'en'
		page.should have_author 'Howard Phillips Lovecraft'
		click_on 'Howard Phillips Lovecraft'
		page.should have_link 'The lamp (Fungi from Yuggoth, 6)'
		click_on 'The lamp (Fungi from Yuggoth, 6)'
		page.should have_content 'We found the lamp inside those hollow cliffs'				
		page.should have_content 'Nous trouvâmes la lampe à l\'intérieur de ces cavités rocheuses'
		page.should_not have_link 'François Truchaud'
    end
    
    scenario 'Free work and non-free translation' do
		visit '/works'
		click_on 'en'
		click_on 'Howard Phillips Lovecraft'
		page.should have_content 'Pas de traduction'
		click_on 'Recognition (Fungi from Yuggoth, 4)'
		page.should have_content 'The day had come again, when as a child'
		page.should_not have_content 'Ce jour était revenu où, étant enfant'
    end
	
	scenario 'No work and free translation' do
		visit '/works'
		click_on 'gr'
		click_on 'Anonyme'
		click_on 'Genesis 8'
		page.should have_content 'Dieu se souvient de Noé'
	end
	
	scenario 'Non-free work and free translation' do
		visit '/works'
		click_on 'fr'
		click_on 'NTM'
		click_on 'La fièvre'
		page.should_not have_content 'Tout a débuté un matin quand à dix heures dix'
		page.should have_content 'Tudo começou uma manhã quando às dez e dez'
	end

end