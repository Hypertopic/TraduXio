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
    expect(page).to have_content 'Fungi from Yuggoth'
    click_on 'Fungi from Yuggoth'
    expect(page).to have_content 'We found the lamp inside those hollow cliffs'
  end

  scenario 'Add work' do
    visit '/works/'
    click_on 'Add a work'
    #print page.html
    page.save_screenshot('AddWork.png')
  end
end
