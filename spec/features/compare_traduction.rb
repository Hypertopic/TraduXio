###
# This acceptance test is based on the V2.0 prototype of Traduxio 
# (http://traduxio.test.hypertopic.org/)
# It will be possible to display more than two translation at the same time in the V2.1 prototype. 
# This test should be updated in order to validate this new feature.   
###

require 'spec_helper'
feature 'Navigate and compare translation' do
	scenario 'navigate and compare translation' do
		visit '/'
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
