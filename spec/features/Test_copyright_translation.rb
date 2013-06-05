require 'spec_helper'

feature 'Copyright translation' do

    scenario 'free work et free translation' do
        visit '/works'
        click_on 'en'
  	page.should have_author 'Howard Phillips Lovecraft'
	click_on 'Howard Phillips Lovecraft'
	page.should have_link 'The lamp (Fungi from Yuggoth, 6)'
	click_on 'The lamp (Fungi from Yuggoth, 6)'
		  
	 #/** LOADING: REDIRECTING TO THE TEXT PAGE **/
		  
	page.should have_content 'We found the lamp inside those hollow cliffs Whose chiseled sign no priest in Thebes could read'
								
	page.should have_content 'Nous trouvâmes la lampe à l\'intérieur de ces cavités rocheuses Aux signes sculptés qu\'aucun prêtre de Thèbes ne déchiffra jamais'

	page.should_not have_link 'François Truchaud'
    end
    
    scenario 'free work private translation' do
	visit '/works'
	click_on 'en'
	click_on 'Howard Phillips Lovecraft'
	page.should have_content 'Pas de traduction'
	click_on 'Recognition (Fungi from Yuggoth, 4)'
	page.should have_content 'The day had come again, when as a child'
	page.should_not have_content 'Ce jour était revenu où, étant enfant'
    end

end
