require 'spec_helper'

feature 'List works' do
  scenario 'List languages' do
    visit '/works/'
    expect(page).to have_content 'English'
  end

  scenario 'Load work' do
    visit '/works/'
    expect(page).to have_content 'Howard Phillips Lovecraft'
    page.find('li.author.closed',text:'Howard Phillips Lovecraft').trigger(:click)
    expect(page).to have_content 'The lamp (Fungi from Yuggoth, 6)'
    click_on 'The lamp (Fungi from Yuggoth, 6)'
    expect(page).to have_content 'We found the lamp inside those hollow cliffs'
  end

end
