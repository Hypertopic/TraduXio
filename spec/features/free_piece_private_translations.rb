###
# This acceptance test is based on the V2.0 prototype of Traduxio
# (http://traduxio.test.hypertopic.org/)
###

require 'spec_helper'

feature 'Free piece, private transaltions' do
  scenario 'navigate and compare translation' do
		visit '/works'
		click_on 'en'
		click_on 'Howard Phillips Lovecraft'
		page.should have_content 'Pas de traduction'
		click_on 'Recognition (Fungi from Yuggoth, 4)'
		page.should have_content 'The day had come again, when as a child'
		page.should_not have_content 'Ce jour était revenu où, étant enfant'
	end
end
