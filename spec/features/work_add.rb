require 'spec_helper'

feature 'Add a work' do

  background do
    visit '/'
    click_on 'Start'
  end

  scenario 'with an original version' do
    title=random_title
    author=random_author
    click_on 'Add a work'
    fill_in 'Title', :with => title
    fill_in 'Author', :with => author
    fill_select 'language','en'
    fill_in 'Date, year, or text century', :with => '1930'
    check 'Original work'
    click_on 'Create'
    expect(page).to have_content "#{title} – #{author}"
    expect(page).not_to have_content 'Trans.'
    click_on 'Edit', :match => :first
    fill_in 'text', :with => sample('the_lamp')
    click_on 'Read', :match => :first
    expect(row(1)).to have_content 'THE LAMP'
    expect(row(2)).to have_content 'We found the lamp'
    expect(row(3)).to have_content 'No more was there'
  end

  anonymus_title=random_title

  scenario 'without an original version (and a known author)' do
    click_on 'Add a work'
    fill_in 'Title', :with => anonymus_title
    fill_select 'language','he'
    click_on 'Create'
    expect(page).to have_content "#{anonymus_title} – Anonymus"
    expect(page).to have_content 'Trans.'
  end

  scenario 'Delete full work' do
    open_work "Anonymus",anonymus_title
    delete_full_work
    expect(page).not_to have_content "#{anonymus_title} – Anonymus"
  end

end
