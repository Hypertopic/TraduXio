require 'spec_helper'

feature 'Create a translation' do

    background do
        visit '/works/'
        expect(page).to have_content 'Howard Phillips Lovecraft'
        page.find('li.author.closed',text:'Howard Phillips Lovecraft').trigger(:click)
        click_on 'Fungi from Yuggoth'
        if page.has_selector? "th[data-version='Aurélien Bénel']" then
          page.first("th[data-version='Aurélien Bénel'] input.edit").click
          page.first("th[data-version='Aurélien Bénel'] span.delete").click
          accept_alert
          expect(page).not_to have_selector "th[data-version='Aurélien Bénel']"
        end
    end

    scenario 'Create a first translation' do
      create_translation('Aurélien Bénel')
      expect(page).to have_translation("Aurélien Bénel")

      edit_translation_metadata('Aurélien Bénel',:date=>'2015',:title=>"La Lampe",:language=>'fr')
      translation=find_translation('Aurélien Bénel')

      fill_block('Aurélien Bénel',0,'LA LAMPE')
      fill_block('Aurélien Bénel',1,"Nous trouvâmes la lampe à l'intérieur de ces cavités rocheuses\n"+
                                   "Aux signes sculptés qu'aucun prêtre de Thèbes ne déchiffra jamais\n"+
                                   "Et dont les hiéroglyphes effrayés de leurs cavernes\n"+
                                   "avertissaient toute créature vivante engendrée par la terre.")
      read_translation('Aurélien Bénel')
      expect(page).to have_content("LA LAMPE")
      expect(page).to have_content("Nous trouvâmes la lampe")
      expect(translation).to have_metadata('date','2015')
      expect(translation).to have_metadata('title','La Lampe')
      expect(translation).to have_metadata('language','fr')
    end

end
