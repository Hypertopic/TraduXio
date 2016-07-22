require 'spec_helper'

feature 'Add a work' do

  background do
    visit '/'
    click_on 'Start'
  end

  scenario 'with an original version' do
    click_on 'Add a work'
    fill_in 'Title', :with => 'Fungi from Yuggoth'
    fill_in 'Author', :with => 'Howard Phillips Lovecraft'
    fill_select 'language','en'
    fill_in 'Date, year, or text century', :with => '1930'
    check 'Original work'
    click_on 'Create'
    expect(page).to have_content 'Fungi from Yuggoth – Howard Phillips Lovecraft'
    expect(page).not_to have_content 'Trans.'
    click_on 'Edit', :match => :first
    fill_in 'text', :with => sample('the_lamp')
    click_on 'Read', :match => :first
    expect(row(1)).to have_content 'THE LAMP'
    expect(row(2)).to have_content 'We found the lamp'
    expect(row(3)).to have_content 'No more was there'
  end

  scenario 'without an original version (and a known author)' do
    click_on 'Add a work'
    fill_in 'Title', :with => 'Genesis'
    fill_select 'language','he'
    click_on 'Create'
    expect(page).to have_content 'Genesis – Anonymus'
    expect(page).to have_content 'Trans.'
  end

  scenario 'Delete full work' do
    open_work "Anonymus","Genesis"
    delete_full_work
    expect(page).not_to have_content 'Genesis – Anonymus'
  end

end
