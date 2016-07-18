require 'spec_helper'

feature 'Create a translation' do

    background 'Open work' do
      open_work "Howard Phillips Lovecraft", "Fungi from Yuggoth"
    end

    scenario 'Delete translation' do
      delete_translation "Aurélien Bénel"
      expect(page).not_to have_translation "Aurélien Bénel"
      delete_translation "François Truchaud"
      expect(page).not_to have_translation "François Truchaud"
    end

    scenario 'Create a first translation' do
      create_translation('Aurélien Bénel')
      expect(page).to have_translation("Aurélien Bénel")
    end

    scenario 'Edit metadata' do
      edit_translation_metadata('Aurélien Bénel',:date=>'2008',:title=>"La Lampe",:language=>'fr')
      read_translation('Aurélien Bénel')
      debug "check metadata"
      translation=find_open_translation('Aurélien Bénel')
      expect(translation).to have_metadata('date','2008')
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

    scenario 'Create a second translation' do
      create_translation('François Truchaud')
      expect(page).to have_translation("François Truchaud")
      edit_translation_metadata('François Truchaud',:title=>"La Lampe",:language=>'fr')
      read_translation('François Truchaud')
      translation=find_open_translation('François Truchaud')
      expect(translation).to have_metadata('creator','François Truchaud')
      expect(translation).to have_metadata('title','La Lampe')
      expect(translation).to have_metadata('language','fr')
      debug "checked metadata"
      edit_translation('François Truchaud')
      fill_block('François Truchaud',0,'LA LAMPE')
      fill_block('François Truchaud',1,"Nous trouvâmes la lampe à l’intérieur de ces falaises creuses\n"+
                                   "Aux signes sculptés qu’aucun prêtre de Thèbes ne déchiffra jamais\n"+
                                   "Et les effrayants hiéroglyphes de ces cavernes étaient\n"+
                                   "Un avertissement pour toute créature vivante de l’espèce humaine..")
      read_translation('François Truchaud')
      debug "checking content"
      expect(page).to have_content("LA LAMPE")
      expect(page).to have_content("Aux signes sculptés")
      debug "checked content"
    end

end
