###
# This acceptance test is based on the V2.0 prototype of Traduxio
# (http://traduxio.test.hypertopic.org/)
###

require 'spec_helper'

feature 'No original work but translations' do
  scenario 'navigate and compare translation' do
		visit '/works'
		click_on 'gr'
		click_on 'Jesus'
		page.should have_content 'Pas d\'original'
		click_on 'Genesis 8'
		page.should have_content 'Dieu se souvient de Noé\net de tous les animaux\nde toutes les bêtes'
		page.should have_content 'And God remembered Noah, and every living thing, and all the cattle'
	end
end
