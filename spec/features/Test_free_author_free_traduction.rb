require 'spec_helper'

feature 'copyright' do

    scenario 'Free work et free translation' do
          visit '/works'
          click_on 'en'
  	  page.should have_author 'Howard Phillips Lovecraft'
		  click_on 'Howard Phillips Lovecraft'
		  page.should have_link 'The lamp (Fungi from Yuggoth, 6)'
		  click_on 'The lamp (Fungi from Yuggoth, 6)'
		  
		  #/** LOADING: REDIRECTING TO THE TEXT PAGE **/
		  
		  page.should have_content 'We found the lamp inside those hollow cliffs'
									
									
		page.should have_content 'Nous trouvâmes la lampe à l\'intérieur de ces cavités rocheuses'	

		page.should_not have_link 'François Truchaud'
    end

end
