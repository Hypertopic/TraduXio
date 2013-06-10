require 'spec_helper'

feature 'Compare translations' do

	scenario 'Compare translations' do
		visit '/works'
		click_on 'The lamp (Fungi from Yuggoth, 6)'
		page.should have_content 'We found the lamp inside those hollow cliffs'
		page.should have_content 'Nous trouvâmes la lampe à l\'intérieur de ces cavités rocheuses'
		page.should_not have_content 'Nous trouvâmes la lampe à l’intérieur de ces falaises creuses'
		click_on 'François Truchaud'
		page.should have_content 'We found the lamp inside those hollow cliffs'
		page.should have_content 'Nous trouvâmes la lampe à l\'intérieur de ces cavités rocheuses'
		page.should have_content 'Nous trouvâmes la lampe à l’intérieur de ces falaises creuses'
		click_on 'Aurélien Bénel'
		page.should_not have_content 'Nous trouvâmes la lampe à l\'intérieur de ces cavités rocheuses'
	end
	
end
