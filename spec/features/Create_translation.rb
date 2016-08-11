require 'spec_helper'

feature 'Create a translation' do

    background 'Open work' do
      open_work "Howard Phillips Lovecraft", "The lamp (Fungi from Yuggoth, 6)"
    end

    trans1_author=random_author
    trans1_title=random_title

    scenario 'Create a first translation' do
      create_translation(trans1_author)
      expect(page).to have_translation(trans1_author)
    end

    scenario 'Edit metadata' do
      open_translation trans1_author
      edit_translation_metadata(trans1_author,:date=>'2008',:title=>trans1_title,:language=>'fr')
      read_translation trans1_author
      debug "check metadata"
      translation=find_open_translation trans1_author
      expect(translation).to have_metadata('date','2008')
      expect(translation).to have_metadata('title',trans1_title)
      expect(translation).to have_metadata('language','fr')
      debug "checked metadata"
    end

    scenario 'Edit text' do
      open_translation trans1_author
      edit_translation trans1_author
      fill_block(trans1_author,0,'LA LAMPE')
      fill_block(trans1_author,1,"Nous trouvâmes la lampe à l'intérieur de ces cavités rocheuses\n"+
                                   "Aux signes sculptés qu'aucun prêtre de Thèbes ne déchiffra jamais\n"+
                                   "Et dont les hiéroglyphes effrayés de leurs cavernes\n"+
                                   "avertissaient toute créature vivante engendrée par la terre.")
      read_translation(trans1_author)
      debug "checking content"
      expect(page).to have_content("LA LAMPE")
      expect(page).to have_content("Nous trouvâmes la lampe")
      debug "checked content"
    end

    trans2_author=random_author
    trans2_title=random_title

    scenario 'Create a second translation' do
      create_translation(trans2_author)
      expect(page).to have_translation(trans2_author)
      edit_translation_metadata(trans2_author,:title=>trans2_title,:language=>'fr')
      read_translation(trans2_author)
      translation=find_open_translation(trans2_author)
      expect(translation).to have_metadata('creator',trans2_author)
      expect(translation).to have_metadata('title',trans2_title)
      expect(translation).to have_metadata('language','fr')
      debug "checked metadata"
      edit_translation(trans2_author)
      fill_block(trans2_author,0,'LA LAMPE')
      fill_block(trans2_author,1,"Nous trouvâmes la lampe à l’intérieur de ces falaises creuses\n"+
                                   "Aux signes sculptés qu’aucun prêtre de Thèbes ne déchiffra jamais\n"+
                                   "Et les effrayants hiéroglyphes de ces cavernes étaient\n"+
                                   "Un avertissement pour toute créature vivante de l’espèce humaine..")
      read_translation(trans2_author)
      debug "checking content"
      expect(page).to have_content("LA LAMPE")
      expect(page).to have_content("Aux signes sculptés")
      debug "checked content"
    end

    scenario 'Delete translation' do
      delete_translation trans1_author
      expect(page).not_to have_translation trans1_author
      delete_translation trans2_author
      expect(page).not_to have_translation trans2_author
    end

end
