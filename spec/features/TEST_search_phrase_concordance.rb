scenario 'Rechercher une suite de mots dans la concordance' do

visit 'http://traduxio.test.hypertopic.org'
click_on 'The lamp (Fungi from Yuggoth, 6)'


fill_in 'Rechercher', :with => 'the ancient oil'
click_on 'Rechercher'

in_bold() should_equals 'the ancient oil'


page.should have_content 'Trad. François Truchaud'
page.should have_content 'Trad. Aurélien Bénel'

end
