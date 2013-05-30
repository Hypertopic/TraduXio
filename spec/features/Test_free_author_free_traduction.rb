require_relative '../spec_helper'

feature 'Droit d''auteurs' do

    scenario 'Oeuvre libre et traduction libre' do
          visit '/'
          click_on 'en'
  	  page.should have_author 'Howard Phillips Lovecraft'
		  click_on 'Howard Phillips Lovecraft'
		  page.should have_link 'The lamp (Fungi from Yuggoth, 6)'
		  click_on 'The lamp (Fungi from Yuggoth, 6)'
		  
		  #/** LOADING: REDIRECTING TO THE TEXT PAGE **/
		  
		  page should have_content 'We found the lamp inside those hollow cliffs
									Whose chiseled sign no priest in Thebes could read
									And from whose caverns frightened hieroglyphs
									Warned every living creature of earth''s breed.
									No more was there - just that one brazen bowl
									With traces of a curious oil within;
									Fretted with some obscurely patterned scroll,
									And symbols hinting vaguely of strange sin.
										
									Little the fears of forty centuries meant
									To us as we bore off our slender spoil,
									And when we scanned it in our darkened tent
									We struck a match to test the ancient oil.
									It blazed - great God!... But the vast shapes we saw
									In that mad flash have seared our lives with awe.'
									
									
		page should have_content '  Nous trouvâmes la lampe à l''intérieur de ces cavités rocheuses
									Aux signes sculptés qu''aucun prêtre de Thèbes ne déchiffra jamais
									Et dont les hiéroglyphes effrayés de leurs cavernes
									avertissaient toute créature vivante engendrée par la terre. 
									Insignifiantes étaient les peurs de quarante siècles
									à nos yeux alors que nous emportions notre maigre butin
									Et en l''examinant dans l''obscurité de nos tentes,
									nous frottâmes une allumette pour tester l''ancienne huile.
									Elle flamba - grand Dieu ! ... Mais les vastes'	

		page should_not have_link 'François Truchaud'
    end

end
