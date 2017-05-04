feature 'List works' do

  scenario 'List languages' do
    visit '/works/'
    expect(page).to have_content 'English'
  end

  scenario 'Load work' do
    open_work 'Howard Phillips Lovecraft', 'The lamp (Fungi from Yuggoth, 6)'
    expect(page).to have_content 'We found the lamp inside those hollow cliffs'
  end

end
