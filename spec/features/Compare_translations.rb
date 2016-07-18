require 'spec_helper'

feature 'Compare translations' do

  background 'Open work' do
    open_work "Howard Phillips Lovecraft", "Fungi from Yuggoth"
  end

  scenario 'Compare translations' do
    expect(page).to have_content 'We found the lamp inside those hollow cliffs'
    expect(page).to have_content 'Nous trouvâmes la lampe à l\'intérieur de ces cavités rocheuses'
    expect(page).to_not have_content 'Nous trouvâmes la lampe à l’intérieur de ces falaises creuses'
    open_translation 'François Truchaud'
    expect(page).to have_content 'We found the lamp inside those hollow cliffs'
    expect(page).to have_content 'Nous trouvâmes la lampe à l\'intérieur de ces cavités rocheuses'
    expect(page).to have_content 'Nous trouvâmes la lampe à l’intérieur de ces falaises creuses'
    close_translation 'Aurélien Bénel'
    expect(page).not_to have_content 'Nous trouvâmes la lampe à l\'intérieur de ces cavités rocheuses'
  end

end
