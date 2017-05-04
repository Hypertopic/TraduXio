feature 'Compare translations' do

  background 'Open work' do
    open_work "Howard Phillips Lovecraft", "The lamp (Fungi from Yuggoth, 6)"
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
