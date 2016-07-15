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

    scenario 'Create a translation' do
      page.find("a#addVersion").trigger(:click)
      fill_in 'work-creator', :with => 'Aurélien Bénel'
      until page.has_selector?("th.pleat.open[data-version='Aurélien Bénel']") do
        begin
          page.find('input[name=do-create]').click
          page.save_screenshot("created.png")
          wait_for_element("th.pleat.open[data-version='Aurélien Bénel']")
        rescue RuntimeError
        end
      end
      within ("thead.header th.pleat.open[data-version='Aurélien Bénel']") do
        save_screenshot("passed.png")
        expect(page).to have_field('creator', with: 'Aurélien Bénel')

        fill_field('date','2015')
        fill_field('title','La Lampe')
        fill_select('language','fr')

        save_screenshot("filled1.png")

        save_screenshot("filled.png")
      end

      fill_block('Aurélien Bénel',0,'LA LAMPE')
      fill_block('Aurélien Bénel',1,"Nous trouvâmes la lampe à l'intérieur de ces cavités rocheuses\n"+
                                   "Aux signes sculptés qu'aucun prêtre de Thèbes ne déchiffra jamais\n"+
                                   "Et dont les hiéroglyphes effrayés de leurs cavernes\n"+
                                   "avertissaient toute créature vivante engendrée par la terre.")
      page.save_screenshot("filled.png")
      first("thead.header th.pleat.open[data-version='Aurélien Bénel']").click_on 'Read'
      expect(page).to have_content("LA LAMPE")
      expect(page).to have_content("Nous trouvâmes la lampe")
      page.save_screenshot("saved.png")
    end

end
