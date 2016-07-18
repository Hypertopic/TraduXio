require 'spec_helper'

feature 'Create a translation' do

    background 'Open work' do
      open_work "Howard Phillips Lovecraft", "Fungi from Yuggoth"
    end

    scenario 'Delete translation' do
      if has_translation? "Aurélien Bénel" then
        edit_translation("Aurélien Bénel")

        debug "delete"
        find_translation("Aurélien Bénel").find("span.delete").click
        debug "confirm"
        accept_alert
        debug "check deleted"
        expect(page).not_to have_selector "th[data-version='Aurélien Bénel']"
        debug "deleted"
      end
    end

    scenario 'Create a first translation' do
      create_translation('Aurélien Bénel')
      expect(page).to have_translation("Aurélien Bénel")
    end

    scenario 'Edit metadata' do
      edit_translation_metadata('Aurélien Bénel',:date=>'2015',:title=>"La Lampe",:language=>'fr')
      read_translation('Aurélien Bénel')
      debug "check metadata"
      translation=find_translation('Aurélien Bénel')
      expect(translation).to have_metadata('date','2015')
      expect(translation).to have_metadata('title','La Lampe')
      expect(translation).to have_metadata('language','fr')
      debug "checked metadata"
    end

    scenario 'Edit text' do
      edit_translation('Aurélien Bénel')
      fill_block('Aurélien Bénel',0,'LA LAMPE')
      fill_block('Aurélien Bénel',1,"Nous trouvâmes la lampe à l'intérieur de ces cavités rocheuses\n"+
                                   "Aux signes sculptés qu'aucun prêtre de Thèbes ne déchiffra jamais\n"+
                                   "Et dont les hiéroglyphes effrayés de leurs cavernes\n"+
                                   "avertissaient toute créature vivante engendrée par la terre.")
      read_translation('Aurélien Bénel')
      debug "checking content"
      expect(page).to have_content("LA LAMPE")
      expect(page).to have_content("Nous trouvâmes la lampe")
      debug "checked content"
    end

end
